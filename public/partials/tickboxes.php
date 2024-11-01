<?php

/**
 * Provide a public-facing view for the Tickboxes step type.
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

$has_descriptions = false;

$default_values = array();
if ( isset( $sanitized_step['default_values'] ) ) {
	$default_values = $sanitized_step['default_values'];
}
?>
<div class="option-group-options tickboxes">
	<?php
	if ( isset( $sanitized_step['show_tick_all'] ) && $sanitized_step['show_tick_all'] && isset( $sanitized_step['tick_all_label'] ) ) {
		echo sprintf(
			'<label for="%1$s">
				<input id="%1$s" type="checkbox" class="tickboxes-all-option">
				<span class="box">
					<span class="box-name">%2$s</span>
				</span>
			</label>',
			esc_attr( $sanitized_step['id'] . '_all' ),
			esc_attr( $sanitized_step['tick_all_label'] )
		);
	}

	foreach ( $sanitized_step['options'] as $key => $option ) {
		// Out of stock and hidden.
		if ( $option['hide_option'] && 0 >= $option['stock_qty'] ) {
			continue;
		}

		$option_id    = staggs_sanitize_title( $sanitized_step['id'] . '_' . $key . '_' . $option['name'] );
		$option_name  = staggs_sanitize_title( $sanitized_step['title'] );
		$preview_urls = get_option_preview_urls( $option, $sanitized_step['preview_index'] );	

		$option_title = $option['name'];
		if ( isset( $option['note'] ) && $option['note'] ) {
			$option_title .= '<small class="box-note">' . $option['note'] . '</small>';
		}

		$option_rules = '';
		if ( is_array( $option['conditional_rules'] ) && count( $option['conditional_rules'] ) > 0 ) {
			$option_rules = ' data-option-rules="' . urldecode( str_replace( '"', "'", json_encode( $option['conditional_rules'] ) ) ) . '"';
		}

		if ( isset( $option['manage_stock'] ) && 'yes' === $option['manage_stock'] && 0 >= $option['stock_qty'] ) {
			$option_html = sprintf(
				'<label for="%1$s"%2$s>
					<input type="checkbox" disabled>
					<span class="box out-of-stock">
						<span class="box-name">%3$s</span>
						<span class="box-price">%4$s</span>
					</span>
				</label>',
				$option_id,
				$option_rules,
				$option_title,
				$option['stock_text']
			);

			echo wp_kses_normalize_entities(  $option_html );
		} else {
			$price_html   = '';
			$option_price = '';
			$attributes   = '';
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
				$attributes .= ' checked="checked" data-default="1"';
			}
	
			$attributes = apply_filters( 'staggs_tickboxes_item_attributes', $attributes, $sanitized_step, $option, $key );

			if ( isset( $option['description'] ) && $option['description'] ) {
				$price_html .= '<a href="#0" class="show-panel show-panel-option" aria-label="' . __( 'Show description', 'staggs' ) . '">';
				$price_html .= staggs_get_icon( 'sgg_group_info_icon', 'panel-info' );
				$price_html .= '</a>';

				$has_descriptions = true;
			}

			$option_html = sprintf(
				'<label for="%1$s"%12$s>
					<input 
						data-step-id="%2$s" 
						data-option-id="%3$s" 
						type="checkbox" 
						name="%4$s" 
						id="%1$s"
						data-index="%5$s" 
						data-price="%6$s" 
						value="%7$s"
						%9$s>
					<span class="box">
						<span class="box-name">%10$s</span>
						<span class="box-price">%8$s</span>
					</span>
					%11$s
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
				$option_title, // 10.
				isset( $option['description'] ) ? '<div class="option-description hidden">' . $option['description'] . '</div>' : '',  // 11.
				$option_rules
			);

			echo wp_kses_normalize_entities(  $option_html );
		}
	}
	?>
</div>
<?php
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
