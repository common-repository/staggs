<?php

/**
 * Provide a public-facing view for the Options step type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.7
 *
 * @package    Staggs
 * @subpackage Staggs/public/partials
 */

global $sanitized_step;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$attributes  = '';
$option_name = staggs_sanitize_title( $sanitized_step['title'] );
if ( isset( $sanitized_step['shared_group'] ) && '' !== $sanitized_step['shared_group'] ) {
	$option_name = staggs_sanitize_title( $sanitized_step['shared_group'] );
	$attributes  = ' data-group="' . $option_name . '"';
}

$price_display        = isset( $sanitized_step['show_option_price'] ) ? $sanitized_step['show_option_price'] : 'hide';
$price_type           = isset( $sanitized_step['calc_price_type'] ) ? $sanitized_step['calc_price_type'] : '';
$price_label_position = isset( $sanitized_step['calc_price_label_pos'] ) ? $sanitized_step['calc_price_label_pos'] : '';
$price_details_html   = '';
if ( function_exists( 'staggs_get_attribute_pricing_details' ) ) {
	$attributes .= staggs_get_attribute_pricing_details( $sanitized_step, $price_type );
}
if ( '' !== $price_label_position ) {
	$attributes .= ' data-price-label-pos="' . $price_label_position . '"';
}
if ( ( 'formula' === $price_type || 'matrix' === $price_type || 'formula-matrix' === $price_type ) && 'hide' !== $price_display ) {
	if ( function_exists( 'staggs_get_attribute_price_details_html' ) ) {
		$price_details_html = staggs_get_attribute_price_details_html( $sanitized_step, $price_label_position );
	}
}

$default_values = array();
if ( isset( $sanitized_step['default_values'] ) ) {
	$default_values = $sanitized_step['default_values'];
}

if ( 'above' === $price_label_position ) {
	echo wp_kses_normalize_entities(  $price_details_html );
}
?>
<div class="option-group-options options buttongroup"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
	<?php
	foreach ( $sanitized_step['options'] as $key => $option ) {
		// Out of stock and hidden.
		if ( $option['hide_option'] && 0 >= $option['stock_qty'] ) {
			continue;
		}

		$option_id    = staggs_sanitize_title( $sanitized_step['id'] . '_' . $key . '_' . $option['name'] );
		$preview_urls = get_option_preview_urls( $option, $sanitized_step['preview_index'] );

		$option_rules = '';
		if ( is_array( $option['conditional_rules'] ) && count( $option['conditional_rules'] ) > 0 ) {
			$option_rules = ' data-option-rules="' . urldecode( str_replace( '"', "'", json_encode( $option['conditional_rules'] ) ) ) . '"';
		}

		if ( isset( $option['manage_stock'] ) && 'yes' === $option['manage_stock'] && 0 >= $option['stock_qty'] ) {
			$option_html = sprintf(
				'<label for="%1$s"%2$s>
					<input 
						id="%1$s" 
						type="radio"
						disabled>
					<span class="button disabled out-of-stock">
						<span class="button-name">%3$s</span>
						<span class="button-price">%4$s</span>
					</span>
				</label>',
				$option_id,
				$option_rules,
				$option['name'],
				$option['stock_text']
			);

			echo wp_kses_normalize_entities(  $option_html );
		} else {
			$price_html   = '';
			$option_price = '';
			$attributes   = '';
			$css_styles   = '';
			if ( 'no' === $option['base_price'] ) {
				$price        = $option['price'];
				$sale         = $option['sale_price'];
				$option_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : '' );
				$price_html   = get_option_price_html( $price, $sale, $sanitized_step['inc_price_label'] );

				$attributes .= 'data-alt-price="' . $price . '"';

				if ( isset( $sanitized_step['show_option_price'] ) && 'hide' === $sanitized_step['show_option_price'] ) {
					$price_html = '';
				}
			}

			if ( 'yes' === $option['enable_preview'] ) {
				if ( count( $preview_urls ) > 0 ) {
					$attributes .= ' data-preview-urls="' . implode( ',', $preview_urls ) . '"';
				}
				if ( isset( $sanitized_step['preview_ref'] ) && '' !== $sanitized_step['preview_ref'] ) {
					$attributes .= ' data-input-key="' . $sanitized_step['preview_ref'] . '"';
				}
				if ( isset( $option['preview_node'] ) && '' !== $option['preview_node'] ) {
					$attributes .= ' data-nodes="' . $option['preview_node'] . '"';
				}
				if ( isset( $option['preview_hotspot'] ) && '' !== $option['preview_hotspot'] ) {
					$attributes .= ' data-hotspots="' . $option['preview_hotspot'] . '"';
				}
				if ( isset( $option['preview_animation'] ) && '' !== $option['preview_animation'] ) {
					$attributes .= ' data-animation="' . $option['preview_animation'] . '"';
				}
			}

			if ( isset( $option['preview_ref_selector'] ) && '' !== $option['preview_ref_selector'] ) {
				$attributes .= ' data-preview-selector="' . $option['preview_ref_selector'] . '"';
			}
			if ( isset( $option['font_family'] ) && '' !== $option['font_family'] ) {
				$attributes .= ' data-font-family="' . $option['font_family'] . '" data-font-weight="' . $option['font_weight'] . '"';
				$css_styles .= 'font-family:' . $option['font_family'] . ';font-weight:' . $option['font_weight'] . ';';
			}
			if ( isset( $option['field_color'] ) && '' !== $option['field_color'] ) {
				$attributes .= ' data-color="' . $option['field_color'] . '"';
			}

			if ( isset( $option['sku'] ) && '' !== $option['sku'] ) {
				$attributes .= ' data-sku="' . $option['sku'] . '"';
				if ( str_contains( $option['sku'], '}' ) ) {
					$attributes .= ' data-sku-format="' . $option['sku'] . '"';
				}
			}
			if ( isset( $option['weight'] ) && '' !== $option['weight'] ) {
				$attributes .= ' data-weight="' . $option['weight'] . '"';
			}
			if ( isset( $option['product_id'] ) && '' !== $option['product_id'] ) {
				$attributes .= ' data-product-id="' . $option['product_id'] . '"';
				$attributes .= ' data-product-qty="' . $option['product_qty'] . '"';
			}
			if ( isset( $option['page_url'] ) && '' !== $option['page_url'] ) {
				$attributes .= ' data-page-url="' . $option['page_url'] . '"';
			}
			if ( isset( $option['price_formula'] ) && '' !== $option['price_formula'] ) {
				$attributes .= ' data-price-formula="' . $option['price_formula'] . '"';
			}
			if ( isset( $option['price_percent'] ) && '' !== $option['price_percent'] ) {
				$attributes .= ' data-price-percent="' . $option['price_percent'] . '"';
				if ( isset( $option['price_field'] ) && '' !== $option['price_field'] ) {
					$attributes .= ' data-price-field="' . $option['price_field'] . '"';
				}
			}

			if ( isset( $sanitized_step['required'] ) && 'yes' === $sanitized_step['required'] ) {
				$attributes .= ' required="required"';
			}
			
			if ( in_array( staggs_sanitize_title( $option['name'] ), $default_values ) ) {
				$attributes .= ' checked="checked" data-default="1"';
			}

			$attributes = apply_filters( 'staggs_buttongroup_item_attributes', $attributes, $sanitized_step, $option, $key );

			$input_type = 'radio';
			if ( isset( $sanitized_step['allowed_options'] ) && 'multiple' == $sanitized_step['allowed_options'] ) {
				$input_type = 'checkbox';
			}
	
			$option_html = sprintf(
				'<label for="%1$s"%12$s>
					<input 
						id="%1$s" 
						data-step-id="%2$s" 
						data-option-id="%3$s" 
						type="%11$s" 
						name="%4$s" 
						data-index="%5$s" 
						data-price="%6$s" 
						value="%7$s"
						%9$s>
					<span class="button"%10$s>
						<span class="button-name">%7$s</span>
						<span class="button-price">%8$s</span>
					</span>
				</label>',
				$option_id, // 1.
				$sanitized_step['id'], // 2.
				$option['id'], // 3.
				$option_name, // 4.
				$step_key, // 5.
				$option_price, // 6.
				$option['name'], // 7.
				$price_html, // 8.
				$attributes, // 9.
				( '' !== $css_styles ) ? ' style="' . $css_styles . '"' : $css_styles, // 10.
				$input_type, // 11.
				$option_rules
			);
			
			echo wp_kses_normalize_entities(  $option_html );
		}
	}
	?>
</div>
<?php
if ( 'below' === $price_label_position ) {
	echo wp_kses_normalize_entities(  $price_details_html );
}
