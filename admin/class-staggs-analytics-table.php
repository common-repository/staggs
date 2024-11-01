<?php

/**
 * The admin-specific analytics table display of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * The admin-specific analytics table display of the plugin.
 *
 * Defines the plugin analytics table view.
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Analytics_Table extends WP_List_Table {

	/**
	 * Track total result count.
	 */
	public static $total_results;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Analytics', 'staggs' ),
			'plural'   => __( 'Analytics', 'staggs' ),
			'ajax'     => false
		) );
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'analytics_per_page', get_option( 'posts_per_page' ) );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args(array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		));

		$this->items = self::get_analytics_items( $per_page, $current_page );
	}

	/**
	 * Retrieve customer’s data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_analytics_items( $per_page = 5, $page_number = 1 ) {
		$product_rows = self::get_all_configurable_order_items();

		// Sorting.
		if ( isset( $_GET['order'] ) && isset( $_GET['orderby'] ) ) {
			$keys = array_column( $product_rows, $_GET['orderby'] );
			$sort = 'asc' == $_GET['order'] ? SORT_ASC : SORT_DESC;

			array_multisort($keys, $sort, $product_rows);
		}

		set_transient( 'staggs_total_results', $product_rows );

		// Pagination.
		$offset = ($page_number - 1) * $per_page;
		$product_rows = array_splice($product_rows, $offset, $per_page);

		return $product_rows;
	}

	/**
	 * Get table total items
	 *
	 * @since    1.3.4
	 */
	public static function get_all_configurable_order_items() {

		$order_statuses = apply_filters( 
			'staggs_analytics_order_statuses', 
			staggs_get_theme_option( 'sgg_analytics_order_statusses' )
		);

		$args = array(
			'status' => $order_statuses,
		);

		$filtered_product_id = false;
		if ( isset( $_GET['product'] ) && '' !== $_GET['product'] ) {
			$filtered_product_id = (int) sanitize_key( $_GET['product'] );
			$order_ids = self::get_order_ids_by_product_id( $filtered_product_id );

			$args['post__in'] = $order_ids;
		}

		if ( isset( $_GET['date_from'] ) && '' !== $_GET['date_from'] ) {
			if ( isset( $_GET['date_to'] ) && '' !== $_GET['date_to'] ) {
				$date_from = sanitize_text_field( $_GET['date_from'] );
				$date_to   = sanitize_text_field( $_GET['date_to'] );

				$args['date_query'] = array(
					'after' => $date_from,
					'before' => $date_to,
				);
			} else {
				$date_from = sanitize_text_field( $_GET['date_from'] );
				$args['date_query'] = array(
					'after' => $date_from,
				);
			}
		} else if ( isset( $_GET['date_to'] ) && '' !== $_GET['date_to'] ) {
			$date_to = sanitize_text_field( $_GET['date_to'] );
			
			$args['date_query'] = array(
				'before' => $date_to,
			);
		}

		$product_rows = array();
		$orders       = array();
		if ( function_exists( 'wc_get_orders' ) ) {
			$orders = wc_get_orders( $args );
		}

		if ( 0 === count( $orders ) ) {
			return $product_rows;
		}

		$hidden_keys  = array( '_product_thumbnail', '_configurator_item_ids' );
		foreach ( $orders as $order ) :
			$order_items = $order->get_items();

			if ( 0 === count( $order_items ) ) {
				continue;
			}

			foreach ( $order_items as $item_id => $item ) {
				$item_data = $item->get_data();
				$item_product = $item->get_product();

				if ( ! $item_product ) {
					continue;
				}

				if ( ! product_is_configurable( $item_product->get_id() ) ) {
					continue;
				}

				if ( $filtered_product_id && $filtered_product_id !== $item_product->get_id() ) {
					continue;
				}

				$details = '';
				$item_meta = array();
				foreach ( $item_data['meta_data'] as $meta ) {
					if ( in_array( $meta->key, $hidden_keys ) ) {
						continue;
					}
					
					$details .= $meta->value;
					$item_meta[] = $meta;
				}

				$key = sanitize_title( $item_data['name'] . $details );
				if ( array_key_exists( $key, $product_rows ) ) {
					$product_rows[ $key ]['qty'] += $item->get_quantity();
					$product_rows[ $key ]['total'] += $item->get_subtotal();
					$product_rows[ $key ]['order'][] = $order->get_id();
				} else {
					$product_rows[ $key ] = array(
						'id'    => $item_product->get_id(),
						'title' => $item_data['name'],
						'meta'  => $item_meta,
						'qty'   => $item->get_quantity(),
						'order' => array( $order->get_id() ),
						'total' => $item->get_subtotal(),
					);
				}
			}
		endforeach;

		return $product_rows;
	}

	/**
	 * Get order ids
	 *
	 * @since    1.3.4
	 */
	public static function get_order_ids_by_product_id( $product_id ) {
		global $wpdb;

		# Get All defined statuses Orders IDs for a defined product ID
		return $wpdb->get_col( 
			$wpdb->prepare( '
				SELECT DISTINCT woi.order_id
				FROM %1$s as woim, 
					%2$s as woi, 
					%3$s as p
				WHERE woi.order_item_id = woim.order_item_id
				AND woi.order_id = p.ID
				AND woim.meta_key = "_product_id"
				AND woim.meta_value LIKE "%4$d"
				ORDER BY woi.order_item_id DESC',
				$wpdb->prefix . 'woocommerce_order_itemmeta',
				$wpdb->prefix . 'woocommerce_order_items',
				$wpdb->prefix . 'posts',
				$product_id
			)
		);
	}

	/**
	 * Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'title' => __( 'Product', 'staggs' ),
			'meta'  => __( 'Configuration', 'staggs' ),
			'qty'   => __( 'Items sold', 'staggs' ),
			'order' => __( 'Orders', 'staggs' ),
			'total' => __( 'Total sales', 'staggs' ),
		];

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'qty'   => array( 'qty', true ),
			'order' => array( 'order', false ),
			'total' => array( 'total', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		return count( self::get_all_configurable_order_items() );
	}

	/**
	 * Text displayed when no customer data is available
	 */
	public function no_items() {
		if ( isset( $_GET['date_from'] ) || isset( $_GET['date_to'] ) ) {
			esc_attr_e( 'No analytics data available for selected date(s).', 'staggs' );
		} else {
			esc_attr_e( 'No analytics data available yet.', 'staggs' );
		}
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'meta':
				$meta_html = '';
				foreach ( $item[ $column_name ] as $meta ) {
					$meta_html .= '<span><strong>' . $meta->key . ':</strong>' . $meta->value . '</span>';
				}
				return $meta_html;
			case 'order':
				return '<a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '&order_ids=' . implode( ',', $item[ $column_name ] ) . '">' . count( $item[ $column_name ] ) . '</a>';
			case 'total':
				if ( function_exists( 'wc_price' ) ) {
					return strip_tags( wc_price( $item[ $column_name ] ) );
				}
				return $item[ $column_name ];
			default:
				return $item[ $column_name ]; //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public function column_title( $item ) {
		$title = '<a href="' . admin_url( 'post.php?post=' . $item['id'] . '&action=edit' ) . '">' . $item['title'] . '</a>';
		return $title;
	}
}
