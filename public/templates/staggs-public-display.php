<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.0.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $sgg_fs, $post, $product;

if ( 'sgg_product' === get_post_type() ) {
	$product = get_post( get_the_ID() );
} else {
	$product = wc_get_product( get_the_ID() );
}

$wrapper_class = ' staggs-configurator';
$view_layout   = sanitize_text_field( staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_view' ) );

if ( file_exists( STAGGS_BASE . '/public/templates/page/template-' . $view_layout . '.php' ) ) {

	include STAGGS_BASE . '/public/templates/page/template-' . $view_layout . '.php';

} else if ( file_exists( STAGGS_BASE . '/public/templates/popup/template-' . $view_layout . '.php' ) ) {

	include STAGGS_BASE . '/public/templates/popup/template-' . $view_layout . '.php';

} else if ( file_exists( STAGGS_BASE . '/pro/public/templates/page/template-' . $view_layout . '.php' ) ) {

	include STAGGS_BASE . '/pro/public/templates/page/template-' . $view_layout . '.php';

} else {

	include STAGGS_BASE . '/public/templates/page/template-classic.php';

}

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
