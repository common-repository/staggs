<?php
/**
 * The Template for displaying all single products
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $sanitized_steps;
$sanitized_steps = Staggs_Formatter::get_formatted_step_content( get_the_ID() );

// Get active theme dir.
$dir = trailingslashit( get_stylesheet_directory() );

// Define location var.
$location = '';
$theme_id = staggs_get_theme_id();

if ( ( 'sgg_product' === get_post_type() || product_is_configurable( get_the_ID() ) ) && $theme_id ) {
	// Configurable product template and not Staggs Popup template.
	$template = staggs_get_configurator_page_template( $theme_id );
	$view_layout = staggs_get_configurator_view_layout( $theme_id );

	if ( 'staggs' === $template && 'popup' !== $view_layout ) {
		// Configurator ID selected, spin up the plugin configuration screen.
		$location = trailingslashit( STAGGS_BASE ) . 'public/templates/staggs-public-display.php';
	}
}

if ( '' === $location && class_exists( 'WooCommerce' ) ) {
	// No Staggs template match.
	if ( file_exists( $dir . WC()->template_path() . 'single-product.php' ) ) {
		// Check if template is overridden in active theme.
		$location = $dir . WC()->template_path() . 'single-product.php';
	} else {
		// No templates. Use base template.
		$location = trailingslashit( ABSPATH . WP_PLUGIN_DIR ) . 'woocommerce/templates/single-product.php';
	}
}

// Allow template overrides.
$location = apply_filters( 'staggs_template_location', $location );

if ( $location ) {
	include $location;
}

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
