<?php

/**
 * Provide a public-facing view for the Dropdown step type.
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

$option_name = staggs_sanitize_title( $sanitized_step['title'] );
$preview_url = '';
$select_attributes  = '';
if ( isset( $sanitized_step['required'] ) && 'yes' === $sanitized_step['required'] ) {
	$select_attributes .= ' required';
}

$price_display        = isset( $sanitized_step['show_option_price'] ) ? $sanitized_step['show_option_price'] : 'hide';
$price_type           = isset( $sanitized_step['calc_price_type'] ) ? $sanitized_step['calc_price_type'] : '';
$price_label_position = isset( $sanitized_step['calc_price_label_pos'] ) ? $sanitized_step['calc_price_label_pos'] : '';
$price_details_html   = '';
$attributes           = '';
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
<div class="option-group-options select"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
	<select name="<?php echo esc_attr( $option_name ); ?>" id="<?php echo esc_attr( $sanitized_step['id'] ); ?>" data-step-id="<?php echo esc_attr( $sanitized_step['id'] ); ?>"<?php echo wp_kses_normalize_entities(  $select_attributes ); ?>>
		<?php 
		$placeholder = '';
		if ( '' !== staggs_get_post_meta( $sanitized_step['id'], 'sgg_step_field_placeholder' ) ) {
			$placeholder = staggs_get_post_meta( $sanitized_step['id'], 'sgg_step_field_placeholder' );
		}
		if ( ( isset( $sanitized_step['required'] ) && 'yes' === $sanitized_step['required'] ) || staggs_get_theme_option( 'sgg_product_dropdown_disable_placeholder' ) ) :
			if ( '' !== $placeholder ) :
				?>
				<option value=""<?php echo wp_kses_normalize_entities(  $attributes ); ?>><?php echo esc_attr( $placeholder ); ?></option>
				<?php
			endif;
		else :
			if ( '' === $placeholder ) {
				$placeholder = '- ' . $sanitized_step['title'] . ' -';
			}
			$placeholder_data = '';
			if ( isset( $sanitized_step['options'][0]['preview_node'] ) && '' !== $sanitized_step['options'][0]['preview_node'] ) {
				$attributes .= ' data-nodes="-"';
			}
			?>
			<option value=""<?php echo wp_kses_normalize_entities(  $attributes ); ?>><?php echo esc_attr( $placeholder ); ?></option>
			<?php
		endif;

		foreach ( $sanitized_step['options'] as $key => $option ) {
			// Out of stock and hidden.
			if ( $option['hide_option'] && 0 >= $option['stock_qty'] ) {
				continue;
			}

			$option_id    = staggs_sanitize_title( $sanitized_step['id'] . '_' . $key . '_' . $option['name'] );
			$option_name  = staggs_sanitize_title( $sanitized_step['title'] );
			$preview_urls = get_option_preview_urls( $option, $sanitized_step['preview_index'] );

			$attributes = '';
			if ( 'yes' === $option['enable_preview'] ) {
				if ( count( $preview_urls ) > 0 ) {
					$attributes .= ' data-preview-urls="' . implode( ',', $preview_urls ) . '"';
				}
				if ( '' !== $sanitized_step['preview_ref'] ) {
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
			if ( isset( $option['font_family'] ) ) {
				$attributes .= 'data-font-family="' . $option['font_family'] . '" data-font-weight="' . $option['font_weight'] . '"';
				$attributes .= 'style="font-family:' . $option['font_family'] . ';font-weight:' . $option['font_weight'] . '"';
			}
			if ( isset( $option['field_color'] ) ) {
				$attributes .= ' data-color="' . $option['field_color'] . '"';
			}
			
			if ( isset( $option['note'] ) && '' !== $option['note'] ) {
				$attributes .= ' data-note="' . $option['note'] . '"';
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

			if ( in_array( staggs_sanitize_title( $option['name'] ), $default_values ) ) {
				$attributes .= ' selected="selected" data-default="1"';
			}

			if ( is_array( $option['conditional_rules'] ) && count( $option['conditional_rules'] ) > 0 ) {
				$attributes .= ' data-option-rules="' . urldecode( str_replace( '"', "'", json_encode( $option['conditional_rules'] ) ) ) . '"';
			}

			$attributes = apply_filters( 'staggs_dropdown_item_attributes', $attributes, $sanitized_step, $option, $key );

			if ( isset( $option['manage_stock'] ) && 'yes' === $option['manage_stock'] && 0 >= $option['stock_qty'] ) {
				$option_html = sprintf(
					'<option disabled>%s</option>',
					$option['name'] . ' (' . $option['stock_text'] . ')'
				);

				echo wp_kses_normalize_entities(  $option_html );
			} else {
				$price_html   = '';
				$option_price = '';
				if ( 'no' === $option['base_price'] ) {
					$price        = $option['price'];
					$sale         = $option['sale_price'];
					$option_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : '' );
						
					$attributes .= 'data-alt-price="' . $price . '"';
					
					$price_html   = strip_tags( get_option_price_html( $price, $sale, $sanitized_step['inc_price_label'] ) );
				}

				$option_name  = $option['name'];
				if ( 'hide' === $sanitized_step['show_summary'] ) {
					if ( isset( $sanitized_step['show_option_price'] ) && 'hide' !== $sanitized_step['show_option_price'] ) {
						$option_name .= ' ' . $price_html;
					}
				}

				$option_html = sprintf(
					'<option data-step-id="%1$s" data-option-id="%2$s" name="%3$s" value="%5$s" data-price="%4$s" %7$s>%6$s</option>',
					$sanitized_step['id'], // 1.
					$option['id'], // 2.
					$option_name, // 3.
					$option_price, // 4.
					$option['name'], // 5.
					$option_name, // 6.
					$attributes // 7
				);

				echo wp_kses_normalize_entities(  $option_html );
			}
		}
		?>
	</select>
</div>
<?php
if ( 'below' === $price_label_position ) {
	echo wp_kses_normalize_entities(  $price_details_html );
}