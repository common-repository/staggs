<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Staggs
 * @subpackage Staggs/public
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'wp_head', array( $this, 'add_scripts_to_head' ), 90 );
	}

	/**
	 * Register the configurator stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * Register plugin styles
		 */

		wp_register_style( $this->plugin_name . '_swiper', plugin_dir_url( __FILE__ ) . 'css/staggs-swiper.min.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '_jquery_ui', plugin_dir_url( __FILE__ ) . 'css/staggs-jquery-ui.min.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '_lightbox', plugin_dir_url( __FILE__ ) . 'css/staggs-lightbox.min.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '_public', plugin_dir_url( __FILE__ ) . 'css/staggs-public.min.css', array(), $this->version, 'all' );

		wp_register_script( $this->plugin_name . '_swiper', plugin_dir_url( __FILE__ ) . 'js/staggs-swiper.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_name . '_jquery_ui', plugin_dir_url( __FILE__ ) . 'js/staggs-jquery-ui.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_name . '_jquery_touch', plugin_dir_url( __FILE__ ) . 'js/staggs-jquery-touch.min.js', array( 'jquery-ui-slider' ), $this->version, true );
		wp_register_script( $this->plugin_name . '_lightbox', plugin_dir_url( __FILE__ ) . 'js/staggs-lightbox.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_name . '_canvas', plugin_dir_url( __FILE__ ) . 'js/staggs-canvas.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_name . '_repeater', plugin_dir_url( __FILE__ ) . 'js/staggs-repeater.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_name . '_public', plugin_dir_url( __FILE__ ) . 'js/staggs-public.min.js', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Only load styles and scripts directly when configurable product template is active.
	 *
	 * @since    1.7.0
	 */
	public function add_scripts_to_head() {
		if ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_product() && product_is_configurable( get_the_ID() ) ) || is_singular( 'sgg_product' ) ) {
			$this->output_scripts();
		}
	}

	/**
	 * Actually output registered scripts to frontend.
	 *
	 * @since    1.4.4.
	 */
	public function output_scripts() {
		
		$theme_id = staggs_get_theme_id();
		$template = staggs_get_configurator_page_template( $theme_id );
		if ( $theme_id && 'none' === $template ) {
			// don't load in swiper
		} else {
			wp_enqueue_style( $this->plugin_name . '_swiper' );
		}

		wp_enqueue_style( $this->plugin_name . '_lightbox' );
		wp_enqueue_style( $this->plugin_name . '_jquery_ui' );
		wp_enqueue_style( $this->plugin_name . '_public' );
		wp_add_inline_style( $this->plugin_name . '_public', $this->enqueue_inline_styles() );

		if ( $theme_id && 'none' === $template ) {
			// don't load in swiper
		} else {
			wp_enqueue_script( $this->plugin_name . '_swiper' );
			wp_enqueue_script( $this->plugin_name . '_canvas' );
		}

		wp_enqueue_script( $this->plugin_name . '_jquery_ui' );
		wp_enqueue_script( $this->plugin_name . '_jquery_touch' );
		wp_enqueue_script( $this->plugin_name . '_lightbox' );
		wp_enqueue_script( $this->plugin_name . '_repeater' );
		wp_enqueue_script( $this->plugin_name . '_public' );
		wp_add_inline_script( $this->plugin_name . '_public', $this->enqueue_inline_scripts(), 'before' );

		$this->enqueue_font_scripts();

		do_action( 'staggs_output_public_scripts' );
	}

	/**
	 * Add body class for configurator page.
	 *
	 * @since    1.3.1
	 */
	public function set_body_configurator_class( $classes ) {
		if ( 'sgg_product' === get_post_type() || 
			( function_exists( 'is_product' ) && is_product() && product_is_inline_configurator( get_the_ID() ) && staggs_get_theme_id( get_the_ID() ) ) ) {
			$classes[] = 'staggs-product-configurator-page';
		}
		return $classes;
	}

	/**
	 * Register shortcodes for outputting configurator on custom page.
	 *
	 * @since    1.3.7
	 */
	public function register_shortcodes() {
		add_shortcode( 'staggs_configurator', array( $this, 'output_product_configurator_template' ) );
		add_shortcode( 'staggs_configurator_gallery', array( $this, 'output_product_configurator_gallery' ) );
		add_shortcode( 'staggs_configurator_form', array( $this, 'output_product_configurator_form_options' ) );
		add_shortcode( 'staggs_configurator_totals', array( $this, 'output_product_configurator_form_totals' ) );
		add_shortcode( 'staggs_configurator_popup_button', array( $this, 'output_product_configurator_popup_button' ) );
		add_shortcode( 'staggs_configurator_summary', array( $this, 'output_product_configurator_summary_widget' ) );
	}

	/**
	 * Output product configurator template.
	 *
	 * @since    1.3.7
	 */
	public function output_product_configurator_template( $atts ) {
		// Validate shortcode.
		$check = $this->can_use_shortcode( $atts );
		if ( ! $check['valid'] ) {
			return $check['note'];
		}

		// Start collecting output.
		ob_start();
		if ( isset( $atts['product_id'] ) ) {

			// Keep reference to original post ID.
			global $original_post_id;
			$original_post_id = get_the_ID();

			$post_type = get_post_type( $atts['product_id'] );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post_type = 'product'; // Force product types when WooCommerce is active.
			}

			$product_query = new WP_Query( array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post__in'    => array( $atts['product_id'] )
			) );

			if ( $product_query->have_posts() ) {
				while ( $product_query->have_posts() ) {
					$product_query->the_post();

					if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
						global $product;
						$product = wc_get_product( get_the_ID() );
					}

					do_action( 'staggs_modify_wc_hooks' );

					global $sanitized_steps;
					$sanitized_steps = Staggs_Formatter::get_formatted_step_content( get_the_ID() );
	
					$this->output_scripts();

					$template = staggs_get_configurator_page_template( staggs_get_theme_id() );
					$view_layout = staggs_get_configurator_view_layout( staggs_get_theme_id() );
	
					if ( 'staggs' === $template ) {

						if ( file_exists( STAGGS_BASE . '/public/templates/inline/template-' . $view_layout . '.php' ) ) {
						
							include STAGGS_BASE . '/public/templates/inline/template-' . $view_layout . '.php';
						
						} else if ( file_exists( STAGGS_BASE . '/public/templates/popup/template-' . $view_layout . '.php' ) ) {
				
							include STAGGS_BASE . '/public/templates/popup/template-' . $view_layout . '.php';
						
						} else if ( file_exists( STAGGS_BASE . '/pro/public/templates/inline/template-' . $view_layout . '.php' ) ) {
						
							include STAGGS_BASE . '/pro/public/templates/inline/template-' . $view_layout . '.php';
						
						} else {
						
							include STAGGS_BASE . '/public/templates/inline/template-classic.php';
		
						}

					} else if ( 'woocommerce' === $template && class_exists( 'WooCommerce' ) ) {

						// No Staggs template match.
						if ( file_exists( $dir . WC()->template_path() . 'single-product.php' ) ) {
							// Check if template is overridden in active theme.
							include $dir . WC()->template_path() . 'single-product.php';
						}
		
					} else {
		
						do_action( 'staggs_before_configurator_form_shortcode' );
							
						include STAGGS_BASE . '/public/templates/shared/form.php';
		
						do_action( 'staggs_after_configurator_form_shortcode' );
		
						do_action( 'staggs_before_configurator_totals_shortcode' );
							
						include STAGGS_BASE . '/public/templates/shared/totals.php';
		
						do_action( 'staggs_after_configurator_totals_shortcode' );
		
					}
				}
			}
			
			wp_reset_postdata();
			wp_reset_postdata();

		} else {

			do_action( 'staggs_modify_wc_hooks' );

			global $sanitized_steps;
			$sanitized_steps = Staggs_Formatter::get_formatted_step_content( get_the_ID() );

			$this->output_scripts();

			$template = staggs_get_configurator_page_template( staggs_get_theme_id() );
			$view_layout = staggs_get_configurator_view_layout( staggs_get_theme_id() );
	
			if ( 'staggs' === $template ) {

				if ( file_exists( STAGGS_BASE . '/public/templates/inline/template-' . $view_layout . '.php' ) ) {
			
					include STAGGS_BASE . '/public/templates/inline/template-' . $view_layout . '.php';
				
				} else if ( file_exists( STAGGS_BASE . '/public/templates/popup/template-' . $view_layout . '.php' ) ) {
				
					include STAGGS_BASE . '/public/templates/popup/template-' . $view_layout . '.php';
				
				} else if ( file_exists( STAGGS_BASE . '/pro/public/templates/inline/template-' . $view_layout . '.php' ) ) {
				
					include STAGGS_BASE . '/pro/public/templates/inline/template-' . $view_layout . '.php';
				
				} else {
				
					include STAGGS_BASE . '/public/templates/inline/template-classic.php';

				}

			} else if ( 'woocommerce' === $template && class_exists( 'WooCommerce' ) ) {

				// No Staggs template match.
				if ( file_exists( $dir . WC()->template_path() . 'single-product.php' ) ) {
					// Check if template is overridden in active theme.
					include $dir . WC()->template_path() . 'single-product.php';
				}

			} else {

				do_action( 'staggs_before_configurator_form_shortcode' );
					
				include STAGGS_BASE . '/public/templates/shared/form.php';

				do_action( 'staggs_after_configurator_form_shortcode' );

				do_action( 'staggs_before_configurator_totals_shortcode' );
					
				include STAGGS_BASE . '/public/templates/shared/totals.php';

				do_action( 'staggs_after_configurator_totals_shortcode' );

			}
		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Output product configurator gallery view.
	 *
	 * @since    1.3.7
	 */
	public function output_product_configurator_gallery( $atts ) {
		// Validate shortcode.
		$check = $this->can_use_shortcode( $atts );
		if ( ! $check['valid'] ) {
			return $check['note'];
		}

		global $inline_style;
		$inline_style = '';
		if ( isset( $atts['width'] ) && '' !== $atts['width'] ) {
			$inline_style .= 'width: ' . $atts['width'] . ';';
		}
		if ( isset( $atts['height'] ) && '' !== $atts['height'] ) {
			$inline_style .= 'height: ' . $atts['height'] . ';';
		}

		ob_start();

		if ( isset( $atts['product_id'] ) ) {

			$post_type = get_post_type( $atts['product_id'] );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post_type = 'product'; // Force product types when WooCommerce is active.
			}

			$product_query = new WP_Query( array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post__in'    => array( $atts['product_id'] )
			) );

			if ( $product_query->have_posts() ) {
				while ( $product_query->have_posts() ) {
					$product_query->the_post();

					if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
						global $product;
						$product = wc_get_product( get_the_ID() );
					}

					do_action( 'staggs_modify_wc_hooks' );

					$this->output_scripts();

					do_action( 'staggs_before_configurator_gallery_shortcode' );
					
					include STAGGS_BASE . '/public/templates/shared/gallery.php';

					do_action( 'staggs_after_configurator_gallery_shortcode' );

				}
			}

			wp_reset_postdata();
			wp_reset_postdata();

		} else {
			
			do_action( 'staggs_modify_wc_hooks' );

			$this->output_scripts();

			do_action( 'staggs_before_configurator_gallery_shortcode' );
					
			include STAGGS_BASE . '/public/templates/shared/gallery.php';

			do_action( 'staggs_after_configurator_gallery_shortcode' );

		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Output product configurator form options.
	 *
	 * @since    1.3.7
	 */
	public function output_product_configurator_form_options( $atts ) {
		// Validate shortcode.
		$check = $this->can_use_shortcode( $atts );
		if ( ! $check['valid'] ) {
			return $check['note'];
		}

		ob_start();

		if ( isset( $atts['product_id'] ) ) {
			
			// Keep reference to original post ID.
			global $original_post_id;
			$original_post_id = get_the_ID();

			$post_type = get_post_type( $atts['product_id'] );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post_type = 'product'; // Force product types when WooCommerce is active.
			}

			$product_query = new WP_Query( array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post__in'    => array( $atts['product_id'] )
			) );

			if ( $product_query->have_posts() ) {
				while ( $product_query->have_posts() ) {
					$product_query->the_post();

					if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
						global $product;
						$product = wc_get_product( get_the_ID() );
					}

					do_action( 'staggs_modify_wc_hooks' );

					$this->output_scripts();

					do_action( 'staggs_before_configurator_form_shortcode' );
					
					include STAGGS_BASE . '/public/templates/shared/form.php';

					do_action( 'staggs_after_configurator_form_shortcode' );

				}
			}

			wp_reset_postdata();
			wp_reset_postdata();
			
		} else {

			do_action( 'staggs_modify_wc_hooks' );

			$this->output_scripts();

			do_action( 'staggs_before_configurator_form_shortcode' );
					
			include STAGGS_BASE . '/public/templates/shared/form.php';

			do_action( 'staggs_after_configurator_form_shortcode' );

		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Output product configurator form totals.
	 *
	 * @since    1.3.7
	 */
	public function output_product_configurator_form_totals( $atts ) {
		// Validate shortcode.
		$check = $this->can_use_shortcode( $atts );
		if ( ! $check['valid'] ) {
			return $check['note'];
		}

		ob_start();

		if ( isset( $atts['product_id'] ) ) {

			$post_type = get_post_type( $atts['product_id'] );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post_type = 'product'; // Force product types when WooCommerce is active.
			}

			$product_query = new WP_Query( array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post__in'    => array( $atts['product_id'] )
			) );

			if ( $product_query->have_posts() ) {
				while ( $product_query->have_posts() ) {
					$product_query->the_post();
	
					if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
						global $product;
						$product = wc_get_product( get_the_ID() );
					}

					do_action( 'staggs_modify_wc_hooks' );

					$this->output_scripts();

					do_action( 'staggs_before_configurator_totals_shortcode' );
					
					include STAGGS_BASE . '/public/templates/shared/totals.php';

					do_action( 'staggs_after_configurator_totals_shortcode' );

				}
			}
	
			wp_reset_postdata();
			wp_reset_postdata();

		} else {

			do_action( 'staggs_modify_wc_hooks' );

			$this->output_scripts();

			do_action( 'staggs_before_configurator_totals_shortcode' );
					
			include STAGGS_BASE . '/public/templates/shared/totals.php';

			do_action( 'staggs_after_configurator_totals_shortcode' );

		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Output product configurator popup button.
	 *
	 * @since    1.3.7
	 */
	public function output_product_configurator_popup_button( $atts ) {
		// Validate shortcode.
		$check = $this->can_use_shortcode( $atts );
		if ( ! $check['valid'] ) {
			return $check['note'];
		}

		$button = '';
		if ( isset( $atts['product_id'] ) ) {
			$post_type = get_post_type( $atts['product_id'] );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post_type = 'product'; // Force product types when WooCommerce is active.
			}

			$product_query = new WP_Query( array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post__in'    => array( $atts['product_id'] )
			) );

			if ( $product_query->have_posts() ) {
				while ( $product_query->have_posts() ) {
					$product_query->the_post();

					if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
						global $product;
						$product = wc_get_product( get_the_ID() );
					}

					$view_layout = staggs_get_configurator_view_layout( staggs_get_theme_id() );
					if ( 'popup' === $view_layout ) {
						$button_text = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_step_popup_button_text' );
						if ( ! $button_text ) {
							$button_text = __( 'Configure', 'staggs' );
						}

						echo wp_kses_normalize_entities(  apply_filters( 
							'staggs_configurator_popup_button_html',
							'<a href="#" class="button staggs-configure-product-button">' . $button_text . '</a>'
						) );
					} else {
						$button = esc_attr__( 'Note: the popup button can only be used in combination with popup template', 'staggs' );
					}
				}
			}

			wp_reset_postdata();
		} else {
			$view_layout = staggs_get_configurator_view_layout( staggs_get_theme_id() );
			if ( 'popup' === $view_layout ) {
				$button_text = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_step_popup_button_text' );
				if ( ! $button_text ) {
					$button_text = __( 'Configure', 'staggs' );
				}
				
				echo wp_kses_normalize_entities(  apply_filters( 
					'staggs_configurator_popup_button_html',
					'<a href="#" class="button staggs-configure-product-button">' . $button_text . '</a>'
				) );
			} else {
				$button = esc_attr__( 'Note: the popup button can only be used in combination with popup template', 'staggs' );
			}
		}

		return $button;
	}

	/**
	 * Output product configurator summary widget.
	 *
	 * @since    1.5.3
	 */
	public function output_product_configurator_summary_widget( $atts ) {
		// Validate shortcode.
		$check = $this->can_use_shortcode( $atts );
		if ( ! $check['valid'] ) {
			return $check['note'];
		}

		$summary = '';
		if ( isset( $atts['product_id'] ) ) {
			$post_type = get_post_type( $atts['product_id'] );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$post_type = 'product'; // Force product types when WooCommerce is active.
			}

			$product_query = new WP_Query( array(
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post__in'    => array( $atts['product_id'] )
			) );

			if ( $product_query->have_posts() ) {
				while ( $product_query->have_posts() ) {
					$product_query->the_post();

					global $product;
					$product = wc_get_product( get_the_ID() );
			
					if ( staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_display_summary' ) ) {
						$location = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_summary_location' );

						if ( 'shortcode' == $location ) {
							ob_start();
							staggs_output_options_summary_widget();
							$summary = ob_get_contents();
							ob_end_clean();
						}
					}
				}
			}

			wp_reset_postdata();
			wp_reset_postdata();
		} else {
			if ( staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_display_summary' ) ) {
				$location = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_summary_location' );

				if ( 'shortcode' == $location ) {
					ob_start();
					staggs_output_options_summary_widget();
					$summary = ob_get_contents();
					ob_end_clean();
				}
			}
		}

		return $summary;
	}

	/**
	 * Register the configurator inline JavaScript for the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_inline_styles() {
		global $sgg_is_shortcode, $sgg_shortcode_id;

		if ( $sgg_is_shortcode ) {
			$theme_id = staggs_get_theme_id( $sgg_shortcode_id );
		} else {
			$theme_id = staggs_get_theme_id();
		}

		$style  = '';
		$layout = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_layout' ) );

		if ( 'right' === $layout ) {
			$style .= ' section.staggs-product-view { order: 1; }';
			$style .= ' div.product-view-wrapper { left: auto; right: 0; }';
			$style .= ' div.staggs-product-options .option-group-panel { right: auto; left: -500px; transition: left .3s ease-in-out; }';
			$style .= ' div.staggs-product-options .option-group-panel.shown { right: auto; left: 0; transition: left .3s ease-in-out; }';
		} else {
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons { order: 1; }';
		}

		$select_icon = staggs_get_icon( 'sgg_select_arrow', 'chevron-down', true );
		$left_icon   = staggs_get_icon( 'sgg_slider_arrow_left', 'chevron-left', true );
		$right_icon  = staggs_get_icon( 'sgg_slider_arrow_right', 'chevron-right', true );

		$style .= ' div.staggs-product-options .select .ui-selectmenu-button .ui-selectmenu-icon,';
		$style .= ' div.staggs-product-options .select:after { background-image: url(' . $select_icon . '); }';

		$style .= ' div.swiper.staggs-view-gallery .swiper-button-prev { background-image: url(' . $left_icon . '); }';
		$style .= ' div.swiper.staggs-view-gallery .swiper-button-next { background-image: url(' . $right_icon . '); }';

		$style .= ' div.ui-widget.ui-datepicker .ui-datepicker-prev { background-image: url(' . $left_icon . '); }';
		$style .= ' div.ui-widget.ui-datepicker .ui-datepicker-next { background-image: url(' . $right_icon . '); }';

		$check_icon  = staggs_get_icon( 'sgg_checkmark', 'checkmark', true );

		$style .= ' div.staggs-product-options .tickboxes label input[type=checkbox]:checked + .box:before,';
		$style .= ' div.staggs-product-options .options input:checked { background-image: url(' . $check_icon . '); }';

		$notice_close_icon = apply_filters( 'staggs_notice_close_icon', plugin_dir_url( __FILE__ ) . '/img/close.svg' );

		$style .= ' div.staggs-message-wrapper .hide-notice { background-image: url(' . $notice_close_icon . '); }';

		$input_preview_close_icon = staggs_get_icon( 'sgg_group_close_icon', 'panel-close' );

		$style .= ' div.option-group-wrapper .image-input .input-image-thumbnail .remove-input-image { background-image: url(' . $input_preview_close_icon . '); }';

		$theme = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_theme' ) );

		if ( 'dark' === $theme ) {

			// Inline configurator styles

			$style .= ' div.option-group-wrapper .staggs-summary-widget { background-color: #333; color: #fff; }';

			$style .= ' div.staggs-configurator,';
			$style .= ' div.swiper.staggs-view-gallery .swiper-button-prev,';
			$style .= ' div.swiper.staggs-view-gallery .swiper-button-next,';
			$style .= ' body.panel-shown .option-group-panel.shown:before,';
			$style .= ' body.panel-shown .staggs:before,';
			$style .= ' div.staggs-product-options .option-group-panel,';
			$style .= ' div.staggs-product-usps,';
			$style .= ' div.staggs-configurator-main #ar-button,';
			$style .= ' div.staggs-configurator-main #ar-button-desktop,';
			$style .= ' div.staggs-configurator-full .staggs-product-view .product-view-usps,';
			$style .= ' div.staggs-configurator-popup .staggs-product-view .product-view-usps,';
			$style .= ' div.product-view-nav button,';
			$style .= ' div.product-view-nav--labels .view-nav-buttons,';
			$style .= ' div.product-view-wrapper:before,';
			$style .= ' div.staggs-product-options .options input,';
			$style .= ' div.staggs-product-options .option-group-options .icon.out-of-stock:before,';
			$style .= ' div.staggs-product-options .option-group-options .box.out-of-stock:before,';
			$style .= ' div.staggs-product-options .option-group-options .option.out-of-stock:before,';
			$style .= ' div.staggs-product-options .option-group-options .button.out-of-stock:before,';
			$style .= ' div.staggs-product-options .single .knobs .switch,';
			$style .= ' div.staggs-full .option-group-wrapper,';
			$style .= ' div.staggs-stepper .option-group-wrapper,';
			$style .= ' div.staggs-floating .option-group-wrapper .option-group-content,';
			$style .= ' div.staggs-floating .option-group-wrapper .staggs-summary-widget,';
			$style .= ' div.staggs-floating .option-group-wrapper .staggs-repeater-item-content,';
			$style .= ' div.staggs-floating .option-group-wrapper .repeater-empty-note,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible select,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .ui-selectmenu-button,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .option,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .range-slider,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .text-input input,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .measurements input,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .single input:checked + .button,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .products input[type=checkbox]:checked + .button,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .products input[type=number],';
			$style .= ' div.staggs-floating .staggs-product-options .fieldset,';
			$style .= ' div.option-group-wrapper .option-group.total.fixed .option-group-content { background-color: #111; color: #fff; }';

			$style .= ' body:not(.staggs-popup-active) .floating-collapsible .ui-menu-item-wrapper { background-color: #111; border-color: #111; }';

			$style .= ' @media (max-width: 991px) {';
			$style .= ' div.staggs-full .staggs-configurator-steps-nav { background-color: #111; }';
			$style .= ' div.staggs-full .staggs-configurator-steps-nav .active .step-content  { background-color: #333; color: #fff; }';
			
			$style .= ' div.staggs-floating .staggs-configurator-steps-nav { background-color: #333; }';
			$style .= ' div.staggs-floating .staggs-configurator-steps-nav .active .step-content { background-color: #111; color: #fff; }';
			$style .= ' }';

			$style .= ' @media (min-width: 992px) {';
			$style .= ' div.staggs-full .staggs-configurator-steps-nav .active .step-content,';
			$style .= ' div.staggs-floating .staggs-configurator-steps-nav .active .step-content { background-color: #111; color: #fff; }';
			$style .= ' }';
	
			$style .= ' div.staggs-product-options .option-group-panel,';
			$style .= ' div.staggs-product-options .select select,';
			$style .= ' div.staggs-product-options .select .ui-selectmenu-button,';
			$style .= ' div.ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper,';
			$style .= ' div.staggs-product-options .measurements input,';
			$style .= ' div.staggs-product-options .text-input input,';
			$style .= ' div.staggs-product-options .text-input textarea,';
			$style .= ' div.staggs-product-options .image-input input,';
			$style .= ' div.staggs-product-options .image-input .image-input-field-label,';
			$style .= ' div.staggs-product-options .image-input .input-image-thumbnail,';
			$style .= ' div.staggs-product-options .single .toggle .knobs .after,';
			$style .= ' div.staggs-product-options .option-group .option-group-description a,';
			$style .= ' div.staggs-product-options .single input:checked + .button .del,';
			$style .= ' div.staggs-product-options .products input[type=checkbox]:checked + .button .del,';
			$style .= ' div.option-group-wrapper .products .sgg-product input[type=number],';
			$style .= ' div.staggs-configurator-main .staggs-product-options .intro .price,';
			$style .= ' div.option-group-wrapper .option-group.total .quantity input.qty,';
			$style .= ' div.staggs-configurator-main h1, .staggs-configurator-main h2, .staggs-configurator-main h3, .staggs-configurator-main p, .staggs-configurator-main a,';
			$style .= ' div.staggs-configurator-steps-nav ul .configurator-step-link,';
			$style .= ' div.staggs-product-options .credit p,';
			$style .= ' div.staggs-product-options .credit a { color: #fff; }';

			$style .= ' div.staggs-configurator-main.staggs-configurator-height-auto,';
			$style .= ' div.staggs-configurator-main .fieldset,';
			$style .= ' section.staggs-product-view .product-view-inner,';
			$style .= ' div.staggs-product-options .product-view-usps,';
			$style .= ' div.staggs-configurator-bottom-bar .product-view-usps,';
			$style .= ' div.product-view-nav--labels button,';
			$style .= ' div.option-group-panel .option-group-panel-header,';
			$style .= ' div.staggs-product-options .single .layer,';
			$style .= ' div.staggs-floating .staggs-product-options,';
			$style .= ' div.staggs-product-options .select select,';
			$style .= ' div.staggs-product-options .select .ui-selectmenu-button,';
			$style .= ' div.ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper,';
			$style .= ' div.staggs-product-options .measurements input,';
			$style .= ' div.option-group-wrapper .measurements .range-slider,';
			$style .= ' div.staggs-product-options .text-input input,';
			$style .= ' div.staggs-product-options .text-input textarea,';
			$style .= ' div.staggs-product-options .image-input input,';
			$style .= ' div.staggs-product-options .image-input .image-input-field-label,';
			$style .= ' div.staggs-product-options .image-input .input-image-thumbnail,';
			$style .= ' div.staggs-product-options .single input:checked + .button,';
			$style .= ' div.staggs-product-options .products input[type=checkbox]:checked + .button,';
			$style .= ' div.staggs-repeater .repeater-empty-note,';
			$style .= ' div.staggs-repeater .staggs-repeater-item-header,';
			$style .= ' div.option-group-wrapper .products .sgg-product input[type=number],';
			$style .= ' div.staggs-product-options label .option,';
			$style .= ' div.staggs-configurator-steps-nav ul a.active .step-content,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .options input,';
			$style .= ' div.option-group-wrapper .option-group.total .quantity input.qty { background-color: #333; border-color: #333; }';

			$style .= ' div.fieldset-legend .icon-plus path, .fieldset-legend .icon-minus path,';
			$style .= ' div.staggs-product-options .option-group .show-panel svg,';
			$style .= ' div.staggs-product-options .option-group .close-panel svg { fill: #fff !important; }';

			$style .= ' div.staggs-configurator-steps-nav ul .configurator-step-link .step-number,';
			$style .= ' div.staggs-repeater .staggs-repeater-item .index,';
			$style .= ' div.staggs-product-options .tickboxes label input[type=checkbox] { border-color: #fff; }';

			$style .= ' body:not(.staggs-popup-active) .option-group-step.collapsible { background-color: #333; }';

			$style .= ' div.staggs-repeater .staggs-repeater-item-header .button:hover, .staggs-repeater .staggs-repeater-item-header .button:focus,';
			$style .= ' body:not(.staggs-popup-active) .option-group-step.collapsible .option-group-step-inner { background-color: #111; }';

			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible { background-color: #111; }';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .option-group-step-inner { background-color: #333; }';

			$style .= ' .staggs-product-view model-viewer .hotspot .hotspot-toggle,';
			$style .= ' .staggs-product-view model-viewer .hotspot.active .hotspot-content,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button:hover,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button:focus,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button .button-label,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button { background-color: #111; color: #fff; }';

			$style .= ' .staggs-summary-template-preview,';
			$style .= ' .staggs-summary-template-action-widget,';
			$style .= ' .staggs-summary-template-totals { background-color: #333; }';

			$style .= ' div.staggs-summary-template .staggs-summary-widget-action-form input:focus,';
			$style .= ' div.staggs-summary-template .staggs-summary-widget-action-form input { color: #fff; background-color: #111; border-color: #111; }';

			$style .= ' div.staggs-configurator-steps-nav ul a.active .step-number { color: #111; background-color: #fff; }';

			$style .= ' div.staggs-repeater .staggs-repeater-item .staggs-repeater-item-content,';
			$style .= ' div.option-group-wrapper .products .sgg-product { border-color: #333; }';

			$style .= ' div.ui-widget.ui-datepicker { background-color: #333; color: #fff; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-datepicker-header { background-color: #111; color: #fff; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-default { background-color: #111; color: #fff; border-color: #111; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-active { background-color: #fff; color: #111; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-highlight, div.ui-widget.ui-datepicker .ui-state-hover { border-color: #fff; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-datepicker-prev, div.ui-widget.ui-datepicker .ui-datepicker-next { background-color: #fff; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-disabled .ui-state-default { background-color: #333; border-color: #333; }';

			// Popup styles

			$style .= ' div.staggs-configurator-popup.active,';
			$style .= ' div.staggs-product-options,';
			$style .= ' div.staggs-configurator-topbar,';
			$style .= ' div.staggs-configurator-bottom-bar,';
			$style .= ' div.staggs-configurator-popup .staggs-configurator-bottom-bar .option-group-step:not(.collapsed) .option-group-step-title,';
			$style .= ' div.staggs-configurator-popup .staggs-configurator-bottom-bar .option-group-step:not(.collapsed) .option-group-step-inner,';
			$style .= ' .staggs-configurator-popup .staggs-product-view .product-view-usps { background-color: #111; color: #fff; }';
			
			$style .= ' .staggs-summary-total-buttons .staggs-cart-form-button .quantity input.qty,';
			$style .= ' div.staggs-configurator-popup .option-group-step .option-group-step-title,';
			$style .= ' div.staggs-configurator-popup.popup-horizontal .staggs-configurator-bottom-bar,';
			$style .= ' div.staggs-configurator-bottom-bar form.cart div.quantity input.qty { background-color: #333; border-color: #333; color: #fff; }';

			$style .= ' div.staggs-configurator-popup.popup-horizontal .staggs-configurator-bottom-bar .bottom-bar-right form.cart .quantity input.qty { background-color: #111; border-color: #111; }';

			$style .= ' .staggs-summary-template-form .staggs-summary-template-table-row,';
			$style .= ' div.option-group-step .option-group-step-title .step-number { border-color: #fff; }';

			$style .= ' .staggs-summary-template-form .product_title,';
			$style .= ' .staggs-configurator-popup .staggs-configurator-topbar #close-popup span { color: #fff; }';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_buttons_hide_disabled' ) ) {
			$style .= ' .option-group-step-buttons .button.disabled { visibility: hidden !important; }';
		} 

		if ( 'custom' === $theme ) {
			$primary_color   = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_primary_color' ) );
			$secondary_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_secondary_color' ) );
			$tertiary_color  = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_tertiary_color' ) );
			if ( ! $tertiary_color ) {
				$tertiary_color = $secondary_color;
			}
			$heading_color   = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_heading_color' ) );
			$text_color      = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_text_color' ) );
			$icon_theme      = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_configurator_icon_theme' ) );

			// Inline configurator styles.

			$style .= ' .option-group-wrapper .staggs-summary-widget { background-color:' . $secondary_color . '; color: ' . $text_color . '; }';

			$style .= ' .staggs-configurator,';
			$style .= ' .swiper.staggs-view-gallery .swiper-button-prev,';
			$style .= ' .swiper.staggs-view-gallery .swiper-button-next,';
			$style .= ' body.panel-shown .option-group-panel.shown:before,';
			$style .= ' body.panel-shown .staggs:before,';
			$style .= ' .staggs-product-options .option-group-panel,';
			$style .= ' .staggs-product-usps,';
			$style .= ' .staggs-configurator-main #ar-button,';
			$style .= ' .staggs-configurator-main #ar-button-desktop,';
			$style .= ' .staggs-configurator-full .staggs-product-view .product-view-usps,';
			$style .= ' .staggs-configurator-popup .staggs-product-view .product-view-usps,';
			$style .= ' .product-view-nav button,';
			$style .= ' .product-view-nav--labels .view-nav-buttons,';
			$style .= ' .product-view-wrapper:before,';
			$style .= ' .staggs-product-options .options input,';
			$style .= ' .staggs-product-options .option-group-options .icon.out-of-stock:before,';
			$style .= ' .staggs-product-options .option-group-options .box.out-of-stock:before,';
			$style .= ' .staggs-product-options .option-group-options .button.out-of-stock:before,';
			$style .= ' .staggs-product-options .option-group-options .option.out-of-stock:before,';
			$style .= ' .staggs-product-options .single .knobs .switch,';
			$style .= ' .staggs-full .option-group-wrapper,';
			$style .= ' .staggs-stepper .option-group-wrapper,';
			$style .= ' div.staggs-floating .option-group-wrapper .option-group-content,';
			$style .= ' div.staggs-floating .option-group-wrapper .staggs-summary-widget,';
			$style .= ' div.staggs-floating .option-group-wrapper .staggs-repeater-item-content,';
			$style .= ' div.staggs-floating .option-group-wrapper .repeater-empty-note,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible select,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .ui-selectmenu-button,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .option,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .range-slider,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .text-input input,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .measurements input,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .single input:checked + .button,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .products input:checked + .button,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .products input[type=number],';
			$style .= ' .staggs-floating .staggs-product-options .fieldset,';
			$style .= ' .option-group-wrapper .option-group.total.fixed .option-group-content { background-color: ' . $primary_color . '; color: ' . $text_color . '; }';

			$style .= ' body:not(.staggs-popup-active) .floating-collapsible .ui-menu-item-wrapper { background-color: ' . $primary_color . '; border-color: ' . $primary_color . '; }';

			$style .= ' @media (max-width: 991px) {';
			$style .= ' .staggs-full .staggs-configurator-steps-nav { background-color: ' . $primary_color . '; }';
			$style .= ' .staggs-full .staggs-configurator-steps-nav .active .step-content  { background-color: ' . $secondary_color . '; color: ' . $heading_color . '; }';
			
			$style .= ' .staggs-floating .staggs-configurator-steps-nav { background-color: ' . $secondary_color . '; }';
			$style .= ' .staggs-floating .staggs-configurator-steps-nav .active .step-content { background-color: ' . $primary_color . '; color: ' . $heading_color . '; }';
			$style .= ' }';

			$style .= ' @media (min-width: 992px) {';
			$style .= ' .staggs-full .staggs-configurator-steps-nav .active .step-content,';
			$style .= ' .staggs-floating .staggs-configurator-steps-nav .active .step-content { background-color: ' . $primary_color . '; color: ' . $heading_color . '; }';
			$style .= ' }';
	
			$style .= ' .staggs-product-options .option-group-panel,';
			$style .= ' .staggs-product-options .select select,';
			$style .= ' .staggs-product-options .select .ui-selectmenu-button,';
			$style .= ' .ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper,';
			$style .= ' .staggs-product-options .measurements input,';
			$style .= ' .staggs-product-options .text-input input,';
			$style .= ' .staggs-product-options .text-input textarea,';
			$style .= ' .staggs-product-options .image-input input,';
			$style .= ' .staggs-product-options .image-input .image-input-field-label,';
			$style .= ' .staggs-product-options .image-input .input-image-thumbnail,';
			$style .= ' .staggs-product-options .single .toggle .knobs .after,';
			$style .= ' .staggs-product-options .option-group .option-group-description a,';
			$style .= ' .staggs-product-options .single input:checked + .button .del,';
			$style .= ' .staggs-product-options .products input[type=checkbox]:checked + .button .del,';
			$style .= ' .option-group-wrapper .products .sgg-product input[type=number],';
			$style .= ' .staggs-configurator-main .staggs-product-options .intro .price,';
			$style .= ' .option-group-wrapper .option-group.total .quantity input.qty,';
			$style .= ' .staggs-configurator-main h1, .staggs-configurator-main h2, .staggs-configurator-main h3, .staggs-configurator-main p, .staggs-configurator-main a,';
			$style .= ' .staggs-product-options .credit p,';
			$style .= ' .staggs-product-options .credit a { color: ' . $text_color . '; }';

			$style .= ' div.staggs-configurator-main.staggs-configurator-height-auto,';
			$style .= ' .staggs-configurator-main .fieldset,';
			$style .= ' .staggs-product-view .product-view-inner,';
			$style .= ' .staggs-product-options .product-view-usps,';
			$style .= ' .product-view-nav--labels button,';
			$style .= ' .option-group-panel .option-group-panel-header,';
			$style .= ' .staggs-product-options .single .layer,';
			$style .= ' .staggs-floating .staggs-product-options,';
			$style .= ' .staggs-product-options .select select,';
			$style .= ' .staggs-product-options .select .ui-selectmenu-button,';
			$style .= ' .ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper,';
			$style .= ' .staggs-product-options .measurements input,';
			$style .= ' .option-group-wrapper .measurements .range-slider,';
			$style .= ' .staggs-product-options .text-input input,';
			$style .= ' .staggs-product-options .text-input textarea,';
			$style .= ' .staggs-product-options .image-input input,';
			$style .= ' .staggs-product-options .image-input .image-input-field-label,';
			$style .= ' .staggs-product-options .image-input .input-image-thumbnail,';
			$style .= ' .staggs-product-options .single input:checked + .button,';
			$style .= ' .staggs-product-options .products input[type=checkbox]:checked + .button,';
			$style .= ' .staggs-repeater .repeater-empty-note,';
			$style .= ' .staggs-repeater .staggs-repeater-item-header,';
			$style .= ' .option-group-wrapper .products .sgg-product input[type=number],';
			$style .= ' .staggs-product-options label .option,';
			$style .= ' .staggs-configurator-steps-nav ul a.active .step-content,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .options input,';
			$style .= ' .option-group-wrapper .option-group.total .quantity input.qty { background-color: ' . $secondary_color . '; border-color: ' . $secondary_color . '; }';

			$style .= ' body:not(.staggs-popup-active) .option-group-step.collapsible { background-color: ' . $secondary_color . '; }';

			$style .= ' .staggs-repeater .staggs-repeater-item-header .button:hover, .staggs-repeater .staggs-repeater-item-header .button:focus,';
			$style .= ' body:not(.staggs-popup-active) .option-group-step.collapsible .option-group-step-inner { background-color: ' . $primary_color . '; }';

			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible { background-color: ' . $primary_color . '; }';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .option-group-step-inner { background-color: ' . $secondary_color . '; }';

			$style .= ' .staggs-product-view model-viewer .hotspot .hotspot-toggle,';
			$style .= ' .staggs-product-view model-viewer .hotspot.active .hotspot-content,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button:hover,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button:focus,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button .button-label,';
			$style .= ' .staggs-view-gallery .staggs-preview-actions button { background-color: ' . $primary_color . '; color: ' . $text_color . '; }';

			$style .= ' .staggs-summary-template-preview,';
			$style .= ' .staggs-summary-template-action-widget,';
			$style .= ' .staggs-summary-template-totals { background-color: ' . $secondary_color . '; }';

			$style .= ' div.staggs-summary-template .staggs-summary-widget-action-form input:focus,';
			$style .= ' div.staggs-summary-template .staggs-summary-widget-action-form input { color: ' . $text_color . '; background-color: ' . $primary_color . '; border-color: ' . $primary_color . '; }';

			$style .= ' .staggs-product-options .single .toggle .knobs .after,';
			$style .= ' .staggs-product-options .title,';
			$style .= ' .staggs-product-options .input-title,';
			$style .= ' .option-group-step .option-group-step-title p,';
			$style .= ' .option-group-step .option-group-step-title .step-number,';
			$style .= ' .staggs-configurator-steps-nav ul .configurator-step-link,';
			$style .= ' .staggs-product-options .option-group-panel-header p,';
			$style .= ' .staggs-product-options .option-group-panel-header a,';
			$style .= ' .staggs h1, .staggs h2, .staggs h3 { color: ' . $heading_color . '; }';

			$style .= ' .staggs-configurator-steps-nav ul .configurator-step-link .step-number,';
			$style .= ' .staggs-repeater .staggs-repeater-item .index,';
			$style .= ' .option-group-step .option-group-step-title .step-number { border-color: ' . $heading_color . '; }';

			$style .= ' .staggs-product-options .single .knobs .switch { background-color: ' . $heading_color . '; }';

			$style .= ' .staggs-repeater .staggs-repeater-item .staggs-repeater-item-content,';
			$style .= ' .option-group-wrapper .products .sgg-product { border-color: ' . $secondary_color . '; }';

			$style .= ' .staggs-product-options .close-panel svg { fill: ' . $heading_color . ' !important; }';

			$style .= ' .staggs-repeater .staggs-repeater-item .index,';
			$style .= ' .staggs-configurator-steps-nav ul a.active .step-number { color: ' . $primary_color . '; background-color:' . $heading_color . '; }';

			$style .= ' .staggs-product-options .tickboxes label input[type=checkbox] { border-color: ' . $text_color . '; }';

			if ( 'light' === $icon_theme ) {
				$style .= ' .fieldset .fieldset-legend .icon-plus path, .fieldset .fieldset-legend .icon-minus path,';
				$style .= ' .staggs-product-options .option-group .close-panel svg { fill: #fff !important; }';
			}

			$style .= ' div.ui-widget.ui-datepicker { background-color: ' . $secondary_color . '; color: ' . $text_color . '; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-datepicker-header { background-color: ' . $primary_color . '; color: ' . $text_color . '; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-default { background-color: ' . $primary_color . '; color: ' . $text_color . '; border-color: ' . $primary_color . '; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-active { color: ' . $primary_color . '; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-disabled .ui-state-default { background-color: ' . $secondary_color . '; border-color: ' . $secondary_color . '; }';

			// Popup styles

			$style .= ' div.staggs-configurator-popup.active,';
			$style .= ' div.staggs-product-options,';
			$style .= ' div.staggs-configurator-topbar,';
			$style .= ' div.staggs-configurator-bottom-bar,';
			$style .= ' div.staggs-configurator-popup .staggs-configurator-bottom-bar .option-group-step:not(.collapsed) .option-group-step-title,';
			$style .= ' div.staggs-configurator-popup .staggs-configurator-bottom-bar .option-group-step:not(.collapsed) .option-group-step-inner,';
			$style .= ' .staggs-configurator-popup .staggs-product-view .product-view-usps { background-color: ' . $primary_color . '; color: ' . $text_color . '; }';
			
			$style .= ' .staggs-summary-total-buttons .staggs-cart-form-button .quantity input.qty,';
			$style .= ' div.staggs-configurator-popup .option-group-step .option-group-step-title,';
			$style .= ' div.staggs-configurator-popup.popup-horizontal .staggs-configurator-bottom-bar,';
			$style .= ' .staggs-configurator-bottom-bar form.cart div.quantity input.qty { background-color: ' . $secondary_color . '; border-color: ' . $secondary_color . '; color: ' . $text_color . '; }';

			$style .= ' .staggs-configurator-popup.popup-horizontal .staggs-configurator-bottom-bar .bottom-bar-right form.cart .quantity input.qty { background-color: ' . $primary_color . '; border-color: ' . $primary_color . '; }';

			$style .= ' .staggs-summary-template-form .staggs-summary-template-table-row,';
			$style .= ' div.option-group-step .option-group-step-title .step-number { border-color: ' . $text_color . '; }';

			$style .= ' .staggs-summary-template-form .product_title,';
			$style .= ' .staggs-configurator-popup .staggs-configurator-topbar #close-popup span { color: ' . $text_color . '; }';

			// Card styles.

			$style .= ' .staggs-product-options .select select,';
			$style .= ' .staggs-product-options .select .ui-selectmenu-button,';
			$style .= ' div.ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper,';
			$style .= ' .staggs-product-options .measurements input,';
			$style .= ' .option-group-wrapper .measurements .range-slider,';
			$style .= ' .staggs-product-options .text-input input,';
			$style .= ' .staggs-product-options .text-input textarea,';
			$style .= ' .staggs-product-options .image-input-field input,';
			$style .= ' .staggs-product-options .image-input-field .image-input-field-label,';
			$style .= ' .staggs-product-options .image-input-field .input-image-thumbnail,';
			$style .= ' .option-group-wrapper .products .sgg-product input,';
			$style .= ' .staggs-product-options label .option { background-color: ' . $tertiary_color . '; border-color: ' . $tertiary_color . '; }';

			$option_hover_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_tertiary_hover_color' ) );
			if ( $option_hover_color ) {
				$style .= ' .staggs-product-options .select select:hover,';
				$style .= ' .staggs-product-options .select .ui-selectmenu-button:hover,';
				$style .= ' .staggs-product-options .measurements input:hover,';
				$style .= ' .staggs-product-options .text-input input:hover,';
				$style .= ' .staggs-product-options .text-input textarea:hover,';
				$style .= ' .option-group-wrapper .products .sgg-product input:hover,';
				$style .= ' .staggs-product-options label:not(.disabled):hover .option { background-color: ' . $option_hover_color . '; }';
			}

			$option_text_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_tertiary_text_color' ) );
			if ( $option_text_color ) {
				$style .= ' .staggs-product-options .icons .icon,';
				$style .= ' .staggs-product-options .select select,';
				$style .= ' .staggs-product-options .select .ui-selectmenu-button,';
				$style .= ' div.ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper,';
				$style .= ' .staggs-product-options .measurements input,';
				$style .= ' .staggs-product-options .text-input input,';
				$style .= ' .staggs-product-options .text-input textarea,';
				$style .= ' .staggs-product-options .image-input-field input,';
				$style .= ' .staggs-product-options .image-input-field .image-input-field-label,';
				$style .= ' .staggs-product-options .image-input-field .input-image-thumbnail,';
				$style .= ' .option-group-wrapper .products .sgg-product input,';
				$style .= ' .staggs-product-options label .option { color: ' . $option_text_color . '; }';
			}
			
			$option_active_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_tertiary_active_color' ) );
			if ( $option_active_color ) {
				$style .= ' .staggs-product-options label input:checked + .option { background-color: ' . $option_active_color . '; }';
			}

			$option_active_text_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_tertiary_active_text_color' ) );
			if ( $option_active_text_color ) {
				$style .= ' .staggs-product-options label input:checked + .option { color: ' . $option_active_text_color . '; }';
			}
		}

		$accent = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_accent_color' ) );
		if ( $accent ) {
			$accent_hover = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_accent_hover_color' ) );
			if ( ! $accent_hover ) {
				$accent_hover = $accent;
			}

			$style .= ' div.option-group-options.tabs ul a.active,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.single_add_to_cart_button,';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup .button,';
			$style .= ' div.staggs-product-options .staggs-repeater-footer .button,';
			$style .= ' div.staggs-product-options .option-group .input-field-button,';
			$style .= ' div.staggs-product-options .option-group .button { background-color: ' . $accent . '; border-color: ' . $accent . '; }';

			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice:hover,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.single_add_to_cart_button:hover,';
			$style .= ' div.staggs-product-options .staggs-repeater-footer .button:hover,';
			$style .= ' div.staggs-product-options .staggs-repeater-footer .button:focus,';
			$style .= ' div.staggs-product-options .option-group .input-field-button:hover,';
			$style .= ' div.staggs-product-options .option-group .button:hover,';
			$style .= ' div.staggs-product-options .option-group .button:focus { background-color: ' . $accent_hover . '; border-color: ' . $accent_hover . '; }';

			$style .= ' div.option-group-options.tabs { border-color: ' . $accent . '; }';
	
			$style .= ' div.option-group-options.tabs ul a:hover,';
			$style .= ' .staggs-summary-total-buttons .staggs-cart-form-button .quantity input.qty:focus,';
			$style .= ' div.staggs-summary-template .staggs-summary-widget-action-form input:focus,';
			$style .= ' div.staggs-product-options .option-group input:focus,';
			$style .= ' div.option-group-options.tabs ul a:focus { border-color: ' . $accent_hover . '; }';

			$style .= ' div.staggs-configurator-main .bar-action-buttons #request-invoice button,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons #request-invoice button .button-label,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons #save-configuration button,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons #save-configuration button .button-label,';
			$style .= ' div.staggs-configurator-main .option-group-tooltip-description,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button.disabled,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.single_add_to_cart_button,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.request-invoice,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs a.request-invoice,';
			$style .= ' div.staggs-configurator-bottom-bar div.bottom-bar-totals form.cart .staggs-cart-form-button.staggs button.single_add_to_cart_button,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button.disabled { background-color: ' . $accent . '; }';

			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button.disabled:hover,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button:hover,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons #save-configuration button:hover,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons #request-invoice button:hover,';
			$style .= ' div.staggs-configurator-bottom-bar div.bottom-bar-totals form.cart .staggs-cart-form-button.staggs button.single_add_to_cart_button:hover,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button:hover { background-color: ' . $accent_hover . '; }';

			$style .= ' div.option-group-options.tabs ul a,';
			$style .= ' div.staggs-configurator-bottom-bar #request-invoice button,';
			$style .= ' div.staggs-configurator-bottom-bar #save-configuration button,';
			$style .= ' div.staggs-product-options .option-group #request-invoice button,';
			$style .= ' div.staggs-product-options .option-group #save-configuration button { color: ' . $accent . '; }';

			$style .= ' div.staggs-configurator-bottom-bar #request-invoice button:hover,';
			$style .= ' div.staggs-configurator-bottom-bar #save-configuration button:hover,';
			$style .= ' div.staggs-product-options .option-group #request-invoice button:hover,';
			$style .= ' div.staggs-product-options .option-group #save-configuration button:hover { color: ' . $accent_hover . '; }';

			$style .= ' div.staggs-configurator-bottom-bar .bottom-bar-totals #request-invoice svg path,';
			$style .= ' div.staggs-configurator-bottom-bar .bottom-bar-totals #save-configuration svg path,';
			$style .= ' div.staggs-product-options #request-invoice svg path,';
			$style .= ' div.staggs-product-options #save-configuration svg path,';
			$style .= ' div.staggs-product-options .show-panel svg { fill: ' . $accent . ' !important; }';

			$style .= ' div.staggs-configurator-main .product-view-nav--labels .view-nav-buttons button.selected,';
			$style .= ' div.ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper.ui-state-active,';
			$style .= ' div.option-group-wrapper .measurements .range-slider .ui-slider-handle,';
			$style .= ' div.option-group-wrapper .measurements .range-slider .ui-slider-valuebox,';
			$style .= ' div.option-group-wrapper .measurements .range-slider .ui-slider-range,';
			$style .= ' div.staggs-product-options .tickboxes label input[type=checkbox]:checked,';
			$style .= ' div.staggs-product-options .icons .tooltip,';
			$style .= ' div.staggs-product-options .single .toggle .checkbox:checked ~ .layer,';
			$style .= ' body:not(.staggs-popup-active) .staggs-floating .option-group-step.collapsible .options input:checked,';
			$style .= ' div.staggs-product-options .options input:checked { background-color: ' . $accent . '; }';

			$style .= ' div.staggs-product-options .tickboxes label input[type=checkbox]:hover,';
			$style .= ' div.staggs-product-options .options label:not(.disabled):hover input { background-color: ' . $accent_hover . '; }';

			$style .= ' .staggs-product-options .icons .icon:hover,';
			$style .= ' .staggs-product-options .select select:hover,';
			$style .= ' .staggs-product-options .select .ui-selectmenu-button:hover,';
			$style .= ' .staggs-product-options .measurements input:hover,';
			$style .= ' .staggs-product-options .text-input input:hover,';
			$style .= ' .staggs-product-options .text-input textarea:hover,';
			$style .= ' .option-group-wrapper .products .sgg-product input[type="number"]:hover,';
			$style .= ' .staggs-product-options label:not(.disabled):hover .option { border-color: ' . $accent_hover . '; }';

			$style .= ' div.staggs-product-options .select select:focus,';
			$style .= ' div.staggs-product-options .measurements input:focus,';
			$style .= ' div.staggs-product-options .text-input input:focus,';
			$style .= ' div.staggs-product-options .text-input textarea:focus { border-color: ' . $accent . '; }';

			$style .= ' div.staggs-product-options label input:checked + .option,';
			$style .= ' div.staggs-product-options .icons input:checked + .icon,';
			$style .= ' div.staggs-product-options .tickboxes label input[type=checkbox],';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup input:not(:checked) + .button,';
			$style .= ' div.option-group-wrapper .products .sgg-product.selected,';
			$style .= ' section.staggs-product-view .product-view-nav button.selected { border-color: ' . $accent . '; }';

			$style .= ' div.staggs-product-options .range-value,';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup input:not(:checked) + .button,';
			$style .= ' div.staggs-product-options .option-group-summary { color: ' . $accent . '; }';

			$style .= ' div.staggs-configurator-main .option-group.intro a,';
			$style .= ' div.staggs-product-options .credit a,';
			$style .= ' div.staggs-product-options .option-group.total a { color: ' . $accent . '; }';
			$style .= ' div.staggs-product-options .option-group.total a svg path { fill: ' . $accent . '; }';

			$style .= ' div.ui-widget.ui-datepicker .ui-state-active,';
			$style .= ' div.ui-widget.ui-datepicker .ui-datepicker-prev, div.ui-widget.ui-datepicker .ui-datepicker-next { background-color: ' . $accent . '; }';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-highlight, div.ui-widget.ui-datepicker .ui-state-hover { border-color: ' . $accent . '; }';

			$style .= ' .staggs-summary-template .staggs-cart-form-button .button,';
			$style .= ' .staggs-summary-template .staggs-button { background-color: ' . $accent . '; }';

			$style .= ' .staggs-summary-template .staggs-cart-form-button .button:hover,';
			$style .= ' .staggs-summary-template .staggs-cart-form-button .button:focus { background-color: ' . $accent_hover . '; }';

			$style .= ' div.staggs-configurator-main .option-group .option-group-title a,';
			$style .= ' .staggs-summary-template .staggs-back-configurator { color: ' . $accent . '; }';
			
			list($r, $g, $b) = sscanf($accent, "#%02x%02x%02x");
			$luma = luma( $r, $g, $b );
			if ( $luma < 0.5) {
				// Apply light icon theme (dark theme).
				$check_icon = staggs_get_icon( 'sgg_checkmark', 'checkmark', true, 'dark' );

				$style .= ' div.staggs-product-options .tickboxes label input[type=checkbox]:checked + .box:before,';
				$style .= ' div.staggs-product-options .options input:checked { background-image: url(' . $check_icon . '); }';
			}
		}

		$button_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_button_text_color' ) );
		if ( $button_color ) {
			$style .= ' div.staggs-configurator-main .bar-action-buttons .secondary-button button,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons .secondary-buttons button,';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup input:checked + .button,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button.disabled,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button.disabled,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.single_add_to_cart_button,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.single_add_to_cart_button:before,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.request-invoice,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.request-invoice:before,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs a.request-invoice,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs a.request-invoice:before,';
			$style .= ' div.staggs-configurator-bottom-bar div.bottom-bar-totals form.cart .staggs-cart-form-button.staggs button.single_add_to_cart_button,';
			$style .= ' div.staggs-configurator-bottom-bar div.bottom-bar-totals form.cart .staggs-cart-form-button.staggs button.single_add_to_cart_button:before,';
			$style .= ' div.staggs-configurator-bottom-bar form.cart div.staggs-cart-form-button.staggs button.button.single_add_to_cart_button,';
			$style .= ' div.staggs-configurator-bottom-bar form.cart div.staggs-cart-form-button.staggs button.button.single_add_to_cart_button:before,';
			$style .= ' div.staggs-configurator-bottom-bar .bar-action-buttons #request-invoice button,';
			$style .= ' div.staggs-configurator-bottom-bar .bar-action-buttons #request-invoice button:before,';
			$style .= ' div.staggs-configurator-bottom-bar .bar-action-buttons #save-configuration button,';
			$style .= ' div.staggs-configurator-bottom-bar .bar-action-buttons #save-configuration button:before,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice:before,';
			$style .= ' div.staggs-configurator-main .product-view-nav--labels .view-nav-buttons button.selected,';
			$style .= ' div.ui-selectmenu-menu .ui-menu .ui-menu-item-wrapper.ui-state-active,';
			$style .= ' div.staggs-product-options .single .button .add,';
			$style .= ' div.staggs-product-options .products .button .add,';
			$style .= ' div.staggs-product-options .image-input .button,';
			$style .= ' div.option-group-options.tabs ul a.active,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice:before,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.single_add_to_cart_button,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.single_add_to_cart_button:before,';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup .button,';
			$style .= ' div.staggs-product-options .staggs-repeater-footer .button,';
			$style .= ' div.staggs-product-options .option-group .button,';
			$style .= ' div.staggs-product-options .option-group .input-field-button,';
			$style .= ' div.staggs-product-options .option-group-options.single .knobs .before,';
			$style .= ' div.option-group-wrapper .measurements .range-slider .ui-slider-valuebox,';
			$style .= ' div.staggs-product-options .option-group .button.add-to-cart { color: ' . $button_color . '; }';

			$style .= ' div.staggs-product-options .option-group-options.single .checkbox:checked + .knobs .switch { background-color: ' . $button_color . '; }';

			$style .= ' div.staggs-configurator-main .option-group-tooltip-description,';
			$style .= ' div.option-group-options.tabs ul a.active,';
			$style .= ' div.ui-widget.ui-datepicker .ui-state-active { color: ' . $button_color . '; }';

			$style .= ' .staggs-summary-template .staggs-button,';
			$style .= ' .staggs-summary-template .staggs-button:before,';
			$style .= ' .staggs-summary-template .staggs-cart-form-button .button,';
			$style .= ' .staggs-summary-template .staggs-cart-form-button .button:before { color: ' . $button_color . '; }';

			$button_hover_color = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_button_hover_text_color' ) );
			if ( ! $button_hover_color ) {
				$button_hover_color = $button_color;
			}

			$style .= ' div.staggs-configurator-main .bar-action-buttons .secondary-button button:hover,';
			$style .= ' div.staggs-configurator-main .bar-action-buttons .secondary-buttons button:hover,';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup input:checked + .button:hover,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button:hover,';
			$style .= ' div.staggs-configurator-main .option-group-step-buttons .button.disabled:hover,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button:hover,';
			$style .= ' div.staggs-configurator-bottom-bar .option-group-step-buttons .button.disabled:hover,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.single_add_to_cart_button:hover,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs button.request-invoice:hover,';
			$style .= ' div.staggs-configurator-bottom-bar .staggs-cart-form-button.staggs a.request-invoice:hover,';
			$style .= ' div.staggs-configurator-bottom-bar div.bottom-bar-totals form.cart .staggs-cart-form-button.staggs button.single_add_to_cart_button:hover,';
			$style .= ' div.staggs-configurator-bottom-bar form.cart div.staggs-cart-form-button.staggs button.button.single_add_to_cart_button:hover,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice:hover,';
			$style .= ' div.staggs-configurator-main .product-view-nav--labels .view-nav-buttons button.selected:hover,';
			$style .= ' div.staggs-product-options .single .button .add:hover,';
			$style .= ' div.staggs-product-options .products .button .add:hover,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.request-invoice:hover,';
			$style .= ' div.option-group-wrapper .option-group.total .staggs-cart-form-button.staggs .button.single_add_to_cart_button:hover,';
			$style .= ' div.option-group-wrapper .option-group-options.buttongroup .button:hover,';
			$style .= ' div.staggs-product-options .staggs-repeater-footer .button:hover,';
			$style .= ' div.staggs-product-options .option-group .button:hover,';
			$style .= ' div.staggs-product-options .option-group .input-field-button:hover,';
			$style .= ' div.staggs-product-options .option-group .button.add-to-cart:hover { color: ' . $button_hover_color . ' ; }';
		}

		/**
		 * Configurator Font Family goes first.
		 * Then the one configured in General Settings.
		 */

		$post_font = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_font_family' ) );
		$base_font = sanitize_text_field( staggs_get_theme_option( 'sgg_font_family' ) );

		if ( $post_font ) {
			$style .= ' div.staggs-configurator-topbar,';
			$style .= ' div.staggs-configurator-bottom-bar,';
			$style .= ' div.staggs-configurator-bottom-bar input,';
			$style .= ' div.staggs-message-wrapper:not(.inline) .woocommerce-message,';
			$style .= ' div.staggs-message-wrapper:not(.inline) .woocommerce-info,';
			$style .= ' div.staggs-message-wrapper:not(.inline) .woocommerce-error,';
			$style .= ' div.staggs-configurator-main .staggs-configurator-steps-nav a,';
			$style .= ' div.staggs-configurator-main .staggs-product-options .option-group .summary .name,';
			$style .= ' div.staggs-product-options .option-group .button,';
			$style .= ' div.staggs-product-options .option-group input,';
			$style .= ' div.staggs-product-options .option-group select,';
			$style .= ' div.staggs-product-options, .staggs-product-view, .staggs-product-options .button,';
			$style .= ' div.staggs-product-options .text-input label p,';
			$style .= ' div.staggs-product-options .text-input label .input-title,';
			$style .= ' div.staggs-product-options .measurements label p,';
			$style .= ' div.staggs-product-options .measurements label .input-title,';
			$style .= ' div.staggs-product-options .image-upload label p,';
			$style .= ' div.staggs-product-options .image-upload label .input-title,';
			$style .= ' div.option-group-wrapper .totals-row,';
			$style .= ' .staggs-configurator-bottom-bar.staggs-configurator-sticky-bar .staggs-sticky-bar-title,';
			$style .= ' div.ui-selectmenu-menu .ui-menu-item-wrapper, .ui-selectmenu-button .ui-selectmenu-text, .ui-datepicker.ui-widget,';
			$style .= ' div.staggs-configurator-main .tooltip .name, .staggs-configurator-main .staggs-product-options .option-group .option-group-summary .name,';
			$style .= ' div.staggs-configurator-main h1, div.staggs-configurator-main h2, div.staggs-configurator-main h3, div.staggs-configurator-main b, div.staggs-configurator-main .product-view-usps ul li .item-header p, div.staggs-configurator-main strong { font-family:' . $post_font . '; }';

			$style .= ' div.staggs-configurator-main .staggs-product-options .option-group .summary .name,';
			$style .= ' div.staggs-product-options .option-group .button,';
			$style .= ' div.staggs-product-options .text-input label .input-title,';
			$style .= ' div.staggs-product-options .measurements label .input-title,';
			$style .= ' div.staggs-product-options .image-upload label .input-title,';
			$style .= ' div.option-group-wrapper .totals-row,';
			$style .= ' .staggs-configurator-bottom-bar.staggs-configurator-sticky-bar .staggs-sticky-bar-title,';
			$style .= ' div.staggs-configurator-main h1, div.staggs-configurator-main h2, div.staggs-configurator-main h3, div.staggs-configurator-main b, div.staggs-configurator-main .product-view-usps ul li .item-header p, div.staggs-configurator-main strong { font-weight: 700; }';
		} elseif ( $base_font ) {
			$style .= ' div.staggs-configurator-topbar,';
			$style .= ' div.staggs-configurator-bottom-bar,';
			$style .= ' div.staggs-configurator-bottom-bar input,';
			$style .= ' div.staggs-message-wrapper:not(.inline) .woocommerce-message,';
			$style .= ' div.staggs-message-wrapper:not(.inline) .woocommerce-info,';
			$style .= ' div.staggs-message-wrapper:not(.inline) .woocommerce-error,';
			$style .= ' div.staggs-configurator-main .staggs-configurator-steps-nav a,';
			$style .= ' div.staggs-configurator-main .staggs-product-options .option-group .summary .name,';
			$style .= ' div.staggs-product-options .option-group .button,';
			$style .= ' div.staggs-product-options .option-group input,';
			$style .= ' div.staggs-product-options .option-group select,';
			$style .= ' div.staggs-product-options .text-input label p,';
			$style .= ' div.staggs-product-options .text-input label .input-title,';
			$style .= ' div.staggs-product-options .measurements label p,';
			$style .= ' div.staggs-product-options .measurements label .input-title,';
			$style .= ' div.staggs-product-options .image-upload label p,';
			$style .= ' div.staggs-product-options .image-upload label .input-title,';
			$style .= ' div.option-group-wrapper .totals-row,';
			$style .= ' .staggs-configurator-bottom-bar.staggs-configurator-sticky-bar .staggs-sticky-bar-title,';
			$style .= ' div.ui-selectmenu-menu, .ui-selectmenu-button, .ui-datepicker.ui-widget,';
			$style .= ' div.staggs-product-options, .staggs-product-view, .staggs-product-options .button,';
			$style .= ' div.staggs-configurator-main h1, div.staggs-configurator-main h2, div.staggs-configurator-main h3, div.staggs-configurator-main b, div.staggs-configurator-main .product-view-usps ul li .item-header p, div.staggs-configurator-main strong { font-family: ' . $base_font . '; }';

			$style .= ' div.staggs-configurator-main .staggs-product-options .option-group .summary .name,';
			$style .= ' div.staggs-product-options .option-group .button,';
			$style .= ' div.staggs-product-options .text-input label .input-title,';
			$style .= ' div.staggs-product-options .measurements label .input-title,';
			$style .= ' div.staggs-product-options .image-upload label .input-title,';
			$style .= ' div.option-group-wrapper .totals-row,';
			$style .= ' .staggs-configurator-bottom-bar.staggs-configurator-sticky-bar .staggs-sticky-bar-title,';
			$style .= ' div.staggs-configurator-main h1, div.staggs-configurator-main h2, div.staggs-configurator-main h3, div.staggs-configurator-main b, div.staggs-configurator-main .product-view-usps ul li .item-header p, div.staggs-configurator-main strong { font-weight: 700; }';
		}

		$bg_size = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_bg_image_size' ) );
		if ( 'cover' === $bg_size ) {
			$style .= ' div.product-view-inner { background-size: cover; background-position: center; }';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_top' ) ) {
			$style .= ' div.staggs-product-options .option-group-wrapper .option-group { padding-top: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_top' ) . '; }';
			$style .= ' .staggs-floating div.staggs-product-options .option-group-wrapper .option-group { margin-top: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_top' ) . '; }';
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_bottom' ) ) {
			$style .= ' div.staggs-product-options .option-group-wrapper .option-group { padding-bottom: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_bottom' ) . '; }';
			$style .= ' .staggs-floating div.staggs-product-options .option-group-wrapper .option-group { margin-bottom: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_bottom' ) . '; }';
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_top_mobile' ) ) {
			$style .= ' @media (max-width: 767px) { ';
			$style .= ' div.staggs-product-options .option-group-wrapper .option-group { padding-top: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_top_mobile' ) . '; }';
			$style .= ' .staggs-floating div.staggs-product-options .option-group-wrapper .option-group { margin-top: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_top_mobile' ) . '; }';
			$style .= ' } ';
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_bottom_mobile' ) ) {
			$style .= ' @media (max-width: 767px) { ';
			$style .= ' div.staggs-product-options .option-group-wrapper .option-group { padding-bottom: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_bottom_mobile' ) . '; }';
			$style .= ' .staggs-floating div.staggs-product-options .option-group-wrapper .option-group { margin-bottom: ' . staggs_get_post_meta( $theme_id, 'sgg_attribute_spacing_bottom_mobile' ) . '; }';
			$style .= ' } ';
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) ) {
			$style .= ' @media (min-width: 992px) { ';
			$style .= ' div.staggs-configurator-main .staggs-product-options { flex: 0 0 ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) . '; }';
			$style .= ' div.staggs-configurator-contained .staggs-product-view, div.staggs-configurator-full .staggs-product-view, div.staggs-configurator-popup .staggs-product-view { width: calc( 100% - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) . '); }';
			$style .= ' div.staggs-configurator-contained .staggs-product-view { flex: 0 0 calc( 100% - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) . '); }';
			$style .= ' .staggs-configurator-bottom-bar .bottom-bar-totals-wrapper { width: calc( 100vw - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) . '); }';
			$style .= ' .staggs-configurator-popup .staggs-configurator-bottom-bar .bottom-bar-left { width: calc( 100% - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) . '); }';
			$style .= ' .staggs-configurator-popup .staggs-configurator-bottom-bar .bottom-bar-right { flex: 0 0 ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width_tablet' ) . '; }';
			$style .= ' }';
		}
		if ( staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) ) {
			$style .= ' @media (min-width: 1200px) { ';
			$style .= ' div.staggs-configurator-main .staggs-product-options { flex: 0 0 ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) . '; }';
			$style .= ' div.staggs-configurator-contained .staggs-product-view, div.staggs-configurator-full .staggs-product-view, div.staggs-configurator-popup .staggs-product-view { width: calc( 100% - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) . '); }';
			$style .= ' div.staggs-configurator-contained .staggs-product-view { flex: 0 0 calc( 100% - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) . '); }';
			$style .= ' .staggs-configurator-bottom-bar .bottom-bar-totals-wrapper { width: calc( 100vw - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) . '); }';
			$style .= ' .staggs-configurator-popup .staggs-configurator-bottom-bar .bottom-bar-left { width: calc( 100% - ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) . '); }';
			$style .= ' .staggs-configurator-popup .staggs-configurator-bottom-bar .bottom-bar-right { flex: 0 0 ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_width' ) . '; }';
			$style .= ' }';
		}

		if ( 'popup' !== staggs_get_post_meta( $theme_id, 'sgg_configurator_view' ) ) {
			// Don't apply custom padding to popups
			if ( staggs_get_post_meta( $theme_id, 'sgg_template_form_options_padding_tablet' ) ) {
				$style .= ' @media (min-width: 992px) { ';
				$style .= ' div.staggs-configurator-main .option-group-wrapper { padding: ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_padding_tablet' ) . '; }';
				$style .= ' }';
			}
			if ( staggs_get_post_meta( $theme_id, 'sgg_template_form_options_padding' ) ) {
				$style .= ' @media (min-width: 1200px) { ';
				$style .= ' div.staggs-configurator-main .option-group-wrapper { padding: ' . staggs_get_post_meta( $theme_id, 'sgg_template_form_options_padding' ) . '; }';
				$style .= ' }';
			}
		}

		/**
		 * Custom CSS.
		 */

		if ( staggs_get_post_meta( $theme_id, 'sgg_configurator_css' ) ) {
			$style .= staggs_get_post_meta( $theme_id, 'sgg_configurator_css' );
		}

		if ( staggs_get_theme_option( 'sgg_custom_css' ) ) {
			$style .= staggs_get_theme_option( 'sgg_custom_css' );
		}

		$style = apply_filters( 'staggs_public_style', $style );

		if ( is_plugin_active( 'breakdance/plugin.php' ) && $sgg_is_shortcode ) {
			echo sprintf('<style type="text/css">%s</style>', wp_kses_normalize_entities(  $style ) );
		}
		else if ( wp_is_block_theme() ) {
			return printf('<style type="text/css">%s</style>', wp_kses_normalize_entities(  $style ) );
		} 
		else {
			return wp_kses_normalize_entities(  $style );
		}
	}

	/**
	 * Register the configurator inline JavaScript for the public-facing side of the site.
	 *
	 * @since    1.3.0
	 */
	public function enqueue_font_scripts() {
		global $sgg_is_shortcode, $sgg_shortcode_id;
        
        if ( $sgg_is_shortcode ) {
        	$theme_id = staggs_get_theme_id( $sgg_shortcode_id );
        } else {    
	        $theme_id = staggs_get_theme_id();
        }

		$script = '';
		global $sanitized_steps;
		if ( isset( $sanitized_steps ) && count( $sanitized_steps ) > 0 ){
			foreach ( $sanitized_steps as $group ) {
				// Check if configuration contains fonts.
				if ( ! isset( $group['options'] ) || ! is_array( $group['options'] ) || count( $group['options'] ) < 1 || $group['options'][0]['type'] !== 'font' ) {
					continue;
				}

				if ( isset( $group['options'] ) && is_array( $group['options'] ) ) {
					/**
					 * Parent options
					 */
					foreach ( $group['options'] as $option ) {
						if ( 'font' === $option['type'] && isset( $option['font_source'] ) ) {
							$script .= '<link rel="stylesheet" crossorigin="anonymous" href="' . $option['font_source'] . '">';
						}
					}
				} else if ( isset( $group['attributes'] ) && is_array( $group['attributes'] ) ) {
					/**
					 * Repeater options
					 */
					foreach ( $group['attributes'] as $sub_group ) {
						if ( isset( $sub_group['options'] ) && is_array( $sub_group['options'] ) ) {
							foreach ( $sub_group['options'] as $sub_option ) {
								if ( 'font' === $sub_option['type'] && isset( $sub_option['font_source'] ) ) {
									$script .= '<link rel="stylesheet" crossorigin="anonymous" href="' . $sub_option['font_source'] . '">';
								}
							}
						} 
					}
				}
			}
		}

		if ( staggs_get_post_meta( $theme_id, 'sgg_header_scripts' ) ) {
			$script .= staggs_get_post_meta( $theme_id, 'sgg_header_scripts' );
		}

		echo wp_kses_normalize_entities(  $script );
	}

	/**
	 * Register the configurator inline JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_inline_scripts() {
		global $product, $staggs_price_settings, $sgg_is_shortcode, $sgg_shortcode_id;
		
		if ( ! $staggs_price_settings ) {
			staggs_define_price_settings();
		}

        if ( $sgg_is_shortcode ) {
        	$theme_id = staggs_get_theme_id( $sgg_shortcode_id );
        } else {    
	        $theme_id = staggs_get_theme_id();
        }

		$price = 0;
		$altprice = 0;
		$inc_price_label = '';
		$weight_unit = get_option( 'woocommerce_weight_unit' ) ?: 'kg';
		$weight_value = 0;
		$tax_value = 0;

		if ( staggs_get_post_meta( $theme_id, 'sgg_step_set_included_option_text' ) ) {
			$inc_price_label = sanitize_text_field( staggs_get_post_meta( $theme_id, 'sgg_step_included_text' ) );
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			if ( ! is_object( $product ) ) {
				$product = wc_get_product( get_the_ID() );
			}

			if ( is_object( $product ) && $product->get_price() > 0 ) {
				$altprice = $product->get_regular_price();

				if ( $product->is_on_sale() ) {
					$price = $product->get_sale_price();
				} else {
					$price = $product->get_regular_price();
				}

				if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {
					// Take only the item rate and round it. 
					$tax = new WC_Tax();
					$taxes = $tax->get_rates($product->get_tax_class());
					if ( $taxes && is_array($taxes)) {
						$rates = array_shift($taxes);
						if ( is_array( $rates ) ) {
							$tax_value = round(array_shift($rates));
						}
					}

					if ( 'no' === $staggs_price_settings['include_tax'] && 'incl' === $staggs_price_settings['price_display'] ) {
						$altprice = wc_get_price_including_tax( $product, array( 'price' => $altprice ) );
						$price = wc_get_price_including_tax( $product, array( 'price' => $price ) );
					} else if ( 'yes' === $staggs_price_settings['include_tax'] && 'excl' === $staggs_price_settings['price_display'] ) {
						$altprice = wc_get_price_excluding_tax( $product, array( 'price' => $altprice ) );
						$price = wc_get_price_excluding_tax( $product, array( 'price' => $price ) );
					}
				}
			}

			$weight_value = $product->get_weight() ?: 0;
	
			$cart_url = wc_get_cart_url();
			$cart_redirect = get_option( 'woocommerce_cart_redirect_after_add' );
			// Add to cart redirect plugin support.
			if ( is_plugin_active( 'woocommerce-direct-checkout/woocommerce-direct-checkout.php' ) ) {
				if ( get_option( 'qlwcdc_add_to_cart_redirect_page' ) && 'checkout' == get_option( 'qlwcdc_add_to_cart_redirect_page' ) ) {
					$cart_url = wc_get_checkout_url();
					$cart_redirect = 'yes';
				}
			}
		} else {
			$cart_redirect   = '';
			$cart_url        = '';

			$price = staggs_get_post_meta( get_the_ID(), 'sgg_product_regular_price' ) ?: 0;
			$altprice = $price;
			if ( '' !== staggs_get_post_meta( get_the_ID(), 'sgg_product_sale_price' ) ) {
				$price = staggs_get_post_meta( get_the_ID(), 'sgg_product_sale_price' );
			}
		}

		$currency_symbol = $staggs_price_settings['currency_symbol'];
		$currency_pos    = $staggs_price_settings['currency_pos'];
		$thousand_sep    = $staggs_price_settings['thousand_sep'];
		$decimal_sep     = $staggs_price_settings['decimal_sep'];
		$decimal_num     = $staggs_price_settings['decimal_num'];

		$tax_label = sgg__( staggs_get_theme_option( 'sgg_product_tax_label' ) ) ?: '';
		$alt_tax_label = sgg__( staggs_get_theme_option( 'sgg_product_alt_tax_label' ) ) ?: '';
		$display_price = staggs_get_post_meta( $theme_id, 'sgg_configurator_display_pricing' );
		if ( staggs_get_theme_option( 'sgg_product_show_price_logged_in_users' ) && ! is_user_logged_in() ) {
			$display_price = 'hide';
		}

		$summary_message  = staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_empty_message' );
		$invalid_message  = staggs_get_theme_option( 'sgg_product_invalid_error_message' );
		$required_message = staggs_get_theme_option( 'sgg_product_required_error_message' );
		$invalid_field_message  = staggs_get_theme_option( 'sgg_product_invalid_field_message' );
		$required_field_message = staggs_get_theme_option( 'sgg_product_required_field_message' );
		if ( '' == $summary_message ) {
			$summary_message ='No options selected';
		}
		if ( '' == $invalid_message ) {
			$invalid_message = 'Please make sure all fields are filled out correctly!';
		}
		if ( '' == $required_message ) {
			$required_message = 'Please fill out all required fields!';
		}
		if ( '' == $invalid_field_message ) {
			$invalid_field_message = 'This field is invalid.';
		}
		if ( '' == $required_field_message ) {
			$required_field_message = 'This field is required.';
		}

		$share_notice_label  = staggs_get_theme_option( 'sgg_product_share_notice_label' );
		$share_notice_button = staggs_get_theme_option( 'sgg_product_share_notice_button' );
		$share_notice_copied = staggs_get_theme_option( 'sgg_product_share_notice_copied' );
		if ( '' == $share_notice_label ) {
			$share_notice_label = 'Configuration succesfully saved';
		}
		if ( '' == $share_notice_button ) {
			$share_notice_button = 'Copy Link';
		}
		if ( '' == $share_notice_copied ) {
			$share_notice_copied = 'Copied!';
		}

		$wishlist_notice_page_url = esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '/my-configurations/' );
		$wishlist_notice_label    = staggs_get_theme_option( 'sgg_product_wishlist_notice' );
		$wishlist_notice_button   = staggs_get_theme_option( 'sgg_product_wishlist_button' );
		if ( '' == $wishlist_notice_label ) {
			$wishlist_notice_label = 'Configuration succesfully added to "My Configurations"';
		}
		if ( '' == $wishlist_notice_button ) {
			$wishlist_notice_button = 'View my configurations';
		}

		$sgg_loader_icon = staggs_get_icon( 'sgg_loader_icon', 'loader' );
		$price_sign = '';
		if ( staggs_get_theme_option( 'sgg_product_additional_price_sign' ) ) {
			$price_sign = '<span class="sign">+</span>';
		}

		$script = "var AJAX_URL = '" . admin_url( '/admin-ajax.php' ) . "';";
		$script .= "var PRODUCT_ID = " . esc_attr( get_the_ID() ) . ";";
		$script .= "var PRODUCT_PRICE = " . esc_attr( $price ) . ";";
		$script .= "var PRODUCT_ALT_PRICE = " . esc_attr( $altprice ) . ";";
		$script .= "var USE_PRODUCT_PRICE = " . ( staggs_get_theme_option( 'sgg_product_exclude_base_price' ) ? 'false' : 'true' ) . ";";
		$script .= "var SHOW_PRODUCT_PRICE = " . ( 'hide' === $display_price ? 'false' : 'true' ) . ";";
		$script .= "var SHOW_PRICE_DIFFERENCE = " . ( staggs_get_theme_option( 'sgg_product_price_show_difference_only' ) ? 'true' : 'false' ) . ";";
		$script .= "var PRODUCT_PRICE_SIGN = '" . wp_kses_normalize_entities(  str_replace( "'", "\'", $price_sign ) ) . "';";
		$script .= "var DISABLE_PRODUCT_PRICE_UPDATE = " . ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_product_price_update' ) ? 'true' : 'false' ) . ";";
		$script .= "var PRODUCT_THUMBNAIL_URL = '" . wp_get_attachment_image_url( get_post_thumbnail_id() ) . "';";
		$script .= "var USE_PRODUCT_THUMBNAIL = " . ( staggs_get_post_meta( $theme_id, 'sgg_use_product_image' ) ? 'true' : 'false' ) . ";";
		$script .= "var POPUP_UPDATE_PAGE = " . ( staggs_get_post_meta( $theme_id, 'sgg_configurator_button_close_popup' ) ? 'true' : 'false' ) . ";";
		$script .= "var DISABLE_DEFAULTS = " . ( staggs_get_post_meta( $theme_id, 'sgg_step_disable_default_option' ) ? 'true' : 'false' ) . ";";
		$script .= "var KEEP_CONDITIONAL_OPTIONS = " . ( staggs_get_theme_option( 'sgg_product_show_invalid_conditional_options' ) ? 'true' : 'false' ) . ";";
		$script .= "var CAPTURE_PREVIEW_IMAGE = " . ( staggs_get_post_meta( $theme_id, 'sgg_configurator_generate_cart_image' ) ? 'true' : 'false' ) . ";";
		$script .= "var CURRENCY_SYMBOL = '" . esc_attr( $currency_symbol ) . "';";
		$script .= "var CURRENCY_POS = '" . esc_attr( $currency_pos ) . "';";
		$script .= "var THOUSAND_SEPARATOR = '" . esc_attr( $thousand_sep ) . "';";
		$script .= "var DECIMAL_SEPARATOR = '" . esc_attr( $decimal_sep ) . "';";
		$script .= "var NUMBER_OF_DECIMALS = " . esc_attr( $decimal_num ) . ";";
		$script .= "var TRIM_PRICE_DECIMALS = " . ( staggs_get_theme_option( 'sgg_product_price_trim_decimals' ) ? 'true' : 'false' ) . ";";
		$script .= "var REDIRECT_TO_CART = '" . esc_attr( $cart_redirect ) . "';";
		$script .= "var CART_URL = '" . esc_url( $cart_url ) . "';";
		$script .= "var INC_PRICE_LABEL = '" . esc_attr( str_replace( "'", "\'", $inc_price_label ) ) . "';";
		$script .= "var TAX_PRICE_SUFFIX = '" . esc_attr( str_replace( "'", "\'", $tax_label ) ) . "';";
		$script .= "var ALT_TAX_PRICE_SUFFIX = '" . esc_attr( str_replace( "'", "\'", $alt_tax_label ) ) . "';";
		$script .= "var PRODUCT_TAX_DISPLAY = '" . esc_attr( $staggs_price_settings['price_display'] ) . "';";
		$script .= "var PRODUCT_TAX = " . esc_attr( $tax_value ) . ";";
		$script .= "var TRACK_OPTIONS = " . ( staggs_get_theme_option( 'sgg_product_track_conditional_values' ) ? 'true' : 'false' ) . ";";
		$script .= "var TRACK_GLOBAL_OPTIONS = " . ( staggs_get_theme_option( 'sgg_product_track_global_conditional_values' ) ? 'true' : 'false' ) . ";";
		$script .= "var IMAGE_STACK = " . ( 'stacked' === esc_attr( staggs_get_post_meta( $theme_id, 'sgg_preview_image_type' ) ) ? 'true' : 'false' ) . ";";
		$script .= "var MOBILE_HEADER_HEIGHT = '" . staggs_get_post_meta( $theme_id, 'sgg_configurator_fixed_header_height' ) . "';";
		$script .= "var REQUIRED_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $required_message ) ) ) . "';";
		$script .= "var INVALID_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $invalid_message ) ) ) . "';";
		$script .= "var REQUIRED_FIELD_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $required_field_message ) ) ) . "';";
		$script .= "var INVALID_FIELD_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $invalid_field_message ) ) ) . "';";
		$script .= "var EMPTY_SUMMARY_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $summary_message ) ) ) . "';";
		$script .= "var SINGLE_ERROR_MESSAGE = " . ( staggs_get_theme_option( 'sgg_product_show_individual_error_messages' ) ? 'false' : 'true' ) . ";";
		$script .= "var SUMMARY_SHOW_NOTES = " . ( ( staggs_get_post_meta( $theme_id, 'sgg_configurator_summary_include_notes' ) ) ? 'true' : 'false' ) . ";";
		$script .= "var SUMMARY_SINGLE_TITLE = " . ( ( staggs_get_theme_option( 'sgg_product_summary_hide_duplicate_titles' ) ) ? 'true' : 'false' ) . ";";
		$script .= "var DISABLE_URL_CLICK = " . ( staggs_get_theme_option( 'sgg_product_url_option_disable_click' ) ? 'true' : 'false' ) . ";";
		$script .= "var COPY_NOTICE_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $share_notice_label ) ) ) . "';";
		$script .= "var COPY_NOTICE_BUTTON_TEXT = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $share_notice_button ) ) ) . "';";
		$script .= "var COPY_NOTICE_BUTTON_COPIED = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $share_notice_copied ) ) ) . "';";
		$script .= "var WISHLIST_PAGE_URL = '" . $wishlist_notice_page_url . "';";
		$script .= "var WISHLIST_NOTICE_MESSAGE = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $wishlist_notice_label ) ) ) . "';";
		$script .= "var VIEW_WISHLIST_BUTTON_TEXT = '" . sgg__( sprintf( '%s', str_replace( "'", "\'", $wishlist_notice_button ) ) ) . "';";
		$script .= "var STEPPER_DISABLE_SCROLL_TOP = " . ( staggs_get_post_meta( $theme_id, 'sgg_configurator_step_disable_scroll_top' ) ? 'true' : 'false' ) . ";";
		$script .= "var DISABLE_MESSAGE_WRAPPER = " . ( staggs_get_post_meta( $theme_id, 'sgg_configurator_disable_floating_notice' ) ? 'true' : 'false' ) . ";";
		$script .= "var SGG_LOADER_ICON = '" . $sgg_loader_icon . "';";
		$script .= "var PRODUCT_WEIGHT_UNIT = '" . $weight_unit . "';";
		$script .= "var PRODUCT_WEIGHT = " . $weight_value . ";";

		if ( ( is_plugin_active( 'breakdance/plugin.php' ) || wp_is_block_theme() ) && $sgg_is_shortcode ) {
			echo wp_kses_normalize_entities(  sprintf('<script type="text/javascript">%s</script>', $script ) );
		} else {
			return $script;
		}
	}

	/**
	 * Loads the main template for the configurator.
	 *
	 * @since    1.0.0
	 */
	public function load_main_template( $templates, $template_name ) {
		// Capture/cache the $template_name which is a file name like single-product.php
		wp_cache_set( 'staggs_wc_main_template', $template_name ); // cache the template name
		return $templates;
	}

	/**
	 * Utilizes the main configurator template when applicable for the given product.
	 *
	 * @since    1.0.0
	 */
	public function include_main_template( $template ) {
		// Check if configurable product
		$is_configurable = ( get_post_meta( get_the_ID(), 'is_configurable', true ) === 'yes' );
		if ( ! $is_configurable ) {
			return $template;
		}

		// Don't override default WooCommerce template.
		if ( staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_disable_template_override' ) ) {
			return $template;
		}

		// Custom template. No shortcode.
		global $sgg_is_shortcode;
		$sgg_is_shortcode = false;

		if ( $template_name = wp_cache_get( 'staggs_wc_main_template' ) ) {
			wp_cache_delete( 'staggs_wc_main_template' ); // delete the cache

			if ( $file = file_exists( STAGGS_BASE . 'woocommerce/' . $template_name ) ) {
				return apply_filters( 'staggs_template_main', STAGGS_BASE . 'woocommerce/' . $template_name );
			}
		} 

		return $template;
	}

	/**
	 * Utilizes the main configurator template when applicable for the given post type.
	 *
	 * @since    1.5.3
	 */
	public function include_single_product_template( $template ) {
		if ( 'sgg_product' !== get_post_type() ) {
			return $template;
		}

		$template_name = 'single-product.php';
		if ( file_exists( STAGGS_BASE . 'woocommerce/' . $template_name ) ) {
			return apply_filters( 'staggs_template_main', STAGGS_BASE . 'woocommerce/' . $template_name );
		}

		return $template;
	}

	/**
	 * Check if the provided shortcode can be used.
	 * 
	 * @since 1.5.0
	 */
	private function can_use_shortcode( $atts ) {
		// Run shortcode check so we shortcode.
		global $sgg_is_shortcode;
		$sgg_is_shortcode = false;

		// Admin page.
		if ( is_admin() ) {
			return array( 'valid' => false, 'note' => '' );
		}

		// Admin update request.
		if ( strpos( $_SERVER['REQUEST_URI'], '/wp-json/' ) !== false ) {
			return array( 'valid' => false, 'note' => '' );
		}

		// Not on product page and no product ID set.
		if ( ! isset( $atts['product_id'] ) && 'product' !== get_post_type() && 'sgg_product' !== get_post_type() ) {
			return array(
				'valid' => false,
				'note' => __( 'Please provide a product ID using the "product_id" option or output the shortcode on a product page.', 'staggs')
			);
		}

		// Check if current or provided product is a configurable product.
		global $sgg_shortcode_id;
		$sgg_shortcode_id = isset( $atts['product_id'] ) ? sanitize_key( $atts['product_id'] ) : get_the_ID();
		$sgg_is_shortcode = true;

		if ( ! product_is_configurable( $sgg_shortcode_id ) && 'sgg_product' !== get_post_type( $sgg_shortcode_id ) ) {
			return array(
				'valid' => false,
				'note' => __( 'This shortcode can only be used for configurable products. Make sure you have checked the box "Enable Staggs Product Configurator".', 'staggs')
			);
		}

		return array( 'valid' => true, 'note' => '' );
	}
}