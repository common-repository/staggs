<?php

/**
 * Provide a public-facing view for the option group post type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.1.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $sanitized_step, $density, $text_align, $is_horizontal_popup;

$style   = isset( $sanitized_step['style'] ) ? $sanitized_step['style'] : 'inherit';
$hidden  = isset( $sanitized_step['hidden'] ) ? $sanitized_step['hidden'] : false;
$classes = isset( $sanitized_step['classes'] ) ? $sanitized_step['classes'] : '';

$attributes = '';
if ( $density ) {
	$attributes .= ' option-group-' . $density;
}
if ( $text_align ) {
	$attributes .= ' option-group-' . $text_align;
}
if ( $style ) {
	$attributes .= ' border-' . $style;
}
if ( $hidden ) {
	$attributes .= ' always-hidden';
}
if ( isset( $sanitized_step['required'] ) && 'yes' == $sanitized_step['required'] ) {
	$attributes .= ' option-group-required';
}
if ( '' !== $classes ) {
	$attributes .= ' ' . $classes;
}

if ( ! $is_horizontal_popup && isset( $sanitized_step['collapsible'] ) ) {
	if ( $sanitized_step['collapsible'] ) {
		$attributes .= ' collapsible';
	}
	if ( $sanitized_step['collapsible_state'] ) {
		$attributes .= ' ' . $sanitized_step['collapsible_state'];
	}
}

if ( sgg_fs()->is_plan_or_trial( 'professional' ) ) {
	if ( $sanitized_step['is_conditional'] ) {
		$attributes .= ' conditional';
	}
	if ( isset( $sanitized_step['model_group'] ) && '' !== $sanitized_step['model_group'] ) {
		$attributes .= '" data-model="' . $sanitized_step['model_group'];
	}
	if ( isset( $sanitized_step['model_metalness'] ) && '' !== $sanitized_step['model_metalness'] ) {
		$attributes .= '" data-metalness="' . $sanitized_step['model_metalness'];
	}
	if ( isset( $sanitized_step['model_roughness'] ) && '' !== $sanitized_step['model_roughness'] ) {
		$attributes .= '" data-roughness="' . $sanitized_step['model_roughness'];
	}
	if ( isset( $sanitized_step['model_type'] ) && '' !== $sanitized_step['model_type'] ) {
		$attributes .= '" data-model-type="' . $sanitized_step['model_type'];
	}
	if ( isset( $sanitized_step['model_material'] ) && '' !== $sanitized_step['model_material'] ) {
		$attributes .= '" data-model-material="' . $sanitized_step['model_material'];
	}
	if ( isset( $sanitized_step['model_target'] ) && '' !== $sanitized_step['model_target'] ) {
		$attributes .= '" data-model-target="' . $sanitized_step['model_target'];
	}
	if ( isset( $sanitized_step['model_orbit'] ) && '' !== $sanitized_step['model_orbit'] ) {
		$attributes .= '" data-model-orbit="' . $sanitized_step['model_orbit'];
	}
	if ( isset( $sanitized_step['model_view'] ) && '' !== $sanitized_step['model_view'] ) {
		$attributes .= '" data-model-view="' . $sanitized_step['model_view'];
	}
	if ( isset( $sanitized_step['required'] ) && 'yes' == $sanitized_step['required'] ) {
		$attributes .= '" required="required';
	}
	if ( isset( $sanitized_step['shared_min'] ) && '' != $sanitized_step['shared_min'] ) {
		$attributes .= '" data-shared-min="' .  $sanitized_step['shared_min'];
	}
	if ( isset( $sanitized_step['shared_max'] ) && '' != $sanitized_step['shared_max'] ) {
		$attributes .= '" data-shared-max="' .  $sanitized_step['shared_max'];
	}
}

if ( isset( $sanitized_step['sku'] ) && '' !== $sanitized_step['sku'] ) {
	$attributes .= '" data-step-sku="' . $sanitized_step['sku'];
}
if ( isset( $sanitized_step['preview_ref'] ) ) {
	$attributes .= '" data-preview-ref="' . $sanitized_step['preview_ref'];
}
if ( isset( $sanitized_step['preview_ref_props'] ) ) {
	$attributes .= '" data-preview-ref-props="' . $sanitized_step['preview_ref_props'];
}
if ( isset( $sanitized_step['preview_slide'] ) ) {
	$attributes .= '" data-slide-preview="' . $sanitized_step['preview_slide'];
}
if ( isset( $sanitized_step['preview_bundle'] ) && 'yes' === $sanitized_step['preview_bundle'] ) {
	$attributes .= '" data-bundle-preview="yes';

	if ( '' !== $sanitized_step['preview_height'] ) {
		$attributes .= '" data-bundle-height="' . $sanitized_step['preview_height'];
	}
}

$attributes .= '" data-step="' . $sanitized_step['id'];
$attributes .= '" data-step-name="' . staggs_sanitize_title( $sanitized_step['title'] );
if ( $sanitized_step['preview_order'] ) {
	$attributes .= '" data-preview-order="' . $sanitized_step['preview_order'];
}
?>

<div id="option-group-<?php echo esc_attr( $sanitized_step['id'] ); ?>" class="option-group sgg-init<?php echo wp_kses_normalize_entities(  $attributes ); ?>">

	<?php
	/**
	 * Hook: staggs_before_single_option_group.
	 *
	 * @hooked -
	 */
	do_action( 'staggs_before_single_option_group' );
	?>

	<div class="option-group-content">

		<?php
			/**
			 * Hook: staggs_single_option_group.
			 *
			 * @hooked staggs_output_option_group_header - 10
			 * @hooked staggs_output_option_group_content - 20
			 * @hooked staggs_output_option_group_summary - 30
			 */
			do_action( 'staggs_single_option_group' );
		?>

	</div>

	<?php
		/**
		 * Hook: staggs_after_single_option_group.
		 *
		 * @hooked staggs_option_group_description_panel - 10
		 */
		do_action( 'staggs_after_single_option_group' );
	?>

</div>
