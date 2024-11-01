<?php

/**
 * Provide a public-facing view for the User Input step type.
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

$field_html = '';

foreach ( $sanitized_step['options'] as $key => $option ) {
	$option_name = staggs_sanitize_title( $option['name'] );

	$supported_attributes = array(
		'sku'         => 'data-sku',
		'field_key'     => 'data-field-key',
		'material_key'  => 'data-material-key',
		'preview_top'   => 'data-preview-top',
		'preview_left'  => 'data-preview-left',
		'preview_width' => 'data-preview-width',
		'preview_height' => 'data-preview-height',
		'preview_overflow'     => 'data-preview-overflow',
		'preview_top_mobile'   => 'data-preview-top-xs',
		'preview_left_mobile'  => 'data-preview-left-xs',
		'preview_width_mobile' => 'data-preview-width-xs',
		'preview_ref_selector' => 'data-preview-selector',
		'field_min' => 'minlength',
		'field_max' => 'maxlength',
		'field_placeholder' => 'placeholder',
	);

	$required_visual_keys = array('preview_width', 'preview_height');
	$filled_visual_keys = array();

	if ( 'date' === $option['field_type'] ) {
		$supported_attributes['field_min'] = 'data-date-min';
		$supported_attributes['field_max'] = 'data-date-max';
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
	$attributes .= ' data-step-id="' . $sanitized_step['id'] . '"';

	$option_heading = $option['name'];
	if ( isset( $option['field_required'] ) && 'yes' === $option['field_required'] ) {
		$attributes .= ' required="required"';
		$option_heading .= ' <span class="required-indicator">*</span>';
	}
	if ( $sanitized_step['preview_index'] && 'yes' === $option['enable_preview'] ) {
		$attributes .= ' data-preview-index="' . $sanitized_step['preview_index'] . '"';
	}

	$option_price = '';
	$price_html   = '';
	if ( 'no' === $option['base_price'] ) {
		$price        = $option['price'];
		$sale         = $option['sale_price'];
		$option_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : '' );
		$price_html   = '<span class="input-price">' . get_option_price_html( $price, $sale, $sanitized_step['inc_price_label'] ) . '</span>';

		if ( isset( $option['price_type'] ) ) {
			if ( 'unit' === $option['price_type'] && isset( $option['unit_price'] ) && '' !== $option['unit_price'] ) {
				$attributes .= ' data-unit-price="' . $option['unit_price'] . '"';
				$price_html = ' <span class="input-price">' . wc_price( $option['unit_price'] ) . __( ' per character', 'staggs' ) . '</span>';
			}

			if ( 'table' === $option['price_type'] && isset( $option['price_table'] ) && '' !== $option['price_table'] ) {
				$attributes .= ' data-table-price="' . $option['price_table'] . '"';
			}
		}

		if ( isset( $sanitized_step['show_option_price'] ) && 'hide' === $sanitized_step['show_option_price'] ) {
			$price_html = '';
		}
	}

	if ( $option_price ) {
		$attributes .= ' data-price="' . $option_price . '"';
		$attributes .= ' data-alt-price="' . $option_price . '"';
	}

	if ( isset( $option['field_value'] ) && '' !== $option['field_value'] ) {
		$attributes .= ' data-default="' . $option['field_value'] . '"';
	}

	$attributes = apply_filters( 'staggs_textinput_item_attributes', $attributes, $sanitized_step, $option, $key );

	$note = '';
	if ( isset( $option['note'] ) && $option['note'] ) {
		$note .= '<p class="option-note">' . $option['note'] . '</p>';
	}

	$comment = '';
	if ( isset( $sanitized_step[ 'field_info' ] ) && 'yes' === $sanitized_step[ 'field_info' ] ) {
		if ( isset( $option[ 'field_min' ] ) && '' !== $option[ 'field_min' ] && isset( $option[ 'field_max' ] ) && '' !== $option[ 'field_max' ] ) {
			$comment = '<small>' . __( 'Min: ', 'staggs' ) . $option[ 'field_min' ] . __( ' and max: ', 'staggs' ) . $option[ 'field_max' ] . ' ' . __( 'characters', 'staggs' ) . '</small>';
		} else if ( isset( $option[ 'field_min' ] ) && '' !== $option[ 'field_min' ] ) {
			$comment = '<small>' . __( 'Min: ', 'staggs' ) . $option[ 'field_min' ] . ' ' . __( 'characters', 'staggs' ) . '</small>';
		} else if ( isset( $option[ 'field_max' ] ) && '' !== $option[ 'field_max' ] ) {
			$comment = '<small>' . __( 'Max: ', 'staggs' ) . $option[ 'field_max' ] . ' ' . __( 'characters', 'staggs' ) . '</small>';
		}
	}

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

	if ( 'textarea' === $option['field_type'] ) {
		$textarea_rows = apply_filters( 'staggs_textarea_rows', 4 );
		$field_html .= sprintf('
			<div class="input-field-wrapper">
				<label for="%2$s">
					<span class="input-heading">
						<p class="input-title">%3$s</p>
						%9$s
					</span>
				</label>
				%8$s
				<textarea id="%2$s" data-step-id="%10$s" name="%1$s" rows="%6$s"%5$s>%4$s</textarea>
				%7$s
			</div>',
			$option_name, // 1.
			$option['id'], // 2.
			$option_heading, // 3.
			isset( $option['field_value'] ) ? $option['field_value'] : '', // 4.
			$attributes, // 5.
			$textarea_rows, // 6.
			$comment, // 7.
			$note, // 8.
			$price_html, // 9.
			$sanitized_step['id'] // 10
		);

	} else if ( 'text' === $option['field_type'] ) {

		$field_html .= sprintf('
			<div class="input-field-wrapper">
				<label for="%2$s" class="input-heading">
					<p class="input-title">%3$s</p>
					%8$s
				</label>
				%7$s
				<input id="%2$s" data-step-id="%9$s" name="%1$s" value="%4$s" type="text"%5$s>
				%6$s
			</div>',
			$option_name, // 1.
			$option['id'], // 2.
			$option_heading, // 3.
			isset( $option['field_value'] ) ? $option['field_value'] : '', // 4.
			$attributes, // 5.
			$comment, // 6.
			$note, // 7.
			$price_html, // 8.
			$sanitized_step['id'] // 9
		);

	} else if ( 'date' === $option['field_type'] ) {

		$format = 'mm/dd/yy';
		if ( isset( $option['datepicker_format'] ) && '' !== $option['datepicker_format'] ) {
			$format = $option['datepicker_format'];
		}

		if ( '' !== $comment ) {
			$comment = '';
		}

		$inline_div = '';
		$attributes .= ' data-date-format="' . $format . '"';
		if ( isset( $option['datepicker_show_inline'] ) && ( 'true' == $option['datepicker_show_inline'] || 'yes' == $option['datepicker_show_inline'] ) ) {
			$attributes .= ' data-inline="' . $option['id'] . '-inline"';
			$inline_div = '<div class="datepicker-input-inline"></div>';
		}

		$icon_html = '';
		if ( '' == $inline_div && ( 'true' == $option['datepicker_show_icon'] || 'yes' == $option['datepicker_show_icon'] ) ) {
			$icon_html = staggs_get_icon( 'sgg_calendar_icon', 'calendar' );
		}

		$field_html .= sprintf('
			<div class="input-field-wrapper">
				<label class="input-heading">
					<p class="input-title">%3$s</p>
				</label>
				%6$s
				<span class="input-field input-field-datepicker">
					<input type="text" data-type="date" data-step-id="%9$s" name="%1$s" class="datepicker-input" %4$s>
					%7$s
					%8$s
				</span>
				%5$s
			</div>',
			$option_name, // 1.
			$option['id'], // 2.
			$option_heading, // 3.
			$attributes, // 4.
			$comment, // 5.
			$note, // 6.
			$inline_div, // 7.
			$icon_html, // 8.
			$sanitized_step['id'] // 9
		);
	}
}

echo sprintf(
	'<div class="option-group-options text-input">
		%1$s
	</div>',
	wp_kses_normalize_entities(  $field_html ) // 1.
);
