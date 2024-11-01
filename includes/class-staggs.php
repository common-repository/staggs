<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */
class Staggs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Staggs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'STAGGS_VERSION' ) ) {
			$this->version = STAGGS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'staggs';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Staggs_Loader. Orchestrates the hooks of the plugin.
	 * - Staggs_i18n. Defines internationalization functionality.
	 * - Staggs_Admin. Defines all hooks for the admin area.
	 * - Staggs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-loader.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/staggs-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-formatter.php';
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-acf.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-attribute.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-theme.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-product.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-forms.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/woocommerce/class-staggs-woocommerce.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/woocommerce/class-staggs-cart.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-staggs-i18n.php';

		/**
		 * The classes responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-migrate.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-admin-order.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-dashboard.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-features.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-analytics-table.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-analytics.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-about.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-acf-fields.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-staggs-carbon-fields.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-staggs-public.php';

		if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) {
			if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'pro/public/staggs-pro-template-functions.php' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'pro/public/staggs-pro-template-functions.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'pro/public/staggs-pro-template-hooks.php';
			}
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/staggs-template-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/staggs-template-hooks.php';

		$this->loader = new Staggs_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Staggs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Staggs_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		/**
		 * Admin hooks.
		 */
		$plugin_admin = new Staggs_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_plugin_admin_notices' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'sgg_add_appearance_submenu_link' );

		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'staggs_admin_header' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'staggs_edit_admin_footer' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_acf_pro_installation' );

		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'staggs_admin_support_and_docs_links', 10, 4 );

		$this->loader->add_action( 'woocommerce_product_options_general_product_data', $plugin_admin, 'add_custom_field_to_simple_products' );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'save_custom_field_to_simple_products' );

		/**
		 * Admin order view
		 */

		$plugin_admin_order = new Staggs_Admin_Order();

		$this->loader->add_filter( 'woocommerce_admin_order_item_thumbnail', $plugin_admin_order, 'filter_admin_order_item_thumbnail', 10, 3 );
		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_admin_order, 'hide_private_order_itemmeta', 10, 1 );

		/**
		 * Plugin Dashboard page.
		 */

		$plugin_dashboard = new Staggs_Dashboard();

		$this->loader->add_action( 'init', $plugin_dashboard, 'redirect_if_top_page' );
		$this->loader->add_action( 'admin_menu', $plugin_dashboard, 'register_sub_menu' );

		/**
		 * Plugin Admin Fields hooks.
		 */

		if ( defined( 'STAGGS_ACF' ) ) {

			/**
			 * Spin up ACF fields
			 */
	
			$plugin_fields = new Staggs_ACF_Fields();

			$this->loader->add_action( 'acf/init', $plugin_fields, 'sgg_register_settings_page' );
			if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) {
				$this->loader->add_action( 'acf/include_fields', $plugin_fields, 'sgg_load_field_groups' );
			}
	
			$plugin_acf = new Staggs_ACF();

			$this->loader->add_action( 'acf/load_field/name=sgg_configurator_view', $plugin_acf, 'sgg_load_radio_image_field' );
			$this->loader->add_action( 'acf/load_field/name=sgg_configurator_popup_type', $plugin_acf, 'sgg_load_radio_image_field' );
			$this->loader->add_action( 'acf/load_field/name=sgg_configurator_theme', $plugin_acf, 'sgg_load_radio_image_field' );
			$this->loader->add_action( 'acf/load_field/name=sgg_configurator_arrows', $plugin_acf, 'sgg_load_radio_image_field' );
			$this->loader->add_action( 'acf/load_field/name=sgg_configurator_usp_location', $plugin_acf, 'sgg_load_radio_image_field' );
			$this->loader->add_action( 'acf/load_field/name=sgg_configurator_step_indicator', $plugin_acf, 'sgg_load_radio_image_field' );
			$this->loader->add_action( 'acf/load_field/name=sgg_pdf_layout', $plugin_acf, 'sgg_load_radio_image_field' );

			$this->loader->add_action( 'acf/load_field/name=sgg_analytics_order_statusses', $plugin_acf, 'sgg_load_woocommerce_order_statusses' );
	
			$this->loader->add_action( 'acf/load_field/name=sgg_product_configurator_theme_id', $plugin_acf, 'sgg_load_staggs_themes' );
			$this->loader->add_action( 'acf/load_field/name=sgg_step_attribute', $plugin_acf, 'sgg_load_staggs_attributes' );

		}

		if ( ! defined( 'STAGGS_ACF' ) || defined( 'STAGGS_RUN_MIGRATE' ) ) {

			/**
			 * Load in default fields.
			 */
	
			if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) {

				$plugin_fields = new Staggs_Carbon_Fields();
	
				$this->loader->add_action( 'after_setup_theme', $plugin_fields, 'sgg_load' );
				$this->loader->add_action( 'carbon_fields_register_fields', $plugin_fields, 'sgg_attribute_fields' );
				$this->loader->add_action( 'carbon_fields_register_fields', $plugin_fields, 'sgg_appearance_page_options' );
				$this->loader->add_action( 'carbon_fields_register_fields', $plugin_fields, 'sgg_init_product_configurator_options' );

				if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
					$this->loader->add_action( 'carbon_fields_register_fields', $plugin_fields, 'sgg_product_fields' );
				}

			}
		}

		/**
		 * Product Attribute hooks.
		 */

		$plugin_attribute = new Staggs_Attribute();

		$this->loader->add_action( 'init', $plugin_attribute, 'register' );
		$this->loader->add_action( 'init', $plugin_attribute, 'register_tag' );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_attribute, 'add_taxonomy_filters' );
		$this->loader->add_filter( 'manage_sgg_attribute_posts_columns', $plugin_attribute, 'add_attribute_columns' );
		$this->loader->add_action( 'manage_sgg_attribute_posts_custom_column', $plugin_attribute, 'fill_attribute_columns', 10, 2 );
		$this->loader->add_action( 'pre_get_posts', $plugin_attribute, 'filter_admin_attribute_table_results' );
		$this->loader->add_action( 'save_post_sgg_attribute', $plugin_attribute, 'clear_transients' );
		$this->loader->add_action( 'save_post_sgg_product', $plugin_attribute, 'clear_builder_transients' );
		$this->loader->add_action( 'save_post_product', $plugin_attribute, 'clear_builder_transients' );

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			/**
			 * Product configurator hooks.
			 */

			$plugin_product = new Staggs_Product();

			$this->loader->add_action( 'init', $plugin_product, 'register' );

		}

		/**
		 * Product Attribute hooks.
		 */

		$plugin_theme = new Staggs_Theme();

		$this->loader->add_action( 'init', $plugin_theme, 'register' );
		$this->loader->add_action( 'save_post_sgg_theme', $plugin_theme, 'clear_transients' );
		$this->loader->add_filter( 'manage_sgg_theme_posts_columns', $plugin_theme, 'add_theme_columns' );
		$this->loader->add_action( 'manage_sgg_theme_posts_custom_column', $plugin_theme, 'fill_theme_columns', 10, 2 );

		/**
		 * Plugin Analytics page.
		 */

		$plugin_analytics = new Staggs_Analytics();

		$this->loader->add_filter( 'set-screen-option', 'Staggs_Analytics', 'set_screen', 10, 3 );
		$this->loader->add_action( 'admin_menu', $plugin_analytics, 'register_sub_menu', 99 );

		$this->loader->add_action( 'admin_post_sgg_generate_analytics_export', $plugin_analytics, 'generate_analytics_csv_report' );

		// Modify WooCommerce admin order table.
		$this->loader->add_action( 'pre_get_posts', $plugin_analytics, 'filter_woocommerce_order_overview_table' );

		/**
		 * Plugin PRO features page.
		*/

		$plugin_features = new Staggs_Features();

		$this->loader->add_action( 'admin_menu', $plugin_features, 'register_sub_menu' );

		/**
		 * Plugin About page.
		*/

		$plugin_about = new Staggs_About( $this->version );

		$this->loader->add_action( 'admin_menu', $plugin_about, 'register_sub_menu' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/**
		 * Plugin Public hooks.
		 */

		$plugin_public = new Staggs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes', 99 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 99 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'enqueue_font_scripts' );

		$this->loader->add_filter( 'woocommerce_template_loader_files', $plugin_public, 'load_main_template', 10, 2 );
		$this->loader->add_filter( 'template_include', $plugin_public, 'include_main_template', 11 );
		$this->loader->add_filter( 'single_template', $plugin_public, 'include_single_product_template', 11 );
		$this->loader->add_filter( 'body_class', $plugin_public, 'set_body_configurator_class' );

		/**
		 * Plugin WooCommerce hooks.
		 */

		$plugin_woocommerce = new Staggs_WooCommerce();

		$this->loader->add_action( 'wp', $plugin_woocommerce, 'modify_configurable_product_hooks', 99 );
		$this->loader->add_action( 'staggs_modify_wc_hooks', $plugin_woocommerce, 'modify_configurable_product_hooks', 99 );

		$this->loader->add_filter( 'woocommerce_product_tabs', $plugin_woocommerce, 'clear_default_product_tabs_array', 99 );
		$this->loader->add_action( 'staggs_after_single_product_options', $plugin_woocommerce, 'staggs_product_tabs_output', 10 );

		$this->loader->add_filter( 'woocommerce_quantity_input_min', $plugin_woocommerce, 'staggs_set_min_product_quantity' );
		$this->loader->add_filter( 'woocommerce_quantity_input_max', $plugin_woocommerce, 'staggs_set_max_product_quantity' );

		$this->loader->add_filter( 'woocommerce_product_add_to_cart_text', $plugin_woocommerce, 'staggs_change_add_to_cart_text', 10, 2 );
		$this->loader->add_filter( 'woocommerce_before_add_to_cart_button', $plugin_woocommerce, 'staggs_before_add_to_cart' ); 
		$this->loader->add_filter( 'woocommerce_product_single_add_to_cart_text', $plugin_woocommerce, 'staggs_single_add_to_cart_text' ); 
		$this->loader->add_filter( 'woocommerce_product_add_to_cart_url', $plugin_woocommerce, 'modify_configurable_product_add_to_cart_link', 99, 2 );
		$this->loader->add_filter( 'woocommerce_product_supports', $plugin_woocommerce, 'modify_configurable_product_supports', 99, 3 );
		$this->loader->add_filter( 'woocommerce_get_price_html', $plugin_woocommerce, 'add_shop_price_prefix', 99, 2 );
		$this->loader->add_filter( 'woocommerce_product_get_regular_price', $plugin_woocommerce, 'filter_woocommerce_configurator_product_price', 99, 2 );

		/**
		 * Plugin Forms hooks.
		 */
		$plugin_forms = new Staggs_Forms();

		$this->loader->add_action( 'wpcf7_form_tag', $plugin_forms, 'wpcf7_fill_form_values', 10, 2 );
		$this->loader->add_action( 'ninja_forms_render_default_value', $plugin_forms, 'na_fill_form_values', 10, 3 );
		$this->loader->add_filter( 'wpforms_field_data', $plugin_forms, 'wpf_fill_form_values', 10, 3 );

		/**
		 * Plugin WooCommerce cart hooks.
		 */

		$plugin_cart = new Staggs_Cart();

		$this->loader->add_action( 'wp_ajax_add_product_to_cart', $plugin_cart, 'staggs_add_product_to_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_add_product_to_cart', $plugin_cart, 'staggs_add_product_to_cart' );

		$this->loader->add_action( 'woocommerce_remove_cart_item', $plugin_cart, 'staggs_remove_all_bundle_products', 10, 2 );

		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_cart, 'filter_add_to_cart_link', 20, 3 );
		$this->loader->add_filter( 'woocommerce_add_to_cart_fragments', $plugin_cart, 'staggs_add_to_cart_fragments' );
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_cart, 'staggs_save_product_data', 20, 2 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_cart, 'staggs_render_data_on_cart_checkout', 20, 2 );
		$this->loader->add_filter( 'woocommerce_cart_item_thumbnail', $plugin_cart, 'staggs_set_product_thumbnail', 20, 3 );

		if ( ! sgg_fs()->is_plan_or_trial( 'professional' ) ) {
			$this->loader->add_filter( 'woocommerce_cart_item_permalink', $plugin_cart, 'staggs_modify_cart_link' , 20, 3 );
		}

		$this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_cart, 'display_product_image_in_checkout', 20, 3 );
		$this->loader->add_filter( 'woocommerce_order_item_name', $plugin_cart, 'display_product_image_in_order_details', 20, 3 );
		$this->loader->add_filter( 'woocommerce_email_order_items_args', $plugin_cart, 'display_product_image_in_woocommerce_mails' );
		$this->loader->add_filter( 'woocommerce_order_item_thumbnail', $plugin_cart, 'replace_product_image_in_order_emails', 20, 2 );

		$this->loader->add_action( 'woocommerce_thankyou', $plugin_cart, 'delete_images_on_thankyou_page', 20, 1 );

		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_cart, 'staggs_save_order_meta', 20, 4 );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_cart, 'staggs_update_order_meta', 20, 1 );

		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_cart, 'staggs_set_price_for_cart_item', 20 );
		$this->loader->add_filter( 'woocommerce_cart_totals_get_item_tax_rates', $plugin_cart, 'apply_custom_cart_item_tax_rate', 20, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Staggs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
