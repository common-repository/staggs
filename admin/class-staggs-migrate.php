<?php

/**
 * The admin-specific analytics display of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.5.0
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
class Staggs_Migrate {

	/**
	 * Analytics admin page html
	 *
	 * @since    1.5.0
	 */
	public static function run() {

		/**
		 * Migrate attributes
 		 */

		$attributes = get_configurator_attribute_values();

		$attribute_fields = self::get_attribute_fields();
		$attribute_sub_fields = self::get_attribute_sub_fields();

		foreach ( $attributes as $attribute_id => $attribute_title ) {
			$attribute_meta = self::get_formatted_attribute( $attribute_id );

			foreach ( $attribute_meta as $meta_key => $meta_value ) {
				if ( is_array( $meta_value ) ) {
					foreach ( $meta_value as $sub_meta_index => $sub_meta_row ) {
						foreach ( $sub_meta_row as $sub_field_key => $sub_field_val ) {
							if ( '_type' === $sub_field_key ) {
								continue;
							}

							// Save field val.
							update_post_meta( 
								$attribute_id,
								$meta_key . '_' . $sub_meta_index . '_' . $sub_field_key,
								$sub_field_val,
								true
							);
							// Save field ref.
							update_post_meta(
								$attribute_id,
								'_' . $meta_key . '_' . $sub_meta_index . '_' . $sub_field_key,
								$attribute_sub_fields[ $sub_field_key ],
								true
							);
						}
					}

					update_post_meta( $attribute_id, $meta_key, count( $meta_value ), true );
					update_post_meta( $attribute_id, '_' . $meta_key, $attribute_fields[ $meta_key ], true );

				} else {

					update_post_meta( $attribute_id, $meta_key, $meta_value, true );
					update_post_meta( $attribute_id, '_' . $meta_key, $attribute_fields[ $meta_key ], true );

				}
			}
		}

		/**
		 * Migrate products
 		 */

		if ( function_exists( 'wc_get_products' ) ) {
			$all_product_ids = get_posts( array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page'  => 200,
				'meta_key' => 'is_configurable',
				'meta_value' => 'yes',
				'fields' => 'ids',
				'no_found_rows' => true,
			));

			$product_fields = self::get_product_fields();

			foreach ( $all_product_ids as $wc_product_id ) {
				if ( product_is_configurable( $wc_product_id ) ) {
					$product_meta = self::get_formatted_product( $wc_product_id );

					foreach ( $product_meta as $meta_key => $meta_value ) {
						if ( 'sgg_configurator_attributes' === $meta_key ) {
							self::set_product_builder_rows( $meta_value, $wc_product_id );
						} else {
							update_post_meta( $wc_product_id, $meta_key, $meta_value, true );
							update_post_meta( $wc_product_id, '_' . $meta_key, $product_fields[ $meta_key ], true );
						}
					}
				}
			}
		}

		/**
		 * Migrate themes
 		 */

		$themes = get_configurator_themes_options();
		foreach ( $themes as $theme_id => $theme_title ) {
			if ( $theme_id ) {
				$theme_meta = self::get_formatted_theme( $theme_id );

				foreach ( $theme_meta as $meta_key => $meta_value ) {
					update_field( $meta_key, $meta_value, $theme_id );
				}
			}
		}

		/**
		 * Migrate settings
 		 */

		global $wpdb;
		$options = $wpdb->get_results( $wpdb->prepare(
			'SELECT `option_name`, `option_value` FROM `%s` WHERE `option_name` LIKE "_sgg_%"',
			$wpdb->options
		) );

		foreach ( $options as $option ) {
			$option_key = str_replace( '_sgg', 'sgg', $option->option_name );
			$option_value = carbon_get_theme_option( $option_key );

			update_field( $option_key, $option_value, 'option' );
		}
	}

	/**
	 * Format options step output
	 *
	 * @since    1.4.0
	 */
	private static function get_formatted_attribute( $attribute_id ) {
		$sanitized_attribute = array();
		$fields = self::get_attribute_fields();

		foreach ( $fields as $field_label => $field_key ) {
			if ( carbon_get_post_meta( $attribute_id, $field_label ) ) {
				$value = carbon_get_post_meta( $attribute_id, $field_label );
				$sanitized_attribute[ $field_label ] = $value;
			}
		}

		return $sanitized_attribute;
	}

	/**
	 * Get formatted theme options.
	 *
	 * @since    1.4.0
	 */
	private static function get_formatted_theme( $theme_id ) {
		$sanitized_theme = array();
		$theme_fields = self::get_product_theme_fields();

		foreach ( $theme_fields as $field_label ) {
			if ( carbon_get_post_meta( $theme_id, $field_label ) ) {
				$value = carbon_get_post_meta( $theme_id, $field_label );
				$sanitized_theme[ $field_label ] = $value;
			}
		}

		return $sanitized_theme;
	}

	/**
	 * Get formatted theme options.
	 *
	 * @since    1.4.0
	 */
	private static function get_formatted_product( $product_id ) {

		$sanitized_product = array();
		$product_fields = self::get_product_fields();

		foreach ( $product_fields as $field_label => $field_key ) {
			if ( carbon_get_post_meta( $product_id, $field_label ) ) {
				$value = carbon_get_post_meta( $product_id, $field_label );

				if ( 'sgg_configurator_attributes' == $field_label ) {
					$key_values = array();
					
					foreach ( $value as $row_val ) {
						$formatted_row = array();
						
						foreach ( $row_val as $row_key => $row_label ) {
							if ( '_type' !== $row_key) {
								$formatted_row[ $row_key ] = $row_label;
							} else {
								$formatted_row[ $row_key ] = $row_label;
							}
						}

						$key_values[] = $formatted_row;
					}

					$sanitized_product[ $field_label ] = $key_values;
				} else {
					$sanitized_product[ $field_label ] = $value;
				}
			}
		}

		return $sanitized_product;
	}

	/**
	 * Set builder rows for flexible content field.
	 *
	 * @since    1.4.0
	 */
	private static function set_product_builder_rows( $meta_value, $product_id ) {
		$fc_rows = array();

		$product_fields = self::get_product_fields();
		$row_fields = self::get_product_row_fields();

		$row_sub_fields = array(
			'sgg_step_conditional_step' => 'field_64c55657947a7',
			'sgg_step_conditional_compare' => 'field_64c55666947a8',
			'sgg_step_conditional_value' => 'field_64c5568e947a9',
			'sgg_step_conditional_relation' => 'field_64c556a3947aa',
			'sgg_step_conditional_link' => 'field_64c556a3947ae',
		);

		$tab_sub_fields = array(
			'sgg_step_tab_title' => 'field_64c55657947a1',
			'sgg_step_tab_attribute' => 'field_64c5568e947a2',
		);

		foreach ( $meta_value as $index => $row_val ) {

			if ( 'attribute' === $row_val['_type'] ) {
				$fc_rows[] = 'attribute';

				foreach ( $row_val as $field_key => $field_val ) {
					if ( '_type' == $field_key ) {
						continue;
					}
					
					if ( ! isset( $row_fields[ $field_key ] ) ) {
						continue;
					}

					if ( is_array( $field_val ) ) {
						foreach ( $field_val as $sub_index => $sub_row ) {
							foreach ( $sub_row as $sub_field_key => $sub_field_val ) {
								if ( '_type' === $sub_field_key ) {
									continue;
								}

								update_post_meta(
									$product_id,
									'sgg_configurator_attributes_' . $index . '_' . $field_key . '_' . $sub_index . '_' . $sub_field_key, 
									$sub_field_val, 
									true
								);
								update_post_meta(
									$product_id,
									'_sgg_configurator_attributes_' . $index . '_' . $field_key . '_' . $sub_index . '_' . $sub_field_key, 
									$row_sub_fields[ $sub_field_key ], 
									true
								);
							}
						}

						update_post_meta( $product_id, 'sgg_configurator_attributes_' . $index . '_' . $field_key, count( $field_val ), true );
						update_post_meta( $product_id, '_sgg_configurator_attributes_' . $index . '_' . $field_key, $row_fields[ $field_key ], true );
					} else {
						update_post_meta( $product_id, 'sgg_configurator_attributes_' . $index . '_' . $field_key, $field_val, true );
						update_post_meta( $product_id, '_sgg_configurator_attributes_' . $index . '_' . $field_key, $row_fields[ $field_key ], true);
					}
				}
			} 
			else if ( 'separator' === $row_val['_type'] ) {
				$fc_rows[] = 'separator';

				foreach ( $row_val as $field_key => $field_val ) {
					if ( '_type' == $field_key ) {
						continue;
					}

					update_post_meta( $product_id, 'sgg_configurator_attributes_' . $index . '_' . $field_key, $field_val, true );
					update_post_meta( $product_id, '_sgg_configurator_attributes_' . $index . '_' . $field_key, $row_fields[ $field_key ], true );
				}
			} 
			else if ( 'tabs' === $row_val['_type'] ) {
				$fc_rows[] = 'tabs';

				if ( is_array( $field_val ) ) {
					foreach ( $field_val as $sub_index => $sub_row ) {
						foreach ( $sub_row as $sub_field_key => $sub_field_val ) {
							if ( '_type' === $sub_field_key ) {
								continue;
							}

							update_post_meta(
								$product_id,
								'sgg_configurator_attributes_' . $index . '_' . $field_key . '_' . $sub_index . '_' . $sub_field_key, 
								$sub_field_val, 
								true
							);
							update_post_meta(
								$product_id,
								'_sgg_configurator_attributes_' . $index . '_' . $field_key . '_' . $sub_index . '_' . $sub_field_key, 
								$tab_sub_fields[ $sub_field_key ], 
								true
							);
						}
					}

					update_post_meta( $product_id, 'sgg_configurator_attributes_' . $index . '_' . $field_key, count( $field_val ), true );
					update_post_meta( $product_id, '_sgg_configurator_attributes_' . $index . '_' . $field_key, $row_fields[ $field_key ], true );
				} else {
					update_post_meta( $product_id, 'sgg_configurator_attributes_' . $index . '_' . $field_key, $field_val, true );
					update_post_meta( $product_id, '_sgg_configurator_attributes_' . $index . '_' . $field_key, $row_fields[ $field_key ], true);
				}
			}
		}

		update_post_meta( $product_id, 'sgg_configurator_attributes', $fc_rows, true );
		update_post_meta( $product_id, '_sgg_configurator_attributes', 'field_64c555129479d', true );
	}

	/**
	 * Get a list of registered atribute fields.
	 * 
	 * @since 1.5.0
	 */
	private static function get_attribute_fields() {
		return array(
			'sgg_step_title' => 'field_64c2db699e96f',
			'sgg_step_template' => 'field_64c2dbf0c2572',
			'sgg_attribute_type' => 'field_64c2dc6ec2573',
			'sgg_step_shared_group' => 'field_64c2dccac2575',
			'sgg_step_short_description' => 'field_64c2dbbfc2570',
			'sgg_step_description' => 'field_64c2dbd5c2571',
			'sgg_step_field_info' => 'field_64c2dc93c2574',
			'sgg_step_style' => 'field_64c2dd5bc257a',
			'sgg_step_option_layout' => 'field_64c2dde2c257e',
			'sgg_step_card_template' => 'field_64c2de0cc257f',
			'sgg_step_show_image' => 'field_64c2dd7ec257b',
			'sgg_step_show_summary' => 'field_64c2de3cc2581',
			'sgg_step_show_option_price' => 'field_64e3144f3bbf8',
			'sgg_step_swatch_size' => 'field_64c2de6ac2582',
			'sgg_step_swatch_style' => 'field_64c2deb3c2584',
			'sgg_step_show_swatch_label' => 'field_64c2dedfc2585',
			'sgg_step_show_tooltip' => 'field_64c2defec2586',
			'sgg_step_tooltip_template' => 'field_64c2df21c2587',
			'sgg_step_product_template' => 'field_64c2df6dc258a',
			'sgg_step_button_view' => 'field_64c2dfaec258c',
			'sgg_step_button_add' => 'field_64c2dfdac258d',
			'sgg_step_button_del' => 'field_64c2e002c258e',
			'sgg_step_calc_price_type' => 'field_64c2e012c258f',
			'sgg_step_price_formula' => 'field_64c2e086c2591',
			'sgg_step_price_table' => 'field_64c2e0b5c2594',
			'sgg_step_price_table_type' => 'field_64c2e0e5c2595',
			'sgg_step_price_table_rounding' => 'field_64c2e10cc2596',
			'sgg_step_price_table_val_x' => 'field_64c2e132c2597',
			'sgg_step_price_table_val_y' => 'field_64c2e159c2598',
			'sgg_step_gallery_type' => 'field_64c2e199c259a',
			'sgg_step_preview_order' => 'field_64c2e1d3c259b',
			'sgg_step_preview_index' => 'field_64c2e1f1c259c',
			'sgg_step_preview_slide' => 'field_64c2e22ac259f',
			'sgg_step_preview_ref' => 'field_64c2e246c25a0',
			'sgg_step_preview_bundle' => 'field_64c2e199c259d',
			'sgg_step_preview_height' => 'field_64c2e199c259e',
			'sgg_step_model_group' => 'field_64c2e261c25a1',
			'sgg_step_model_image_type' => 'field_64c2e27bc25a2',
			'sgg_step_model_image_material' => 'field_64c2e2aac25a3',
			'sgg_attribute_items' => 'field_64c2e306c25a5',
		);
	}

	/**
	 * Get a list of registered atribute item fields.
	 * 
	 * @since 1.5.0
	 */
	private static function get_attribute_sub_fields() {
		return array(
			'sgg_option_label' => 'field_64c2e319c25a6',
			'sgg_option_note' => 'field_64c2e32fc25a7',
			'sgg_option_image' => 'field_64c541b1fa063',
			'sgg_option_field_type' => 'field_64c541fcfa067',
			'sgg_option_field_placeholder' => 'field_64c547266d863',
			'sgg_option_field_value' => 'field_64c5478e6d868',
			'sgg_option_datepicker_show_icon' => 'field_64e0a7a22d05a',
			'sgg_option_datepicker_show_inline' => 'field_64e0a7ef2d05b',
			'sgg_option_field_color' => 'field_64c547ae6d869',
			'sgg_option_linked_product_id' => 'field_64c542a1fa06c',
			'sgg_option_product_quantity' => 'field_64c5447cfa082',
			'sgg_option_font_source' => 'field_64c544acfa083',
			'sgg_option_font_family' => 'field_64c544c7fa084',
			'sgg_option_font_weight' => 'field_64c544d7fa085',
			'sgg_option_base_price' => 'field_64c547d66d86a',
			'sgg_option_calc_price_type' => 'field_64c549ea874a1',
			'sgg_option_price_table' => 'field_64c54a65874a4',
			'sgg_option_calc_price_value' => 'field_64c54aa0874a5',
			'sgg_option_price' => 'field_64c54ac1874a6',
			'sgg_option_sale_price' => 'field_64c54b2e874ac',
			'sgg_option_manage_stock' => 'field_64c54b5e874ae',
			'sgg_option_stock_qty' => 'field_64c54bbf874b3',
			'sgg_option_manage_out_of_stock' => 'field_64c54beb874b4',
			'sgg_option_hide_out_of_stock' => 'field_64c54c20874b5',
			'sgg_option_out_of_stock_message' => 'field_64c54c4b874b8',
			'sgg_option_field_key' => 'field_64c5450bfa086',
			'sgg_option_material_key' => 'field_64c545adfa091',
			'sgg_option_field_required' => 'field_64c5426cfa06b',
			'sgg_option_allowed_file_types' => 'field_64c5464bfa094',
			'sgg_option_max_file_size' => 'field_64c5466efa095',
			'sgg_option_range_increments' => 'field_64c546a76d85d',
			'sgg_option_datepicker_format' => 'field_64e0a7502d059',
			'sgg_option_field_min' => 'field_64c546c86d85e',
			'sgg_option_field_max' => 'field_64c546e96d85f',
			'sgg_option_field_unit' => 'field_64c546f76d860',
			'sgg_option_enable_preview' => 'field_64c542c9fa06d',
			'sgg_option_preview' => 'field_64c542f0fa06e',
			'sgg_option_preview_node' => 'field_64c5433dfa074',
			'sgg_option_preview_top' => 'field_64c54390fa07a',
			'sgg_option_preview_left' => 'field_64c543c8fa07b',
			'sgg_option_preview_width' => 'field_64c543c8fa07c',
			'sgg_option_preview_height' => 'field_64c54439fa080',
			'sgg_option_preview_overflow' => 'field_64c542c9fa06e',
			'sgg_option_preview_custom_mobile' => 'field_64c542c9fa06g',
			'sgg_option_preview_top_mobile' => 'field_64c543e2fa07c',
			'sgg_option_preview_left_mobile' => 'field_64c543f8fa07d',
			'sgg_option_preview_width_mobile' => 'field_64c543c8fa07e',
			'sgg_option_preview_image_width' => 'field_64c54411fa07e',
			'sgg_option_preview_image_fill' => 'field_64c5444efa081',
		);
	}

	/**
	 * Get a list of registered product fields.
	 * 
	 * @since 1.5.0
	 */
	private static function get_product_fields() {
		return array(
			'sgg_configurator_type' => 'field_64c5539494791',
			'sgg_configurator_3d_model' => 'field_64c553d694792',
			'sgg_configurator_3d_nodes' => 'field_64c553fd94793',
			'sgg_product_configurator_theme_id' => 'field_64c554de9479c',
			'sgg_configurator_attributes' => 'field_64c555129479d',
		);
	}

	/**
	 * Get a list of registered product row item fields.
	 * 
	 * @since 1.5.0
	 */
	private static function get_product_row_fields() {
		return array(
			'sgg_step_separator_title' => 'field_64c5553a9479e',
			'sgg_step_separator_collapsible' => 'field_64c5555f9479f',
			'sgg_step_collapsible_state' => 'field_64c55583947a0',
			'sgg_step_tab_options' => 'field_64c5563d947ad',
			'sgg_step_attribute' => 'field_64c555b0947a2',
			'sgg_step_attribute_hidden' => 'field_64c555b0947a5',
			'sgg_step_attribute_default_value' => 'field_64c555b0947ac',
			'sgg_step_attribute_value' => 'field_64c555b0947ad',
			'sgg_step_attribute_collapsible' => 'field_64c555b0947a3',
			'sgg_step_attribute_state' => 'field_64c555b0947a4',
			'sgg_step_conditional_logic' => 'field_64c55620947a5',
			'sgg_step_conditional_rules' => 'field_64c5563d947a6',
		);
	}

	/**
	 * Get a list of registered theme fields.
	 * 
	 * @since 1.5.0
	 */
	private static function get_product_theme_fields() {
		return array(
			'sgg_configurator_page_template',
			'sgg_disable_attribute_styles',
			'sgg_configurator_view',
			'sgg_configurator_popup_type',
			'sgg_configurator_layout',
			'sgg_configurator_step_density',
			'sgg_configurator_text_align',
			'sgg_configurator_borders',
			'sgg_configurator_gallery_type',
			'sgg_preview_image_type',
			'sgg_bg_image_size',
			'sgg_configurator_initial_view',
			'sgg_configurator_initial_pos_x',
			'sgg_configurator_initial_pos_y',
			'sgg_configurator_initial_zoom',
			'sgg_configurator_custom_shadow',
			'sgg_configurator_shadow_intensity',
			'sgg_configurator_shadow_softness',
			'sgg_configurator_custom_view_limits',
			'sgg_configurator_counter_clockwise_limit',
			'sgg_configurator_clockwise_limit',
			'sgg_configurator_topdown_limit',
			'sgg_configurator_bottomup_limit',
			'sgg_configurator_auto_rotation',
			'sgg_configurator_disable_zoom',
			'sgg_configurator_display_ar_button',
			'sgg_configurator_ar_button_text',
			'sgg_configurator_display_desktop_ar_button',
			'sgg_configurator_model_placement',
			'sgg_use_product_image',
			'sgg_configurator_gallery_sticky',
			'sgg_show_bg_image',
			'sgg_bg_image',
			'sgg_model_env_image',
			'sgg_show_model_env_image',
			'sgg_configurator_env_image_exposure',
			'sgg_configurator_thumbnails',
			'sgg_configurator_thumbnails_align',
			'sgg_configurator_thumbnails_position',
			'sgg_configurator_thumbnail_labels',
			'sgg_configurator_arrows',
			'sgg_configurator_usp_location',
			'sgg_step_usps',
			'sgg_configurator_theme',
			'sgg_primary_color',
			'sgg_secondary_color',
			'sgg_heading_color',
			'sgg_text_color',
			'sgg_configurator_icon_theme',
			'sgg_accent_color',
			'sgg_button_text_color',
			'sgg_configurator_custom_font',
			'sgg_font_family',
			'sgg_header_scripts',
			'sgg_theme_disable_cart_styles',
			'sgg_configurator_css',
			'sgg_show_theme_header_footer',
			'sgg_show_logo',
			'sgg_configurator_disable_product_price',
			'sgg_configurator_disable_product_meta',
			'sgg_configurator_disable_product_tabs',
			'sgg_configurator_disable_template_override',
			'sgg_step_popup_button_text',
			'sgg_mobile_gallery_display',
			'sgg_configurator_step_indicator',
			'sgg_configurator_step_separator_function',
			'sgg_configurator_step_separator_nav',
			'sgg_configurator_step_prev_text',
			'sgg_configurator_step_next_text',
			'sgg_step_hide_inline_option_step_title',
			'sgg_step_set_included_option_text',
			'sgg_step_included_text',
			'sgg_step_disable_default_option',
			'sgg_configurator_display_summary',
			'sgg_configurator_summary_title',
			'sgg_configurator_summary_empty_message',
			'sgg_configurator_summary_location',
			'sgg_configurator_button_type',
			'sgg_step_add_to_cart_text',
			'sgg_configurator_total_calculation',
			'sgg_configurator_total_price_formula',
			'sgg_configurator_display_pricing',
			'sgg_configurator_total_price_display',
			'sgg_configurator_price_display_template',
			'sgg_configurator_totals_product_label',
			'sgg_configurator_totals_options_label',
			'sgg_configurator_totals_combined_label',
			'sgg_configurator_totals_label',
			'sgg_configurator_form_page',
			'sgg_configurator_step_totals_display',
			'sgg_configurator_sticky_button',
			'sgg_configurator_sticky_button_mobile',
			'sgg_configurator_display_qty_input',
			'sgg_configurator_request_invoice_button',
			'sgg_step_request_invoice_text',
			'sgg_configurator_save_button',
			'sgg_step_save_button_text',
			'sgg_configurator_generate_cart_image',
			'sgg_configurator_store_cart_image',
		);
	}
}
