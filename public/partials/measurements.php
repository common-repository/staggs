<?php

/**
 * Provide a public-facing view for the True/False step type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.2.1
 *
 * @package    Staggs
 * @subpackage Staggs/public/partials
 */

global $sanitized_step, $sgg_minus_button, $sgg_plus_button;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$option_name    = staggs_sanitize_title( $sanitized_step['title'] );
$price_type     = isset( $sanitized_step['calc_price_type'] ) ? $sanitized_step['calc_price_type'] : '';
$formula        = isset( $sanitized_step['price_formula'] ) ? $sanitized_step['price_formula'] : '';
$matrix_table   = isset( $sanitized_step['price_table'] ) ? $sanitized_step['price_table'] : '';
$field_html     = '';

foreach ( $sanitized_step['options'] as $key => $option ) {
	$option_id = staggs_sanitize_title( $sanitized_step['id'] . '_' . $key . '_' . $option['name'] );

	$supported_attributes = array(
		'sku'           => 'data-sku',
		'preview_top'   => 'data-preview-top',
		'preview_left'  => 'data-preview-left',
		'preview_width' => 'data-preview-width',
		'preview_overflow'     => 'data-preview-overflow',
		'preview_top_mobile'   => 'data-preview-top-xs',
		'preview_left_mobile'  => 'data-preview-left-xs',
		'preview_width_mobile' => 'data-preview-width-xs',
		'preview_ref_selector' => 'data-preview-selector',
		'field_key'      => 'data-field-key',
		'field_unit'     => 'data-unit',
		'field_min'      => 'min',
		'field_max'      => 'max',
		'field_placeholder' => 'placeholder',
	);

	$attributes = '';
	$range_div  = '';
	foreach ( $supported_attributes as $field_key => $data_attribute ) {
		if ( isset( $option[ $field_key ] ) && '' !== $option[ $field_key ] ) {
			$attributes .= ' ' . $data_attribute . '="' . $option[ $field_key ] . '"';
		}
	}

	$attributes .= ' data-option-id="' . staggs_sanitize_title( $option['name'] ) . '"';
	$attributes .= ' data-step-id="' . $sanitized_step['id'] . '"';

	$unit = ( isset( $option['field_unit'] ) && '' !== $option['field_unit'] ) ? ' ' . $option['field_unit'] : '';
	$comment = '';
	if ( isset( $sanitized_step[ 'field_info' ] ) && 'yes' === $sanitized_step[ 'field_info' ] ) {
		if ( isset( $option[ 'field_min' ] ) && '' !== $option[ 'field_min' ] && isset( $option[ 'field_max' ] ) && '' !== $option[ 'field_max' ] ) {
			$comment = '<small>' . __( 'Min: ', 'staggs' ) . $option[ 'field_min' ] . __( ' and max: ', 'staggs' ) . $option[ 'field_max' ] . $unit . '</small>';
		} else if ( isset( $option[ 'field_min' ] ) && '' !== $option[ 'field_min' ] ) {
			$comment = '<small>' . __( 'Min: ', 'staggs' ) . $option[ 'field_min' ] . $unit . '</small>';
		} else if ( isset( $option[ 'field_max' ] ) && '' !== $option[ 'field_max' ] ) {
			$comment = '<small>' . __( 'Max: ', 'staggs' ) . $option[ 'field_max' ] . $unit . '</small>';
		}
	}

	$option_heading = $option['name'];
	if ( $sanitized_step['preview_index'] && 'yes' === $option['enable_preview'] ) {
		$attributes .= ' data-preview-index="' . $sanitized_step['preview_index'] . '"';
	}

	if ( isset( $option['field_required'] ) && 'yes' === $option['field_required'] ) {
		$attributes .= ' required="required"';
		$option_heading .= ' <span class="required-indicator">*</span>';
	}

	$price_html = '';
	if ( 'no' === $option['base_price'] ) {
		$price        = $option['price'];
		$sale         = $option['sale_price'];
		$option_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : '' );
		$price_html   = '<span class="input-price">' . get_option_price_html( $price, $sale, $sanitized_step['inc_price_label'] ) . '</span>';	

		if ( 'unit' === $option['price_type'] && isset( $option['unit_price'] ) && '' !== $option['unit_price'] ) {
			$attributes .= ' data-unit-price="' . $option['unit_price'] . '"';
		} else if ( 'table' === $option['price_type'] && isset( $option['price_table'] ) && '' !== $option['price_table'] ) {
			$attributes .= ' data-table-price="' . $option['price_table'] . '"';
		} else if ( $option_price ) {
			$attributes .= ' data-price="' . $option_price . '"';
		}
	}

	$note = '';
	if ( isset( $option['note'] ) && $option['note'] ) {
		$note = '<p class="option-note">' . $option['note'] . '</p>';
	}

	if ( 'range' === $option['field_type'] ) {
		$attributes .= ' data-type="range"';

		$increments = 1;
		if ( isset( $option['range_increments'] ) ) {
			$increments = $option['range_increments'];
		}

		if ( isset( $option['range_bubble'] ) && 'yes' == $option['range_bubble'] ) {
			$attributes .= ' data-range-bubble="1"';
		}

		$attributes .= ' data-range-increments="' . $increments. '"';
		$attributes .= ' readonly';

		$range_div = '<div id="range-slider-' . $option_id . '" class="range-slider"></div>';
		$range_div .= '<div class="range-value"><span class="name">' . $option['name'] . ':</span><span class="value"></span></div>';
	} else if ( isset( $option['range_increments'] ) ) {
		$attributes .= ' step="' . $option['range_increments'] . '"';
	}

	if ( isset( $option['field_value'] ) && '' !== $option['field_value'] ) {
		$attributes .= ' data-default="' . $option['field_value'] . '"';
	}

	$attributes = apply_filters( 'staggs_numberinput_item_attributes', $attributes, $sanitized_step, $option, $key );

	if ( isset( $sanitized_step['show_option_price'] ) && 'hide' === $sanitized_step['show_option_price'] ) {
		$price_html = '';
	}

	$field_html .= sprintf(
		'<label for="%1$s" class="input-field-wrapper">
			<span class="input-heading">
				<p class="input-title">%2$s</p>
				%11$s
			</span>
			%10$s
			<span class="input-field">
				<span class="input-field-inner">
					%12$s
					<input id="%1$s" name="%3$s" type="%4$s" value="%5$s"%6$s>
					%13$s
				</span>
				%7$s
				%8$s
			</span>
			%9$s
		</label>',
		staggs_sanitize_title( $option['name'] ), // 1.
		$option_heading, // 2.
		staggs_sanitize_title( $option['name'] ), // 3.
		'number', // 4.
		( isset( $option['field_value'] ) ? $option['field_value'] : '' ), // 5.
		$attributes, // 6.
		$range_div, // 7.
		( isset( $option['field_unit'] ) && '' !== $option['field_unit'] && $option['field_type'] !== 'range' ) ? '<span class="unit">' . $option['field_unit'] . '</span>' : '', // 8.
		$comment, // 9.
		$note, // 10.
		$price_html, // 11.
		( 'number' === $option['field_type'] ? $sgg_minus_button : '' ), // 12.
		( 'number' === $option['field_type'] ? $sgg_plus_button : '' ) // 13.
	);
}

if ( $formula != '' || $matrix_table != '' ) {
	$parent_attributes = '';
	$option_price = 0;
	$price_label_position = isset( $sanitized_step['calc_price_label_pos'] ) ? $sanitized_step['calc_price_label_pos'] : '';
	if ( function_exists( 'staggs_get_attribute_pricing_details' ) ) {
		$parent_attributes .= staggs_get_attribute_pricing_details( $sanitized_step, $price_type );
	}

	if ( function_exists( 'staggs_get_attribute_price_details_html' ) ) {
		$price_details_html = staggs_get_attribute_price_details_html( $sanitized_step, $price_label_position );
	}

	$price_details_above = '';
	$price_details_below = '';

	if ( 'above' === $price_label_position ) {
		$price_details_above = $price_details_html;
	} else {
		$price_details_below = $price_details_html;
	}

	if ( isset( $sanitized_step['show_option_price'] ) && 'hide' === $sanitized_step['show_option_price'] ) {
		$price_details_above = '';
		$price_details_below = '';	
	}
	
	$attribute_html = sprintf('
		%1$s
		<div class="option-group-options measurements"%2$s>
			<input type="hidden" id="%3$s" value="%4$s">
			%5$s
		</div>
		%6$s',
		$price_details_above, // 1.
		$parent_attributes, // 2.
		$option_name, // 3.
		$option_price, // 4.
		$field_html, // 5.
		$price_details_below // 6.
	);

	echo wp_kses_normalize_entities(  $attribute_html );
} else {
	echo sprintf('
		<div class="option-group-options measurements">
			%1$s
		</div>',
		wp_kses_normalize_entities(  $field_html ) // 1.
	);
}
