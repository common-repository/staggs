<?php

/**
 * Provide a public-facing view for the Image Upload step type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.1.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/partials
 */

global $sanitized_step;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$label_html = '';
$class = 'image-input-field';

$is_custom_upload = staggs_get_theme_option( 'sgg_product_disable_system_file_upload' );
if ( $is_custom_upload ) {
	$class .= ' image-input-field-custom';

	$field_label = staggs_get_theme_option( 'sgg_product_file_upload_label' ) ?: __( 'No file selected', 'staggs' );
	$field_button = staggs_get_theme_option( 'sgg_product_file_upload_button_label' ) ?: __( 'Browse', 'staggs' );

	$label_html = '<p class="image-input-field-label"><span class="input-field-button">' . $field_button . '</span>' . $field_label . '</p>';
}

$field_html = '';
foreach ( $sanitized_step['options'] as $key => $option ) {
	$option_name = staggs_sanitize_title( $option['name'] );

	$supported_attributes = array(
		'allowed_file_types' => 'accept',
		'sku'          => 'data-sku',
		'max_file_size' => 'data-size',
	);

	$required_visual_keys = array('preview_width', 'preview_height');
	$filled_visual_keys = array();

	if ( 'yes' === $option['enable_preview'] ) {
		$supported_attributes = array(
			'allowed_file_types' => 'accept',
			'sku'           => 'data-sku',
			'max_file_size'  => 'data-size',
			'field_key'      => 'data-field-key',
			'material_key'   => 'data-material-key',
			'preview_top'    => 'data-preview-top',
			'preview_left'   => 'data-preview-left',
			'preview_width'  => 'data-preview-width',
			'preview_height' => 'data-preview-height',
			'preview_top_mobile'   => 'data-preview-top-xs',
			'preview_left_mobile'  => 'data-preview-left-xs',
			'preview_image_fill'   => 'data-preview-fill',
		);
	}

	$attributes = '';
	foreach ( $supported_attributes as $field_key => $data_attribute ) {
		if ( isset( $option[ $field_key ] ) && '' !== $option[ $field_key ] ) {
			if (in_array( $field_key, $required_visual_keys)) {
				$filled_visual_keys[] = $field_key;
			}
			$attributes .= ' ' . $data_attribute . '="' . $option[ $field_key ] . '"';
		}
	}

	$attributes .= ' data-option-id="' . staggs_sanitize_title( $option['name'] ) . '"';

	if ( isset( $option['field_required'] ) && 'yes' === $option['field_required'] ) {
		$attributes .= ' required="required"';
	}
	if ( $sanitized_step['preview_index'] && 'yes' === $option['enable_preview'] ) {
		$attributes .= ' data-preview-index="' . $sanitized_step['preview_index'] . '"';
	}

	$price_html   = '';
	$option_price = '';
	if ( 'no' === $option['base_price'] ) {
		$price        = $option['price'];
		$sale         = $option['sale_price'];
		$option_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : '' );
		$price_html   = '<span class="input-price">' . get_option_price_html( $price, $sale, $sanitized_step['inc_price_label'] ) . '</span>';

		if ( isset( $sanitized_step['show_option_price'] ) && 'hide' === $sanitized_step['show_option_price'] ) {
			$price_html = '';
		}
	}

	$option_heading = $option['name'];
	if ( isset( $option['field_required'] ) && 'yes' === $option['field_required'] ) {
		$attributes .= ' required="required"';
		$option_heading .= ' <span class="required-indicator">*</span>';
	}

	$note = '';
	if ( $price_html ) {
		$attributes .= ' data-price="' . $option_price . '"';
		$attributes .= ' data-alt-price="' . $option_price . '"';
	}

	if ( isset( $option['note'] ) && $option['note'] ) {
		$note .= '<p class="option-note">' . $option['note'] . '</p>';
	}

	$attributes = apply_filters( 'staggs_fileinput_item_attributes', $attributes, $sanitized_step, $option, $key );

	if ( $required_visual_keys === $filled_visual_keys && ( isset( $option['material_key'] ) || isset( $option['model_group'] ) ) ) {
		$material_key = isset( $option['material_key'] ) ? $option['material_key'] : $sanitized_step['model_group'];
		if ( $material_key ) {
			$field_html .= sprintf(
				'<canvas id="%s_canvas" class="option-group-canvas" width="%d" height="%d" data-key="%s"></canvas>',
				$option['id'],
				$option['preview_width'],
				$option['preview_height'],
				$material_key,
			);
		}
	}

	$field_html .= sprintf('
		<div class="input-field-wrapper %8$s">
			<span class="input-heading">
				<p class="input-title">%3$s</p>
				%6$s
			</span>
			%5$s
			<div class="show-if-input-value hidden"></div>
			<div class="hide-if-input-value">
				<label for="%1$s-input">
					<input type="file" name="%1$s-input" value="" id="%1$s-input" data-step-id="%2$s"%4$s>
					<input type="hidden" name="%1$s" value="">
					%7$s
				</label>
			</div>
		</div>',
		$option_name, // 1.
		$option['id'], // 2.
		$option_heading, // 3.
		$attributes, // 4.
		$note, // 5.
		$price_html, // 6.
		$label_html, // 7.
		$class // 8.
	);
}

echo sprintf(
	'<div class="option-group-options image-input">
		%1$s
	</div>',
	wp_kses_normalize_entities(  $field_html ) // 1.
);
