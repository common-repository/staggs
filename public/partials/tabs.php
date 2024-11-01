<?php

/**
 * Provide a public-facing view for the Tabs step type.
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.1.0
 *
 * @package    Staggs
 * @subpackage Staggs/public/templates/shared
 */

global $sanitized_step;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="option-group-options tabs">
	<ul class="tab-list">
		<?php
		foreach ( $sanitized_step['tabs'] as $tab ) {
			$preview = isset( $tab['sgg_step_tab_preview_slide'] ) ? 'data-slide-preview="' . $tab['sgg_step_tab_preview_slide'] . '"' : '';
			?>
			<li>
				<a href="" data-tabs="<?php echo esc_attr( implode( ',', $tab['sgg_step_tab_attribute'] ) ); ?>"<?php echo wp_kses_normalize_entities(  $preview ); ?>>
					<?php echo esc_attr( $tab['sgg_step_tab_title'] ); ?>
				</a>
			</li>
			<?php
		}
		?>
	</ul>
</div>
