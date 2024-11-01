<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Admin {

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
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'crb_upload_image_button_html', array( $this, 'staggs_admin_upload_image_button_html' ) );

		// add_filter( 'pll_get_post_types', array( $this, 'mark_post_types_translatable' ), 20, 2 );
		// add_action( 'admin_init', array( $this, 'register_polylang_setting_strings' ) );
	}

	/**
	 * Marks attributes and themes translatable for Polylang.
	 *
	 * @since    1.8.0
	 */
	public function mark_post_types_translatable($post_types, $hide) {
		$post_types['sgg_attribute'] = 'sgg_attribute';
		$post_types['sgg_theme'] = 'sgg_theme';
		return $post_types;
	}

	/**
	 * Register string translations for setting options.
	 *
	 * @since    1.8.2
	 */
	public function register_polylang_setting_strings() {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		$translation_settings = array(
			'sgg_gallery_fullscreen_label',
			'sgg_gallery_camera_label',
			'sgg_gallery_wishlist_label',
			'sgg_gallery_pdf_label',
			'sgg_gallery_share_label',
			'sgg_gallery_reset_label',
			'sgg_configurator_summary_intro',
			'sgg_configurator_summary_table_title',
			'sgg_configurator_summary_table_total_label',
			'sgg_configurator_summary_table_step_label',
			'sgg_configurator_summary_table_value_label',
			'sgg_configurator_summary_share_link_label',
			'sgg_configurator_summary_share_link_intro',
			'sgg_configurator_summary_share_link_button',
			'sgg_configurator_summary_pdf_label',
			'sgg_configurator_summary_pdf_intro',
			'sgg_configurator_summary_pdf_button',
			'sgg_configurator_summary_mail_link_label',
			'sgg_configurator_summary_mail_link_intro',
			'sgg_configurator_summary_mail_link_placeholder',
			'sgg_configurator_summary_mail_link_button',
			'sgg_product_share_notice_label',
			'sgg_product_share_notice_button',
			'sgg_product_share_notice_copied',
			'sgg_product_wishlist_notice',
			'sgg_product_wishlist_button',
			'sgg_product_wishlist_delete_notice',
			'sgg_product_wishlist_empty_notice',
			'sgg_product_required_error_message',
			'sgg_product_invalid_error_message',
			'sgg_product_required_field_message',
			'sgg_product_invalid_field_message',
			'sgg_account_configurations_page_title',
			'sgg_account_configurations_table_heading',
			'sgg_account_configurations_table_price',
			'sgg_pdf_product_heading',
			'sgg_pdf_options_heading',
			'sgg_pdf_table_step_heading',
			'sgg_pdf_table_value_heading',
			'sgg_pdf_table_price_heading',
			'sgg_pdf_forbidden_option_labels',
			'sgg_option_out_of_stock_message',
			'sgg_product_add_label',
			'sgg_product_remove_label',
			'sgg_product_prefix_label',
			'sgg_product_price_format',
			'sgg_product_loop_cart_label',
			'sgg_step_included_text',
			'sgg_product_tax_label'
		);

		foreach ( $translation_settings as $setting ) {
			if ( '' !== staggs_get_theme_option( $setting ) ) {
				icl_register_string( 'Staggs', $setting, staggs_get_theme_option( $setting ) );
			}
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$script_type = 'cb';
		if ( defined( 'STAGGS_ACF' ) ) {
			$script_type = 'acf';
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/staggs-admin.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '_font', plugin_dir_url( __FILE__ ) . 'css/staggs-font.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_' . $script_type, plugin_dir_url( __FILE__ ) . 'css/staggs-' . $script_type . '-fields.css', array(), $this->version, 'all' );
	
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/staggs-admin.js', array( 'jquery'), $this->version, true );
		wp_enqueue_script( $this->plugin_name . '_' . $script_type, plugin_dir_url( __FILE__ ) . 'js/staggs-admin-' . $script_type . '.js', array( 'jquery'), $this->version, true );
	}

	/**
	 * Add helpful links to plugin listing item row meta
	 * 
	 * @since   1.5.3
	 */
	public function staggs_admin_support_and_docs_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
		if ( strpos( $plugin_file_name, 'staggs.php' ) !== false ) {
			$links_array[] = '<a href="https://staggs.app/docs/" target="_blank">Documentation</a>';
			$links_array[] = '<a href="https://staggs.app/support/" target="_blank">Support</a>';
			$links_array[] = '<a href="https://staggs.app/faq/" target="_blank">FAQ</a>';

			if ( ! sgg_fs()->is_plan( 'professional' ) ) {
				$links_array[] = '<a href="' . admin_url() . '/edit.php?post_type=sgg_attribute&page=staggs-pricing">Go PRO</a>';
			}
		}

		return $links_array;
	}

	/**
	 * Check if ACF PRO is installed and active.
	 *
	 * @since    1.5.0
	 */
	public function check_acf_pro_installation() {
		// Decline button clicked.
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'decline-acf' ) {
			update_option( 'sgg_acf_pro_active', 'disabled' );
		}

		if ( isset( $_GET['migrate'] ) && 'success' === esc_attr( $_GET['migrate'] ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_attr_e( 'Migrate successful! Your Staggs admin fields now consist of ACF fields. Enjoy!', 'staggs' ); ?></p>
			</div>
			<?php
		}

		if ( ! defined( 'ACF_PRO' ) || ! ACF_PRO ) {
			if ( get_option( 'sgg_acf_pro_active' ) ) {
				update_option( 'sgg_acf_pro_active', 'disabled' );
			}
			return;
		}

		if ( ! isset( $_GET['post_type'] ) ) {
			return;
		}

		if ( esc_attr( $_GET['post_type'] ) !== 'sgg_attribute' && esc_attr( $_GET['post_type'] ) !== 'sgg_theme' ) {
			return;
		}

		if ( ! get_option( 'sgg_acf_pro_active' ) ) {
			if ( defined( 'ACF_PRO' ) && ACF_PRO ) {
				$attributes = get_posts( array( 'post_type' => 'sgg_attribute' ) );
				?>
				<div class="notice notice-info notice-staggs is-dismissible">
					<div class="notice-image">
						<img src="<?php echo esc_url( STAGGS_BASE_URL ); ?>admin/img/staggs-acf.png" width="100" height="100" alt="Staggs + ACF">
					</div>
					<div class="notice-message">
						<h2><?php esc_attr_e( 'Staggs + ACF', 'staggs' ); ?></h2>
						<p>
							<?php esc_attr_e( 'We noticed you have ACF PRO installed. Supercharge your Staggs admin interface with the ACF PRO fields structure. <a href="https://staggs.app/staggs-and-acf-pro/" target="_blank">Learn more</a>', 'staggs' ); ?><br>
							<?php
							if ( count( $attributes ) > 0 ) {
								echo '<strong>' . esc_attr__( 'Important! Always make a back-up before migration (just in case something goes south).', 'staggs' ) . '</strong>';
							}
							?>
						</p>
						<div class="notice-buttons">
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=dashboard&action=migrate-acf' ) ); ?>" class="button button-primary"><?php esc_attr_e( 'Migrate to ACF fields', 'staggs' ); ?></a>
							<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=dashboard&action=decline-acf' ) ); ?>" class="button"><?php esc_attr_e( 'No thanks', 'staggs' ); ?></a>
						</div>
					</div>
				</div>
				<?php
				// Migrate button clicked.
				if ( isset( $_GET['action'] ) && $_GET['action'] === 'migrate-acf' ) {
					define( 'STAGGS_ACF', '1' );
					define( 'STAGGS_RUN_MIGRATE', '1' );

					Staggs_Migrate::run();

					update_option( 'sgg_acf_pro_active', 'active' );

					wp_safe_redirect( admin_url( 'edit.php?post_type=sgg_attribute&page=dashboard&migrate=success' ) );
					die();
				}
			}
		}
	}

	/**
	 * Add link to settings page in submenu when CB fields have not been loaded
	 *
	 * @since    1.5.0
	 */
	public function sgg_add_appearance_submenu_link() {
		global $submenu;

		if ( defined( 'STAGGS_ACF' ) ) {
			return;
		}

		if ( ! staggs_should_load_fields() ) {
			$permalink = esc_url( admin_url( 'edit.php' ).'?post_type=sgg_attribute&page=appearance' );
			$submenu['edit.php?post_type=sgg_attribute'][] = array( 'Settings', 'edit_posts', $permalink );	
		}
	}

	/**
	 * Override default upload image button html
	 */
	public function staggs_admin_upload_image_button_html( $upload_image_button ) {
		$upload_image_button = '<a href="#" class="button insert-media add_media" data-editor="<%- id %>" title="' . __( 'Add Media', 'staggs' ) . '">
			<span class="wp-media-buttons-icon"></span> ' . __( 'Add Media', 'staggs' ) . '
		</a>';
		return wp_kses_normalize_entities( $upload_image_button );
	}

	/**
	 * Add filter for the footer copy for the admin area.
	 *
	 * @since    1.2.6
	 */
	public function staggs_edit_admin_footer() {
		global $typenow;
		if ( 'sgg_attribute' !== $typenow && 'sgg_theme' !== $typenow && 'sgg_product' !== $typenow ) {
			return;
		}

		add_filter( 'admin_footer_text', array( $this, 'staggs_edit_admin_footer_text' ) , 11 );
	}

	/**
	 * Customize the footer copy for the admin area.
	 *
	 * @since    1.2.6
	 */
	public function staggs_edit_admin_footer_text( $content ) {
		if ( $content ) {
			return sprintf(
				// translators: brand link
				__( 'Thank you for creating with %1$s! Please rate %2$s to help spread the word!', 'staggs'),
				'<a href="https://staggs.app" target="_blank">Staggs</a>',
				'<a href="https://wordpress.org/support/plugin/staggs/reviews/?filter=5" target="_blank">★★★★★</a>'
			);
			return str_replace( '.', ' ', $content ) . __( 'and', 'staggs') . ' <i><a href="https://staggs.app" target="_blank">Staggs</a></i>.';
		}
	}

	/**
	 * Register the header for the admin area.
	 *
	 * @since    1.2.6
	 */
	public function staggs_admin_header() {
		global $typenow;
		if ( 'sgg_attribute' !== $typenow && 'sgg_theme' !== $typenow && 'sgg_product' !== $typenow ) {
			return;
		}

		$actions = array(
			array(
				'url' => admin_url( 'edit.php?post_type=sgg_attribute&page=dashboard' ),
				'type' => 'sgg_attribute',
				'page' => 'dashboard',
				'text' =>  __( 'Dashboard', 'staggs' )
			),
			array(
				'url' => admin_url( 'edit.php?post_type=sgg_attribute' ),
				'type' => 'sgg_attribute',
				'page' => '',
				'text' =>  __( 'Attributes', 'staggs' )
			)
		);

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$actions[] = array(
				'url' => admin_url( 'edit.php?post_type=sgg_product' ),
				'type' => 'sgg_product',
				'page' => '',
				'text' =>  __( 'Products', 'staggs' )
			);
		}

		$actions[] = array(
			'url' => admin_url( 'edit.php?post_type=sgg_theme' ),
			'type' => 'sgg_theme',
			'page' => '',
			'text' =>  __( 'Templates', 'staggs' )
		);

		if ( defined( 'STAGGS_ACF' ) ) {
			$actions[] = array(
				'url' => admin_url( 'edit.php?post_type=sgg_attribute&page=acf-options-settings' ),
				'type' => 'sgg_attribute',
				'page' => 'acf-options-settings',
				'text' =>  __( 'Settings', 'staggs' )
			);
		} else {
			$actions[] = array(
				'url' => admin_url( 'edit.php?post_type=sgg_attribute&page=appearance' ),
				'type' => 'sgg_attribute',
				'page' => 'appearance',
				'text' =>  __( 'Settings', 'staggs' )
			);
		}

		$actions[] = array(
			'url' => admin_url( 'edit.php?post_type=sgg_attribute&page=analytics' ),
			'type' => 'sgg_attribute',
			'page' => 'analytics',
			'text' =>  __( 'Analytics', 'staggs' )
		);

		if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) {
			$actions[] = array(
				'url' => admin_url( 'edit.php?post_type=sgg_attribute&page=compare_features' ),
				'type' => 'sgg_attribute',
				'page' => 'compare_features',
				'text' =>  __( 'Free vs PRO', 'staggs' )
			);
		}
		?>
		<div class="staggs-admin-header">
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=sgg_attribute&page=about' ) ); ?>" class="staggs-admin-logo">
				<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'img/admin-icon.png' ); ?>" height="40" width="40" alt="<?php echo esc_attr( $this->plugin_name ); ?>">
			</a>
			<ul class="staggs-admin-actions">
				<?php
				foreach ( $actions as $key => $action ) {
					$class = '';
					if ( strpos( $action['url'], $_SERVER['REQUEST_URI'] ) !== false ) {
						if ( isset( $_GET['page'] ) ) {
							if ( $_GET['page'] == $action['page'] ) {
								$class = ' class="active"';
							}
						} else if ( isset( $_GET['post_type'] ) ) {
							if ( $_GET['post_type'] == $action['type'] && $action['page'] === '' ) {
								$class = ' class="active"';
							}
						}
					}
					if ( $key === 0 && ( isset( $_GET['s'] ) || isset( $_GET['filter_action'] ) ) && ! isset( $_GET['taxonomy'] ) ) {
						$class = ' class="active"';
					}
					?>
					<li>
						<a href="<?php echo esc_url( $action['url'] ); ?>"<?php echo wp_kses_post( $class ); ?>><?php echo esc_attr( $action['text'] ); ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			<div class="staggs-admin-info">
				<a href="<?php echo esc_url( admin_url( '/edit.php?post_type=sgg_attribute&page=about' ) ); ?>">
					<div class="staggs-version">
						v<?php echo esc_attr( $this->version ); ?>
					</div>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Show plugin notices.
	 *
	 * @since 1.2.6
	 */
	public function show_plugin_admin_notices() {
		if ( $notices = get_option('staggs_admin_notices') ) {
			foreach ( $notices as $notice ) {
				echo '<div class="notice notice-' . esc_attr( $notice['type'] ) . ' is-dismissible"><p>' . esc_attr( $notice['text'] ) . '</p></div>';
			}
			delete_option('staggs_admin_notices');
		}
	}

	/**
	 * Registers the Configurator Checkbox Field for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_custom_field_to_simple_products() {
		global $post;

		echo '<div class="options_group show_if_simple">';

		woocommerce_wp_checkbox(
			array(
				'id'            => 'is_configurable',
				'name'          => 'is_configurable',
				'class'         => 'is_configurable',
				'label'         => __( 'Configurator', 'staggs' ),
				'value'         => get_post_meta( $post->ID, 'is_configurable', true ),
				'description'   => __( 'Enable Staggs Product Configurator', 'staggs' ),
				'wrapper_class' => 'form-row',
			)
		);

		echo '</div>';
	}

	/**
	 * Saves the Configurator Checkbox Field for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function save_custom_field_to_simple_products( $post_id ) {
		$is_configurable = isset( $_POST['is_configurable'] ) ? sanitize_text_field( $_POST['is_configurable'] ) : '';
		update_post_meta( $post_id, 'is_configurable', $is_configurable );
	}

}
