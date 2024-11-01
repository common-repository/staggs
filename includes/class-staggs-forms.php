<?php

/**
 * The configurator form functions for the plugin.
 *
 * @link       https://staggs.app
 * @since      1.4.0
 *
 * @package    Staggs/
 * @subpackage Staggs/includes
 */

if ( ! defined('ABSPATH') ) {
	die();
}

/**
 * The plugin form functions
 *
 * @package    Staggs/
 * @subpackage Staggs/includes
 */
class Staggs_Forms {

	/**
	 * Add support for Contact Form 7 values.
	 *
	 * @since    1.3.7.
	 */
	public function wpcf7_fill_form_values( $scanned_tag, $replace ) {
		if ( isset( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( strtolower( $key ) === strtolower( $scanned_tag['raw_name'] ) ) {
					// $field['default_value'] = 
					$scanned_tag['values'][] = $value;
				}
			}

			if ( strtolower( $scanned_tag['raw_name'] ) === 'configuration' ) {
				$scanned_tag['values'][] = $this->format_configuration_value();
			}
		}
		return $scanned_tag;
	}

	/**
	 * Add support for Ninja Forms values.
	 *
	 * @since    1.3.7.
	 */
	public function na_fill_form_values( $default_value, $field_type, $field_settings ) {
		if ( isset( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( strtolower( $key ) === strtolower( $field_settings['label'] ) ) {
					$default_value = $value;
				}
			}
			if ( strtolower( $field_settings['label'] ) === 'configuration' ) {
				$default_value = $this->format_configuration_value();
			}
		}
		return $default_value;
	}

	/**
	 * Run smart tags on all field labels.
	 *
	 * @link   https://wpforms.com/developers/wpforms_textarea_field_display/
	 *
	 * @param  array $field        Sanitized field data.
	 * @param  array $form_data    Form data and settings.
	 *
	 * return  array
	 */
	public function wpf_fill_form_values( $field, $form_data ) {
		if ( isset( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( strtolower( $key ) === strtolower( $field['label'] ) ) {
					$field['default_value'] = $value;
				}
			}
			if ( strtolower( $field['label'] ) === 'configuration' ) {
				$field['default_value'] = $this->format_configuration_value();
			}
		}
		return $field;
	}

	/**
	 * Helper function for formatting configuration for textarea field.
	 * 
	 * @since 1.10.0
	 */
	private function format_configuration_value() {
		$options_text = '';

		if ( isset( $_GET['product_name'] ) ) {
			$options_text .= 'Product: ' . $_GET['product_name'] . "\r\n";
		}
		
		foreach ( $_GET as $name => $value ) {
			if ( 'product_name' === $name || 'product_pdf' === $name || 'product_price' === $name ) {
				continue;
			}
			$options_text .= ucfirst( str_replace( '-', ' ', $name ) ) . ': ' . $value . "\r\n";
		}

		if ( isset( $_GET['product_price'] ) ) {
			$options_text .= 'Price: ' . $_GET['product_price'] . "\r\n";
		}

		return $options_text;
	}
}
