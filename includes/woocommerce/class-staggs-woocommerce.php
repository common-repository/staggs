<?php

/**
 * The file that defines the WooCommerce plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://staggs.app
 * @since      1.3.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The WooCommerce plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.3.0
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_WooCommerce {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.3.0
	 */
	public function __construct() {
		// Constructor
	}

	/**
	 * Prepend 'From' to price html of configurable products.
	 *
	 * @since 1.3.0
	 * @param string     $price   Product price
	 * @param WC_Product $product WooCommerce Product Object.
	 */
	public function add_shop_price_prefix( $price_html, $product ) {
		if ( is_admin() ) {
			return $price_html;
		}

		if ( ! empty( $product->get_price() ) && product_is_configurable( $product->get_id() ) ) {
			$prefix = staggs_get_theme_option( 'sgg_product_prefix_label' );
			$format = staggs_get_theme_option( 'sgg_product_price_format' );

			$display_price = staggs_get_post_meta( staggs_get_theme_id( $product->get_id() ), 'sgg_configurator_display_pricing' );
			if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
				$display_price = 'hide';
			}

			if ( 'hide' === $display_price ) {
				return '';
			}

			$price_label = '';
			if ( '' !== $format ) {
				$price = $product->get_regular_price();
				$sale = $product->get_sale_price();

				$min = staggs_get_post_meta( $product->get_id(), 'sgg_configurator_min_price' );
				if ( ! is_numeric($min) && '' !== $min ) {
					$min = staggs_get_product_min_price($product->get_id(), $price, $min);
				}
				$max = staggs_get_post_meta( $product->get_id(), 'sgg_configurator_max_price' );
				if ( ! is_numeric( $max ) && '' !== $max ) {
					$max = staggs_get_product_max_price($product->get_id(), $price, $max);
				}

				if ( $price ) {
					$format = str_replace( '{price}', wc_price( $price ), $format );
				}
				if ( $sale ) {
					$format = str_replace( '{sale_price}', wc_price( $sale ), $format );
				}
				if ( $min ) {
					$format = str_replace( '{min_price}', wc_price( $min ), $format );
				}
				if ( $max ) {
					$format = str_replace( '{max_price}', wc_price( $max ), $format );
				}
				
				if ( ! str_contains( $format, '{' ) ) {
					$price_html = $format;
				} else {
					$price_html = $sale ? wc_price( $sale ) : wc_price( $price );
				}
			}

			if ( $prefix ) {
				$price_label = sgg__( $prefix );
			}

			return $price_label . ' ' . $price_html;
		}

		return $price_html;
	}

	/**
	 * Get table price for product.
	 *
	 * @since    1.11.0
	 */
	public function filter_woocommerce_configurator_product_price( $price, $product ) {
		if ( ! is_object( $product ) ) {
			return $price;
		}

		$theme_id = staggs_get_theme_id( $product->get_id() );
		if ( ! $theme_id ) {
			return $price;
		}
		
		if ( 'table' === staggs_get_post_meta( $theme_id, 'sgg_configurator_total_calculation' ) ) {
			$total_table_id = sanitize_key( staggs_get_post_meta( $theme_id, 'sgg_configurator_total_price_table' ) );

			if ( $total_table_id && function_exists( 'get_price_from_range_table' ) ) {
				$price = get_price_from_range_table( $total_table_id, 1 );
			}
		}

		return $price; 
	} 

	/**
	 * Block AJAX add to cart requests for configurable products.
	 *
	 * @since    1.3.0
	 */
	public function modify_configurable_product_add_to_cart_link( $permalink, $product ) {
		if ( product_is_inline_configurator( $product->get_id() ) ) {
			return get_permalink( $product->get_id() );
		}
		return $permalink;
	}

	/**
	 * Block AJAX add to cart requests for configurable products.
	 *
	 * @since    1.3.0
	 */
	public function staggs_change_add_to_cart_text($cart_text, $product) {
		if ( product_is_inline_configurator( $product->get_id() ) ) {
			if ( staggs_get_theme_option('sgg_product_loop_cart_label') ) {
				return staggs_get_theme_option('sgg_product_loop_cart_label');
			}
		}
		return $cart_text;
	}

	/**
	 * Remove AJAX add to cart support for configurable products.
	 *
	 * @since    1.3.0
	 */
	public function modify_configurable_product_supports( $support, $feature, $product ) {
		if ( 'ajax_add_to_cart' === $feature && product_is_inline_configurator( $product->get_id() ) ) {
			return false;
		}
		return $support;
	}

	/**
	 * Register the configurator stylesheets for the public-facing side of the site.
	 *
	 * @since    1.3.0
	 */
	public function modify_configurable_product_hooks() {
		global $sgg_is_shortcode;

		if ( ! product_is_configurable( get_the_ID() ) && 'sgg_product' !== get_post_type() ) {
			return;
		}

		$theme_id = staggs_get_theme_id();
		$template = staggs_get_configurator_page_template( $theme_id );
		$view_layout = staggs_get_configurator_view_layout( $theme_id );
		
		// Don't override default WooCommerce template.
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_template_override' ) && ! $sgg_is_shortcode ) {
			return;
		}

		if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_floating_notice' ) ) {
			/**
			 * Notices
			 */	
			add_action( 'woocommerce_before_single_product', 'staggs_output_message_content_wrapper', 5 );
			add_action( 'woocommerce_before_single_product', 'staggs_output_message_content_wrapper_close', 15 );
		}

		/**
		 * Configurator gallery thumbnails position.
		 */

		if ( 'classic' === $view_layout && 'under' === staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_position' ) ) {	
			add_action( 'staggs_after_single_product_gallery', 'staggs_output_preview_gallery_thumbnails', 10 );
		} else {
			add_action( 'staggs_single_product_gallery', 'staggs_output_preview_gallery_thumbnails', 25 );
		}

		if ( ( ! $theme_id || 'none' === $template ) && ! $sgg_is_shortcode ) {

			/**
			 * Configurator clean template. Only adds options.
			 */
			
			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_wrapper', 10 );
			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_form', 20 );
			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_single_product_options', 30 );
			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_form_close', 40 );
			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_wrapper_close', 50 );

			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_single_product_options_totals_wrapper', 80 );
			add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_product_totals_list', 90 );

			add_action( 'woocommerce_after_add_to_cart_form', 'staggs_output_options_credit', 20 );
			add_action( 'woocommerce_after_add_to_cart_form', 'staggs_single_product_options_totals_wrapper_close', 30 );

		} else if ( 'popup' === $view_layout ) {

			/**
			 * Configurator popup view
			 */

			$type = staggs_get_post_meta( $theme_id, 'sgg_configurator_popup_type' );
			if ( ! $type ) {
				$type = 'vertical';
			}

			add_action( 'woocommerce_before_template_part', array( $this, 'capture_single_add_to_cart_form_html' ) );
			add_action( 'woocommerce_after_template_part', array( $this, 'display_configure_product_button_html' ) );

			add_action( 'woocommerce_after_single_product', array( $this, 'staggs_display_product_configurator_popup' ), 10 );

			if ( 'vertical' === $type ) {

				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_product_totals_list', 20 );

				if ( 'gallery' === staggs_get_post_meta( $theme_id, 'sgg_configurator_usp_location' ) ) {
					add_action( 'staggs_after_single_product_gallery', 'staggs_output_options_usps', 20 );
				} else {
					add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_options_usps', 10 );
				}

			} else {

				if ( 'gallery' === staggs_get_post_meta( $theme_id, 'sgg_configurator_usp_location' ) ) {
					add_action( 'staggs_after_single_product_gallery', 'staggs_output_options_usps', 20 );
				}

				remove_action( 'staggs_before_single_product_options', 'staggs_output_options_form', 20 );
				remove_action( 'staggs_single_product_options', 'staggs_output_single_product_options', 10 );
				remove_action( 'staggs_single_product_options', 'staggs_output_options_form_close', 20 );

				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_options_wrapper', 10 );
				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_options_form', 20 );
				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_popup_options_bar', 30 );
				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_options_form_close', 40 );
				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_options_wrapper_close', 50 );
				add_action( 'staggs_output_popup_bottom_bar_left', 'staggs_output_popup_options_bar_nav', 60 );
				
				add_action( 'staggs_output_popup_bottom_bar_right', 'staggs_output_product_totals_list', 10 );

				remove_action( 'staggs_after_single_option_group', 'staggs_option_group_description_panel', 10 );
				add_action( 'staggs_after_single_product_content', 'staggs_output_description_panels', 10 );

			}

			add_action( 'staggs_output_popup_bottom_bar_right', 'staggs_output_product_main_button', 10 );

			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'staggs_single_product_configurator_button' ), 10 );

		} elseif ( 'default' === $template ) {

			/**
			 * Configurator default WooCommerce template
			 */

			// Gallery

			add_filter( 'woocommerce_single_product_image_thumbnail_html', '__return_false' );

			add_action( 'woocommerce_product_thumbnails', 'staggs_output_gallery_section', 15 );
			add_action( 'woocommerce_product_thumbnails', 'staggs_output_company_logo', 20 );
			add_action( 'woocommerce_product_thumbnails', 'staggs_output_preview_gallery_wrapper', 30 );
			add_action( 'woocommerce_product_thumbnails', 'staggs_output_preview_gallery', 40 );
			add_action( 'woocommerce_product_thumbnails', 'staggs_output_preview_gallery_nav', 50 );
			add_action( 'woocommerce_product_thumbnails', 'staggs_output_preview_gallery_wrapper_close', 60 );
			add_action( 'woocommerce_product_thumbnails', 'staggs_output_gallery_section_close', 65 );

			if ( 'under' === staggs_get_post_meta( $theme_id, 'sgg_configurator_thumbnails_position' ) ) {	
				add_action( 'woocommerce_product_thumbnails', 'staggs_output_preview_gallery_thumbnails', 70 );
			} else {
				add_action( 'woocommerce_product_thumbnails', 'staggs_output_preview_gallery_thumbnails', 45 );
			}

			// Options

			if ( ! $sgg_is_shortcode ) {
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_wrapper', 10 );
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_form', 20 );
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_single_product_options', 30 );
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_form_close', 40 );
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_wrapper_close', 50 );
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_single_product_options_totals_wrapper', 60 );
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_product_totals_list', 70 );
	
				add_action( 'woocommerce_after_add_to_cart_form', 'staggs_output_options_credit', 20 );
				add_action( 'woocommerce_after_add_to_cart_form', 'staggs_single_product_options_totals_wrapper_close', 30 );
			}

			if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) {
				add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_configurator_step_progress', 5 );
				add_action( 'staggs_before_single_product_options_totals', 'staggs_output_steps_navigation_buttons', 10 );

				add_action( 'woocommerce_after_add_to_cart_form', 'staggs_output_product_secondary_buttons', 90 );
			}

		} else {

			/**
			 * Configurator Staggs product template
			 */

			/**
			 * Remove default gallery.
			 */
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

			/**
			 * Display usps in configured location.
			 */
			if ( 'gallery' === staggs_get_post_meta( $theme_id, 'sgg_configurator_usp_location' ) ) {

				add_action( 'staggs_after_single_product_gallery', 'staggs_output_options_usps', 20 );

			} else {

				if ( 'floating' === $view_layout ) {

					add_action( 'staggs_after_single_product_options_totals', 'staggs_output_options_usps', 10 );

				} else if ( 'steps' === $view_layout ) {

					add_action( 'staggs_after_single_add_to_cart', 'staggs_output_options_usps', 10 ); // Mobile
					add_action( 'staggs_before_bottom_bar_totals', 'staggs_output_options_usps', 10 ); // Desktop

				} else if ( 'splitter' === $view_layout ) {

					add_action( 'staggs_after_single_add_to_cart', 'staggs_output_options_usps', 10 ); // Mobile
					add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_options_usps', 25 ); // Desktop

				} else {

					add_action( 'staggs_single_product_options_totals', 'staggs_output_options_usps', 30 );

				}
			}

			/**
			 * Layout
			 */

			if ( 'steps' !== $view_layout ) {
				add_action( 'staggs_before_single_product_gallery', 'staggs_output_company_logo', 10 );

				if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) {
					add_action( 'staggs_before_single_product_content', 'staggs_output_configurator_step_progress', 30 );
					add_action( 'staggs_before_single_product_options_totals', 'staggs_output_steps_navigation_buttons', 10 );
				}
			} else {
				add_action( 'staggs_after_single_add_to_cart', 'staggs_output_product_secondary_buttons', 20 );
			}

			/**
			 * Optionally display WooCommerce product templates.
			 */

			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_price' ) 
					|| 0 == staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_price' ) ) {
					add_action( 'staggs_single_product_summary', 'woocommerce_template_single_price', 20 );
				}

				if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_meta' ) 
					|| 0 == staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_meta' ) ) {
					add_action( 'staggs_single_product_summary', 'woocommerce_template_single_meta', 35 );
				}
			}

			if ( 'classic' !== $view_layout ) {
				/**
				 * Remove upsells and related products.
				 */	
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			}

			if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_tabs' ) ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			}

			if ( ! staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_tabs_override' ) 
				|| 0 == staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_tabs_override' ) ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			}
		}

		/**
		 * Configurator summary position
		 */

		if ( $theme_id ) {
			if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_display_summary' ) ) {
				$location = staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_location' );

				if ( 'before_totals' == $location ) {
					if ( 'none' === $template || 'default' === $template ) {
						add_action( 'woocommerce_before_add_to_cart_form', 'staggs_output_options_summary_widget', 50 );
					} else {
						add_action( 'staggs_single_product_options', 'staggs_output_options_summary_widget', 30 );
					}
				} else if ( 'after_totals' == $location ) {
					if ( 'none' === $template || 'default' === $template ) {
						add_action( 'woocommerce_after_add_to_cart_form', 'staggs_output_options_summary_widget', 30 );
					} else {
						add_action( 'staggs_after_single_product_options_totals', 'staggs_output_options_summary_widget', 50 );
					}
				}
			}
		}
	}

	/**
	 * Add configure button when popup
	 *
	 * @since    1.3.5
	 */
	public function staggs_single_product_configurator_button() {
		if ( ! product_is_configurable( get_the_ID() ) ) {
			return;
		}

		// Prevent duplicate echo.
		global $is_popup;
		if ( ! $is_popup ) {
			$button_text = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_step_popup_button_text' );
			if ( ! $button_text ) {
				$button_text = __( 'Configure', 'staggs' );
			}

			echo wp_kses_normalize_entities(  apply_filters( 
				'staggs_configurator_popup_button_html',
				'<a href="#" class="button staggs-configure-product-button">' . $button_text . '</a>'
			) );
		}
	}

	/**
	 * Capture add to cart form html when setting active.
	 *
	 * @since    1.10.0
	 */
	public function capture_single_add_to_cart_form_html( $template_name ) {
		if ( ! product_is_configurable( get_the_ID() ) ) {
			return;
		}

		$replace_form = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_popup_replace_cart_form' );
		if ( ! $replace_form ) {
			return;
		}

		global $is_popup;
		if ( ! $is_popup && 'single-product/add-to-cart/simple.php' === $template_name ) {
			ob_start();
		}
	}
	
	/**
	 * Replace add to cart form when setting active.
	 *
	 * @since    1.10.0
	 */
	public function display_configure_product_button_html( $template_name ) {
		if ( ! product_is_configurable( get_the_ID() ) ) {
			return;
		}
		
		$theme_id = staggs_get_theme_id();
		$replace_form = staggs_get_post_meta( $theme_id, 'sgg_configurator_popup_replace_cart_form' );
		if ( ! $replace_form ) {
			return;
		}

		global $is_popup;
		if ( ! $is_popup && 'single-product/add-to-cart/simple.php' === $template_name ) {
			$content = ob_get_clean();
			// Prevent duplicate echo.
			$button_text = staggs_get_post_meta( $theme_id, 'sgg_step_popup_button_text' );
			if ( ! $button_text ) {
				$button_text = __( 'Configure', 'staggs' );
			}
			
			echo wp_kses_normalize_entities(  apply_filters( 
				'staggs_configurator_popup_button_html',
				'<a href="#" class="button staggs-configure-product-button">' . $button_text . '</a>'
			) );
		}
	}

	/**
	 * Include popup template when activated.
	 *
	 * @since    1.3.5
	 */
	public function staggs_display_product_configurator_popup() {
		require_once STAGGS_BASE . '/public/templates/popup/template-popup.php';
	}

	/**
	 * Set min allowed product quantity.
	 *
	 * @since    1.3.2
	 */
	public function staggs_set_min_product_quantity( $min ) {
		if ( product_is_configurable( get_the_ID() ) ) {
			if ( ! product_is_inline_configurator( get_the_ID() ) ) {
				global $is_popup;
				if ( $is_popup ) {
					if ( ! staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_display_qty_input' ) ) {
						return 1;
					}
				} 
			} else {
				if ( ! staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_display_qty_input' ) ) {
					return 1;
				}
			}
		}

		return $min;
	}

	/**
	 * Set max allowed product quantity.
	 *
	 * @since    1.3.2
	 */
	public function staggs_set_max_product_quantity( $max ) {
		if ( product_is_configurable( get_the_ID() ) ) {
			if ( ! product_is_inline_configurator( get_the_ID() ) ) {
				global $is_popup;
				if ( $is_popup ) {
					if ( ! staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_display_qty_input' ) ) {
						return 1;
					}
				}
			} else {
				if ( ! staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_display_qty_input' ) ) {
					return 1;
				}
			}
		}

		return $max;
	}

	/**
	 * Add hidden cart meta.
	 *
	 * @since    1.3.2.
	 */
	public function staggs_before_add_to_cart() {
		?>
		<input type="hidden" name="product_id" value="<?php echo sanitize_key( get_the_ID() ); ?>">
		<?php
	}

	/**
	 * Modify the add to cart button text.
	 *
	 * @since    1.3.0
	 */
	public function staggs_single_add_to_cart_text( $text ) {
		global $is_popup;
		if ( ! $is_popup && ! product_is_inline_configurator( get_the_ID() ) ) {
			return $text;
		}

		$button_text = sanitize_text_field( staggs_get_post_meta( staggs_get_theme_id(), 'sgg_step_add_to_cart_text' ) );
		if ( '' !== $button_text ) {
			$text = $button_text;
		}
		
		return $text;
	}

	/**
	 * Register the configurator stylesheets for the public-facing side of the site.
	 *
	 * @since    1.3.0
	 */
	public function clear_default_product_tabs_array( $tabs ) {
		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			// Block theme. Don't change tabs display.
			return $tabs;
		}

		if ( ! product_is_inline_configurator( get_the_ID() ) ) {
			return $tabs;
		}

		$theme_id = staggs_get_theme_id();
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_template_override' ) ) {
			return $tabs;
		}
		
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_tabs_override' ) ) {
			return $tabs;
		}
		
		$view_layout = staggs_get_configurator_view_layout( $theme_id );
		if ( 'classic' === $view_layout || 'classic_top' === $view_layout ) {
			return $tabs; // Classic view, no need to modify tabs output.
		}

		set_transient( 'staggs_product_tabs', $tabs );

		return array(); // Clear tabs.
	}

	/**
	 * Register the configurator stylesheets for the public-facing side of the site.
	 *
	 * @since    1.3.0
	 */
	public function staggs_product_tabs_output() {
		if ( ! product_is_inline_configurator( get_the_ID() ) ) {
			return;
		}

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			// Block theme. Don't change tabs display.
			return;
		}

		global $post;
		if ( ! function_exists( 'woocommerce_default_product_tabs' ) ) {
			return;
		}

		$theme_id = staggs_get_theme_id();
		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_tabs' ) ) {
			return; // Tabs disabled.
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_tabs_override' ) ) {
			return; // Custom tabs disabled.
		}
		
		$view_layout = staggs_get_configurator_view_layout( $theme_id );
		if ( 'classic' === $view_layout ) {
			return; // Classic view, no need to modify tabs output.
		}

		$product_tabs = get_transient( 'staggs_product_tabs' );
		if ( $product_tabs && is_array( $product_tabs ) ) :
			?>
			<div class="option-group option-group-tabs">
				<ul class="tabs-content" role="tablist">
					<?php 
					foreach ( $product_tabs as $key => $product_tab ) : ?>
						<li class="fieldset closed" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
							<strong class="fieldset-legend">
								<?php echo wp_kses_normalize_entities(  apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
								<span class="icon">
									<span class="icon-plus">
										<?php
										echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_plus_icon', 'plus' ) );
										?>
									</span>
									<div class="icon-minus">
										<?php
										echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_minus_icon', 'minus' ) );
										?>
									</div>
								</span>
							</strong>
							<span class="fieldset-description" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
								<?php
								if ( isset( $product_tab['callback'] ) ) {
									call_user_func( $product_tab['callback'], $key, $product_tab );
								}
								?>
							</span>
						</li>
						<?php
					endforeach;
					?>
				</ul>
			</div>
			<?php
		endif;
	}
}
