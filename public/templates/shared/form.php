<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.7
 *
 * @package    Staggs
 * @subpackage Staggs/public/templates/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	/**
	 * Hook: staggs_before_single_product_options.
	 *
	 * @hooked staggs_output_options_wrapper - 10
	 * @hooked staggs_output_options_form - 20
	 */
	do_action( 'staggs_before_single_product_options' );
?>

<?php
	/**
	 * Hook: staggs_single_product_options
	*
	* @hooked staggs_output_single_product_options - 10
	* @hooked staggs_output_options_form_close - 20
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
