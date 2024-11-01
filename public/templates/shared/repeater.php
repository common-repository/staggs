<?php

/**
 * Provide a public-facing view for the repeater step type.
 *
 * This file is used to markup the public-facing repeater aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.1.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/templates/shared
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="staggs-repeater">
	<div class="staggs-repeater-header">
		<strong class="title"><?php echo esc_attr($step_attribute['text_title']); ?></strong>
	</div>
	<div class="staggs-repeater-main">
		<p class="repeater-empty-note"><?php echo esc_attr($step_attribute['text_empty']); ?></p>
		<div data-repeater-list="<?php echo esc_attr($step_attribute['repeater_id']); ?>" data-repeater-min="<?php echo esc_attr($step_attribute['repeater_min']); ?>"  data-repeater-max="<?php echo esc_attr($step_attribute['repeater_max']); ?>">
			<div data-repeater-item class="staggs-repeater-item">
				<div class="staggs-repeater-item-content">
					<?php
					foreach ( $step_attribute['attributes'] as $sanitized_step ) {
						if ( isset( $sanitized_step['preview_ref'] ) && '' !== $sanitized_step['preview_ref'] ) {
							$sanitized_step['preview_ref'] = $step_attribute['repeater_id'] . '-0-' . $sanitized_step['preview_ref'];
						}

						foreach ( $sanitized_step['options'] as $index => $option ) {
							$sanitized_step['options'][$index]['repeater_attribute'] = $step_attribute['repeater_id'];

							$sanitized_step['options'][$index]['id'] = $sanitized_step['options'][$index]['id'] . '-0';
							$sanitized_step['options'][$index]['group'] = $sanitized_step['id'] . '-0';

							if ( isset( $option['field_key'] ) ) {
								$sanitized_step['options'][$index]['field_key'] = $step_attribute['repeater_id'] . '-0-' . $sanitized_step['options'][$index]['field_key'];
							}
						}

						$sanitized_step['id'] = $sanitized_step['id'] . '-0';

						include STAGGS_BASE . 'public/templates/shared/attribute.php';
					}
					?>
				</div>
				<div class="staggs-repeater-item-action">
					<button data-repeater-delete type="button" class="button">
						<?php echo wp_kses_normalize_entities(  staggs_get_icon( 'sgg_repeater_delete_icon', 'trash' ) ); ?>
					</button>
				</div>
			</div>
		</div>
		<div class="staggs-repeater-footer">
			<button data-repeater-create type="button" class="button"><?php echo esc_attr( $step_attribute['text_add'] ); ?></button>
		</div>
	</div>
</div>