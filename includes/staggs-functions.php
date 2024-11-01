<?php

/**
 * The main functions of this plugin.
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

if ( ! function_exists( 'sgg__' ) ) {
	function sgg__( $string ) {
		if ( function_exists( 'pll__' ) ) {
			return pll__( $string );
		}
		return __( sprintf( '%s', $string ), 'staggs' );
	}
}

if ( ! function_exists( 'staggs_unserialize' ) ) {
	function staggs_unserialize($data) {
		return is_string($data) ? unserialize($data) : $data;
	}
}

if ( ! function_exists( 'staggs_get_post_meta' ) ) {
	/**
	 * Get post meta from ACF or CB
	 *
	 * @since    1.5.0
	 */
	function staggs_get_post_meta( $post_id, $meta_key ) {
		if ( defined( 'STAGGS_ACF' ) && function_exists( 'get_field' ) ) {
			$meta = get_field( $meta_key, $post_id );
			return apply_filters( 'sgg_get_post_meta_value', $meta, $post_id, $meta_key );
		} else if ( function_exists( 'carbon_get_post_meta' ) ) {
			$meta = carbon_get_post_meta( $post_id, $meta_key );
			return apply_filters( 'sgg_get_post_meta_value', $meta, $post_id, $meta_key );
		}
	}
}

if ( ! function_exists( 'staggs_get_theme_option' ) ) {
	/**
	 * Get theme option from ACF or CB
	 *
	 * @since    1.5.0
	 */
	function staggs_get_theme_option( $option_key ) {
		if ( defined( 'STAGGS_ACF' ) && function_exists( 'get_field' ) ) {
			$option = get_field( $option_key, 'option' );
			return apply_filters( 'sgg_get_theme_option_value', $option, $option_key );
		} else if ( function_exists( 'carbon_get_theme_option' ) ) {
			$option = carbon_get_theme_option( $option_key );
			return apply_filters( 'sgg_get_theme_option_value', $option, $option_key );
		}
	}
}

if ( ! function_exists( 'staggs_set_post_meta' ) ) {
	/**
	 * Set post meta for ACF or CB
	 *
	 * @since    1.5.0
	 */
	function staggs_set_post_meta( $post_id, $meta_key, $meta_value ) {
		if ( defined( 'STAGGS_ACF' ) && function_exists( 'update_field' ) ) {
			update_field( $meta_key, $meta_value, $post_id );
		} else if ( function_exists( 'carbon_set_post_meta' ) ) {
			carbon_set_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
}

if ( ! function_exists( 'staggs_set_theme_option' ) ) {
	/**
	 * Set theme option for ACF or CB
	 *
	 * @since    1.5.0
	 */
	function staggs_set_theme_option( $option_key, $option_value ) {
		if ( defined( 'STAGGS_ACF' ) && function_exists( 'get_field' ) ) {
			update_field( $option_key, $option_value, 'option' );
		} else if ( function_exists( 'carbon_set_theme_option' ) ) {
			carbon_set_theme_option( $option_key, $option_value );
		}
	}
}

if ( ! function_exists( 'staggs_sanitize_title' ) ) {
	/**
	 * Sanitize and filter the title.
	 *
	 * @since    1.5.2
	 */
	function staggs_sanitize_title( $title ) {
		return urldecode( sanitize_title( $title ) );
	}
}

if ( ! function_exists( 'staggs_product_class' ) ) {
	/**
	 * Echo product wrapper classes.
	 *
	 * @since    1.5.2
	 */
	function staggs_product_class( $class, $product ) {
		if ( function_exists( 'wc_product_class' ) ) {
			wc_product_class( $class, $product );
		} else {
			echo 'class="product ' . esc_attr( $class ) . '"';
		}
	}
}

if ( ! function_exists( 'staggs_should_load_fields' ) ) {
	/**
	 * Check if we should load in all the fields.
	 * Don't load on not-Staggs pages because slows down the page load.
	 *
	 * @since    1.5.0
	 */
	function staggs_should_load_fields() {
		if ( defined( 'STAGGS_RUN_MIGRATE' ) ) {
			return true;
		}

		if ( ! defined( 'STAGGS_ACF' ) ) {
			if ( isset( $_POST ) && count( $_POST ) > 0 ) {
				return true;
			}
			// Only check when no ACF fields.
			if ( is_admin() ) {
				$sgg_post_types = array( 'product', 'sgg_product', 'sgg_attribute', 'sgg_theme' );
				if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $sgg_post_types ) ) {
					return true;
				} 
				else if ( isset( $_GET['post'] ) ) {
					$post_type = get_post_type( $_GET['post'] );
					return in_array( $post_type, $sgg_post_types );
				} else {
					return false;
				}
			}
			// Frontend. Return true.
			return true;
		}
	}
}

if ( ! function_exists( 'product_is_configurable' ) ) {
	/**
	 * Checks if a configurable product is found for given id.
	 *
	 * @since    1.0.0
	 */
	function product_is_configurable( $id ) {
		if ( 'product' !== get_post_type( $id ) ) {
			return false;
		}
		if ( ! get_post_meta( $id, 'is_configurable', true ) ) {
			return false;
		}
		return get_post_meta( $id, 'is_configurable', true );
	}
}

if ( ! function_exists( 'product_is_popup' ) ) {
	/**
	 * Checks if a configurable product is found for given id.
	 *
	 * @since    1.0.0
	 */
	function product_is_popup( $theme_id ) {
		$view_layout = staggs_get_configurator_view_layout( $theme_id );
		return 'popup' === $view_layout;
	}
}

if ( ! function_exists( 'product_is_inline_configurator' ) ) {
	/**
	 * Checks if a configurable product is found for given id.
	 *
	 * @since    1.0.0
	 */
	function product_is_inline_configurator( $id ) {
		$theme_id = staggs_get_theme_id();
		$template = staggs_get_configurator_page_template( $theme_id );
		if ( 'staggs' === $template ) {
			return ( product_is_configurable( $id ) && ! product_is_popup( $theme_id ) );
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'staggs_get_configurator_page_template' ) ) {
	/**
	 * Get configurator page template setting for theme id
	 *
	 * @since    1.11.1
	 */
	function staggs_get_configurator_page_template( $theme_id ) {
		if ( ! get_transient( 'sgg_configurator_page_template_' . $theme_id ) ) {
			$template = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_page_template' ) );
			set_transient( 'sgg_configurator_page_template_' . $theme_id, $template );
			return $template;
		}
		return get_transient( 'sgg_configurator_page_template_' . $theme_id );
	}
}

if ( ! function_exists( 'staggs_get_configurator_view_layout' ) ) {
	/**
	 * Get configurator page template setting for theme id
	 *
	 * @since    1.11.1
	 */
	function staggs_get_configurator_view_layout( $theme_id ) {
		if ( ! get_transient( 'sgg_configurator_view_layout_' . $theme_id ) ) {
			$view_layout = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_view' ) );
			set_transient( 'sgg_configurator_view_layout_' . $theme_id, $view_layout );
			return $view_layout;
		}
		return get_transient( 'sgg_configurator_view_layout_' . $theme_id );
	}
}

if ( ! function_exists( 'configurator_has_input_fields' ) ) {
	/**
	 * Checks if a configurable product has input fields.
	 *
	 * @since    1.2.5
	 */
	function configurator_has_input_fields( $id ) {
		$has_input_fields = false;

		$steps = staggs_get_post_meta( $id, 'sgg_configurator_step_options' );
		if ( is_array( $steps ) && count( $steps ) > 0 ) {
			foreach ( $steps as $step ) {
				if ( 'text-input' === $step['_type'] || 'measurements' === $step['_type'] ) {
					$has_input_fields = true;
					break;
				}
			}
		}

		return $has_input_fields;
	}
}

if ( ! function_exists( 'get_configurator_themes_options' ) ) {
	/**
	 * Queries the database for configurator themes
	 *
	 * @since    1.4.0
	 */
	function get_configurator_themes_options() {
		$base_args = array(
			'fields'          => 'ids',
			'post_type'      => 'sgg_theme',
			'post_status'    => 'publish',
			'posts_per_page' => 999999,
			'no_found_rows'  => true,
		);

		$theme_args = apply_filters( 'sgg_theme_query_args', array() );
		
		$args = array_merge( $base_args, $theme_args );
		
		$result_ids = get_posts( $args );

		$results_dictionary = array( '' => '- Choose theme -' );
		foreach ( $result_ids as $result_id ) {
			$results_dictionary[ $result_id ] = get_the_title( $result_id );
		}

		return $results_dictionary;
	}
}

if ( ! function_exists( 'get_page_options' ) ) {
	/**
	 * Gets all published pages.
	 *
	 * @since    1.3.7
	 */
	function get_page_options() {
		$all_pages = array(); 

		$pages = get_posts( array(
			'numberposts' => 99999,
			'no_found_rows'  => true,
			'orderby' => 'ID',
			'order' => 'ASC',
			'post_type' => 'page'
		) );

		foreach ( $pages as $page ) {
			if ( ! empty( $page->post_title ) ) {
				$all_pages[ $page->ID ] = $page->post_title;       
			}
		}

		return apply_filters( 'staggs_form_page_options', $all_pages );
	}
}

if ( ! function_exists( 'get_configurator_attribute_values' ) ) {
	/**
	 * Queries the database for attribute values.
	 *
	 * @since    1.4.0
	 */
	function get_configurator_attribute_values() {

		$transient_name = 'staggs_attribute_values';

		if ( get_transient( $transient_name ) ) {

			$results_dictionary = get_transient($transient_name );
            if(!is_array($results_dictionary )){
                $results_dictionary = staggs_unserialize($results_dictionary );
            }

		} else {

			$base_args = array(
				'post_type'      => 'sgg_attribute',
				'post_status'    => 'publish',
				'posts_per_page' => 99999,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);

			$attr_args = apply_filters( 'sgg_attribute_query_args', array() );

			$args = array_merge( $base_args, $attr_args );

			$attribute_ids = get_posts( $args );

			$results_dictionary = array();

			if ( is_array( $attribute_ids ) && count( $attribute_ids ) > 0 ) {
					
				global $wpdb;
				$titles = $wpdb->get_results( $wpdb->prepare( 
					'SELECT `ID`, `post_title` FROM `%1$s` WHERE `ID` IN (%2$s)',
					$wpdb->prefix . 'posts',
					implode( ',', $attribute_ids )
				), ARRAY_A );

				foreach ( $attribute_ids as $attribute_id ) {
					$title_key = array_search( $attribute_id, array_column( $titles, 'ID' ) );
					$results_dictionary[ $attribute_id ] = $titles[ $title_key ]['post_title'];
				}
			}

			set_transient( $transient_name, serialize( $results_dictionary ) );

		}

		return $results_dictionary;
	}
}

if ( ! function_exists( 'get_configurator_attribute_conditional_values' ) ) {
	/**
	 * Queries the database for attribute values.
	 *
	 * @since    1.4.0
	 */
	function get_configurator_attribute_conditional_values() {

		$transient_name = 'staggs_attribute_conditional_values';

		if ( get_transient( $transient_name ) ) {

			$results_dictionary = get_transient($transient_name);
            if (!is_array($results_dictionary)) {
                $results_dictionary = staggs_unserialize($results_dictionary);
            }

		} else {

			if ( defined( 'STAGGS_ACF' ) ) {
				$metakey = 'sgg_attribute_type';
			} else {
				$metakey = '_sgg_attribute_type';
			}

			$base_args = array(
				'post_type'      => 'sgg_attribute',
				'post_status'    => 'publish',
				'posts_per_page' => 99999,
				'meta_query'     => array(
					array(
						'key'     => $metakey,
						'value'   => 'input',
						'compare' => '!=',
					),
				),
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);
			$option_args = apply_filters( 'sgg_attribute_option_query_args', array() );

			$args = array_merge( $base_args, $option_args );

			$result_ids = get_posts( $args );

			if ( is_array( $result_ids ) && count( $result_ids ) > 0 ) {
				global $wpdb;

				$titles = $wpdb->get_results( $wpdb->prepare( 
					'SELECT `ID`, `post_title` FROM `%1$s` WHERE `ID` IN (%2$s)',
					$wpdb->prefix .'posts',
					implode( ',', $result_ids )
				), ARRAY_A );

				foreach ( $result_ids as $result_id ) {
					$title_key = array_search( $result_id, array_column( $titles, 'ID' ) );
					$results_dictionary[ $result_id ] = $titles[ $title_key ]['post_title'];
				}
			}

			set_transient( $transient_name, serialize( $results_dictionary ) );
		}

		return $results_dictionary;
	}
}

if ( ! function_exists( 'get_configurator_attribute_conditional_inputs' ) ) {
	/**
	 * Queries the database for attribute inputs.
	 *
	 * @since    1.6.0
	 */
	function get_configurator_attribute_conditional_inputs() {

		$transient_name = 'staggs_attribute_conditional_inputs';

		if ( get_transient( $transient_name ) ) {

			$results_dictionary = get_transient($transient_name);
            if (!is_array($results_dictionary)) {
                $results_dictionary = staggs_unserialize($results_dictionary);
            }

		} else {

			if ( defined( 'STAGGS_ACF' ) ) {
				$metakey = 'sgg_attribute_type';
			} else {
				$metakey = '_sgg_attribute_type';
			}

			$results_dictionary = array();

			$base_args = array(
				'post_type'      => 'sgg_attribute',
				'post_status'    => 'publish',
				'posts_per_page' => 99999,
				'meta_query'     => array(
					array(
						'key'     => $metakey,
						'value'   => 'input',
					),
				),
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			);
			$input_args = apply_filters( 'sgg_attribute_input_query_args', array() );

			$args = array_merge( $base_args, $input_args );

			$result_ids = get_posts( $args );

			if ( is_array( $result_ids ) && count( $result_ids ) > 0 ) {
				global $wpdb;

				$titles = $wpdb->get_results( $wpdb->prepare( 
					'SELECT `ID`, `post_title` FROM `%1$s` WHERE `ID` IN (%2$s)',
					$wpdb->prefix . 'posts',
					implode( ',', $result_ids )
				), ARRAY_A );

				foreach ( $result_ids as $result_id ) {
					$title_key = array_search( $result_id, array_column( $titles, 'ID' ) );

					if ( defined( 'STAGGS_ACF' ) ) {
						$attribute_item_labels = $wpdb->get_col( $wpdb->prepare(
							'SELECT `meta_value` FROM %1$s WHERE `post_id` = %2$d AND `meta_key` LIKE "sgg_attribute_items_%_sgg_option_label"',
							$wpdb->postmeta,
							$result_id
						) );
					} else {
						$attribute_item_labels = $wpdb->get_col( $wpdb->prepare(
							'SELECT `meta_value` FROM %1$s WHERE `post_id` = %2$d AND `meta_key` LIKE "_sgg_attribute_items|sgg_option_label%"',
							$wpdb->postmeta,
							$result_id
						) );
					}

					$results_dictionary[ $result_id ] = esc_attr( $titles[ $title_key ]['post_title'] );

					if ( count( $attribute_item_labels ) > 1 ) {
						foreach ( $attribute_item_labels as $key => $label ) {
							if ( $key === 0 ) {
								continue;
							}
							$results_dictionary[ $result_id . '-' . $key ] = esc_attr( $titles[ $title_key ]['post_title'] . ' - ' . $label );
						}
					}
				}
			}

			set_transient( $transient_name, serialize( $results_dictionary ) );
		}

		return $results_dictionary;
	}
}

if ( ! function_exists( 'get_configurator_attribute_item_values' ) ) {
	/**
	 * Queries the database for attribute item values.
	 *
	 * @since    1.4.0
	 */
	function get_configurator_attribute_item_values() {
		$attribute_ids = get_configurator_attribute_conditional_values();
		$results_dictionary = array();

		foreach ( $attribute_ids as $attribute_id => $attribute_title ) {
			$transient_name = 'staggs_attribute_conditional_labels_' . $attribute_id;
			$formatted_item_labels = array();

			if ( get_transient( $transient_name ) ) {
				$formatted_item_labels = unserialize( get_transient( $transient_name ) );
			} else {
				global $wpdb;

				if ( defined( 'STAGGS_ACF' ) ) {
					$attribute_item_labels = $wpdb->get_col( $wpdb->prepare(
						'SELECT `meta_value` FROM %1$s WHERE `post_id` = %2$d AND `meta_key` LIKE "sgg_attribute_items_%_sgg_option_label"',
						$wpdb->postmeta,
						$attribute_id
					) );
				} else {
					$attribute_item_labels = $wpdb->get_col( $wpdb->prepare(
						'SELECT `meta_value` FROM %1$s WHERE `post_id` = %2$d AND `meta_key` LIKE "_sgg_attribute_items|sgg_option_label%"',
						$wpdb->postmeta,
						$attribute_id
					) );
				}

				foreach ( $attribute_item_labels as $label ) {
					$formatted_item_labels[ urldecode( sanitize_title( $label ) ) ] = esc_attr( $label );
				}

				set_transient( $transient_name, serialize( $formatted_item_labels ) );
			}

			foreach ( $formatted_item_labels as $label_key => $label_value ) {
				if ( ! array_key_exists( $label_key, $results_dictionary ) ) {
					$results_dictionary[ $label_key ] = esc_attr( $label_value );
				}
			}
		}

		return $results_dictionary;
	}
}

if ( ! function_exists( 'ajax_get_configurator_attribute_values' ) ) {
	/**
	 * Queries the database for attribute items.
	 *
	 * @since    1.4.0
	 */
	function ajax_get_configurator_attribute_values() {
		global $wpdb;

		$attribute_id = sanitize_key( $_POST['attribute_id'] );
		$results_dictionary = array();

		if ( defined( 'STAGGS_ACF' ) ) {
			$attribute_item_labels = $wpdb->get_col( $wpdb->prepare(
				'SELECT `meta_value` FROM %1$s WHERE `post_id` = %2$d AND `meta_key` LIKE "sgg_attribute_items_%_sgg_option_label"',
				$wpdb->postmeta,
				$attribute_id
			) );
		} else {
			$attribute_item_labels = $wpdb->get_col( $wpdb->prepare(
				'SELECT `meta_value` FROM %1$s WHERE `post_id` = %2$d AND `meta_key` LIKE "_sgg_attribute_items|sgg_option_label%"',
				$wpdb->postmeta,
				$attribute_id
			) );
		}

		if ( is_array( $attribute_item_labels ) && count( $attribute_item_labels ) > 0 ) {
			foreach ( $attribute_item_labels as $label ) {
				$key = urldecode( sanitize_title( $label ) );
                if ( ! array_key_exists( $key, $results_dictionary ) ) {
					$results_dictionary[ $key ] = esc_attr( $label );
                }
			}
		}

		echo wp_json_encode( $results_dictionary );
		die();
	}
}
add_action( 'wp_ajax_nopriv_get_configurator_attribute_values', 'ajax_get_configurator_attribute_values' );
add_action( 'wp_ajax_get_configurator_attribute_values', 'ajax_get_configurator_attribute_values' );

if ( ! function_exists( 'get_woocommerce_simple_product_list' ) ) {
	/**
	 * Queries the database for WooCommerce Simple Products that are not configurable.
	 *
	 * @since    1.5.0.
	 */
	function get_woocommerce_simple_product_list() {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return array();
		}

		$args = array(
			'limit'  => 999999,
			'type'   => 'simple',
			'status' => 'publish',
			'return' => 'ids',
		);

		$args = apply_filters( 'sgg_product_query_args', $args );

		$product_ids = wc_get_products( $args );

		$simple_products = array( '' => '- Select WooCommerce product -' );

		if ( staggs_get_theme_option( 'sgg_product_list_include_configurators' ) ) {
			foreach ( $product_ids as $product_id ) {
				$simple_products[ $product_id ] = esc_attr( get_the_title( $product_id ) );
			}
		} else {
			foreach ( $product_ids as $product_id ) {
				$is_configurable = ( get_post_meta( $product_id, 'is_configurable', true ) === 'yes' );
	
				if ( ! $is_configurable ) {
					$simple_products[ $product_id ] = esc_attr( get_the_title( $product_id ) );
				}
			}
		}

		$variation_args = array(
			'post_type' => 'product_variation',
			'post_status' => 'publish',
			'posts_per_page' => 99999,
			'fields' => 'ids',
			'no_found_rows' => true,
		);

		$variation_args = apply_filters( 'sgg_product_variation_query_args', $variation_args );

		$variation_ids = get_posts( $variation_args );

		if ( is_array( $variation_ids ) && count( $variation_ids ) > 0 ) {
			foreach ( $variation_ids as $variation_id ) {
				$simple_products[ $variation_id ] = esc_attr( get_the_title( $variation_id ) );
			}
		}

		return $simple_products;
	}
}

if ( ! function_exists( 'get_woocommerce_staggs_product_list' ) ) {
	/**
	 * Queries the database for WooCommerce Staggs Products that are configurable.
	 *
	 * @since    1.6.0.
	 */
	function get_woocommerce_staggs_product_list() {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return array();
		}

		$args = array(
			'limit'  => 999999,
			'type'   => 'simple',
			'status' => 'publish',
			'return' => 'ids',
		);

		$args = apply_filters( 'sgg_product_query_args', $args );

		$staggs_products = array();
		$product_ids     = wc_get_products( $args );

		foreach ( $product_ids as $product_id ) {
			$is_configurable = ( get_post_meta( $product_id, 'is_configurable', true ) === 'yes' );

			if ( $is_configurable ) {
				$staggs_products[ $product_id ] = esc_attr( get_the_title( $product_id ) );
			}
		}
		
		return $staggs_products;
	}
}

if ( ! function_exists( 'get_term_name' ) ) {
	/**
	 * Returns term name from database by term id
	 *
	 * @since    1.0.0
	 */
	function get_term_name( $term_id ) {
		global $wpdb;
		if ( ! $term_id ) {
			return '';
		}

		$names = $wpdb->get_col( $wpdb->prepare( 
			'SELECT `name` FROM %1$s WHERE `term_id` = %2$s',
			$wpdb->prefix . 'terms',
			$term_id
		) );

		if ( ! is_array( $names ) ) {
			return '';
		}

		if ( ! isset( $names[0] ) ) {
			return '';
		}

		return $names[0];
	}
}

if ( ! function_exists( 'staggs_get_product_min_price' ) ) {
	/**
	 * Get minimum configuration price based on attribute list
	 */
	function staggs_get_product_min_price( $product_id, $product_price, $attribute_list ) {
		$attributes = Staggs_Formatter::get_formatted_step_content( $product_id );
		$attribute_list = explode( ',', $attribute_list );
		$attribute_list = array_map( 'trim', $attribute_list );

		$post_options = array();
		foreach ( $attributes as $attribute ) {
			$attribute_label = staggs_sanitize_title( $attribute['title'] );

			if ( ! in_array( $attribute_label, $attribute_list ) ) {
				continue;
			}

			if ( 'yes' !== $attribute['required'] && 'single' !== $attribute['allowed_options'] ) {
				continue;
			}

			if ( ! is_array( $attribute['options'] ) || count( $attribute['options'] ) === 0 ) {
				continue;
			}

			if ( count( $attribute['options'] ) === 1 ) {
				$post_options[] = array(
					'name' => esc_attr( $attribute_label ),
					'value' => esc_attr( $attribute['options'][0]['name'] ),
					'step_id' => esc_attr( $attribute['options'][0]['group'] ),
					'id' => esc_attr( $attribute['options'][0]['id'] )
				);
				continue;
			}

			$options = $attribute['options'];
			usort($options, function($a, $b) {
				if ( -1 !== $a['sale_price'] ) {
					if ( -1 !== $b['sale_price'] ) {
						return $a['sale_price'] > $b['sale_price'];
					}
					return $a['sale_price'] > $b['price'];
				} else {
					if ( -1 !== $b['sale_price'] ) {
						return $a['price'] > $b['sale_price'];
					}
					return $a['price'] > $b['price'];
				}
			});

			$post_options[] = array(
				'name' => esc_attr( $attribute_label ),
				'value' => esc_attr( $options[0]['name'] ),
				'step_id' => esc_attr( $options[0]['group'] ), 
				'id' => esc_attr( $options[0]['id'] )
			);
		}

		$min = get_configurator_cart_totals( array( 'options' => $post_options, 'product_price' => $product_price ), 0, $product_id, false );

		return $min['product_price'];
	}
}

if ( ! function_exists( 'staggs_get_product_max_price' ) ) {
	/**
	 * Get maximum configuration price based on attribute list
	 */
	function staggs_get_product_max_price( $product_id, $product_price, $attribute_list ) {
		$attributes = Staggs_Formatter::get_formatted_step_content( $product_id );
		$attribute_list = explode( ',', $attribute_list );
		$attribute_list = array_map( 'trim', $attribute_list );

		$post_options = array();
		foreach ( $attributes as $attribute ) {
			$attribute_label = staggs_sanitize_title( $attribute['title'] );

			if ( ! in_array( $attribute_label, $attribute_list ) ) {
				continue;
			}

			if ( ! is_array( $attribute['options'] ) || count( $attribute['options'] ) === 0 ) {
				continue;
			}

			if ( count( $attribute['options'] ) === 1 ) {
				$post_options[] = array(
					'name' => $attribute_label,
					'value' => $attribute['options'][0]['name'],
					'step_id' => $attribute['options'][0]['group'],
					'id' => $attribute['options'][0]['id']
				);
				continue;
			}

			if ( 'multiple' === $attribute['allowed_options'] ) {
				// Multiple option selection. Get all options

				foreach ( $attribute['options'] as $option ) {
					$post_options[] = array(
						'name' => $attribute_label,
						'value' => $option['name'],
						'step_id' => $option['group'],
						'id' => $option['id']
					);
				}

			} else {
				// Single option selection. Get highest price.
				
				$options = $attribute['options'];
				usort($options, function($a, $b) {
					if ( -1 !== $a['sale_price'] ) {
						if ( -1 !== $b['sale_price'] ) {
							return $a['sale_price'] < $b['sale_price'];
						}
						return $a['sale_price'] < $b['price'];
					} else {
						if ( -1 !== $b['sale_price'] ) {
							return $a['price'] < $b['sale_price'];
						}
						return $a['price'] < $b['price'];
					}
				});

				$post_options[] = array(
					'name' => $attribute_label,
					'value' => $options[0]['name'],
					'step_id' => $options[0]['group'],
					'id' => $options[0]['id']
				);
			}
		}

		$max = get_configurator_cart_totals( array( 'options' => $post_options, 'product_price' => $product_price ), 0, $product_id, false );

		return $max['product_price'];
	}
}

if ( ! function_exists( 'staggs_round_price_up' ) ) {
	/**
	 * Round price value up.
	 *
	 * @param    float $value Value to round.
	 * @param    int   $places Places to round up.
	 * @since    1.0.0
	 */
	function staggs_round_price_up($value, $places) {
		$mult = pow(10, abs($places)); 
		return $places < 0 ? ceil($value / $mult) * $mult : ceil($value * $mult) / $mult;
	}
}

if ( ! function_exists( 'staggs_round_price_to' ) ) {
	/**
	 * Round price value to arbitrairy float.
	 *
	 * @param    float $value Value to round.
	 * @param    int   $to    Float value to round up to.
	 * @since    1.0.0
	 */
	function staggs_round_price_to($value, $to) {
		return round($value/$to, 0) * $to;
	}
}

if ( ! function_exists( 'staggs_trim_value' ) ) {
	/**
	 * Returns a trimmed value.
	 *
	 * @param    string $value Value to trim.
	 * @since    1.0.0
	 */
	function staggs_trim_value( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				if ( is_array( $v ) ) {
					array_filter( $v, 'staggs_trim_value' );
				} else {
					$value[$k] = trim( $v );
				}
			}
		} else {
			$value = trim( $value );
		}
	}
}

if ( ! function_exists( 'get_option_preview_urls' ) ) {
	/**
	 * Get options image preview urls
	 *
	 * @since    1.0.0
	 */
	function get_option_preview_urls( $option, $indexes = '' ) {
		$preview_urls = array();

		if ( isset( $option['preview'] ) && is_array( $option['preview'] ) && count( $option['preview'] ) > 0 ) {
			if ( '' !== $indexes ) {
				if ( strpos( $indexes, ',' ) !== false ) {
					$preview_indexes = explode( ',', $indexes );
				} else {
					$preview_indexes = array( (int) $indexes );
				}
			} else {
				$preview_indexes = array(1);
			}

			foreach ( $option['preview'] as $index => $preview ) {
				if ( isset( $preview_indexes[ $index ] ) ) {
					$preview_index = ( (int) $preview_indexes[ $index ] - 1 < 0 ) ? 0 : (int) $preview_indexes[ $index ] - 1;
				} else {
					$preview_index = $index;
				}
				$preview_urls[] = (int) $preview_index . '|' . esc_url( $preview );
			}
		}

		return $preview_urls;
	}
}

if ( ! function_exists( 'staggs_define_price_settings') ) {
	/**
	 * Set global staggs price settings
	 *
	 * @since    1.0.0
	 */
	function staggs_define_price_settings() {
		global $staggs_price_settings;
		if ( ! $staggs_price_settings ) {
			$staggs_price_settings = array(
				'sign' => '',
				'postfix' => staggs_get_theme_option( 'sgg_product_price_trim_decimals' ) ? 'yes' : 'no',
				'include_tax' => 'yes',
				'price_display' => 'incl',
			);

			if ( function_exists( 'wc_price' ) ) {
				$staggs_price_settings['decimal_sep'] = get_option( 'woocommerce_price_decimal_sep' );

				$staggs_price_settings['currency_symbol'] = get_woocommerce_currency_symbol();
				$staggs_price_settings['currency_pos']    = get_option( 'woocommerce_currency_pos' );
				$staggs_price_settings['thousand_sep']    = get_option( 'woocommerce_price_thousand_sep' );
				$staggs_price_settings['decimal_sep']     = get_option( 'woocommerce_price_decimal_sep' );
				$staggs_price_settings['decimal_num']     = get_option( 'woocommerce_price_num_decimals' );

				if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {
					$staggs_price_settings['include_tax']   = get_option( 'woocommerce_prices_include_tax' );
					$staggs_price_settings['price_display'] = get_option( 'woocommerce_tax_display_shop' );
				}
			} else {
				$staggs_price_settings['currency_symbol'] = staggs_get_theme_option( 'sgg_price_currency_symbol' ) ?: '';
				$staggs_price_settings['currency_pos']    = staggs_get_theme_option( 'sgg_price_currency_pos' ) ?: 'left_space';
				$staggs_price_settings['thousand_sep']    = staggs_get_theme_option( 'sgg_price_thousand_sep' );
				$staggs_price_settings['decimal_sep']     = staggs_get_theme_option( 'sgg_price_decimal_sep' );
				$staggs_price_settings['decimal_num']     = '' !== staggs_get_theme_option( 'sgg_price_decimal_number' ) ? staggs_get_theme_option( 'sgg_price_decimal_number' ) : 2;
			}

			if ( staggs_get_theme_option( 'sgg_product_additional_price_sign' ) ) {
				$staggs_price_settings['sign'] = '<span class="sign">+</span>';
			}
		}
	}
}

if ( ! function_exists( 'get_option_price_html' ) ) {
	/**
	 * Returns a formatted price html based on regular and sale price.
	 *
	 * @since    1.0.0
	 */
	function get_option_price_html( $price, $sale, $text = '' ) {
		global $staggs_price_settings;
		if ( ! $staggs_price_settings ) {
			staggs_define_price_settings();
		}
		
		$price_html = '';
		$sign = '';
		$postfix = $staggs_price_settings['postfix'];

		if ( $sale !== -1 && $price !== -1 ) {
			$sign = $staggs_price_settings['sign'];
			$price_html = staggs_format_price( $sale, $sign, $postfix ) . ' <del>' . staggs_format_price( $price, $sign, $postfix ) . '</del>';
		} elseif ( $sale !== -1 || $price !== -1 ) {
			$sign = $staggs_price_settings['sign'];
			$price_html = ( $sale !== -1 ? staggs_format_price( $sale, $sign, $postfix ) : staggs_format_price( $price, $sign, $postfix ) );
		} else if ( '' !== $text ) {
			$price_html = sgg__( $text ); // Included.
		}

		return $price_html;
	}
}

if ( ! function_exists( 'staggs_format_price' ) ) {
	/**
	 * Returns a formatted price html.
	 * If WooCommerce active, use wc_price function.
	 *
	 * @since    1.0.0
	 */
	function staggs_format_price( $price, $sign = '', $trim = 'no', $args = array() ) {
		global $staggs_price_settings;
		if ( ! $staggs_price_settings ) {
			staggs_define_price_settings();
		}

		$decimal_sep = $staggs_price_settings['decimal_sep'];
		if ( function_exists( 'wc_price' ) ) {
			if ( '' !== $sign && $price > 0 ) {
				$price = $sign . wc_price( $price, $args );
			} else {
				$price = wc_price( $price, $args );
			}

			if ( 'yes' == $trim ) {
				$price = str_replace( $decimal_sep . '00', $decimal_sep . '-', $price );
			}

			return $price;
		}

		$currency_symbol = $staggs_price_settings['currency_symbol'];
		$currency_pos    = $staggs_price_settings['currency_pos'];
		$thousand_sep    = $staggs_price_settings['thousand_sep'];
		$decimal_num     = $staggs_price_settings['decimal_num'];

		// Convert to float to avoid issues on PHP 8.
		$price    = (float) $price;
		$negative = $price < 0;
		$price   = number_format( $price, $decimal_num, $decimal_sep, $thousand_sep );
	
		if ( '' !== $currency_symbol ) {
			if ( 'left_space' == $currency_pos ) {
				$currency_symbol .= ' ';
			}
			if ( 'right_space' == $currency_pos ) {
				$currency_symbol = ' ' . $currency_symbol;
			}
		}

		if ( 'left' == $currency_pos || 'left_space' == $currency_pos ) {
			$formatted_price = ( $negative ? '-' : '' ) . '<span class="woocommerce-Price-currencySymbol">' . $currency_symbol . '</span>' . $price;
		} else {
			$formatted_price = ( $negative ? '-' : '' ) . $price . '<span class="woocommerce-Price-currencySymbol">' . $currency_symbol . '</span>';
		}

		$formatted_html_price = '<span class="woocommerce-Price-amount amount"><bdi>' . $formatted_price . '</bdi></span>';

		if ( '' !== $sign && $price > 0 ) {
			$price = $sign . $formatted_html_price;
		} else {
			$price = $formatted_html_price;
		}

		if ( 'yes' == $trim ) {
			$price = str_replace( $decimal_sep . '00', $decimal_sep . '-', $price );
		}

		return $price;
	}
}

if ( ! function_exists( 'staggs_get_image_ids' ) ) {
	/**
	 * Get image ids for the configurator gallery
	 *
	 * @since    1.5.3
	 */
	function staggs_get_image_ids() {
		global $product;

		$image_ids = array();

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$post_thumbnail_id = $product->get_image_id();
			if ( $post_thumbnail_id ) {
				$image_ids[] = $post_thumbnail_id;
			}

			$attachment_ids = $product->get_gallery_image_ids();
			if ( $attachment_ids ) {
				$image_ids = array_merge( $image_ids, $attachment_ids );
			}
		} else {
			// WordPress gallery images.
			$post_thumbnail_id = get_post_thumbnail_id();
			if ( $post_thumbnail_id ) {
				$image_ids[] = $post_thumbnail_id;
			}

			$attachment_ids = staggs_get_post_meta( get_the_ID(), 'sgg_product_gallery' );
			if ( $attachment_ids ) {
				$image_ids = array_merge( $image_ids, $attachment_ids );
			}
		}

		return $image_ids;
	}
}

if ( ! function_exists( 'get_configurator_background_urls' ) ) {
	/**
	 * Checks if the configurator has background images set.
	 *
	 * @since    1.3.0
	 */
	function get_configurator_background_urls() {
		$theme_id = staggs_get_theme_id();
		if ( ! staggs_get_post_meta( $theme_id, 'sgg_show_bg_image' ) ) {
			return array();
		}

		$bg_image_urls = array();
		if ( is_array( staggs_get_post_meta( $theme_id, 'sgg_bg_image' ) ) ) {
			$bg_images = staggs_get_post_meta( $theme_id, 'sgg_bg_image' );
			foreach ( $bg_images as $bg_image_id ) {
				$bg_image_urls[] = sanitize_url( wp_get_attachment_image_url( $bg_image_id, 'full' ) );
			}
		} else if ( is_array( staggs_get_theme_option( 'sgg_bg_image' ) ) ) {
			$bg_images = staggs_get_theme_option( 'sgg_bg_image' );
			foreach ( $bg_images as $bg_image_id ) {
				$bg_image_urls[] = sanitize_url( wp_get_attachment_image_url( $bg_image_id, 'full' ) );
			}
		}

		return $bg_image_urls;
	}
}

if ( ! function_exists( 'staggs_get_registered_image_sizes' ) ) {
	/**
	 * Returns an array of registered image sizes
	 *
	 * @since    1.5.0
	 */
	function staggs_get_registered_image_sizes() {
		global $_wp_additional_image_sizes;

        $sizes = array();
        foreach ( get_intermediate_image_sizes() as $s ) {
            $sizes[$s] = array(0, 0);
            if ( in_array( $s, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
                $sizes[$s][0] = get_option($s . '_size_w');
                $sizes[$s][1] = get_option($s . '_size_h');
            } else {
                if ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[$s] ) ) {
                    $sizes[ $s ] = array( $_wp_additional_image_sizes[$s]['width'], $_wp_additional_image_sizes[$s]['height'] );
				}
            }
        }

        return $sizes;
	}
}

if ( ! function_exists( 'staggs_get_image_size_choices' ) ) {
	/**
	 * Returns an array of registered image sizes
	 *
	 * @since    1.5.0
	 */
	function staggs_get_image_size_choices() {
        $image_sizes = staggs_get_registered_image_sizes();
		$choices = array( '' => '- Select image size -');

		foreach ( $image_sizes as $size => $atts ) {
			$choices[ $size ] = $size . ' (' . implode('x', $atts) . ')';
		}
	
        return $choices;
	}
}

if ( ! function_exists( 'get_configurator_cart_totals' ) ) {
	/**
	 * Validates the posted price to check if there is not tampered with.
	 *
	 * @since    1.0.0
	 */
	function get_configurator_cart_totals( $filtered_post_options, $total_price, $post_id, $is_cart = true ) {
		// Recalculate totals to verify that posted totals are matching.
		$calculated_total = 0.00;
		$calculated_tax   = 0.00;
		$total_weight     = 0.00;
		$formulas         = array();
		$matrixes         = array();
		$field_key_prices  = array();

		// Get available steps.
		$sanitized_steps = Staggs_Formatter::get_formatted_step_content( $post_id, 'cart' );
		if ( ! is_array( $sanitized_steps ) || count( $sanitized_steps ) === 0 ) {
			return $filtered_post_options;
		}
		
		// Loop through options and calculate final price.
		$post_options = $filtered_post_options['options'];
		if ( ! is_array( $post_options ) || count( $post_options ) === 0 ) {
			return $filtered_post_options;
		}

		$product_quantity = 1;
		if ( isset( $filtered_post_options['quantity'] ) ) {
			$product_quantity = $filtered_post_options['quantity'];
		}

		$exclude_addons = staggs_get_theme_option('sgg_checkout_exclude_product_addons') ?: false;
		$exclude_linked_ids = staggs_get_theme_option('sgg_checkout_exclude_linked_products') ?: false;
		$total_percent_options = array();

		foreach ( $post_options as $post_index => $post_option ) {
			if ( 'original_post_id' == $post_option['name'] ) {
				continue;
			}
			if ( $is_cart && $exclude_addons && ( isset( $post_option['product'] ) && $post_option['product'] ) ) {
				continue;
			}
			if ( $is_cart && $exclude_linked_ids && ( isset( $post_option['product_id'] ) && $post_option['product_id'] ) ) {
				continue;
			}

			$is_repeater = false;
			$post_option_values = array( $post_option );

			if ( is_array( $post_option['value'] ) ) {
				$is_repeater = true;
				$post_option_values = $post_option['value'];
			}

			foreach ( $post_option_values as $post_sub_index => $post_option_val ) {
				if ( $is_repeater ) {
					// Exclude addon prices
					if ( $is_cart && $exclude_addons && ( isset( $post_option_val['product'] ) && $post_option_val['product'] ) ) {
						continue;
					}
					// Exclude linked product prices
					if ( $is_cart && $exclude_linked_ids && ( isset( $post_option_val['product_id'] ) && $post_option_val['product_id'] ) ) {
						continue;
					}

					// Repeater value.
					$repeater_id_parts = explode( '-', $post_option['name'] );
					unset( $repeater_id_parts[ count($repeater_id_parts) - 1] ); // Remove row index from ID

					$repeater_id = implode( '-', $repeater_id_parts );
					$repeater_index = false;

					foreach ( $sanitized_steps as $step_index => $step ) {
						if ( ! isset( $step['repeater_id'] ) ) {
							continue;
						}
						if ( $repeater_id === $step['repeater_id'] ) {
							$repeater_index = $step_index;
						}
					}

					$step_id_parts = explode( '-', $post_option_val['step_id'] );
					unset( $step_id_parts[ count($step_id_parts) - 1] ); // Remove row index from ID
					$step_id = implode( '-', $step_id_parts ); 

					$group_index = array_search( $step_id, array_column( $sanitized_steps[ $repeater_index ]['attributes'], 'id' ) );
					if ( false === $group_index ) {
						continue;
					}
					
					if ( in_array( $sanitized_steps[ $repeater_index ]['attributes'][ $group_index ]['type'], array('input', 'product')) ) {
						$search_option_val = $post_option_val['name'];
					} else {
						$search_option_val = sanitize_title( $post_option_val['value'] );
					}

					$option_index = array_search( $search_option_val, array_column( $sanitized_steps[ $repeater_index ]['attributes'][ $group_index ]['options'], 'id' ) );
					if ( false === $option_index ) {
						continue;
					}

					$selected_option = $sanitized_steps[ $repeater_index ]['attributes'][ $group_index ]['options'][ $option_index ];
					$selected_group  = $sanitized_steps[ $repeater_index ]['attributes'][ $group_index ];

				} else {

					// Single value.
					if ( isset( $post_option_val['id'] ) ) {
						$option_search_id = $post_option_val['id'];
					} else {
						$option_search_id = $post_option_val['name'];
					}

					$group_index = array_search( $post_option_val['step_id'], array_column( $sanitized_steps, 'id' ) );
					if ( false === $group_index ) {
						continue;
					}

					$option_index = array_search( $option_search_id, array_column( $sanitized_steps[ $group_index ]['options'], 'id' ) );
					if ( false === $option_index ) {
						continue;
					}

					$selected_option = $sanitized_steps[ $group_index ]['options'][ $option_index ];
					$selected_group  = $sanitized_steps[ $group_index ];
				}

				// Price included. Continue,
				if ( 'no' !== $selected_option['base_price'] 
					&& ( ! isset( $selected_group['calc_price_type'] ) || 'none' == $selected_group['calc_price_type'] ) ) {
					if ( $is_repeater ) {
						$post_options[ $post_index ]['value'][ $post_sub_index ]['price'] = 0;
					} else {
						$post_options[ $post_index ]['price'] = 0;
					}

					if ( isset( $selected_option['weight'] ) && '' !== $selected_option['weight'] ) {
						$total_weight += (float) $selected_option['weight'];
					}

					continue;
				}
				
				// No valid group. Continue.
				if ( ! isset( $post_option_val['step_id'] ) && ! in_array( $selected_group['type'], array( 'measurements', 'number-input', 'text-input', 'image-upload' ) ) ) {
					if ( $is_repeater ) {
						$post_options[ $post_index ]['value'][ $post_sub_index ]['price'] = 0;
					} else {
						$post_options[ $post_index ]['price'] = 0;
					}
					continue;
				}

				if ( isset( $selected_group['calc_price_type'] ) && $selected_group['calc_price_type'] === 'matrix'
					&& isset( $selected_group['price_table'] ) && '' !== $selected_group['price_table'] ) {

					/**
					 * Matrix option
					 */
					
					$selected_group_label = staggs_sanitize_title( $selected_group['title'] );

					if ( ! array_key_exists( $selected_group_label, $matrixes ) ) {
						$matrixes[ $selected_group_label ] = array(
							'index'    => $post_index,
							'matrix'   => $selected_group['price_table'],
							'type'     => $selected_group['price_table_type'],
							'round'    => $selected_group['price_table_rounding'],
							'range'    => $selected_group['price_table_range'],
							'key_x'    => $selected_group['price_table_val_x'],
							'key_y'    => $selected_group['price_table_val_y'],
							'type_x'   => $selected_group['price_table_type_x'],
							'type_y'   => $selected_group['price_table_type_y'],
							'value_x'  => '',
							'value_y'  => '',
						);
					}

					if ( isset( $selected_group['price_table_sale'] ) && $selected_group['price_table_sale'] ) {
						$matrixes[ $selected_group_label ]['matrix'] = $selected_group['price_table_sale'];
					}

					if ( isset( $selected_group['price_table_val_min'] ) ) {
						$matrixes[ $selected_group_label ]['minprice'] = $selected_group['price_table_val_min'];
					}

					if ( ! isset( $selected_option['field_key'] ) || '' === $selected_option['field_key'] ) {
						$field_key = $selected_group_label;
					} else {
						$field_key = $selected_option['field_key'];
					}

					if ( $field_key === $selected_group['price_table_val_x'] ) {
						$matrixes[ $selected_group_label ]['value_x'] = $post_option['value'];
					} else if ( $field_key === $selected_group['price_table_val_y'] ) {
						$matrixes[ $selected_group_label ]['value_y'] = $post_option['value'];
					}
					
					// Matrix calculation. Clear regular price.
					$post_options[ $post_index ]['price'] = 0;
				}
				else if ( isset( $selected_group['calc_price_type'] ) 
					&& ( $selected_group['calc_price_type'] === 'formula' || $selected_group['calc_price_type'] === 'formula-matrix' )
					&& isset( $selected_group['price_formula'] ) && '' !== $selected_group['price_formula'] ) {

					/**
					 * Formula option
					 */

					$selected_group_label = staggs_sanitize_title( $selected_group['title'] );
					if ( isset( $selected_group['calc_price_key'] ) ) {
						$selected_group_label = $selected_group['calc_price_key'];
					}

					if ( ! array_key_exists( $selected_group_label, $formulas ) ) {
						$formulas[ $selected_group_label ] = array(
							'index'   => $post_index,
							'formula' => $selected_group['price_formula']
						);
					}

					$filled_formula = $formulas[ $selected_group_label ]['formula'];
					$addon_price = 0;

					if ( ! isset( $selected_option['field_key'] ) || '' === $selected_option['field_key'] ) {
						$field_key = $selected_group_label;
					} else {
						$field_key = $selected_option['field_key'];
					}

					if ( 'formula-matrix' === $selected_group['calc_price_type'] ) {

						/**
						 * Formula + matrix option
						 */

						$matrix_values = array(
							'matrix'   => $selected_group['price_table'],
							'type'     => $selected_group['price_table_type'],
							'round'    => $selected_group['price_table_rounding'],
							'range'    => $selected_group['price_table_range'],
							'key_x'    => $selected_group['price_table_val_x'],
							'key_y'    => $selected_group['price_table_val_y'],
							'type_x'   => $selected_group['price_table_type_x'],
							'type_y'   => $selected_group['price_table_type_y'],
							'value_x'  => '',
							'value_y'  => '',
						);

						if ( isset( $selected_group['price_table_sale'] ) && $selected_group['price_table_sale'] ) {
							$matrix_values['matrix'] = $selected_group['price_table_sale'];
						}

						foreach ( $post_options as $post_index => $post_option ) {
							if ( $post_option['name'] === $matrix_values['key_x'] ) {
								$matrix_values['value_x'] = $post_option['value'];
							}
						}
	
						foreach ( $post_options as $post_index => $post_option ) {
							if ( $post_option['name'] === $matrix_values['key_y'] ) {
								$matrix_values['value_y'] = $post_option['value'];
							}
						}
		
						$price = get_price_from_matrix_table( $matrix_values );
						if ( isset( $matrix_values['minprice'] ) && $price < $matrix_values['minprice'] ) {
							$price = $matrix_values['minprice'];
						}

						if ( $price ) {
							if ( 'lookup' === $matrix_values['type'] ) {
								$addon_price = (float) $price;
							} elseif ( 'doublemultiply' === $matrix_values['type'] ) {
								$option_price = ( $matrix_values['value_x'] * $matrix_values['value_y'] ) * $price;
								$addon_price = (float) $option_price;
							} else {
								$option_price = ( $matrix_values['value_x'] * $price ) + ( $matrix_values['value_y'] * $price );
								$addon_price = (float) $option_price;
							}
						} else {
							$addon_price = 0;
						}

						// Table price
						$filled_formula = str_replace( $field_key, $addon_price, $filled_formula );

					} else {

						/**
						 * Regular formula option
						 */

						if ( isset( $selected_option['price_type'] ) && 'unit' === $selected_option['price_type'] ) {
							// Unit price
							$unit_price = ( isset( $selected_option['unit_price'] ) && $selected_option['unit_price'] ) ? $selected_option['unit_price'] : 0;

							if ( 'text-input' === $selected_group['type'] ) {
								$addon_price = $unit_price * strlen( $post_option['value'] );

								$filled_formula = str_replace( $field_key . '_value', strlen( $post_option['value'] ), $filled_formula );
								$filled_formula = str_replace( $field_key . '_price', $unit_price, $filled_formula );
								$filled_formula = str_replace( $field_key, $addon_price, $filled_formula );
							} else {
								$sub_eval    = 'return (' . $post_option['value'] . ' * ' . $unit_price . ');';
								$addon_price = eval( $sub_eval );

								$filled_formula = str_replace( $field_key . '_value', $post_option['value'], $filled_formula );
								$filled_formula = str_replace( $field_key . '_price', $selected_option['unit_price'], $filled_formula );
								$filled_formula = str_replace( $field_key, $addon_price, $filled_formula );
							}
						} else if ( isset( $selected_option['price_type'] ) && 'table' === $selected_option['price_type'] && function_exists( 'get_price_from_range_table' ) ) {
							// Table price
							$found_price = get_price_from_range_table( $selected_option['price_table'], $post_option['value'] );
							$sub_eval    = 'return (' . $post_option['value'] . ' * ' . $found_price . ');';
							$addon_price = eval( $sub_eval );

							$filled_formula = str_replace( $field_key, $addon_price, $filled_formula );
						} else {
							// Single price
							$price       = $selected_option['price'];
							$sale        = $selected_option['sale_price'];
							$addon_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : 0 );

							$filled_formula = str_replace( $field_key, $addon_price, $filled_formula );
						}
					}
					
					/**
					 * Collect field key and values for later reference.
					 */

					$field_key_prices[ $field_key ] = $addon_price;

					if ( $is_repeater ) {
						$post_options[ $post_index ]['value'][ $post_sub_index ]['price'] = $addon_price;
					} else {
						$post_options[ $post_index ]['price'] = $addon_price;
					}

					$formulas[ $selected_group_label ]['formula'] = str_replace( ' ', '', $filled_formula );

					// Formula calculation. Clear regular price.
					$post_options[ $post_index ]['price'] = 0;
				}
				else {

					/**
					 * Default option
					 */

					$price       = $selected_option['price'];
					$sale        = $selected_option['sale_price'];
					$addon_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : 0 );

					if ( isset( $selected_option['price_type'] ) && 'formula' === $selected_option['price_type'] ) {
						$price_formula = $selected_option['price_formula'];

						if ( preg_match( "/([a-zA-Z]+-*_*)+/", $price_formula ) ) {
							foreach ( $field_key_prices as $formula_field_key => $formula_field_price ) {
								$price_formula = str_replace( $formula_field_key, $formula_field_price, $price_formula );
							}
						}

						// Replace quantity
						$price_formula = str_replace( 'quantity', $product_quantity, $price_formula );

						if ( ! preg_match( "/([a-zA-Z]+-*_*)+/", $price_formula ) ) {
							$option_price = eval( 'return (' . $price_formula . ');' );
							$addon_price = $option_price;
						} else {
							$addon_price = 0;
						}
					} else if ( isset( $selected_option['price_type'] ) && 'percentage' === $selected_option['price_type'] ) {
						// percentage price
						$percent = $selected_option['price_percent'];
						$field = $selected_option['price_field'];

						if ($percent && $field) {
							if ( 'sgg_total_price' === $field ) {
								$total_percent_options[] = array(
									'index'   => $post_index,
									'percent' => $percent
								);
							} else {
								foreach ( $field_key_prices as $formula_field_key => $formula_field_price ) {
									$field = str_replace( $formula_field_key, $formula_field_price, $field );
								}
	
								if ( ! preg_match( "/([a-zA-Z]+-*_*)+/", $field ) ) {
									$option_price = $field * ($percent / 100);
									$addon_price = $option_price;
								} else {
									$addon_price = 0;
								}
							}
						}
					}

					if ( isset( $selected_option['price_type'] ) && 'unit' === $selected_option['price_type'] ) {
						// Unit price
						$unit_price = ( isset( $selected_option['unit_price'] ) && $selected_option['unit_price'] ) ? $selected_option['unit_price'] : 0;

						if ( 'text-input' === $selected_group['type'] ) {
							$addon_price = $unit_price * strlen( $post_option['value'] );
						} else {
							$sub_eval    = 'return (' . $post_option['value'] . ' * ' . $unit_price . ');';
							$addon_price = eval( $sub_eval );
						}
					} else if ( isset( $selected_option['price_type'] ) && 'table' === $selected_option['price_type'] && function_exists( 'get_price_from_range_table' ) ) {

						// Table price
						$addon_price = get_price_from_range_table( $selected_option['price_table'], $post_option['value'] );

					} else {

						// Product price
						if ( 'product' === $selected_group['type'] ) {
							$quantity    = (int) $post_option_val['value'];
							$addon_price = ( $addon_price * $quantity );
						}
					}
					
					if ( isset( $selected_option['field_key'] ) ) {
						$field_key_prices[ $selected_option['field_key'] ] = $addon_price;
					} else {
						$option_name = staggs_sanitize_title( $selected_group['title'] );
						$field_key_prices[ $option_name ] = $addon_price;
					}

					if ( $is_repeater ) {
						$post_options[ $post_index ]['value'][ $post_sub_index ]['price'] = $addon_price;
					} else {
						$post_options[ $post_index ]['price'] = $addon_price;
					}

					if ( isset( $selected_option['is_taxable'] ) && $selected_option['is_taxable'] && class_exists( 'WC_Tax' ) ) {
						$tax_class = $selected_option['tax_class'];
						$tax_rates = WC_Tax::get_rates($tax_class);
						
						if ( is_array( $tax_rates ) && count( $tax_rates ) > 0 ) {
							$tax = reset( $tax_rates );
							$calculated_tax += ( $tax['rate'] / 100 ) * $addon_price;
						}
					}

					if ( isset( $selected_option['weight'] ) && '' !== $selected_option['weight'] ) {
						$total_weight += (float) $selected_option['weight'];
					}

					$calculated_total += (float) $addon_price;
				}

			}

			krsort( $field_key_prices );
		}

		/**
		 * Add measurement prices.
		 */
		if ( function_exists( 'get_price_from_matrix_table' ) && count( $matrixes ) > 0 ) {
			$matrixes_total = 0.00;

			foreach ( $matrixes as $option_name => $matrix_values ) {
				if ( ! $matrix_values['value_x'] || '' == $matrix_values['value_x'] ) {
					if ( 'quantity' === $matrix_values['key_x'] ) {
						$matrix_values['value_x'] = $product_quantity;
					} else {
						foreach ( $post_options as $post_index => $post_option ) {
							if ( $post_option['name'] === $matrix_values['key_x'] ) {
								$matrix_values['value_x'] = $post_option['value'];
							}
						}
					}
				}

				if ( ! $matrix_values['value_y'] || '' == $matrix_values['value_y'] ) {
					if ( 'quantity' === $matrix_values['key_y'] ) {
						$matrix_values['value_y'] = $product_quantity;
					} else {
						foreach ( $post_options as $post_index => $post_option ) {
							if ( $post_option['name'] === $matrix_values['key_y'] ) {
								$matrix_values['value_y'] = $post_option['value'];
							}
						}
					}
				}

				$price = get_price_from_matrix_table( $matrix_values );
				if ( isset( $matrix_values['minprice'] ) && $price < $matrix_values['minprice'] ) {
					$price = $matrix_values['minprice'];
				}

				if ( $price ) {
					if ( 'lookup' === $matrix_values['type'] ) {
						$matrixes_total += (float) $price;
					} elseif ( 'doublemultiply' === $matrix_values['type'] ) {
						$option_price = ( $matrix_values['value_x'] * $matrix_values['value_y'] ) * $price;
						$matrixes_total += (float) $option_price;
					} else {
						$option_price = ( $matrix_values['value_x'] * $price ) + ( $matrix_values['value_y'] * $price );
						$matrixes_total += (float) $option_price;
					}
				}
			}

			if ( isset( $matrix_values['index'] ) ) {
				$post_options[ $matrix_values['index'] ]['price'] = $matrixes_total;
			}

			$calculated_total += $matrixes_total;
		}

		if ( count( $formulas ) > 0 ) {
			$formulas_total = 0.00;

			foreach ( $formulas as $formula_key => $formula_result ) {
				$filled_formula = $formula_result['formula'];

				if ( preg_match( "/([a-zA-Z]+-*_*)+/", $filled_formula ) ) {
					foreach ( $field_key_prices as $formula_field_key => $formula_field_price ) {
						$filled_formula = str_replace( $formula_field_key, $formula_field_price, $filled_formula );
					}
				}

				$filled_formula = str_replace( 'quantity', $product_quantity, $filled_formula );

				if ( ! preg_match( "/([a-zA-Z]+-*_*)+/", $filled_formula ) ) {
					$option_price = eval( 'return (' . $filled_formula . ');' );
					$formulas_total += $option_price;

					$formulas[ $formula_key ]['total'] = $option_price;

					if ( isset( $formula_result['index'] ) ) {
						$post_options[ $formula_result['index'] ]['price'] = $option_price;
					}
				} else {
					$formulas[ $formula_key ]['total'] = 0;
				}
			}

			$calculated_total += $formulas_total;
		}

		// Add base product price.
		if ( function_exists('wc_get_product') ) {
			$product    = wc_get_product( $post_id );
			$base_price = $product->get_price();
		} else {
			$base_price = staggs_get_post_meta( $post_id, 'sgg_product_regular_price' ) ?: 0;
		}

		$option_price  = $calculated_total;
		$total_formula = '';
		if ( 'custom' === staggs_get_post_meta( staggs_get_theme_id( $post_id ), 'sgg_configurator_total_calculation' ) ) {
			$total_formula = staggs_get_post_meta( staggs_get_theme_id( $post_id ), 'sgg_configurator_total_price_formula' );
		}

		if ( $total_formula ) {
			// Remove spaces.
			$total_formula = str_replace( ' ', '', $total_formula );

			// Replace formula keys first.
			if ( count( $formulas ) > 0 ) {
				foreach ( $formulas as $formula_key => $formula_result ) {
					$search = '/' . preg_quote($formula_key, '/').'/';
					$total_formula = preg_replace($search, $formula_result['total'], $total_formula);
				}
			}

			// Then individual field keys.
			foreach ( $field_key_prices as $field_key => $field_key_price ) {
				// Fill keys.
				$search = '/' . preg_quote($field_key, '/').'/';
    			$total_formula = preg_replace($search, $field_key_price, $total_formula);
			}

			$total_formula = str_replace( 'product_price', $base_price, $total_formula );
			$total_formula = str_replace( 'option_price', $option_price, $total_formula );
			$total_formula = str_replace( 'total_price', $calculated_total, $total_formula );
			$total_formula = str_replace( 'quantity', $product_quantity, $total_formula );

			if ( ! preg_match( "/([a-zA-Z]+-*_*)+/", $total_formula ) ) {
				$calculated_total = eval( 'return (' . $total_formula . ');' );
			} else {
				preg_match_all( "/([a-zA-Z]+-*_*)+/", $total_formula, $matches );

				if ( isset( $matches[0] ) && is_array( $matches[0] ) ) {
					foreach ( $matches[0] as $match ) {
						$search = '/'.preg_quote($match, '/').'/';
    					$total_formula = preg_replace($search, '0', $total_formula);
					}

					$calculated_total = eval( 'return (' . $total_formula . ');' );
				}
			}
		}

		if ( 'table' === staggs_get_post_meta( staggs_get_theme_id( $post_id ), 'sgg_configurator_total_calculation' ) ) {
			$total_table_id = sanitize_key( staggs_get_post_meta( staggs_get_theme_id( $post_id ), 'sgg_configurator_total_price_table' ) );

			if ( $total_table_id && function_exists( 'get_price_from_range_table' ) ) {
				$base_price = get_price_from_range_table( $total_table_id, $product_quantity );
			}
		}

		$use_base_price = staggs_get_theme_option( 'sgg_product_exclude_base_price' ) ? false : true;
		if ( $use_base_price ) {
			$calculated_total += $base_price;
		}
		
		if ( count( $total_percent_options ) > 0 ) {
			foreach ( $total_percent_options as $percent_option ) {
				$percentage = $percent_option['percent'];
				$post_index = $percent_option['index'];

				$percentage_option_price = $calculated_total * ($percentage / 100);
				$post_options[ $post_index ]['price'] = $percentage_option_price;

				$calculated_total += $percentage_option_price;
			}
		}

		// Set validated price.
		$total_price = $filtered_post_options['product_price'];
		if ( $total_price !== $calculated_total ) {
			$filtered_post_options['product_price'] = $calculated_total;
		}
		
		if ( $calculated_tax && 0 !== (int) $calculated_tax ) {
			$filtered_post_options['product_tax'] = $calculated_tax;
		}

		if ( $total_weight && 0 !== (int) $total_weight ) {
			$filtered_post_options['product_weight'] = $total_weight;
		}

		// Refresh options
		$filtered_post_options['options'] = $post_options;

		return $filtered_post_options;
	}
}

if ( ! function_exists( 'get_sanitized_post_data' ) ) {
	/**
	 * Sanitize and filter all POST variables
	 *
	 * @since    1.0.0
	 */
	function get_sanitized_post_data() {
		// Get our post data values ready for sanitization.
		$post_data = $_POST;
		unset( $post_data['action'] );

		// We trim the $_POST data before any spaces get encoded to "%20"
		// Trim array values using the function "staggs_trim_value"
		array_filter( $post_data, 'staggs_trim_value' ); // the data in $_POST is trimmed

		// Set up the filters to be used with the trimmed post array.
		$base_filter = array(
			'options' => array(
				'filter' => 'strip_tags',
				'flags'  => FILTER_FORCE_ARRAY,
			),
		);

		// Must be referenced via a variable which is now an array that takes the place of $_POST[].
		$revised_post_array = filter_var_array( $post_data, $base_filter );

		// Add other post meta.
		foreach ( $post_data as $key => $value ) {
			if ( 'options' !== $key ) {
				$revised_post_array[ $key ] = sanitize_text_field( $value );
			}
		}

		return $revised_post_array;
	}
}

if ( ! function_exists( 'luma' ) ) {
	/**
	 * Determine lightness value of given RGB color using the Luma algorithm.
	 *
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 *
	 * @since    1.1.0
	 */
	function luma( $r, $g, $b ) {
		return ( 0.2126 * $r + 0.7152 * $g + 0.0722 * $b ) / 255;
	}
}

if ( ! function_exists( 'staggs_get_icon' ) ) {
	/**
	 * Delete images to save up server space.
	 * 
	 * @param  $setting_label  Carbon Fields setting field label
	 * @param  $is_dark_theme  Is dark theme active?
	 * @param  $icon_name 	   Name of icon to retrieve when image icon not found.
	 * @param  $link_only      Return image link only
	 * 
	 * @return string Image|SVG
	 * 
	 * @since  1.3.1
	 */
	function staggs_get_icon( $setting_label, $icon_name, $link_only = false, $theme = '' ) {
		if ( '' === $theme ) {
			$theme = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_theme' );
		}

		$icontheme = 'dark';
		if ( 'dark' === $theme ) {
			$icontheme = 'light';
		} else if ( 'custom' === $theme ) {
			$icontheme = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_icon_theme' );
		}

		$cb_setting = $icontheme === 'light' ? $setting_label . '_dark' : $setting_label;
		if ( $image_id = staggs_get_theme_option( $cb_setting ) ) {
			$staggs_icon_size = apply_filters( 'staggs_icon_image_size', 'full' );
			if ( $link_only ) {
				$icon = wp_get_attachment_image_url( $image_id, $staggs_icon_size );
			} else {	
				$icon = wp_get_attachment_image( $image_id, $staggs_icon_size );
			}
		} else {
			$icon_rel_path = 'public/img/' . $icon_name . ( $icontheme == 'dark' ? '-dark' : '' ) . '.svg';
			if ( $link_only ) {
				$icon = STAGGS_BASE_URL . $icon_rel_path;
			} else {
				$icon = file_get_contents( STAGGS_BASE . $icon_rel_path );
			}
		}

		return $icon;
	}
}

if ( ! function_exists( 'store_final_product_image' ) ) {
	/**
	 * Render final product image
	 * 
	 * @since 1.3.1
	 */
	function store_final_product_image( $imagename, $data, $options, $main_image = true ) {
		/**
		 * Validate type
		 */
		if ( preg_match( '/^data:image\/(.*);base64,/', $data, $type ) ) {
			// Valid base64 image.
			$data = substr( $data, strpos($data, ',') + 1 );
			$type = strtolower( $type[1] ); // jpg, png, gif

			$data = str_replace( ' ', '+', $data );
			$data = base64_decode($data);
		
			if ( $data === false ) {
				throw new \Exception('Image decode failed. Try again.');
			}
		} else {
			// Can't validate base64 string somehow? Try conversion with png extension.
			$data = substr( $data, strpos($data, ',') + 1 );
			$type = 'png';

			$data = str_replace( ' ', '+', $data );
			$data = base64_decode($data);
		
			if ( $data === false ) {
				throw new \Exception('Invalid image data provided. Try again.');
			}
		}

		/**
		 * Store image
		 */
		$upload_dir = wp_get_upload_dir();
		$base_dir   = $upload_dir['basedir'];

		// Allow different location.
		$save_path  = apply_filters( 'staggs_image_save_dir', $base_dir . '/staggs' );

        if ( ! file_exists( $save_path ) ) {
            mkdir( $save_path, 0777, true );
		}

		if ( $main_image ) {
			// Main product image.
			if ( is_array( $options ) && count( $options ) > 0 ) {
				$filename = $imagename . '-' . md5( wp_json_encode( $options ) ) . '.' . $type;
			} else {
				$filename = $imagename . '.' . $type;
			}
		} else {
			// User uploaded file.
			if ( is_array( $options ) && count( $options ) > 0 ) {
				$filename = $imagename . '-' . md5( wp_json_encode( $options ) ) . '.' . $type;
			} else {
				$filename = wp_unique_filename( $save_path, $imagename );
			}
		}

		// Always override in case something went wrong earlier
		file_put_contents( $save_path . "/{$filename}", $data );

		$image_path_url = str_replace( ABSPATH, trailingslashit( get_site_url() ), $save_path );

		return $image_path_url . "/{$filename}";
	}
}

if ( ! function_exists( 'store_final_product_file' ) ) {
	/**
	 * Store final product file for later access
	 * 
	 * @since 1.12.1
	 */
	function store_final_product_file( $file, $options = array() ) {
		global $wp_filesystem;

		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			return array(
				'error' => 'Invalid file upload',
				'path' => false,
			);
		}

		/**
		 * Start file upload
		 */
		$fileTmpPath = $file['tmp_name'];
		$imagename = $file['name'];
		$fileSize = $file['size'];
		$fileType = $file['type'];
		
        $allowedFileTypes = wp_get_mime_types();
        $maxFileSize = wp_max_upload_size();

        // Validate file type
        if (!in_array($fileType, $allowedFileTypes)) {
            return array(
				'error' => __( 'Invalid file type', 'staggs' ),
				'path' => false
			);
        }

        // Validate file size
        if ($fileSize > $maxFileSize) {
            return array(
				'error' => __( 'File size exceeds the maximum allowed size', 'staggs' ),
				'path' => false
			);
        }

        $fileNameParts = pathinfo($imagename);
        $fileExtension = $fileNameParts['extension'];

		$upload_dir = wp_get_upload_dir();
		$base_dir   = $upload_dir['basedir'];
		$save_path  = apply_filters( 'staggs_image_save_dir', $base_dir . '/staggs' );
        if ( ! file_exists( $save_path ) ) {
            mkdir( $save_path, 0777, true );
		}

		if ( is_array( $options ) && count( $options ) > 0 ) {
			$filename = $imagename . '-' . md5( wp_json_encode( $options ) ) . '.' . $fileExtension;
		} else {
			$filename = wp_unique_filename( $save_path, $imagename );
		}

		// Move original uploaded file
		$wp_filesystem->move($fileTmpPath, $save_path . "/{$filename}");
		$file_path_url = str_replace( ABSPATH, trailingslashit( get_site_url() ), $save_path );

		return array(
			'error' => false,
			'path' => $file_path_url . "/{$filename}"
		);
	}
}

if ( ! function_exists( 'staggs_delete_product_image' ) ) {
	/**
	 * Delete images to save up server space.
	 * 
	 * @since 1.3.1
	 */
	function staggs_delete_product_image( $path ) {
		if ( file_exists( $path ) ) {
			unlink( $path );
		}
	}
}

if ( ! function_exists( 'staggs_get_configurator_theme_id' ) ) {
	/**
	 * Get configurator setting ID.
	 *
	 * @since    1.4.0
	 */
	function staggs_get_theme_id( $theme_product_id = '' ) {
		if ( ! $theme_product_id ) {
			$theme_product_id = get_the_ID();
		}

		if ( get_transient( 'sgg_product_configurator_theme_id_' . $theme_product_id ) ) {
			return get_transient( 'sgg_product_configurator_theme_id_' . $theme_product_id );
		}

		$theme_id = staggs_get_post_meta( $theme_product_id, 'sgg_product_configurator_theme_id' );
		if ( $theme_id ) {
			set_transient( 'sgg_product_configurator_theme_id_' . $theme_product_id, $theme_id );
			return $theme_id;
		}

		// product ID by default.
		return $theme_product_id;
	}
}

if ( ! function_exists( 'get_all_woocommerce_order_statusses' ) ) {
	/**
	 * Get all WooCommerce order statusses
	 *
	 * @since    1.4.0
	 */
	function get_all_woocommerce_order_statusses() {
		if ( function_exists( 'wc_get_order_statuses' ) ) {
			return wc_get_order_statuses();
		}
		return array();
	}
}
