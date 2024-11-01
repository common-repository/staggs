<?php

/**
 * Provide a public-facing view for the True/False step type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.1.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/partials
 */

global $sanitized_step, $density, $text_align;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$option_name = staggs_sanitize_title( $sanitized_step['title'] );
$single_html = '';

$default_values = array();
if ( isset( $sanitized_step['default_values'] ) ) {
	$default_values = $sanitized_step['default_values'];
}

if ( is_array( $sanitized_step['options'] ) && count( $sanitized_step['options'] ) > 0 ) {
	foreach ( $sanitized_step['options'] as $option ) {
		// Out of stock and hidden.
		if ( $option['hide_option'] && 0 >= $option['stock_qty'] ) {
			continue;
		}

		$is_disabled  = ( isset( $option['manage_stock'] ) && 'yes' === $option['manage_stock'] && 0 >= $option['stock_qty'] );
		$preview_urls = get_option_preview_urls( $option, $sanitized_step['preview_index'] );

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
			if ( isset( $sanitized_step['preview_ref'] ) && '' !== $sanitized_step['preview_ref'] ) {
				$attributes .= ' data-input-key="' . $sanitized_step['preview_ref'] . '"';
			}
			if ( isset( $option['field_color'] ) && '' !== $option['field_color']  ) {
				$attributes .= ' data-color="' . $option['field_color'] . '"';
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

		if ( in_array( staggs_sanitize_title( $option['name'] ), $default_values ) ) {
			$attributes .= ' checked="checked" data-default="1"';
		}

		$attributes = apply_filters( 'staggs_truefalse_item_attributes', $attributes, $sanitized_step, $option, $key );

		if ( 'toggle' === $sanitized_step['button_view'] ) {
			$toggle = sprintf(
				'<div class="option-group-options single single-toggle%9$s">
					<label for="%1$s">
						<div class="toggle">
							<input
								data-step-id="%2$s"
								data-option-id="%8$s"
								data-index="%6$s"
								name="%1$s"
								id="%1$s"
								data-price="%3$s"
								value="%11$s"
								class="checkbox"
								type="checkbox"
								%7$s
								%10$s>
							<div class="knobs%10$s">
								<span class="before">%4$s</span>
								<span class="switch"></span>
								<span class="after">%5$s</span>
							</div>
							<div class="layer"></div>
							<span class="toggle-label"><span class="before">%4$s</span><span class="after">%5$s</span></span>
						</div>
					</label>
				</div>',
				$option_name, // 1, 5, 6
				$sanitized_step['id'], // 2.
				$option_price, // 3.
				$sanitized_step['button_add'], // 4.
				$sanitized_step['button_del'], // 5.
				$step_key, // 6.
				$attributes, // 7.
				$option['id'], // 8.
				$is_disabled ? ' disabled out-of-stock' : '', // 9.
				$is_disabled ? ' disabled' : '', // 10.
				$option['name'] // 11.
			);

			if ( 'left' === $text_align ) {
				?>
				<div class="option-group-header">
					<?php
					echo wp_kses_normalize_entities(  $toggle );
					?>
					<div class="option-group-title">
						<strong class="title"><?php echo esc_attr( $sanitized_step['title'] ); ?></strong>
						<?php if ( $sanitized_step['description'] ) : ?>
							<a href="#0" class="show-panel" aria-label="<?php esc_attr_e( 'Show description', 'staggs' ); ?>">
								<?php
								echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_group_info_icon', 'panel-info' ) );
								?>
							</a>
						<?php endif; ?>
						<?php if ( '' !== $price_html ) : ?>
							<p class="option-group-price"><?php echo wp_kses_normalize_entities(  $price_html ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<?php
			} else {
				if ( '' !== $price_html ) :
					?>
					<p class="option-group-price"><?php echo wp_kses_normalize_entities(  $price_html ); ?></p>
					<?php
				endif;
				echo wp_kses_normalize_entities(  $toggle );
			}
		} else {
			$option_html = sprintf(
				'<p class="option-group-price">%1$s</p>
				<div class="option-group-options single%10$s">
					<label for="%2$s">
						<input
							data-step-id="%3$s"
							data-option-id="%9$s"
							data-index="%7$s"
							name="%2$s"
							id="%2$s"
							data-price="%4$s"
							value="%12$s"
							class="checkbox"
							type="checkbox"
							%8$s
							%11$s>
						<span class="button%11$s">
							<span class="add">%5$s</span>
							<span class="del">%6$s</span>
						</span>
					</label>
				</div>',
				$price_html, // 1.
				$option_name, // 2, 4, 5
				$sanitized_step['id'], // 3.
				$option_price, // 4.
				$sanitized_step['button_add'], // 5.
				$sanitized_step['button_del'], // 6.
				$step_key, // 7.
				$attributes, // 8.
				$option['id'], // 9.
				$is_disabled ? ' disabled out-of-stock' : '', // 10,
				$is_disabled ? ' disabled' : '', // 11.
				$option['name'] // 12.
			);

			echo wp_kses_normalize_entities(  $option_html );
		}

		break; // Only show first and single option.
	}
}
