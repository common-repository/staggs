<?php

/**
 * Provide a public-facing view for the Options step type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.1.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $sanitized_step;

$show_image = $sanitized_step['show_image'] === 'show';

$attributes = '';
$option_name  = staggs_sanitize_title( $sanitized_step['title'] );
if ( isset( $sanitized_step['shared_group'] ) && '' !== $sanitized_step['shared_group'] ) {
	$option_name = staggs_sanitize_title( $sanitized_step['shared_group'] );
	$attributes  = ' data-group="' . $option_name . '"';
}

$has_descriptions     = false;
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
<div class="option-group-options options list<?php if ( $show_image ) echo ' list--preview'; ?>"<?php echo wp_kses_normalize_entities(  $attributes ); ?>>
	<?php
	foreach ( $sanitized_step['options'] as $key => $option ) {
		// Out of stock and hidden.
		if ( $option['hide_option'] && 0 >= $option['stock_qty'] ) {
			continue;
		}

		$option_id    = staggs_sanitize_title( $sanitized_step['id'] . '_' . $key . '_' . $option['name'] );
		$preview_urls = get_option_preview_urls( $option, $sanitized_step['preview_index'] );

		$image_html   = '';
		if ( $option['image'] && $show_image ) {
			$image_html = '<span class="option-image">' . $option['image'];

			if ( isset( $option['image_url'] ) && '' !== $option['image_url'] && $sanitized_step['show_zoom'] ) {
				$zoom_icon = staggs_get_icon( 'sgg_zoom_icon', 'zoom' );
				$image_html .= '<a href="' . $option['image_url'] . '" data-lightbox="gallery" class="icon-zoom">' . $zoom_icon . '</a>';
			}

			$image_html .= '</span>';
		}

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
					<span class="option out-of-stock">
						%3$s
						<span class="option-name">%4$s</span>
						<span class="option-price">%5$s</span>
					</span>
				</label>',
				$option_id,
				$option_rules,
				$image_html,
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
			
			if ( isset( $sanitized_step['required'] ) && 'yes' === $sanitized_step['required'] ) {
				$attributes .= ' required="required"';
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

			$option_title = '<span class="option-label">' . $option['name'] . '</span>';
			if ( isset( $option['note'] ) && $option['note'] ) {
				$option_title .= '<small class="option-note">' . $option['note'] . '</small>';
			}

			if ( isset( $option['description'] ) && $option['description'] ) {
				$price_html .= '<a href="#0" class="show-panel show-panel-option" aria-label="' . __( 'Show description', 'staggs' ) . '">';
				$price_html .= staggs_get_icon( 'sgg_group_info_icon', 'panel-info' );
				$price_html .= '</a>';

				$has_descriptions = true;
			}

			if ( in_array( staggs_sanitize_title( $option['name'] ), $default_values ) ) {
				$attributes .= ' checked="checked" data-default="1"';
			}
	
			$input_type = 'radio';
			if ( isset( $sanitized_step['allowed_options'] ) && 'multiple' == $sanitized_step['allowed_options'] ) {
				$input_type = 'checkbox';
			}

			$attributes = apply_filters( 'staggs_options_item_attributes', $attributes, $sanitized_step, $option, $key );

			$option_html = sprintf(
				'<label for="%1$s"%14$s>
					<input 
						id="%1$s" 
						data-step-id="%2$s" 
						data-option-id="%3$s" 
						type="%15$s" 
						name="%4$s" 
						data-index="%5$s" 
						data-price="%6$s" 
						value="%7$s"
						%10$s>
					<span class="option"%12$s>
						%8$s
						<span class="option-name">%11$s</span>
						<span class="option-price">%9$s</span>
					</span>
					%13$s
				</label>',
				$option_id, // 1.
				$sanitized_step['id'], // 2.
				$option['id'], // 3.
				$option_name, // 4.
				$step_key, // 5.
				$option_price, // 6.
				$option['name'], // 7.
				$image_html, // 8.
				$price_html, // 9.
				$attributes, // 10.
				$option_title, // 11.
				( '' !== $css_styles ? ' style="' . $css_styles . '"' : $css_styles ), // 12.
				( isset( $option['description'] ) ? '<div class="option-description hidden">' . $option['description'] . '</div>' : '' ),  // 13.
				$option_rules, // 14.
				$input_type // 15
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

if ( $has_descriptions ) {
	?>
	<div id="option-panel-<?php echo esc_attr( $sanitized_step['id'] ); ?>" class="option-group-panel">
		<div class="option-group-panel-header">
			<p><strong class="option-group-panel-label"></strong></p>
			<a href="#0" class="close-panel" aria-label="<?php esc_attr_e( 'Hide description', 'staggs' ); ?>">
				<?php
				echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_close_icon', 'panel-close' ) );
				?>
			</a>
		</div>
		<div class="option-group-panel-content">
		</div>
	</div>
	<?php
}
