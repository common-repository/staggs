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

global $sgg_fs, $post, $product, $sanitized_steps;

if ( 'sgg_product' === get_post_type() ) {
	$product = get_post( get_the_ID() );
} else {
	$product = wc_get_product( get_the_ID() );
}

/**
 * Default Header.
 */
get_header( 'shop' );
?>

<div class="staggs-configurator staggs-configurator-contained">

	<?php
		/**
		 * Hook: staggs_before_single_product.
		 *
		 * @hooked -
		 */
		do_action( 'staggs_before_single_product' );
	?>

	<?php
		/**
		* Hook: woocommerce_before_single_product.
		*
		* @hooked woocommerce_output_all_notices - 10
		*/
		do_action( 'woocommerce_before_single_product' );
	?>

	<div id="product-<?php the_ID(); ?>" <?php staggs_product_class( '', $product ); ?>>

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
			* @hooked staggs_output_options_form - 20
			*/
			do_action( 'staggs_before_single_product_options' );
		?>

		<div class="option-group intro">
			<div class="option-group-content">

				<?php
					/**
					 * Hook: staggs_single_product_summary.
					*
					* @hooked woocommerce_template_single_title - 5
					* @hooked woocommerce_template_single_rating - 10
					* @hooked woocommerce_template_single_price - 20
					* @hooked woocommerce_template_single_excerpt - 30
					* @hooked woocommerce_template_single_meta - 40
					* @hooked woocommerce_template_single_sharing - 50
					*/
					do_action( 'staggs_single_product_summary' );
				?>

			</div>
		</div>

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
			 * Hook: staggs_single_product_options_totals
			*
			* @hooked staggs_output_options_cart_button - 10
			* @hooked staggs_output_options_usps - 20
			* @hooked staggs_output_options_credit - 30
			*/
			do_action( 'staggs_single_product_options_totals' );
		?>

		<?php
			/**
			 * Hook: staggs_after_single_product_options.
			*
			* @hooked staggs_output_options_wrapper_close - 20
			*/
			do_action( 'staggs_after_single_product_options' );
		?>

	</div>

	<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

	<?php
		/**
		 * Hook: staggs_after_single_product_content.
		 *
		 * @hooked staggs_output_content_wrapper_close - 10
		 */
		do_action( 'staggs_after_single_product_content' );
	?>

	<?php
		/**
		 * Hook: woocommerce_after_single_product.
		 *
		 * @hooked -
		 */
		do_action( 'woocommerce_after_single_product' );
	?>

	<?php
		/**
		 * Hook: staggs_after_single_product.
		 *
		 * @hooked -
		 */
		do_action( 'staggs_after_single_product' );
	?>

</div>

<?php
/**
 * Default Footer.
 */
get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
