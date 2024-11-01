<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) {
			load_plugin_textdomain(
				'staggs',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/pro/languages/'
			);
		} else {
			load_plugin_textdomain(
				'staggs',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);
		}
	}
}
