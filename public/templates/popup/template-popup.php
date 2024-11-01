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

global $is_popup;
$is_popup = true;

$popup_layout = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_popup_type' );
$popup_mobile_class = '';
if ( staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_popup_mobile_inline' ) ) {
	$popup_mobile_class = ' show-popup-mobile-inline';
}
if ( ! $popup_layout ) {
	$popup_layout = 'vertical';
}
?>
<div class="staggs-configurator staggs-configurator-popup popup-<?php echo esc_attr( $popup_layout ); ?><?php echo esc_attr( $popup_mobile_class ); ?>">

	<?php
		/**
		 * Hook: staggs_before_single_product_content.
		 *
		 * @hooked staggs_output_content_wrapper - 20
		 */
		do_action( 'staggs_before_single_product_content' );
	?>

	<?php
		/**
		 * Hook: staggs_single_product_popup_topbar.
		 *
		 * @hooked staggs_single_product_popup_topbar - 20
		 */
		do_action( 'staggs_single_product_popup_topbar' );
	?>

	<?php
		/**
		 * Shared template: gallery
		 *
		 */
		require STAGGS_BASE . 'public/templates/shared/gallery.php';
	?>

	<?php
		/**
		 * Hook: staggs_before_single_product_options.
		 *
		 * @hooked staggs_output_options_wrapper - 10
		 */
		do_action( 'staggs_before_single_product_options' );
	?>

	<?php
		/**
		 * Hook: staggs_single_product_options
		 *
		 * @hooked -
		 */
		do_action( 'staggs_single_product_options' );
	?>

	<?php
		/**
		 * Hook: staggs_after_single_product_options.
		 *
		 * @hooked staggs_output_options_wrapper_close - 20
		 */
		do_action( 'staggs_after_single_product_options' );
	?>

	<?php
		/**
		 * Hook: staggs_single_product_popup_bottom_bar.
		 *
		 * @hooked staggs_single_product_popup_bottom_bar - 20
		 */
		do_action( 'staggs_single_product_popup_bottom_bar' );
	?>

	<?php
		/**
		 * Hook: staggs_after_single_product_content.
		 *
		 * @hooked staggs_output_content_wrapper_close - 10
		 */
		do_action( 'staggs_after_single_product_content' );
	?>

</div>

<?php

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
