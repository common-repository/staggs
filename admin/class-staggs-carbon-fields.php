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

use Carbon_Fields\Container;
use Carbon_Fields\Field;

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
class Staggs_Carbon_Fields {

	/**
	 * Spin up the Carbon Fields library.
	 *
	 * @since    1.0.0
	 */
	public function sgg_load() {
		if ( ! staggs_should_load_fields() ) {
			return false;
		}

		// load fields.
		require_once( STAGGS_BASE . 'vendor/autoload.php' );
		\Carbon_Fields\Carbon_Fields::boot();
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Empty
	}

	/**
	 * Registers the Carbon Fields for our Custom Taxonomy Configurator Steps.
	 *
	 * @since    1.0.0
	 */
	public function sgg_appearance_page_options() {

		if ( ! staggs_should_load_fields() ) {
			return false;
		}
		require_once STAGGS_BASE . '/admin/fields/carbon/settings.php';

	}

	/**
	 * Registers the Carbon Fields for our Custom Post Type Attribute
	 *
	 * @since    1.5.3
	 */
	public function sgg_product_fields() {

		if ( ! staggs_should_load_fields() ) {
			return false;
		}
		require_once STAGGS_BASE . '/admin/fields/carbon/product.php';

	}

	/**
	 * Registers the Carbon Fields for our Custom Post Type Attribute
	 *
	 * @since    1.4.0
	 */
	public function sgg_attribute_fields() {

		if ( ! staggs_should_load_fields() ) {
			return false;
		}
		require_once STAGGS_BASE . '/admin/fields/carbon/attribute.php';

	}

	/**
	 * Registers the Carbon Fields for our Custom Taxonomy Configurator Steps.
	 *
	 * @since    1.0.0
	 */
	public function sgg_init_product_configurator_options() {

		if ( ! staggs_should_load_fields() ) {
			return false;
		}
		require_once STAGGS_BASE . '/admin/fields/carbon/builder.php';

		require_once STAGGS_BASE . '/admin/fields/carbon/theme.php';

	}
}
