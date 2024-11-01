<?php

/**
 * Provide a public-facing view for the option group post type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.5.2
 *
 * @package    Staggs
 * @subpackage Staggs/public/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $sanitized_step, $density, $text_align, $is_horizontal_popup;

$density    = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_step_density' );
$text_align = staggs_get_post_meta( staggs_get_theme_id(), 'sgg_configurator_text_align' );
$style      = isset( $sanitized_step['style'] ) ? $sanitized_step['style'] : 'inherit';

$attributes = ' option-group-tabs';
if ( $density ) {
	$attributes .= ' option-group-' . $density;
}
if ( $text_align ) {
	$attributes .= ' option-group-' . $text_align;
}
if ( $style ) {
	$attributes .= ' border-' . $style;
}
?>

<div id="option-group-<?php echo esc_attr($sanitized_step['id']); ?>" class="option-group<?php echo wp_kses_normalize_entities( $attributes); ?>">
	<div class="option-group-content">

		<?php
			/**
			 * Hook: staggs_single_option_group.
			 *
			 * @hooked staggs_output_option_tab_content - 20
			 */
			do_action( 'staggs_tab_option_group' );
		?>

	</div>
</div>
