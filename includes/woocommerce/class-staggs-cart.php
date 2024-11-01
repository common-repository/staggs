<?php

/**
 * The file that defines the core plugin cart class
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The core plugin cart handler class.
 *
 * This is used to define all WooCommerce cart hooks.
 *
 * @since      1.0.0
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Cart {

	public function __construct()
	{
		add_filter( 'woocommerce_product_get_sku', function($sku, $product) {
			$changes = $product->get_changes();
			if ( isset( $changes['sku'] ) ) {
				$sku = $changes['sku'];
			}
			return $sku;
		}, 99, 2);

		add_filter( 'woocommerce_product_get_price', function($price, $product) {
			$changes = $product->get_changes();
			if ( isset( $changes['price'] ) ) {
				$price = $changes['price'];
			}
			return $price;
		}, 99, 2);
	}

	/**
	 * Remove loop add to cart links for configurable products.
	 *
	 * @since    1.0.0
	 */
	public function filter_add_to_cart_link( $add_to_cart_link, $product, $args = array() ) {
		if ( product_is_inline_configurator( $product->get_id() ) ) {
			$add_to_cart_link = sprintf(
				'<a href="%s" class="%s">%s</a>',
				get_permalink( $product->get_id() ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				esc_html( $product->add_to_cart_text() )
			);
		}

		return $add_to_cart_link;
	}

	/**
	 * Add fragments for notices.
	 *
	 * @since 1.3.0
	 * @param array $fragments WooCommerce AJAX fragments
	 * @return array $fragments Modified WooCommerce AJAX fragments
	 */
	public function staggs_add_to_cart_fragments( $fragments ) {
		$all_notices  = WC()->session->get( 'wc_notices', array() );
		$notice_types = apply_filters( 'woocommerce_notice_types', array( 'error', 'success', 'notice' ) );

		ob_start();
		foreach ( $notice_types as $notice_type ) {
			if ( wc_notice_count( $notice_type ) > 0 ) {
				wc_get_template(
					"notices/{$notice_type}.php",
					array(
						'notices' => array_filter( $all_notices[ $notice_type ] ),
					)
				);

				if ( 'error' === $notice_type ) {
					break;
				}
			}
		}
		$fragments['notices_html'] = ob_get_clean();

		wc_clear_notices();

		return $fragments;
	}

	/**
	 * Add configured product to the WooCommerce shopping cart.
	 *
	 * @since    1.0.0
	 */
	public function staggs_add_product_to_cart() {
		// Get filtered POST data.
		$revised_post_array = get_sanitized_post_data();

		$exclude_addons = staggs_get_theme_option('sgg_checkout_exclude_product_addons') ?: false;
		$exclude_base = staggs_get_theme_option('sgg_checkout_exclude_product_base') ?: false;
		$exclude_linked_ids = staggs_get_theme_option('sgg_checkout_exclude_linked_products') ?: false;
		$bundle_lines = staggs_get_theme_option('sgg_checkout_bundle_product_lines') ?: false;

		if ( is_array( $revised_post_array ) && count( $revised_post_array ) > 0 ) {
			// Sanitize all post array variables.
			$cart_product_id = $revised_post_array['product_id'];

			$quantity = 1;
			if ( isset( $revised_post_array['quantity'] ) && (int) $revised_post_array['quantity'] > 1 ) {
				$quantity = (int) $revised_post_array['quantity'];
			}

			if ( function_exists('staggs_get_product_stock_quantity') ) {
				// Get low stock for configuration.
				$low_stock = staggs_get_product_stock_quantity( $revised_post_array['options'] );

				// Check quantity.
				if ( $quantity > $low_stock ) {
					// Change the quantity to the limit allowed
					$quantity = $low_stock;

					$error_msg = sprintf(
						__( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available).', 'staggs' ), 
						get_the_title( $revised_post_array['product_id'] ),
						$low_stock
					);

					// Add a custom notice
					wc_add_notice( $error_msg, 'error' );

					// No redirection, get refreshed notices.
					WC_AJAX::get_refreshed_fragments();
					die();
				}
			}

			$cart_item_data = array();
			if ( $bundle_lines ) {
				$bundle_hash = md5( serialize( $revised_post_array['options'] ) );
				$cart_item_data = array(
					'sgg_bundle_key' => $bundle_hash
				);
			}

			if ( ! $exclude_base ) {
				$added = WC()->cart->add_to_cart( $cart_product_id, $quantity, 0, array(), $cart_item_data );
			}

			if ( $exclude_addons && is_array( $revised_post_array['options'] ) ) {
				foreach ( $revised_post_array['options'] as $option ) {
					if ( isset( $option['product'] ) && $option['product'] ) {
						// Single option
						$option_product_id = $option['product'];
						$option_product_qty = $option['value'] * $quantity;
						$option_cart_data = array(
							'sgg_addon_product' => true,
							'product_price' => $option['price'],
						);
						if ( $bundle_lines ) {
							$option_cart_data['sgg_bundle_key'] = $bundle_hash;
						}

						$added = WC()->cart->add_to_cart( $option_product_id, $option_product_qty, 0, array(), $option_cart_data );

					} else if ( is_array( $option['value'] ) && count( $option['value'] ) > 0 ) {

						// Repeater option
						foreach ( $option['value'] as $option_val ) {
							$option_product_id = $option_val['product'];
							$option_product_qty = $option_val['value'] * $quantity;
							$option_cart_data = array(
								'sgg_addon_product' => true,
								'product_price' => $option_val['price'],
							);
							if ( $bundle_lines ) {
								$option_cart_data['sgg_bundle_key'] = $bundle_hash;
							}

							$added = WC()->cart->add_to_cart( $option_product_id, $option_product_qty, 0, array(), $option_cart_data );
						}
					}
				}
			}

			if ( $exclude_linked_ids && is_array( $revised_post_array['options'] ) ) {
				foreach ( $revised_post_array['options'] as $option ) {
					if ( isset( $option['product_id'] ) && $option['product_id'] ) {
						// Single option
						$option_product_id = $option['product_id'];
						$option_product_qty = isset( $option['product_qty'] ) ? $option['product_qty'] : 1;
						$option_cart_data = array(
							'sgg_addon_product' => true,
							'product_price' => (float) $option['price'] / $option_product_qty,
						);
						if ( $bundle_lines ) {
							$option_cart_data['sgg_bundle_key'] = $bundle_hash;
						}

						$option_qty = $quantity * $option_product_qty;

						$added = WC()->cart->add_to_cart( $option_product_id, $option_qty, 0, array(), $option_cart_data );

					} else if ( is_array( $option['value'] ) && count( $option['value'] ) > 0 ) {

						// Repeater option
						foreach ( $option['value'] as $option_val ) {
							$option_product_id = $option_val['product_id'];
							$option_cart_data = array(
								'sgg_addon_product' => true,
								'product_price' => $option_val['price'],
							);
							if ( $bundle_lines ) {
								$option_cart_data['sgg_bundle_key'] = $bundle_hash;
							}

							$added = WC()->cart->add_to_cart( $option_product_id, $quantity, 0, array(), $option_cart_data );
						}
					}
				}
			}

			if ( $added ) {
				// Succesfully added.
				do_action( 'woocommerce_ajax_added_to_cart', $cart_product_id );

				wc_add_to_cart_message( $cart_product_id );
			}

			if ( 'no' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				// No redirection, get refreshed notices.
				WC_AJAX::get_refreshed_fragments();
			}

			echo $added ? '1' : '0';
		} else {
			echo '0';
		}
	}

	/**
	 * Delete other linked products on bundle product remove.
	 *
	 * @since    1.8.2
	 */
	public function staggs_remove_all_bundle_products( $cart_item_key, $cart ) {
		if ( ! isset( $cart->cart_contents[$cart_item_key]['sgg_bundle_key'] ) ) {
			return;
		}

		remove_action( 'woocommerce_remove_cart_item', array( $this, 'staggs_remove_all_bundle_products' ), 10, 2 );
		
		//Get the key of the bundle product removed
		$removed_bundle_key = $cart->cart_contents[$cart_item_key]['sgg_bundle_key'];

		// Loop through the cart items
		foreach ( $cart->get_cart() as $item_key => $item ) {
			if ( ! array_key_exists( 'sgg_bundle_key', $item ) ) {
				continue;
			}
			if ( $item_key === $cart_item_key ) {
				continue;
			}
			if ( $removed_bundle_key !== $item['sgg_bundle_key'] ) {
				continue;
			}
			// Remove the product from the cart
			$cart->remove_cart_item( $item_key );
		}

		add_action( 'woocommerce_remove_cart_item', array( $this, 'staggs_remove_all_bundle_products' ), 10, 2 );
	}

	/**
	 * Save configurator data to cart item.
	 *
	 * @since    1.0.0
	 */
	public function staggs_save_product_data( $cart_item_data, $product_id ) {

		if ( ! product_is_configurable( $product_id ) ) {
			return $cart_item_data;
		}
		
		if ( isset( $cart_item_data['sgg_addon_product'] ) ) {
			return $cart_item_data;
		}

		// Get filtered POST data.
		$revised_post_array = get_sanitized_post_data();
		if ( ! isset( $revised_post_array['options'] ) && ! isset( $revised_post_array['product_price'] ) ) {
			return $cart_item_data;
		}

		$post_product_price  = isset( $revised_post_array['product_price'] ) ? sanitize_text_field( $revised_post_array['product_price'] ) : 0;
		$configurator_totals = get_configurator_cart_totals( $revised_post_array, $post_product_price, $product_id );
		$configurator_totals_price = $configurator_totals['product_price'];

		$cart_item_data['sgg_post_options'] = $revised_post_array;

		// Set custom image
		if ( isset( $revised_post_array['product_image'] ) ) {
			$image_name = sanitize_title( get_the_title( $product_id ) );

			if ( strpos( $revised_post_array['product_image'], 'base64' ) ) {
				$image_url = store_final_product_image( $image_name, $revised_post_array['product_image'], $revised_post_array['options'] );
			} else {
				$image_url = $revised_post_array['product_image'];
			}

			$cart_item_data['product_image'] = sanitize_url( $image_url );
		}

		// Set options array.
		$esc_product_options = array();
		$product_options     = isset( $configurator_totals['options'] ) ? filter_var_array( $configurator_totals['options'] ) : array();
		$product_addon_total = 0;
		$exclude_addons      = staggs_get_theme_option('sgg_checkout_exclude_product_addons') ?: false;
		$exclude_linked_ids  = staggs_get_theme_option('sgg_checkout_exclude_linked_products') ?: false;
		$product_skus        = array();
		$product_sku_labels  = array();
		if ( staggs_get_post_meta( $product_id, 'sgg_configurator_sku' ) ) {
			$product_sku_labels = explode(',', staggs_get_post_meta( $product_id, 'sgg_configurator_sku' ) );
			if ( count( $product_sku_labels ) > 0 ) {
				$product_sku_labels = array_map( 'trim', $product_sku_labels );
			}
		}

		if ( is_array( $product_options ) && count( $product_options ) > 0 ) {
			// Sanitize all post data option key items.
			foreach ( $product_options as $option ) {
				$filtered_option = array();

				if ( 'original_post_id' == $option['name'] ) {
					$cart_item_data['original_id'] = sanitize_key( $option['value'] );
					continue;
				}

				if ( isset( $option['sku'] ) && in_array( $option['name'], $product_sku_labels ) ) {
					$product_skus[] = $option['sku'];
				}

				if ( $exclude_addons && isset( $option['product'] ) && $option['product'] ) {
					$product_addon_total += $option['price'];
					continue;
				}

				if ( $exclude_linked_ids && isset( $option['product_id'] ) && $option['product_id'] ) {
					$product_addon_total += $option['price'];
					continue;
				}

				foreach ( $option as $key => $value ) {
					if ( 'id' === $key || 'step_id' == $key || 'price' === $key || 'sku' === $key || 'step_sku' === $key ) {

						$filtered_option[ '_' . sanitize_text_field( $key ) ] = sanitize_text_field( $value );

					} else if ( $value !== null && $value !== '' ) {

						if ( is_array( $value ) ) {

							// multi value val.
							foreach ( $value as $rep_key => $rep_val ) {

								foreach ( $rep_val as $sub_key => $sub_value ) {

									if ( 'id' === $sub_key || 'step_id' == $sub_key ) {

										$value[ $rep_key ][ '_' . sanitize_text_field( $sub_key ) ] = sanitize_text_field( $sub_value );
										
									} else if ( $value !== null && $value !== '' ) {

										if ( 'value' == $sub_key && strpos( $sub_value, 'base64' ) ) {
											
											// file val.
											$image_parts = explode( '|', $sub_value );
											$image_name = $image_parts[0];
											$image_url = store_final_product_image( $image_name, $image_parts[1], array(), false );

											$value[ $rep_key ][ sanitize_text_field( $sub_key ) ] = $image_name;
											$value[ $rep_key ][ sanitize_text_field( $sub_key . '_url' ) ] = $image_url;
											
										} else if ( 'value' == $sub_key && strpos( $sub_value, 'uploads/staggs' ) ) {

											// image val.
											$image_name = basename( $sub_value );
											if ( str_contains( $sub_value, '|' ) ) {
												$sub_value = explode( '|', $sub_value )[1]; // only get url
											}

											$value[ $rep_key ][ sanitize_text_field( $sub_key ) ] = $image_name;
											$value[ $rep_key ][ sanitize_text_field( $sub_key . '_url' ) ] = $sub_value;
											
										} else {

											// single val.
											$value[ $rep_key ][ sanitize_text_field( $sub_key ) ] = sanitize_text_field( $sub_value );
										}
									}
								}
							}

							$filtered_option[ sanitize_text_field( $key ) ] = $value;

						} else if ( 'value' == $key && strpos( $value, 'base64' ) ) {

							// file val.
							$image_parts = explode( '|', $value );
							$image_name = $image_parts[0];
							$image_url = store_final_product_image( $image_name, $image_parts[1], array(), false );

							$filtered_option[ sanitize_text_field( $key ) ] = $image_name;
							$filtered_option[ sanitize_text_field( $key . '_url' ) ] = $image_url;

						} else if ( 'value' == $key && strpos( $value, 'uploads/staggs' ) ) {

							// image val.
							$image_name = basename( $value );
							if ( str_contains( $value, '|' ) ) {
								$value = explode( '|', $value )[1];
							}

							$filtered_option[ sanitize_text_field( $key ) ] = $image_name;
							$filtered_option[ sanitize_text_field( $key . '_url' ) ] = $value;

						} else {

							// single val.
							$filtered_option[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
						}
					}
				}

				$esc_product_options[] = $filtered_option;
			}

			$cart_item_data['product_parts'] = $esc_product_options;
		}

		// Set final price
		$cart_item_data['product_price'] = ( $configurator_totals_price - $product_addon_total );

		// Set final weight
		if ( isset( $configurator_totals['product_weight'] ) ) {
			$cart_item_data['product_weight'] = $configurator_totals['product_weight'];
		}

		// Set final SKU
		if ( count( $product_skus ) > 0 ) {
			$sku_separator = apply_filters( 'staggs_sku_separator', '-' );
			$cart_item_data['product_sku'] = implode( $sku_separator, $product_skus );
		}

		return $cart_item_data;
	}

	/**
	 * Modify product item link when in cart.
	 *
	 * @since    1.0.0
	 */
	public function staggs_modify_cart_link( $permalink, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['original_id'] ) ) {
			$permalink = get_permalink( $cart_item['original_id'] );
		}
		return $permalink;
	}

	/**
	 * Add custom items to cart meta so it is visible in the cart.
	 *
	 * @since    1.0.0
	 */
	public function staggs_render_data_on_cart_checkout( $cart_data, $cart_item = null ) {
		$custom_items = array();

		if ( ! empty( $cart_data ) ) {
			$custom_items = $cart_data;
		}

		if ( isset( $cart_item['product_parts'] ) && is_array( $cart_item['product_parts'] ) ) {
			$cart_show_sku = staggs_get_theme_option( 'sgg_cart_show_option_skus' );
			$cart_show_image = 'preview' === staggs_get_theme_option( 'sgg_checkout_file_upload_display' );
			$cart_image_size = $this->get_checkout_image_sizes();
			$custom_item_count = array();

			foreach ( $cart_item['product_parts'] as $product_part ) {
				if ( isset( $product_part['hidden'] ) && 'true' === (string) $product_part['hidden'] ) {
					continue;
				}
				
				$product_part_label = isset( $product_part['label'] ) ? $product_part['label'] : ucfirst( str_replace( '-', ' ', $product_part['name'] ) );
				if ( is_array( $product_part['value'] ) ) {
					$product_part_value = '';

					foreach ( $product_part['value'] as $index => $repeat_value ) {
						if ( isset( $repeat_value['hidden'] ) && 'true' === (string) $repeat_value['hidden'] ) {
							continue;
						}
						
						$repeat_part_label = isset( $repeat_value['label'] ) ? $repeat_value['label'] : ucfirst( str_replace( '-', ' ', $repeat_value['name'] ) );
						$product_part_value .= '<br><span class="subitem"><span>' . $repeat_part_label . ':</span> '; 

						if ( isset( $repeat_value['value_url'] ) && '' !== $repeat_value['value_url'] ) {
							if ( $cart_show_image ) {
								$product_part_value .= '<img src="' . $repeat_value['value_url'] . '" width="' . $cart_image_size[0] . '" height="' . $cart_image_size[1] . '" alt="' . $repeat_value['value'] . '" />';
							} else {
								$product_part_value .= '<a href="' . $repeat_value['value_url'] . '">' . $repeat_value['value'] . '</a>';
							}
						} else {
							$product_part_value .= $repeat_value['value'];
						}

						if ( 'yes' == $cart_show_sku && isset( $repeat_value['_sku'] ) ) {
							$product_part_value .= ' (' . $repeat_value['_sku'] . ')';
						}

						$product_part_value .= '</span>';
					}

					$custom_items[] = array(
						'name'  => $product_part_label,
						'value' => $product_part_value
					);
				} else {

					if ( isset( $product_part['value_url'] ) && '' !== $product_part['value_url'] ) {
						if ( $cart_show_image ) {
							$product_part_value = '<img src="' . $product_part['value_url'] . '" width="' . $cart_image_size[0] . '" height="' . $cart_image_size[1] . '" alt="' . $product_part['value'] . '" />';
						} else {
							$product_part_value = '<a href="' . $product_part['value_url'] . '">' . $product_part['value'] . '</a>';
						}
					} else {
						$product_part_value = $product_part['value'];
					}

					if ( 'yes' == $cart_show_sku && isset( $product_part['_sku'] ) ) {
						$product_part_value .= ' (' . $product_part['_sku'] . ')';
					}

					$product_part_key = staggs_sanitize_title( $product_part_label );
					if ( array_key_exists( $product_part_key, $custom_items ) ) {
						if ( ! array_key_exists( $product_part_key, $custom_item_count ) ) {
							$custom_item_count[ $product_part_key ] = 2;
						}
						
						$product_temp_key = $product_part_key;
						$product_part_key = $product_part_key . '_' . $custom_item_count[ $product_temp_key ];
						$product_part_label = $product_part_label . ' ' . $custom_item_count[ $product_temp_key ];
						$custom_item_count[ $product_temp_key ]++;
					}
					// } else {
					// 	$custom_items[ $product_part_key ]['value'] = $custom_items[ $product_part_key ]['value'] . ', ' . $product_part_value;
					// }

					$custom_items[ $product_part_key ] = array(
						'name'  => $product_part_label,
						'value' => $product_part_value
					);
				}
			}
		}

		return $custom_items;
	}

	/**
	 * Add custom items to order item meta for permanent access.
	 *
	 * @since    1.0.0
	 */
	public function staggs_save_order_meta( $order_item, $cart_item_key, $cart_item, $order ) {
		$save_option_price = staggs_get_theme_option( 'sgg_order_save_attribute_prices' );
		$save_option_sku = staggs_get_theme_option( 'sgg_order_save_attribute_skus' );
		$exclude_attribute_sku = staggs_get_theme_option( 'sgg_order_exclude_attribute_parent_skus' );
		$save_option_qty = staggs_get_theme_option( 'sgg_order_save_attribute_qty' );
		$save_option_pdf = staggs_get_theme_option( 'sgg_order_save_pdf_attachment' );

		if ( $save_option_pdf ) {
			/**
			 * Generate PDF 
			 */
			if ( class_exists( 'Staggs_PDF' ) ) {
				$pdf_data = array(
					'configuration' => $cart_item['product_parts'],
					'product_image' => $cart_item['product_image'],
					'product_price' => $cart_item['product_price'],
					'cart_item_key' => $cart_item_key,
				);

				$pdf_data = apply_filters( 'staggs_order_pdf_data', $pdf_data, $cart_item );

				$order_item->update_meta_data( '_staggs_pdf_data', $pdf_data );
			}
		}

		if ( isset( $cart_item['product_parts'] ) && is_array( $cart_item['product_parts'] ) ) {
			$product_item_ids = array();
			$product_item_skus = array();

			$configuration_name_count = array();

			foreach ( $cart_item['product_parts'] as $configuration ) {
				$configuration['name'] = ucfirst( str_replace( '-', ' ', $configuration['name'] ) );
				$configuration_name    = $configuration['name'];
				$configuration_value   = $configuration['value'];

				if ( $order_item->get_meta( $configuration['name'] ) ) {
					if ( ! array_key_exists( $configuration['name'], $configuration_name_count ) ) {
						$configuration_name_count[ $configuration['name'] ] = 2;
					}

					$configuration_temp = $configuration['name'];
					$configuration_name = $configuration['name'] . ' ' . $configuration_name_count[ $configuration['name'] ];
					$configuration_name_count[ $configuration_temp ]++;
				}

				$order_item->update_meta_data( $configuration_name, $configuration_value );

				if ( isset( $configuration['value_url'] ) ) {
					$url_label = strtolower( str_replace( ' ', '_', $configuration_name ) );
					$url_label = '_' . $url_label . '_url';

					$order_item->update_meta_data( $url_label, esc_url( $configuration['value_url'] ) );
				}

				if ( isset( $configuration['_id'] ) ) {
					$product_item_ids[] = $configuration['_step_id'] . '::' . $configuration['_id'];
				}

				if ( isset( $configuration['_price'] ) && $save_option_price ) {
					$price_label = strtolower( str_replace( ' ', '_', $configuration_name ) );
					$price_label = '_' . $price_label . '_price';

					$price_value = (float) $configuration['_price'];
					if ( $order_item->get_meta( $price_label ) ) {
						$price_value = (float) $order_item->get_meta( $price_label ) + (float) $price_value;
					}

					$order_item->update_meta_data( $price_label, $price_value );
				}

				if ( isset( $configuration['_sku'] ) && $save_option_sku ) {
					$sku_label = strtolower( str_replace( ' ', '_', $configuration_name ) );

					if ( isset( $configuration['_step_sku'] ) ) {
						if ( $exclude_attribute_sku ) {
							$order_item->update_meta_data( '_' . $sku_label . '_sku', $configuration['_sku'] );
						} else {
							$order_item->update_meta_data( '_' . $configuration['_step_sku'], $configuration['_sku'] );
						}

						$product_item_skus[] = array(
							'key' => $configuration['_step_sku'],
							'value' => $configuration['_sku'],
						);
					} else {
						$order_item->update_meta_data( '_' . $sku_label . '_sku', $configuration['_sku'] );

						$product_item_skus[] = array(
							'value' => $configuration['_sku'],
						);
					}
				}

				if ( $save_option_qty ) {
					$qty_label = strtolower( str_replace( ' ', '_', $configuration_name ) );
					$qty_label = '_' . $qty_label . '_qty';
					$qty = 1;
					if ( $order_item->get_meta( $qty_label ) ) {
						$qty = $qty + $order_item->get_meta( $qty_label );
					}
					$order_item->update_meta_data( $qty_label, $qty );
				}
			}

			if ( count( $product_item_ids ) > 0 ) {
				$order_item->update_meta_data( '_configurator_item_ids', sanitize_text_field( implode( ',', $product_item_ids ) ) );
			}

			if ( count( $product_item_skus ) > 0 ) {
				$order_item->update_meta_data( '_configurator_sku_data', serialize( $product_item_skus ) );
			}
		}

		if ( isset( $cart_item['product_image'] ) ) {
			$order_item->update_meta_data( '_product_thumbnail', $cart_item['product_image'] );
		}

		if ( isset( $cart_item['product_sku'] ) ) {
        	$order_item->update_meta_data( '_sku', $cart_item['product_sku'] );
		}

		if ( isset( $cart_item['product_weight'] ) ) {
        	$order_item->update_meta_data( '_weight', $cart_item['product_weight'] );
		}
	}

	/**
	 * Add custom items to order item meta for permanent access.
	 *
	 * @since    1.0.0
	 */
	public function staggs_update_order_meta( $order_id ) {
		$save_option_pdf = staggs_get_theme_option( 'sgg_order_save_pdf_attachment' );
		if ( ! $save_option_pdf ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( class_exists( 'Staggs_PDF' ) ) {
			$sgg_pdf = new Staggs_PDF();

			foreach ( $order->get_items() as $order_item ) {
				if ( $order_item->get_meta('_staggs_pdf_data') ) {
					// collect pdf data
					$pdf_data = $order_item->get_meta('_staggs_pdf_data');
					if ( staggs_get_theme_option( 'sgg_order_pdf_include_order_id' ) ) {
						$pdf_data['order_id'] = $order->get_id();
					}

					// Set pdf.
					$pdf_url = $sgg_pdf->generate_pdf_file_url( $order_item->get_product_id(), $pdf_data, $pdf_data['cart_item_key'] );
					$order_item->update_meta_data( '_staggs_pdf_url', $pdf_url );
				}
			}

			$order->save();
		}
	}

	/**
	 * Override base product price with calculated configurator price.
	 *
	 * @since    1.0.0
	 */
	public function staggs_set_price_for_cart_item( $cart ) {
		if ( is_admin() && !defined('DOING_AJAX')) {
			return;
		}

		if ( ! WC()->session->__isset( 'reload_checkout' ) ) {
			$dynamic_pricing = staggs_get_theme_option( 'sgg_cart_enable_dynamic_pricing' );

			foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
				if ( isset( $cart_item['product_price'] ) && isset( $cart_item['product_parts'] ) ) {
					if ( $dynamic_pricing ) {
						$revised_post_array = $cart_item['sgg_post_options'];
						$revised_post_array['quantity'] = $cart_item['quantity'];
						$configurator_totals = get_configurator_cart_totals( $revised_post_array, $revised_post_array['product_price'], $cart_item['product_id'] );

						$price = $configurator_totals['product_price'];
					} else {
						$price = $cart_item['product_price'];
					}

					$cart_item['data']->set_price( $price );

					if ( isset( $configurator_totals['product_tax'] ) && $configurator_totals['product_tax'] ) {
						$cart_item['staggs_line_tax'] = $configurator_totals['product_tax'];
					}

					if ( isset( $cart_item['product_weight'] ) ) {
						$cart_item_weight = (float) get_post_meta( $cart_item['product_id'], '_weight', true );
						$cart_item_weight += $cart_item['product_weight'];

						$cart_item['data']->set_weight( $cart_item_weight );
					}

					if ( isset( $cart_item['product_sku'] ) ) {
						$cart_item['data']->set_sku( $cart_item['product_sku'] );
					}
				}

				if ( isset( $cart_item['product_price'] ) && isset( $cart_item['sgg_addon_product'] ) ) {
					$price = $cart_item['product_price'];
					$cart_item['data']->set_price( $price );
				}
			}
		}
	}

	/**
	 * Calculate custom total tax percentage to apply to configurator item
	 *
	 * @since    1.8.0
	 */
	public function apply_custom_cart_item_tax_rate($item_tax_rates, $item, $cart) {
		$cart_item = $item->object;
		if ( ! isset( $cart_item['staggs_line_tax'] ) ) {
			return $item_tax_rates;
		}

		$tax = $cart_item['staggs_line_tax'];
		$price = $cart_item['data']->get_price();
		$new_rate = $tax / $price * 100;

		foreach ( $item_tax_rates as $key => $tax ) {
			$item_tax_rates[$key]['rate'] = $new_rate;
		}

		return $item_tax_rates;
	}

	/**
	 * Override base product thumbnail with generated configurator image.
	 *
	 * @since    1.0.0
	 */
	public function staggs_set_product_thumbnail( $product_image, $cart_item, $cart_item_key ) {
		if ( is_admin() ) {
			// Elementor fix.
			return $product_image;
		}

		if ( $cart_item && isset( $cart_item['product_image'] ) && '' !== $cart_item['product_image'] ) {
			$product_image = '<img class="lazy" src="' . sanitize_url( $cart_item['product_image'] ) . '" alt="Product Image" />';
		}

		return $product_image;
	}

	/**
	 * Delete images if selected.
	 *
	 * @since    1.3.1
	 */
	public function delete_images_on_thankyou_page( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		// Get an instance of the WC_Order object
		$order = wc_get_order( $order_id );

		// Loop through order items
		foreach ( $order->get_items() as $item_id => $item ) {
			// Get the product object
			$product = $item->get_product();

			// Image thumbnail settings.
			$store_images = staggs_get_post_meta( staggs_get_theme_id( $product->get_id() ), 'sgg_configurator_store_cart_image' );
			if ( $store_images ) {
				continue;
			}

			// No storage. Get image and delete it.
			if ( $image_url = $item->get_meta( '_product_thumbnail' ) ) {
				$image_path = str_replace( trailingslashit( get_site_url() ), ABSPATH, $image_url );
				staggs_delete_product_image( $image_path );
			}
		}
	}

	/**
	 * Show product image in checkout overview table.
	 *
	 * @since    1.5.0
	 */
	public function display_product_image_in_checkout( $product_name, $cart_item, $cart_item_key ){
		if ( ! is_checkout() ) {
			return $product_name;
		}

		// Check Staggs image setting for checkout.
		$display_checkout_images = staggs_get_theme_option( 'sgg_checkout_display_image' );
		if ( ! $display_checkout_images ) {
			return $product_name;
		}

		$image_size = $this->get_checkout_image_sizes();
		if ( isset( $cart_item['product_image'] ) ) {
			$image_html = '<div class="product-item-thumbnail"><img src="' . $cart_item['product_image'] . '" width="' . $image_size[0] . '" height="' . $image_size[1] . '" /></div>';
		} else {
			$thumbnail  = $cart_item['data']->get_image( $image_size );
			$image_html = '<div class="product-item-thumbnail">' . $thumbnail . '</div> ';
		}

		$product_name = $image_html . $product_name;

		return $product_name;
	}

	/**
	 * Show image in order details table.
	 *
	 * @since    1.5.0
	 */
	public function display_product_image_in_order_details( $item_name, $item, $is_visible ) {
		// Targeting view order pages only
		if ( ! is_wc_endpoint_url( 'view-order' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
			return $item_name;
		}

		// Check Staggs image setting for order items.
		$display_order_images = staggs_get_theme_option( 'sgg_view_order_display_image' );
		if ( ! $display_order_images ) {
			return $item_name;
		}

		$thumbnail = $this->get_order_item_product_thumbnail( $item );
		if ( '' == $thumbnail ) {
			$product = $item->get_product();
			$thumbnail = $product->get_image( $this->get_checkout_image_sizes() );
		}

		$item_name = '<div class="item-thumbnail">' . $thumbnail . '</div>' . $item_name;

		return $item_name;
	}

	/**
	 * Show images in woocommerce order email tables.
	 *
	 * @since    1.5.0
	 */
	public function display_product_image_in_woocommerce_mails( $args ) {
		if ( $args['show_image'] ) {
			// Show image already enabled.
			return $args;
		}

		// Check Staggs image setting for order items.
		$display_order_images = staggs_get_theme_option( 'sgg_confirmation_email_display_image' );
		if ( ! $display_order_images ) {
			return $args;
		}

		$args['show_image'] = true;
		$args['image_size'] = $this->get_checkout_image_sizes();

		return $args;
	}

	/**
	 * Show image in customer account order view table.
	 *
	 * @since    1.5.0
	 */
	public function replace_product_image_in_order_emails( $image_html, $item ) {
		// Check Staggs image setting for order items.
		$display_email_images = staggs_get_theme_option( 'sgg_confirmation_email_display_image' );
		if ( ! $display_email_images ) {
			return $image_html;
		}

		$thumbnail = $this->get_order_item_product_thumbnail( $item );
		if ( '' == $thumbnail ) {
			return $image_html;
		}

		return $thumbnail;
	}

	/**
	 * Get custom product thumbnail stored in order item meta.
	 *
	 * @since    1.5.0
	 */
	private function get_order_item_product_thumbnail( $order_item ) {
		$thumbnail_html = '';

		$item_meta = $order_item->get_meta_data();
		if ( is_array( $item_meta ) && count( $item_meta ) > 0 ) {
			$thumbnail_url = '';
			foreach ( $item_meta as $meta_obj ) {
				if ( '_product_thumbnail' === $meta_obj->key ) {
					$thumbnail_url = $meta_obj->value;
				}
			}

			$thumbnail_path = str_replace( trailingslashit( get_site_url() ), ABSPATH, $thumbnail_url );
			if ( $thumbnail_url && file_exists( $thumbnail_path ) ) {
				$image_size = $this->get_checkout_image_sizes();
				$thumbnail_html = '<img src="' . $thumbnail_url . '" width="' . $image_size[0] . '" height="' . $image_size[1] . '">';
			}
		}

		return $thumbnail_html;
	}

	/**
	 * Get image sizes for checkout tables.
	 *
	 * @since    1.5.0
	 */
	private function get_checkout_image_sizes() {
		$width = 70;
		$height = 70;

		if ( staggs_get_theme_option( 'sgg_checkout_image_width' ) ) {
			$width = staggs_get_theme_option( 'sgg_checkout_image_width' );
		}

		if ( staggs_get_theme_option( 'sgg_checkout_image_height' ) ) {
			$height = staggs_get_theme_option( 'sgg_checkout_image_height' );
		}

		return array( $width, $height );
	}
}
