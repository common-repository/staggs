<?php

/**
 * The admin-specific analytics display of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

/**
 * The admin-specific analytics display of the plugin.
 *
 * Defines the plugin analytics view.
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Analytics {

	/**
	 * Analytics WP_List_Table object
	 */
	public $analytics_table;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Empty.
	}

	/**
	 * Register admin analytics page.
	 *
	 * @since    1.3.4
	 */
	public function register_sub_menu() {
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$hook = add_submenu_page(
				'edit.php?post_type=sgg_attribute',
				__( 'Analytics', 'staggs' ),
				__( 'Analytics', 'staggs' ),
				'edit_posts',
				'analytics',
				array( $this, 'analytics_page_contents' ),
				10
			);
		} else {
			$hook = add_submenu_page(
				'edit.php?post_type=sgg_attribute',
				__( 'Analytics', 'staggs' ),
				__( 'Analytics', 'staggs' ),
				'edit_posts',
				'analytics',
				array( $this, 'analytics_empty_content' ),
				10
			);
		}

		add_action( "load-$hook", array( $this, 'screen_option' ) );
	}

	/**
	 * Set page screen options
	 *
	 * @since    1.3.4
	 */
	public function screen_option() {
		$option = 'per_page';
		$args   = array(
			'label' => 'Analytics per page',
			'default' => get_option( 'posts_per_page' ),
			'option' => 'analytics_per_page'
		);

		add_screen_option( $option, $args );

		$this->analytics_table = new Staggs_Analytics_Table();
	}

	/**
	 * Set current screen.
	 *
	 * @since    1.3.4
	 */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Analytics admin page empty html
	 *
	 * @since    1.5.3
	 */
	public function analytics_empty_content() {
		?>
		<div class="wrap">
			<div class="staggs-page-content-wide staggs-page-analytics">
				<div class="staggs-page-header">
					<div class="staggs-page-title">
						<h1><?php esc_attr_e( 'Analytics', 'staggs' ); ?></h1>
						<br>
					</div>
					<div class="staggs-notice">
						<?php esc_attr_e( 'Note: WooCommerce has to be installed and active for the Analytics to work.', 'staggs' ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Analytics admin page html
	 *
	 * @since    1.3.4
	 */
	public function analytics_page_contents() {
		$product_rows = $this->analytics_table->get_all_configurable_order_items();
		?>
		<div class="wrap">
			<div class="staggs-page-content-wide staggs-page-analytics">
				<div class="staggs-page-header">
					<div class="staggs-page-title">
						<h1><?php esc_attr_e( 'Analytics', 'staggs' ); ?></h1>
					</div>
					<div class="staggs-page-actions">
						<?php
						$product_filters = array();
						if ( isset( $product_rows ) ) {
							foreach ( $product_rows as $product_row ) {
								$product_filters[ esc_attr( $product_row['id'] ) ] = esc_attr( $product_row['title'] );
							}
						}
						?>
						<form action="" method="get" class="analytics-form-actions">
							<input type="hidden" name="post_type" value="sgg_attribute">
							<input type="hidden" name="page" value="analytics">
							<div class="option-group">
								<span>Filter by product</span>
								<label for="product">
									<select name="product" id="product">
										<option value="">- Select product -</option>
										<?php 
										foreach ( $product_filters as $id => $title ) :
											$selected = isset( $_GET['product'] ) && (int) esc_attr( $_GET['product'] ) === $id ? ' selected' : '';
											?>
											<option value="<?php echo esc_attr( $id ); ?>"<?php echo esc_attr( $selected );?>><?php echo esc_attr( $title ); ?></option>
											<?php
										endforeach;
										?>
									</select>
								</label>
							</div>
							<div class="option-group">
								<label for="date_from">
									<span>Date from</span>
									<input type="date" id="date_from" name="date_from"<?php echo isset( $_GET['date_from'] ) ? ' value="' . esc_attr( $_GET['date_from'] ) . '"' : '';?>>
								</label>
								<label for="date_to">
									<span>to</span>
									<input type="date" id="date_to" name="date_to"<?php echo isset( $_GET['date_to'] ) ? ' value="' . esc_attr( $_GET['date_to'] ) . '"' : '';?>>
								</label>
							</div>
							<input type="submit" value="filter" class="button">
						</form>
						<form action="<?php echo esc_url( admin_url( '/admin-post.php' ) ); ?>" method="post" class="analytics-form-actions">
							<input type="hidden" name="action" value="sgg_generate_analytics_export">
							<input type="submit" class="button button-primary" value="Export to CSV">
						</form>
					</div>
				</div>

				<?php
				$this->analytics_table->prepare_items();
				$this->analytics_table->display();
				?>

			</div>
		</div>
		<?php
	}

	/**
	 * Generate CSV report based on analytics data. 
	 *
	 * @since    1.3.4
	 */
	public function generate_analytics_csv_report() {

		// Get data.
		$this->analytics_table = new Staggs_Analytics_Table();

		$product_rows  = $this->analytics_table->get_all_configurable_order_items();
		$export_header = array(
			'Product',
			'Configuration',
			'Items sold',
			'Orders',
			'Total sales'
		);

		// Set filename.
		$filename = "staggs-analytics-" . date('Y-m-d') . ".csv";
		$report = fopen('php://output', 'w');

		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename=' . $filename);

		// Fill data.
		fputcsv($report, $export_header);
		foreach ($product_rows as $row) {
			$formatted_row = array();
			foreach ( $row as $key => $value ) {
				if ( 'id' === $key ) {
					continue;
				}

				if ( 'meta' === $key ) {
					$meta_html = '';
					foreach ( $row[ $key ] as $meta ) {
						$meta_html .= $meta->key . ':' . $meta->value . "\r\n";
					}
					$formatted_row[$key] = $meta_html;
				} else if ( 'order' === $key ) {
					$formatted_row[$key] = count( $value );
				} else {
					$formatted_row[$key] = sanitize_text_field( $value );
				}
			}

			fputcsv($report, $formatted_row);
		}
		exit;
	}

	/**
	 * Add filter for WooCommerce order page to show relevant orders by id.
	 *
	 * @since    1.3.4
	 */
	public function filter_woocommerce_order_overview_table( $query ) {
		global $pagenow;
		
		if ( isset( $query ) && $query->is_main_query() ) {
			if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' && isset( $_GET['order_ids'] ) && '' !== $_GET['order_ids'] ) {
				$order_ids = esc_attr( explode( ',', $_GET['order_ids'] ) );
				$query->set( 'post__in', $order_ids );
			}
		}

		return $query;
	}
}
