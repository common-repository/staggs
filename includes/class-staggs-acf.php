<?php

/**
 * The admin-specific ACF functionality of the plugin.
 *
 * @link       https://staggs.app
 * @since      1.5.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The admin-specific ACF functionality of the plugin.
 *
 * Defines the hooks that help load the ACF fields in the WordPress back-end.
 *
 * @package    Staggs/
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_ACF {

	/**
	 * Replace radio input labels with the corresponding images.
	 *
	 * @since    1.5.0
	 */
	public function sgg_load_radio_image_field( $field ) {
		if ( isset( $field['choices'] ) ) {
			$new_choices = array();

			foreach ( $field['choices'] as $name => $label ) {
				switch ( $label ) {
					case 'Classic Template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/classic.png">';
						break;
					case 'Floating Template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/floating.png">';
						break;
					case 'Full Template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/full.png">';
						break;
					case 'Steps Template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/steps.png">';
						break;
					case 'Popup Template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/popup.png">';
						break;
					case 'Vertical Popup':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/popup-vertical.png">';
						break;
					case 'Horizontal Popup':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/popup-horizontal.png">';
						break;
					case 'None':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/boxed.png">';
						break;
					case 'Bottom right':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/arrow-bottom-right.png">';
						break;
					case 'Bottom left':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/arrow-bottom-left.png">';
						break;
					case 'Center':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/arrow-center.png">';
						break;
					case 'Totals':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/usp-cart.png">';
						break;
					case 'Gallery':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/usp-gallery.png">';
						break;
					case 'Light Theme':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/light.png">';
						break;
					case 'Dark Theme':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/dark.png">';
						break;
					case 'Custom Theme':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/custom.png">';
						break;
					case 'Separator One':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/indicator-one.png">';
						break;
					case 'Separator Two':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/indicator-two.png">';
						break;
					case 'Separator Three':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/indicator-three.png">';
						break;
					case 'Default template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/pdf-default.png">';
						break;
					case 'Wide template':
						$label = '<img src="' . STAGGS_BASE_URL . 'admin/img/pdf-wide.png">';
						break;
					default:
						break;
				}

				$new_choices[ $name ] = $label;
			}

			$field['choices'] = $new_choices;
		}

		return $field;
	}

	/**
	 * Get available order statusses.
	 *
	 * @since    1.5.0
	 */
	public function sgg_load_woocommerce_order_statusses( $field ) {
		if ( isset( $field['choices'] ) ) {
			$new_choices = get_all_woocommerce_order_statusses();
			$field['choices'] = $new_choices;
		}
		return $field;
	}

	/**
	 * Get available staggs theme options
	 *
	 * @since    1.5.0
	 */
	public function sgg_load_staggs_themes( $field ) {
		if ( isset( $field['choices'] ) ) {
			$new_choices = get_configurator_themes_options();
			$field['choices'] = $new_choices;
		}
		return $field;
	}

	/**
	 * Get available builder attributes
	 *
	 * @since    1.5.0
	 */
	public function sgg_load_staggs_attributes( $field ) {
		if ( isset( $field['choices'] ) ) {
			$new_choices = get_configurator_attribute_values();
			$field['choices'] = $new_choices;
		}
		return $field;
	}
}
