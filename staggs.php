<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://staggs.app/
 * @since             1.0.0
 * @package           Staggs
 *
 * @wordpress-plugin
 * Plugin Name: 	  Staggs - Product Configurator Toolkit
 * Plugin URI:        https://wordpress.org/plugins/staggs/
 * Description:       A complete toolkit to build stunning product configurators in WordPress and WooCommerce. Boost sales and increase user engagement.
 * Version:           2.1.0
 * Author:            Staggs
 * Author URI:        https://staggs.app/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       staggs
 * Domain Path:       /languages
 * 
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( function_exists( 'sgg_fs' ) ) {
    sgg_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'sgg_fs' ) ) {
        // Create a helper function for easy SDK access.
        function sgg_fs() {
            global $sgg_fs;
            if ( !isset( $sgg_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $sgg_fs = fs_dynamic_init( array(
                    'id'             => '11184',
                    'slug'           => 'staggs',
                    'premium_slug'   => 'staggs-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_e909215d9579f064c2f55a91cbfe4',
                    'is_premium'     => false,
                    'premium_suffix' => '(PRO)',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 14,
                        'is_require_payment' => false,
                    ),
                    'menu'           => array(
                        'slug'       => 'edit.php?post_type=sgg_attribute',
                        'first-path' => 'edit.php?post_type=sgg_attribute&page=about',
                        'contact'    => false,
                        'support'    => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $sgg_fs;
        }

        // Init Freemius.
        sgg_fs();
        // Signal that SDK was initiated.
        do_action( 'sgg_fs_loaded' );
    }
    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define( 'STAGGS_VERSION', '2.1.0' );
    define( 'STAGGS_BASE', plugin_dir_path( __FILE__ ) );
    define( 'STAGGS_BASE_URL', plugin_dir_url( __FILE__ ) );
    if ( 'active' == get_option( 'sgg_acf_pro_active' ) ) {
        define( 'STAGGS_ACF', 'active' );
    } else {
        $staggs_cb_url = apply_filters( 'staggs_carbon_fields_url', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'vendor/htmlburger/carbon-fields/' );
        define( 'Carbon_Fields\\URL', $staggs_cb_url );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-staggs-activator.php
     */
    function activate_staggs() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-staggs-activator.php';
        Staggs_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-staggs-deactivator.php
     */
    function deactivate_staggs() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-staggs-deactivator.php';
        Staggs_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'activate_staggs' );
    register_deactivation_hook( __FILE__, 'deactivate_staggs' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-staggs.php';
    if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) {
        if ( file_exists( plugin_dir_path( __FILE__ ) . 'pro/includes/class-staggs-pro.php' ) ) {
            require plugin_dir_path( __FILE__ ) . 'pro/includes/class-staggs-pro.php';
        }
    }
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_staggs() {
        $plugin = new Staggs();
        $plugin->run();
        if ( sgg_fs()->is_plan_or_trial( 'professional' ) && class_exists( 'Staggs_PRO' ) ) {
            $plugin_pro = new Staggs_PRO();
            $plugin_pro->run();
        }
    }

    run_staggs();
}