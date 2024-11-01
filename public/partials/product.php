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

global $sanitized_step, $sgg_minus_button, $sgg_plus_button;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$add_label = __( 'Add', 'staggs' );
if (staggs_get_theme_option('sgg_product_add_label')) {
	$add_label = __( staggs_get_theme_option('sgg_product_add_label'), 'staggs' );
}
$remove_label = __( 'Remove', 'staggs' );
if (staggs_get_theme_option('sgg_product_remove_label')) {
	$remove_label = __( staggs_get_theme_option('sgg_product_remove_label'), 'staggs' );
}

$has_descriptions = false;
?>
<div class="option-group-options products products--<?php echo esc_attr( $sanitized_step['product_template'] ); ?>">
	<?php
	foreach ( $sanitized_step['options'] as $key => $option ) {
		// Out of stock and hidden.
		if ( $option['hide_option'] && 0 >= $option['stock_qty'] ) {
			continue;
		}

		$option_id    = staggs_sanitize_title( $sanitized_step['id'] . '_' . $key . '_' . $option['name'] );
		$option_name  = staggs_sanitize_title( $option['name'] );
		
		$preview_urls = get_option_preview_urls( $option, $sanitized_step['preview_index'] );

		$image_html   = '';
		if ( $option['image'] ) {
			$image_html = $option['image'];
		}

		$option_rules = '';
		if ( is_array( $option['conditional_rules'] ) && count( $option['conditional_rules'] ) > 0 ) {
			$option_rules = ' data-option-rules="' . urldecode( str_replace( '"', "'", wp_json_encode( $option['conditional_rules'] ) ) ) . '"';
		}

		if ( isset( $option['manage_stock'] ) && 'yes' === $option['manage_stock'] && 0 >= $option['stock_qty'] ) {
			$option_html = sprintf(
				'<label for="%1$s"%2$s>
					<input 
						id="%1$s" 
						type="radio"
						disabled>
					<span class="sgg-product out-of-stock">
						<span class="sgg-product-image">
							%3$s
						</span>
						<span class="sgg-product-info">
							<span class="sgg-product-title">
								<span class="sgg-product-name">%4$s</span>
								<span class="sgg-product-price">%5$s</span>
							</span>
						</span>
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
			$price = $option['price'];
			$sale = $option['sale_price'];

			$attributes   = '';
			$option_price = $sale !== -1 ? $sale : ( $price !== -1 ? $price : '' );
			$option_title = $option['name'];
			$price_html   = get_option_price_html( $price, $sale, $sanitized_step['inc_price_label'] );

			$attributes .= 'data-alt-price="' . $price . '"';
			
			if ( isset( $sanitized_step['show_option_price'] ) && 'hide' === $sanitized_step['show_option_price'] ) {
				$price_html = '';
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
			if ( isset( $option['stock_qty'] ) && '' !== $option['stock_qty'] ) {
				$attributes .= ' max="' . $option['stock_qty'] . '"';
			}
			if ( isset( $option['linked_product_id'] ) && '' !== $option['linked_product_id'] ) {
				$attributes .= ' data-product="' . $option['linked_product_id'] . '"';
			}
	
			$attributes = apply_filters( 'staggs_product_item_attributes', $attributes, $sanitized_step, $option, $key );

			if ( isset( $option['description'] ) && $option['description'] ) {
				$option_title .= '<a href="#0" class="show-panel show-panel-option" aria-label="' . __( 'Show description', 'staggs' ) . '">';
				$option_title .= staggs_get_icon( 'sgg_group_info_icon', 'panel-info' );
				$option_title .= '</a>';

				$has_descriptions = true;
			}

			if ( 'multiple' === $option['product_quantity'] ) {
				$option_html = sprintf(
					'<label for="%1$s"%11$s>
						<span class="sgg-product">
							<span class="sgg-product-image">
								%7$s
							</span>
							<span class="sgg-product-info">
								<span class="sgg-product-title">
									<span class="sgg-product-name">%10$s</span>
									<span class="sgg-product-price">%8$s</span>
								</span>
								<span class="sgg-product-action">
									%13$s
									<input 
										id="%1$s" 
										data-step-id="%2$s" 
										data-option-id="%3$s" 
										type="number" 
										name="%4$s" 
										data-index="%5$s" 
										data-price="%6$s" 
										value="0"
										%9$s>
									%14$s
								</span>
							</span>
						</span>
						%12$s
					</label>',
					$option_id, // 1.
					$sanitized_step['id'], // 2.
					$option['id'], // 3.
					$option_name, // 4.
					$step_key, // 5.
					$option_price, // 6.
					$image_html, // 7.
					$price_html, // 8.
					$attributes, // 9.
					$option_title, // 10.
					$option_rules, // 11.
					isset( $option['description'] ) ? '<div class="option-description hidden">' . $option['description'] . '</div>' : '', // 12.
					$sgg_minus_button, // 13.
					$sgg_plus_button // 14.
				);
			} else {
				$option_html = sprintf(
					'<label for="%1$s"%13$s>
						<span class="sgg-product">
							<span class="sgg-product-image">
								%7$s
							</span>
							<span class="sgg-product-info">
								<span class="sgg-product-title">
									<span class="sgg-product-name">%10$s</span>
									<span class="sgg-product-price">%8$s</span>
								</span>
								<span class="sgg-product-action">
									<input 
										id="%1$s" 
										data-step-id="%2$s" 
										data-option-id="%3$s" 
										type="checkbox" 
										name="%4$s" 
										data-index="%5$s" 
										data-price="%6$s" 
										value="1"
										%9$s>
									<span class="button">
										<span class="add">%11$s</span>
										<span class="del">%12$s</span>
									</span>
								</span>
							</span>
						</span>
						%14$s
					</label>',
					$option_id, // 1.
					$sanitized_step['id'], // 2.
					$option['id'], // 3.
					$option_name, // 4.
					$step_key, // 5.
					$option_price, // 6.
					$image_html, // 7.
					$price_html, // 8.
					$attributes, // 9.
					$option_title, // 10.
					$add_label, // 11.
					$remove_label, // 12.
					$option_rules, // 13.
					isset( $option['description'] ) ? '<div class="option-description hidden">' . $option['description'] . '</div>' : '' // 14.
				);
			}

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
