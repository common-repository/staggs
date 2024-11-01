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

global $sgg_is_shortcode;
if ( $sgg_is_shortcode ) {
	echo '<div class="staggs-message-wrapper"></div>';
}
?>

<div class="staggs-product-options">
	<div class="staggs-options-totals option-group-wrapper">

		<?php
			/**
			 * Hook: staggs_single_product_options_totals
			 *
			 * @hooked staggs_output_options_cart_button - 10
			 * @hooked staggs_output_options_usps - 20
			 * @hooked staggs_output_options_credit - 30
			 */
			do_action( 'staggs_single_product_options_totals' );
		?>

	</div>
</div>