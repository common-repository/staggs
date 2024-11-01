<?php

/**
 * The admin-specific ACF functionality of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.5.0
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

/**
 * The admin-specific ACF functionality of the plugin.
 *
 * Defines the ACF fields
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_ACF_Fields {

	/**
	 * Registers the settings options page
	 *
	 * @since    1.5.0
	 */
	public function sgg_register_settings_page() {

        acf_add_options_sub_page(array(
			'page_title'  => __('Staggs Settings', 'staggs'),
			'menu_title'  => __('Settings', 'staggs'),
			'menu_slug'   => 'acf-options-settings',
			'parent_slug' => 'edit.php?post_type=sgg_attribute',
			'capability'  => 'edit_posts'
        ));

	}

	/**
	 * Registers the ACF fields groups.
	 *
	 * @since    1.5.0
	 */
	public function sgg_load_field_groups() {

		require STAGGS_BASE . '/admin/fields/acf/settings.php';
		require STAGGS_BASE . '/admin/fields/acf/attribute.php';
		require STAGGS_BASE . '/admin/fields/acf/builder.php';
		require STAGGS_BASE . '/admin/fields/acf/theme.php';

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			require STAGGS_BASE . '/admin/fields/acf/product.php';
		}

	}
}
