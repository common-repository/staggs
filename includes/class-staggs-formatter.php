<?php

/**
 * The file that defines the core plugin formatter class
 *
 * @link       https://staggs.app
 * @since      1.2.4
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The core plugin formatter handler class.
 *
 * This is used to define all formatting and sanitization hooks.
 *
 * @since      1.2.4
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Formatter {

	/**
	 * Format step output
	 *
	 * @since    1.2.4
	 */
	public static function get_formatted_step_content( $post_id, $type = 'display' ) {
		global $staggs_price_settings, $global_conditionals, $global_option_rules, $sgg_is_pro, $sgg_stock_settings;
		if ( ! $staggs_price_settings ) {
			staggs_define_price_settings();
		}

		$sgg_is_pro = sgg_fs()->is_plan_or_trial( 'professional' );
		$sanitized_steps = array();
		$inc_price_label = '';
		$indicator_version = '';
		$sgg_stock_settings = array();
		if ( $sgg_is_pro ) {
			$sgg_stock_settings['no_stock'] = staggs_get_theme_option( 'sgg_option_out_of_stock_message' ) ?: __( 'Out of stock', 'staggs' );
		}

		if ( staggs_get_post_meta( $post_id, 'sgg_step_set_included_option_text' ) ) {
			$inc_price_label = sanitize_text_field( staggs_get_post_meta( $post_id, 'sgg_step_included_text' ) );
		}

		if ( staggs_get_post_meta( $post_id, 'sgg_configurator_step_indicator' ) ) {
			$indicator_version = sanitize_text_field( staggs_get_post_meta( $post_id, 'sgg_configurator_step_indicator' ) );
		}

		$wc_product = false;
		if ( function_exists( 'wc_get_product' ) && 'product' === get_post_type( $post_id ) && 'display' === $type ) {
			$wc_product = wc_get_product( $post_id );
		}

		$global_conditionals = array();
		if ( $sgg_is_pro && staggs_get_theme_option( 'sgg_global_conditional_attributes' ) ) {
			$global_conditional_attributes = staggs_get_theme_option( 'sgg_global_conditional_attributes' );

			if ( is_array( $global_conditional_attributes ) && count( $global_conditional_attributes ) > 0 ) {
				foreach ( $global_conditional_attributes as $conditional_attribute ) {
					$attribute_id = $conditional_attribute['sgg_global_attribute'];
	
					if ( ! isset( $conditional_attribute['sgg_global_conditional_rules'] ) || count( $conditional_attribute['sgg_global_conditional_rules'] ) === 0 ) {
						continue;
					}

					$attr_conditional_rules = array();
					foreach ( $conditional_attribute['sgg_global_conditional_rules'] as $global_conditional_rule ) {
						$ruleset = array(
							'key'      => $global_conditional_rule['sgg_global_conditional_step'],
							'value'    => $global_conditional_rule['sgg_global_conditional_value'],
							'compare'  => $global_conditional_rule['sgg_global_conditional_compare'],
							'relation' => $global_conditional_rule['sgg_global_conditional_relation'],
							'newset'   => $global_conditional_rule['sgg_global_conditional_link'],
						);

						if ( ! in_array( $ruleset['compare'], array( '=', '!=', 'empty', '!empty' ) ) ) {
							$ruleset['key'] = $global_conditional_rule['sgg_step_conditional_input'];
							$ruleset['value'] = $global_conditional_rule['sgg_step_conditional_input_value'];
						}

						$attr_conditional_rules[] = $ruleset;
					}

					$global_conditionals[ $attribute_id ] = $attr_conditional_rules;
				}
			}
		}

		$global_option_rules = array();
		if ( $sgg_is_pro && staggs_get_theme_option( 'sgg_global_option_conditional_display' ) ) {
			$conditional_option_rules = staggs_get_theme_option( 'sgg_global_option_conditional_display' );

			if ( is_array( $conditional_option_rules ) && count( $conditional_option_rules ) > 0 ) {
				foreach ( $conditional_option_rules as $conditional_option ) {
					if ( ! isset( $conditional_option['sgg_global_attribute'] ) || ! $conditional_option['sgg_global_attribute'] ) {
						continue;
					}
					if ( ! isset( $conditional_option['sgg_global_conditional_option'] ) || ! $conditional_option['sgg_global_conditional_option'] ) {
						continue;
					}
					if ( ! isset( $conditional_option['sgg_global_option_conditional_rules'] ) || count( $conditional_option['sgg_global_option_conditional_rules'] ) === 0 ) {
						continue;
					}

					$attribute_id = $conditional_option['sgg_global_attribute'];
					$option_id = $conditional_option['sgg_global_conditional_option'];

					$conditional_rules = array();
					foreach ( $conditional_option['sgg_global_option_conditional_rules'] as $conditional_rule ) {
						$ruleset = array(
							'key'      => $conditional_rule['sgg_global_conditional_step'],
							'value'    => $conditional_rule['sgg_global_conditional_value'],
							'compare'  => $conditional_rule['sgg_global_conditional_compare'],
							'relation' => $conditional_rule['sgg_global_conditional_relation'],
							'newset'   => $conditional_rule['sgg_global_conditional_link'],
						);

						if ( ! in_array( $ruleset['compare'], array( '=', '!=', 'empty', '!empty' ) ) ) {
							$ruleset['key'] = $conditional_rule['sgg_global_conditional_input'];
							$ruleset['value'] = $conditional_rule['sgg_global_conditional_input_value'];
						}

						$conditional_rules[] = $ruleset;
					}

					if ( ! isset( $global_option_rules[ $attribute_id ] ) ) {
						$global_option_rules[ $attribute_id ] = array();
					}

					$global_option_rules[ $attribute_id ][ $option_id ] = $conditional_rules;
				}
			}
		}

		$new_steps = staggs_get_post_meta( $post_id, 'sgg_configurator_attributes' );
		if ( is_array( $new_steps ) && count( $new_steps ) > 0 ) {
			$step_number = 1;

			foreach ( $new_steps as $step_key => $step ) {
				if ( isset( $step['_type'] ) ) {
					$step_type = $step['_type'];
				} else if ( isset( $step['acf_fc_layout'] ) ) {
					$step_type = $step['acf_fc_layout'];
				}

				if ( 'summary' === $step_type ) {
					$sanitized_option_group = array(
						'id'   => $step_key,
						'type' => sanitize_text_field( $step_type ),
					);
					$sanitized_steps[] = $sanitized_option_group;

				} else if ( 'html' === $step_type ) {
					$sanitized_option_group = array(
						'id'   => $step_key,
						'type' => sanitize_text_field( $step_type ),
						'html' => wp_kses_normalize_entities(  $step['sgg_step_html'] ),
					);
					$sanitized_steps[] = $sanitized_option_group;

				} else if ( 'separator' === $step_type ) {

					$sanitized_option_group = array(
						'id'           => $step_key,
						'number'       => $step_number,
						'type'         => sanitize_text_field( $step_type ),
						'version'      => $indicator_version,
						'title'        => sanitize_text_field( $step['sgg_step_separator_title'] ),
						'collapsible'  => sanitize_text_field( $step['sgg_step_separator_collapsible'] ),
						'state'        => sanitize_text_field( $step['sgg_step_collapsible_state'] ),
					);

					$step_number++;
					$sanitized_steps[] = $sanitized_option_group;

				} elseif ( 'tabs' === $step_type ) {

					$sanitized_option_group = array(
						'id'   => $step_key,
						'type' => sanitize_text_field( $step_type ),
						'tabs' => $step['sgg_step_tab_options'],
					);

					$sanitized_steps[] = $sanitized_option_group;
					
				} elseif ( 'repeater' === $step_type ) {

					$repeater_name = 'Repeater ' . $step_key;
					if ( isset( $step['sgg_step_repeater_title'] ) && '' !== $step['sgg_step_repeater_title'] ) {
						$repeater_name = sanitize_text_field( $step['sgg_step_repeater_title'] );
					}
					$repeater_id = staggs_sanitize_title( $repeater_name );

					$repeater_empty = __( 'Click add row to add your first item.', 'staggs' );
					if ( isset( $step['sgg_step_repeater_empty_note'] ) && '' !== $step['sgg_step_repeater_empty_note'] ) {
						$repeater_empty = sanitize_text_field( $step['sgg_step_repeater_empty_note'] );
					}

					$repeater_btn_add = __( 'Add row', 'staggs' );
					if ( isset( $step['sgg_step_repeater_add_text'] ) && '' !== $step['sgg_step_repeater_add_text'] ) {
						$repeater_btn_add = sanitize_text_field( $step['sgg_step_repeater_add_text'] );
					}

					$repeater_min = 0;
					if ( isset( $step['sgg_step_repeater_min'] ) && '' !== $step['sgg_step_repeater_min'] ) {
						$repeater_min = sanitize_key( $step['sgg_step_repeater_min'] );
					}

					$repeater_max = false;
					if ( isset( $step['sgg_step_repeater_max'] ) && '' !== $step['sgg_step_repeater_max'] ) {
						$repeater_max = sanitize_key( $step['sgg_step_repeater_max'] );
					}

					$sanitized_option_group = array(
						'id'                => $step_key,
						'type'              => sanitize_text_field( $step_type ),
						'repeater_id'       => $repeater_id,
						'text_title'        => $repeater_name,
						'text_empty'        => $repeater_empty,
						'text_add'          => $repeater_btn_add,
						'repeater_min'      => $repeater_min,
						'repeater_max'      => $repeater_max,
						'attributes'        => array(),
						'is_conditional'    => '',
						'conditional_rules' => array(),
					);

					if ( isset( $step['sgg_step_repeater_attributes'] ) && is_array( $step['sgg_step_repeater_attributes'] ) ) {
						foreach ( $step['sgg_step_repeater_attributes'] as $repeater_attribute ) {
							$sanitized_repeater_group = self::get_formatted_attribute( $repeater_attribute, $wc_product );

							if ( is_array( $sanitized_repeater_group ) && count( $sanitized_repeater_group ) > 0 ) {
								$sanitized_repeater_group['inc_price_label'] = $inc_price_label;	
								$sanitized_option_group['attributes'][] = $sanitized_repeater_group;
							}
						}
					}

					if ( isset( $step['sgg_step_conditional_logic'] ) && $step['sgg_step_conditional_logic'] ) {
						$sanitized_option_group['is_conditional'] = sanitize_text_field( $step['sgg_step_conditional_logic'] );
			
						$conditional_rules = array();
						foreach ( $step['sgg_step_conditional_rules'] as $conditional_rule ) {
							$ruleset = array(
								'key'      => $conditional_rule['sgg_step_conditional_step'],
								'value'    => $conditional_rule['sgg_step_conditional_value'],
								'compare'  => $conditional_rule['sgg_step_conditional_compare'],
								'relation' => $conditional_rule['sgg_step_conditional_relation'],
								'newset'   => $conditional_rule['sgg_step_conditional_link'],
							);
			
							if ( ! in_array( $ruleset['compare'], array( '=', '!=', 'empty', '!empty' ) ) ) {
								$ruleset['key'] = $conditional_rule['sgg_step_conditional_input'];
								$ruleset['value'] = $conditional_rule['sgg_step_conditional_input_value'];
							}
			
							$conditional_rules[] = $ruleset;
						}
			
						$sanitized_option_group['conditional_rules'] = $conditional_rules;
					}
			
					if ( count( $sanitized_option_group['attributes'] ) > 0 ) {
						$sanitized_steps[] = $sanitized_option_group;
					}

				} else {

					// Get options
					$sanitized_option_group = self::get_formatted_attribute( $step, $wc_product );

					if ( is_array( $sanitized_option_group ) && count( $sanitized_option_group ) > 0 ) {
						$sanitized_option_group['inc_price_label'] = $inc_price_label;	
						$sanitized_steps[] = $sanitized_option_group;
					}
				}
			}
		}

		$sanitized_steps = apply_filters( 'staggs_get_formatted_steps', $sanitized_steps, $post_id );

		return $sanitized_steps;
	}

	/**
	 * Format options step output
	 *
	 * @since    1.4.0
	 */
	public static function get_formatted_attribute( $step, $wc_product = false ) {
		global $global_conditionals, $global_option_rules, $sgg_is_pro;

		$attribute_id = (int) $step['sgg_step_attribute'];
		if ( ! $attribute_id ) {
			return false;
		}

		$show_option_price = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_show_option_price' ) );
		if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
			$show_option_price = 'hide';
		}

		$sanitized_attribute = array(
			'id'                => $attribute_id,
			'type'              => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_template' ) ),
			'layout'            => '',
			'style'             => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_style' ) ),
			'title'             => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_title' ) ),
			'classes'           => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_custom_class' ) ),
			'sku'               => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_sku' ) ),
			'required'          => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_field_required' ) ),
			'shared_group'      => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_shared_group' ) ),
			'short_description' => wpautop( wp_kses( staggs_get_post_meta( $attribute_id, 'sgg_step_short_description' ), 'post' ) ),
			'description'       => wpautop( wp_kses( staggs_get_post_meta( $attribute_id, 'sgg_step_description' ), 'post' ) ),
			'preview_index'     => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_index' ) ),
			'preview_slide'     => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_slide' ) ),
			'preview_order'     => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_order' ) ),
			'preview_ref'       => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_ref' ) ),
			'preview_ref_props' => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_ref_props' ) ),
			'preview_bundle'    => sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_bundle' ) ),
			'preview_height'    => '',
			'collapsible'       => isset( $step['sgg_step_attribute_collapsible'] ) ? sanitize_text_field( $step['sgg_step_attribute_collapsible'] ) : '',
			'collapsible_state' => isset( $step['sgg_step_attribute_state'] ) ? sanitize_text_field( $step['sgg_step_attribute_state'] ) : '',
			'show_option_price' => $show_option_price,
			'show_zoom'         => false,
			'default_options'   => array(),
			'options'           => array(),
			'is_conditional'    => '',
			'conditional_rules' => array(),
			'show_image'        => false,
			'hidden'            => isset( $step['sgg_step_attribute_hidden'] ) ? sanitize_text_field( $step['sgg_step_attribute_hidden'] ) : '',
		);

		if ( 'show' === sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_enable_zoom' ) ) ) {
			$sanitized_attribute['show_zoom'] = true;
		}

		if ( $sgg_is_pro ) {
			if ( staggs_get_post_meta( $attribute_id, 'sgg_gallery_type' ) ) {
				$sanitized_attribute['gallery_type'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_gallery_type' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_group' ) ) {
				$sanitized_attribute['model_group'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_group' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_material_metalness' ) ) {
				$sanitized_attribute['model_metalness'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_material_metalness' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_material_roughness' ) ) {
				$sanitized_attribute['model_roughness'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_material_roughness' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_type' ) ) {
				$sanitized_attribute['model_type'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_type' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_material' ) ) {
				$sanitized_attribute['model_material'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_material' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_target' ) ) {
				$sanitized_attribute['model_target'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_target' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_orbit' ) ) {
				$sanitized_attribute['model_orbit'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_orbit' ) );
			}
			if ( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_view' ) ) {
				$sanitized_attribute['model_view'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_model_image_view' ) );
			}
		}

		if ( '' === $sanitized_attribute['preview_index'] ) {
			$sanitized_attribute['preview_index'] = 1;
		}

		if ( 'yes' === $sanitized_attribute['preview_bundle'] ) {
			$sanitized_attribute['preview_height'] = sanitize_text_field( staggs_get_post_meta( $attribute_id, 'sgg_step_preview_height' ) );
		}

		if ( isset( $step['sgg_step_conditional_logic'] ) && $step['sgg_step_conditional_logic'] ) {
			$sanitized_attribute['is_conditional'] = sanitize_text_field( $step['sgg_step_conditional_logic'] );

			$conditional_rules = array();
			foreach ( $step['sgg_step_conditional_rules'] as $conditional_rule ) {
				$ruleset = array(
					'key'      => $conditional_rule['sgg_step_conditional_step'],
					'value'    => $conditional_rule['sgg_step_conditional_value'],
					'compare'  => $conditional_rule['sgg_step_conditional_compare'],
					'relation' => $conditional_rule['sgg_step_conditional_relation'],
					'newset'   => $conditional_rule['sgg_step_conditional_link'],
				);

				if ( ! in_array( $ruleset['compare'], array( '=', '!=', 'empty', '!empty' ) ) ) {
					$ruleset['key'] = $conditional_rule['sgg_step_conditional_input'];
					$ruleset['value'] = $conditional_rule['sgg_step_conditional_input_value'];
				}

				$conditional_rules[] = $ruleset;
			}

			$sanitized_attribute['conditional_rules'] = $conditional_rules;
		}
		else if ( $global_conditionals && array_key_exists( $attribute_id, $global_conditionals ) ) {
			$sanitized_attribute['is_conditional'] = true;
			$sanitized_attribute['conditional_rules'] = $global_conditionals[ $attribute_id ];
		}

		$variable_step_fields = array(
			'field_info'    => 'sgg_step_field_info',
			'card_template' => 'sgg_step_card_template',
			'show_image'    => 'sgg_step_show_image',
			'swatch_size'   => 'sgg_step_swatch_size',
			'swatch_style'  => 'sgg_step_swatch_style',
			'show_swatch_label' => 'sgg_step_show_swatch_label',
			'show_tooltip'      => 'sgg_step_show_tooltip',
			'tooltip_template'  => 'sgg_step_tooltip_template',
			'product_template'  => 'sgg_step_product_template',
			'allowed_options'   => 'sgg_step_allowed_options',
			'show_tick_all' => 'sgg_step_show_tick_all',
			'tick_all_label' => 'sgg_step_tick_all_label',
			'layout'        => 'sgg_step_option_layout',
			'button_add'    => 'sgg_step_button_add',
			'button_del'    => 'sgg_step_button_del',
			'button_view'   => 'sgg_step_button_view',
			'shared_min'   => 'sgg_step_shared_field_min',
			'shared_max'   => 'sgg_step_shared_field_max',
			'calc_price_type'   => 'sgg_step_calc_price_type',
			'calc_price_key'    => 'sgg_step_calc_price_key',
			'calc_price_label'  => 'sgg_step_calc_price_label',
			'calc_price_label_pos' => 'sgg_step_calc_price_label_position',
			'price_formula'     => 'sgg_step_price_formula',
			'price_table'       => 'sgg_step_price_table',
			'price_table_sale'  => 'sgg_step_price_table_sale',
			'price_table_type'  => 'sgg_step_price_table_type',
			'price_table_rounding' => 'sgg_step_price_table_rounding',
			'price_table_range' => 'sgg_step_price_table_range',
			'price_table_val_x' => 'sgg_step_price_table_val_x',
			'price_table_val_y' => 'sgg_step_price_table_val_y',
			'price_table_type_x' => 'sgg_step_price_table_type_x',
			'price_table_type_y' => 'sgg_step_price_table_type_y',
			'price_table_val_min' => 'sgg_step_price_table_val_min',
		);

		$sanitized_attribute['show_summary'] = '';
		if ( in_array( $sanitized_attribute['type'], array( 'dropdown', 'options', 'cards', 'icons', 'swatches', 'button-group' ) ) ) {
			$variable_step_fields['show_summary'] = 'sgg_step_show_summary';
		}

		foreach ( $variable_step_fields as $label => $step_key ) {
			if ( $value = staggs_get_post_meta( $attribute_id, $step_key ) ) {
				$sanitized_attribute[ $label ] = $value;
			}
		}

		$step_type    = staggs_get_post_meta( $attribute_id, 'sgg_attribute_type' );
		$step_options = staggs_get_post_meta( $attribute_id, 'sgg_attribute_items' );
		$template     = $sanitized_attribute['type'];

		$price_settings = array();
		if ( staggs_get_theme_option( 'sgg_product_multi_currencies' ) ) {
			$price_settings = staggs_get_theme_option( 'sgg_product_multi_currencies' );
		}

		if ( is_array( $step_options ) && count( $step_options ) > 0 ) {
			if ( isset( $step['sgg_step_attribute_default_value'] ) && $step['sgg_step_attribute_default_value'] ) {
				$sanitized_attribute['default_values'] = array( $step['sgg_step_attribute_value'] );
			}

			foreach ( $step_options as $option ) {
				$sanitized_attribute_item = self::get_formatted_attribute_item( $attribute_id, $option, $step_type, $template, $price_settings, $wc_product );

				if ( ! is_array( $sanitized_attribute_item ) ) {
					continue;
				}

				if ( ( ! isset( $sanitized_attribute_item['field_required'] ) || 'no' === $sanitized_attribute_item['field_required'] )
					&& 'yes' === $sanitized_attribute['required'] ) {
					$sanitized_attribute_item['field_required'] = 'yes';
				}

				if ( $sgg_is_pro ) {
					if ( isset( $step['sgg_step_option_conditional_logic'] ) && $step['sgg_step_option_conditional_logic'] ) {
						if ( ! is_array( $step['sgg_step_option_conditional_display'] ) || count( $step['sgg_step_option_conditional_display'] ) === 0 ) {
							continue;
						}

						$sanitized_attribute_item['is_conditional'] = sanitize_text_field( $step['sgg_step_option_conditional_logic'] );
		
						$conditional_rules = array();
						foreach ( $step['sgg_step_option_conditional_display'] as $conditional_option ) {
							$option_id = $conditional_option['sgg_step_conditional_option'];

							if ( ! isset(  $conditional_option['sgg_step_option_conditional_rules'] ) || count( $conditional_option['sgg_step_option_conditional_rules'] ) === 0 ) {
								continue;
							}
		
							if ( $option_id == $sanitized_attribute_item['id'] ) {
								foreach ( $conditional_option['sgg_step_option_conditional_rules'] as $conditional_rule ) {
									$ruleset = array(
										'key'      => $conditional_rule['sgg_step_conditional_step'],
										'value'    => $conditional_rule['sgg_step_conditional_value'],
										'compare'  => $conditional_rule['sgg_step_conditional_compare'],
										'relation' => $conditional_rule['sgg_step_conditional_relation'],
										'newset'   => $conditional_rule['sgg_step_conditional_link'],
									);
		
									if ( ! in_array( $ruleset['compare'], array( '=', '!=', 'empty', '!empty' ) ) ) {
										$ruleset['key'] = $conditional_rule['sgg_step_conditional_input'];
										$ruleset['value'] = $conditional_rule['sgg_step_conditional_input_value'];
									}
		
									$conditional_rules[] = $ruleset;
								}
							}
						}
						
						$sanitized_attribute_item['conditional_rules'] = $conditional_rules;
					}
					else if ( $global_option_rules && array_key_exists( $attribute_id, $global_option_rules ) ) {
						$attr_conditional_rules = $global_option_rules[ $attribute_id ];

						if ( array_key_exists( $sanitized_attribute_item['id'], $attr_conditional_rules ) ) {
							$sanitized_attribute_item['conditional_rules'] = $attr_conditional_rules[ $sanitized_attribute_item['id'] ];
						}
					}
				}

				$sanitized_attribute['options'][] = $sanitized_attribute_item;
			}
		}

		// Open up for modification.
		$sanitized_attribute = apply_filters( 'staggs_sanitized_attribute', $sanitized_attribute, $attribute_id );

		return $sanitized_attribute;
	}

	/**
	 * Format options option output
	 *
	 * @since    1.4.0
	 */
	public static function get_formatted_attribute_item( $group_id, $item, $type, $template, $price_settings = array(), $wc_product = false ) {
		global $staggs_price_settings, $sgg_is_pro;
		
		$sanitized_item = array(
			'id'                => staggs_sanitize_title( $item['sgg_option_label'] ),
			'type'              => $type,
			'group'             => $group_id,
			'image'             => '',
			'image_url'         => '',
			'base_price'        => 'yes',
			'price'             => -1,
			'sale_price'        => -1,
			'is_taxable'        => false,
			'tax_class'         => '',
			'weight'            => 0,
			'stock_qty'         => '',
			'hide_option'       => '',
			'stock_text'        => '',
			'is_conditional'    => '',
			'conditional_rules' => array(),
		);

		if ( isset( $item['sgg_option_weight'] ) && '' !== $item['sgg_option_weight'] ) {
			$sanitized_option['weight'] = (float) $item['sgg_option_weight'];
		}
		
		if ( '' !== $item['sgg_option_price'] ) {
			$sanitized_item['base_price'] = 'no';

			$item_price = (float) sanitize_text_field( $item['sgg_option_price'] );

			$exchange_rate = '';
			$exchange_rounding = '';

			if ( is_array($price_settings) && count($price_settings) > 0) {
				if ( function_exists( 'get_woocommerce_currency' ) ) {
					foreach ( $price_settings as $price_setting ) {
						if ( $price_setting['currency_id'] == get_woocommerce_currency()) {
							$exchange_rate = $price_setting['exchange_rate'];
							$exchange_rounding = $price_setting['exchange_rounding'];
						}
					}
				}

				if ( $exchange_rate && $exchange_rounding ) {
					if ( in_array( $exchange_rounding, array( '0.25', '0.50' ) ) ) {
						$item_price = staggs_round_price_to( ( $item_price * $exchange_rate ), $exchange_rounding );
					} else {
						$item_price = staggs_round_price_up( ( $item_price * $exchange_rate ), $exchange_rounding );
					}
				}
			}

			if ( 'no' === $staggs_price_settings['include_tax'] && 'incl' === $staggs_price_settings['price_display'] && $wc_product ) {
				$item_price = wc_get_price_including_tax( $wc_product, array( 'price' => $item_price ) );
			} else if ( 'yes' === $staggs_price_settings['include_tax'] && 'excl' === $staggs_price_settings['price_display'] && $wc_product ) {
				$item_price = wc_get_price_excluding_tax( $wc_product, array( 'price' => $item_price ) );
			}

			$sanitized_item['price'] = $item_price;

			if ( $item['sgg_option_sale_price'] ) {
				$item_sale_price = (float) sanitize_text_field( $item['sgg_option_sale_price'] );

				if ( $exchange_rate && $exchange_rounding ) {
					if ( in_array( $exchange_rounding, array( '0.25', '0.50' ) ) ) {
						$item_sale_price = staggs_round_price_to( ( $item_sale_price * $exchange_rate ), $exchange_rounding );
					} else {
						$item_sale_price = staggs_round_price_up( ( $item_sale_price * $exchange_rate ), $exchange_rounding );
					}
				}

				if ( 'no' === $staggs_price_settings['include_tax'] && 'incl' === $staggs_price_settings['price_display'] && $wc_product ) { 
					$item_sale_price = wc_get_price_including_tax( $wc_product, array( 'price' => $item_sale_price ) );
				} else if ( 'yes' === $staggs_price_settings['include_tax'] && 'excl' === $staggs_price_settings['price_display'] && $wc_product ) {
					$item_sale_price = wc_get_price_excluding_tax( $wc_product, array( 'price' => $item_price ) );
				}

				$sanitized_item['sale_price'] = $item_sale_price;
			}
		} else if ( 'formula' === $item['sgg_option_calc_price_type'] && isset( $item['sgg_option_price_formula'] ) && '' !== $item['sgg_option_price_formula'] ) {
			$sanitized_item['base_price'] = 'no';
			$sanitized_item['price_formula'] = $item['sgg_option_price_formula'];
		} else if ( 'percentage' === $item['sgg_option_calc_price_type'] && isset( $item['sgg_option_percentage'] ) && '' !== $item['sgg_option_percentage'] ) {
			$sanitized_item['base_price'] = 'no';
			$sanitized_item['price_percent'] = $item['sgg_option_percentage'];
			$sanitized_item['price_field'] = $item['sgg_option_percentage_field'] ?: 'sgg_total_price';
		} else if ( 'table' === $item['sgg_option_calc_price_type'] && isset( $item['sgg_option_price_table'] ) && '' !== $item['sgg_option_price_table'] ) {
			$sanitized_item['base_price'] = 'no';
			$sanitized_item['price_table'] = $item['sgg_option_price_table'];
		} else if ( isset( $item['sgg_option_calc_price_value'] ) && '' !== $item['sgg_option_calc_price_value'] ) {
			$sanitized_item['base_price'] = 'no';
		}

		foreach ( $item as $key => $value ) {
			$item_key = str_replace( 'sgg_option_', '', $key );

			if ( $item_key == 'price' || $item_key == 'sale_price' ) {
				continue;
			}

			// TODO remove later on.
			if ( $item_key === 'label' ) {
				$item_key = 'name';
			}
			if ( $item_key === 'calc_price_type' ) {
				$item_key = 'price_type';
			}
			if ( $item_key === 'calc_price_value' ) {
				$item_key = 'unit_price';
			}

			if ( '' !== $value ) {
				$sanitized_item[ $item_key ] = $value;
			}
		}

		if ( 'input' === $type && 'yes' === $sanitized_item['enable_preview'] && ! isset( $sanitized_item['field_key'] ) ) {
			$sanitized_item['field_key'] = staggs_sanitize_title( $item['sgg_option_label'] );
		}

		if ( '' !== $sanitized_item['image'] ) {
			$image_size = apply_filters( 'staggs_product_option_image_size', 'thumbnail' );
			$image_id   = $sanitized_item['image'];
			$sanitized_item['image'] = wp_get_attachment_image( $image_id, $image_size );
			$sanitized_item['image_url'] = wp_get_attachment_image_url( $image_id, 'full' );
		}

		if ( 'yes' === $sanitized_item['enable_preview'] && isset( $sanitized_item['preview'] ) ) {
			if ( is_array( $sanitized_item['preview'] ) && count( $sanitized_item['preview'] ) > 0 ) {
				$preview_size = apply_filters('staggs_product_image_size', 'full' );
				foreach ( $sanitized_item['preview'] as $key => $preview_id ) {
					if ( $preview_id ) {
						$sanitized_item['preview'][ $key ] = esc_url( wp_get_attachment_image_url( $preview_id, $preview_size ) );
					}
				}
			} else {
				$sanitized_item['preview'] = array();
			}
		}

		if ( $sgg_is_pro ) {
			if ( '' !== $item['sgg_option_stock_qty'] ) {
				$sanitized_item['manage_stock'] = 'yes';
				$sanitized_item['stock_qty'] = (int) sanitize_key( $item['sgg_option_stock_qty'] );
			}

			$sanitized_item['hide_option'] = (int) sanitize_key( staggs_get_theme_option( 'sgg_option_hide_out_of_stock' ) );
			if ( staggs_get_theme_option( 'sgg_option_out_of_stock_message' ) ) {
				$sanitized_item['stock_text'] = sanitize_text_field( staggs_get_theme_option( 'sgg_option_out_of_stock_message' ) );
			} else {
				$sanitized_item['stock_text'] = apply_filters( 'staggs_out_of_stock_message', __( 'Out of stock', 'staggs' ) );
			}

			if ( 'link' === $type ) {
				$product_id = sanitize_key( $item['sgg_option_linked_product_id'] );
				$product_qty = sanitize_key( $item['sgg_option_linked_product_qty'] ) ?: 1;

				if ( $product_id && function_exists('wc_get_product') ) {
					$link_item = self::get_formatted_link_item( $product_id, $product_qty, $template === 'product' );

					foreach ( $link_item as $link_key => $link_value ) {
						if ( 'no' === $sanitized_item['base_price'] ) {
							// Allow custom price to be set in configurator.
							if ( 'price' === $link_key || 'sale_price' === $link_key ) {
								continue;
							}
						} 

						if ( ( 'image' == $link_key || 'image_url' == $link_key ) && $sanitized_item[ $link_key] !== '' ) {
							// Don't replace image if configured.
							continue;
						}

						$sanitized_item[ $link_key ] = $link_value;
					}

					$sanitized_item['product_id'] = $product_id;
					$sanitized_item['product_qty'] = $product_qty;
				}
			}
		}

		// Open up for modification.
		$sanitized_item = apply_filters( 'staggs_sanitized_attribute_item', $sanitized_item, $item );

		return $sanitized_item;
	}

	/**
	 * Format options model output
	 *
	 * @since    1.3.2
	 */
	public static function get_formatted_link_item( $product_id, $product_qty, $use_thumbnail = false ) {
		global $staggs_price_settings, $sgg_stock_settings;

		$sanitized_option = array(
			'price'       => -1,
			'sale_price'  => -1,
			'is_taxable'  => false,
			'tax_class'   => '',
			'weight'      => 0,
		);

		$wc_product = wc_get_product($product_id);
		if ( get_post_meta( $product_id, '_regular_price', true ) ) {
			$sanitized_option['base_price'] = 'no';

			$item_price = (float) $wc_product->get_regular_price();
			if ( 'no' === $staggs_price_settings['include_tax'] && 'incl' === $staggs_price_settings['price_display'] ){ 
				$item_price = wc_get_price_including_tax( $wc_product, array( 'price' => $item_price ) );
			} else if ( 'yes' === $staggs_price_settings['include_tax'] && 'excl' === $staggs_price_settings['price_display'] && $wc_product ) {
				$item_price = wc_get_price_excluding_tax( $wc_product, array( 'price' => $item_price ) );
			}

			$sanitized_option['price'] = $item_price * $product_qty;

			if ( get_post_meta( $product_id, '_sale_price', true ) ) {
				$item_sale_price = (float) $wc_product->get_sale_price();
				if ( 'no' === $staggs_price_settings['include_tax'] && 'incl' === $staggs_price_settings['price_display'] ){ 
					$item_sale_price = wc_get_price_including_tax( $wc_product, array( 'price' => $item_sale_price ) );
				} else if ( 'yes' === $staggs_price_settings['include_tax'] && 'excl' === $staggs_price_settings['price_display'] && $wc_product ) {
					$item_sale_price = wc_get_price_excluding_tax( $wc_product, array( 'price' => $item_price ) );
				}
				
				$sanitized_option['sale_price'] = $item_sale_price * $product_qty;
			}
		}

		if ( $wc_product->get_weight() ) {
			$sanitized_option['weight'] = (float) $wc_product->get_weight() * $product_qty;
		}
		
		if ( 'none' !== $wc_product->get_tax_status() ) {
			$sanitized_option['is_taxable'] = true;
			
			if ( $wc_product->get_tax_class() ) {
				$sanitized_option['tax_class'] = $wc_product->get_tax_class();
			}
		}

		if ( $use_thumbnail ) {
			$thumbnail_id = get_post_thumbnail_id( $product_id );

			if ( $thumbnail_id ) {
				$image_size = apply_filters('staggs_product_option_image_size', 'thumbnail' );
				$sanitized_option['image'] = wp_get_attachment_image( $thumbnail_id, $image_size );
				$sanitized_option['image_url'] = wp_get_attachment_image_url( $thumbnail_id, 'full' );
			} else if ( $parent_id = wp_get_post_parent_id( $product_id ) ) {
				$parent_thumbnail_id = get_post_thumbnail_id( $parent_id );

				if ( $parent_thumbnail_id ) {
					$image_size = apply_filters('staggs_product_option_image_size', 'thumbnail' );
					$sanitized_option['image'] = wp_get_attachment_image( $parent_thumbnail_id, $image_size );
					$sanitized_option['image_url'] = wp_get_attachment_image_url( $parent_thumbnail_id, 'full' );
				}
			}
		}

		$manage_stock = get_post_meta( $product_id, '_manage_stock', true );
		$stock_qty = (int) $wc_product->get_stock_quantity();
		$stock_status = get_post_meta( $product_id, '_stock_status', true );
		$backorders = get_post_meta( $product_id, '_backorders', true );

		if ( ( 'no' === $manage_stock && 'instock' === $stock_status ) || $wc_product->is_on_backorder() ) {
			// On backorder or in stock without defined stock.
			// Allow orders
		} else {
			// No backorder.
			if ( 'yes' === $manage_stock ) {
				$sanitized_option['manage_stock'] = 'yes';
				$sanitized_option['stock_qty'] = $stock_qty;
			}
			
			if ( ( 'outofstock' === $stock_status && 'no' === $manage_stock ) 
				|| ( 'yes' === $manage_stock && 0 === $stock_qty && 'no' === $backorders ) 
				|| ( $product_qty > $stock_qty ) ) {
				$sanitized_option['manage_stock'] = 'yes';
				$sanitized_option['stock_qty'] = 0; // no available stock
				$sanitized_option['hide_option'] = false;
				$sanitized_option['stock_text'] = apply_filters( 'staggs_out_of_stock_message', $sgg_stock_settings['no_stock'] );
			}
		}

		return $sanitized_option;
	}
}
