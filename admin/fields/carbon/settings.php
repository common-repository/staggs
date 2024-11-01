<?php

/**
 * Provide a admin-facing view for the General Settings Fields form of the plugin
 *
 * This file is used to markup the admin-facing settings form of the plugin admin side.
 *
 * @link       https://staggs.app
 * @since      1.4.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin/fields
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$price_fields = array(
	Field::make( 'separator', 'sgg_setting_price_tab_label', __( 'Price settings', 'staggs' ) ),
);

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	array_push( $price_fields,
		Field::make( 'select', 'sgg_price_currency_pos', __( 'Currency position', 'staggs' ) )
			->set_help_text( __( 'Select the position of the currency symbol', 'staggs' ) )
			->set_width( 20 )
			->add_options( array(
				'left_space' => __( 'Left with a space', 'staggs' ),
				'left' => __( 'Left', 'staggs' ),
				'right_space' => __( 'Right with a space', 'staggs' ),
				'right' => __( 'Right', 'staggs' ),
			)),
		Field::make( 'text', 'sgg_price_currency_symbol', __( 'Currency symbol', 'staggs' ) )
			->set_help_text( __( 'Optionally enter your currency symbol', 'staggs' ) )
			->set_width( 80 ),
		Field::make( 'text', 'sgg_price_thousand_sep', __( 'Thousand separator', 'staggs' ) )
			->set_width( 33 ),
		Field::make( 'text', 'sgg_price_decimal_sep', __( 'Decimal separator', 'staggs' ) )
			->set_width( 33 ),
		Field::make( 'text', 'sgg_price_decimal_number', __( 'Number of decimals', 'staggs' ) )
			->set_width( 33 )
			->set_attribute( 'placeholder', '2' ),
	);
} else {
	array_push( $price_fields,
		Field::make( 'html', 'sgg_product_price_settings' )
			->set_html( '<p>Configure product price settings in WooCommerce -> Settings -> Currency options.</p>' ),
	);
}

$settings = Container::make( 'theme_options', __( 'Settings', 'staggs' ) )
	->set_page_parent( 'edit.php?post_type=sgg_attribute' )
	->set_layout( 'tabbed-vertical' )
	->set_page_file( 'appearance' )
	->add_tab( __( 'Brand', 'staggs' ), array(
		Field::make( 'image', 'sgg_logo', __( 'Company Logo', 'staggs' ) )
			->set_help_text( __( 'Note: please keep image sizes lower than 2 MB to maintain the configurator performance.', 'staggs' ) ),
		Field::make( 'text', 'sgg_company_name', __( 'Company Name', 'staggs' ) ),
		Field::make( 'text', 'sgg_font_family', __( 'Font Family', 'staggs' ) )
			->set_help_text( __( 'If you need a custom font, you should enter the Font Family name here.', 'staggs' ) ),
		Field::make( 'footer_scripts', 'sgg_footer_scripts', __( 'Footer Scripts', 'staggs' ) )
			->set_help_text( __( 'If you need to add scripts to your footer (like Google Fonts), you should enter them here.', 'staggs' ) )
			->set_hook_name( 'wp_footer' ),
	) )
	->add_tab( __( 'Labels', 'staggs' ), array(

		Field::make( 'text', 'sgg_gallery_fullscreen_label', __( 'Fullscreen button label', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Toggle fullscreen mode', 'staggs' ))
			->set_width( 50 ),
		Field::make( 'text', 'sgg_gallery_camera_label', __( 'Camera button label', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Capture configuration image', 'staggs' ))
			->set_width( 50 ),
		Field::make( 'text', 'sgg_gallery_reset_label', __( 'Reset button label', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Reset configuration', 'staggs' )),
	
		Field::make( 'text', 'sgg_configurator_summary_table_title', __( 'Product total label', 'staggs' ) )
			->set_attribute( 'placeholder', ' Your configuration' ),

		Field::make( 'text', 'sgg_product_required_error_message', __( 'Required configuration error message', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Please fill out all required fields!', 'staggs' ))
			->set_help_text( __( 'Optionally override required error message.', 'staggs' ) )
			->set_width( 50 ),
		Field::make( 'text', 'sgg_product_invalid_error_message', __( 'Invalid configuration error message', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Please make sure all fields are filled out correctly!', 'staggs' ))
			->set_help_text( __( 'Optionally override invalid error message.', 'staggs' ) )
			->set_width( 50 ),

		Field::make( 'text', 'sgg_product_required_field_message', __( 'Required configuration field message', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'This field is required.', 'staggs' ))
			->set_help_text( __( 'Optionally override required field message.', 'staggs' ) )
			->set_width( 50 ),
		Field::make( 'text', 'sgg_product_invalid_field_message', __( 'Invalid configuration field message', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'This field is invalid.', 'staggs' ))
			->set_help_text( __( 'Optionally override invalid field message.', 'staggs' ) )
			->set_width( 50 ),

	) )
	->add_tab( __( 'Icons', 'staggs' ), array(
		Field::make( 'image', 'sgg_slider_arrow_left', __( 'Gallery Arrow Left', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_slider_arrow_left_dark', __( 'Gallery Arrow Left (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_slider_arrow_right', __( 'Gallery Arrow Right', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_slider_arrow_right_dark', __( 'Gallery Arrow Right (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_select_arrow', __( 'Select Arrow Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_select_arrow_dark', __( 'Select Arrow Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_checkmark', __( 'Checkmark Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_checkmark_dark', __( 'Checkmark Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_close_icon', __( 'Panel Close Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_close_icon_dark', __( 'Panel Close Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_close_icon', __( 'Close Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_close_icon_dark', __( 'Close Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_plus_icon', __( 'Description Plus Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_plus_icon_dark', __( 'Description Plus Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_minus_icon', __( 'Description Minus Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_minus_icon_dark', __( 'Description Minus Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_info_icon', __( 'Info Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_group_info_icon_dark', __( 'Info Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_popup_back_icon', __( 'Popup Back Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_popup_back_icon_dark', __( 'Popup Back Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_separator_collapse_icon', __( 'Separator Collapse Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_separator_collapse_icon_dark', __( 'Separator Collapse Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_fullscreen_icon', __( 'Fullscreen Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_fullscreen_icon_dark', __( 'Fullscreen Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_fullscreen_close_icon', __( 'Fullscreen Close Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_fullscreen_close_icon_dark', __( 'Fullscreen Close Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_calendar_icon', __( 'Calendar Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_calendar_icon_dark', __( 'Calendar Icon (Dark Theme)', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_zoom_icon', __( 'Image Zoom Icon', 'staggs' ) )
			->set_width( 25 ),
		Field::make( 'image', 'sgg_zoom_icon_dark', __( 'Image Zoom Icon (Dark Theme)', 'staggs' ) )
			->set_width( 75 ),
	))
	->add_tab( __( 'Attributes', 'staggs' ), array(

		Field::make( 'checkbox', 'sgg_product_dropdown_disable_placeholder', __( 'Disable dropdown placeholders', 'staggs' ) )
			->set_help_text( __( 'Disable dropdown placeholders for non-required dropdowns.', 'staggs' ) ),
		
		Field::make( 'checkbox', 'sgg_product_url_option_disable_click', __( 'Disable click for URL options', 'staggs' ) )
			->set_help_text( __( 'Removes the default click handler for URL options so you can handle the link your own way.', 'staggs' ) ),

		Field::make( 'text', 'sgg_step_included_text', __( 'Included Option Price label', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_product_additional_price_sign', __( 'Show sign for additional prices', 'staggs' ) )
			->set_help_text( __( 'Displays a plus sign before the option price to indicate additional charges (+$99).', 'staggs' ) )
			->set_width( 50 ),
		Field::make( 'checkbox', 'sgg_product_price_trim_decimals', __( 'Trim price decimals on full prices', 'staggs' ) )
			->set_help_text( __( 'Display prices that ends on ",00" in a format of ",-"', 'staggs' ) )
			->set_width( 50 ),

		Field::make( 'checkbox', 'sgg_product_price_show_difference_only', __( 'Show price difference only for option prices', 'staggs' ) )
			->set_help_text( __( 'Dynamically update difference in price options based on current selected option', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_product_show_individual_error_messages', __( 'Display individual error messages for each attribute', 'staggs' ) )
			->set_help_text( __( 'Adds error messages to all invalid fields instead of one general alert.', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_product_summary_hide_duplicate_titles', __( 'Hide duplicate attribute titles in summary widget', 'staggs' ) )
			->set_help_text( __( 'If multi select is active, show attribute title for first option only.', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_product_number_input_show_icons', __( 'Include button controls in number inputs', 'staggs' ) )
			->set_help_text( __( 'Shows plus and minus button icons for number input fields.', 'staggs' ) ),

	))
	->add_tab( __( 'WooCommerce', 'staggs' ), array(
		Field::make( 'text', 'sgg_product_prefix_label', __( 'Price prefix', 'staggs' ) )
			->set_help_text( __( 'Optionally display price prefix label on archives. E.g. From:', 'staggs' ) )
			->set_width( 50 ),

		Field::make( 'text', 'sgg_product_loop_cart_label', __( 'Configurator loop add to cart button label', 'staggs' ) )
			->set_width( 50 ),

		Field::make( 'text', 'sgg_product_price_format', __( 'Price format', 'staggs' ) )
			->set_help_text( __( 'Optionally override price display on archives. E.g. {min_price} - {max_price}. Available placeholders: {price}, {sale_price}, {min_price}, {max_price}', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_product_exclude_base_price', __( 'Exclude product base price in totals', 'staggs' ) )
			->set_help_text( __( 'Exclude WooCommerce product price from total price calculation.', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_product_show_price_logged_in_users', __( 'Show prices for logged in users only', 'staggs' ) )
			->set_help_text( __( 'Hide pricings for website visitors. Show prices to logged in customers', 'staggs' ) ),
			
		Field::make( 'checkbox', 'sgg_product_redirect_visitors_to_login_page', __( 'Redirect visitors to login page', 'staggs' ) )
			->set_help_text( __( 'Redirects visitors to the login page when clicking the main configuration button.', 'staggs' ) ),
			
		Field::make( 'text', 'sgg_product_tax_label', __( 'Price tax suffix', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Inc. VAT', 'staggs' ))
			->set_help_text( __( 'Optionally display price tax suffix in configurator totals.', 'staggs' ) ),

	))
	->add_tab( __( 'Price settings', 'staggs' ), $price_fields )
	->add_tab( __( 'Checkout', 'staggs' ), array(
		Field::make( 'checkbox', 'sgg_order_save_attribute_prices', __( 'Save attribute option prices to order items', 'staggs' ) )
			->set_help_text( __( 'Saves the individual option prices to the attributes in the order item for later reference.', 'staggs' ) ),
		Field::make( 'text', 'sgg_checkout_image_width', __( 'Product image width', 'staggs' ) )
			->set_default_value( 70 )
			->set_width( 50 )
			->set_help_text( __( 'Enter the width value of the preview image in pixels (px).', 'staggs' ) ),
		Field::make( 'text', 'sgg_checkout_image_height', __( 'Product image height', 'staggs' ) )
			->set_default_value( 70 )
			->set_width( 50 )
			->set_help_text( __( 'Enter the height value of the preview image in pixels (px).', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_checkout_display_image', __( 'Display generated product image on checkout page', 'staggs' ) )
			->set_help_text( __( 'Displays product image in table on checkout page when enabled. Applies to all products.', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_view_order_display_image', __( 'Display generated product image on order pages', 'staggs' ) )
			->set_help_text( __( 'Displays product image in tables on order pages when enabled. Applies to all products.', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_confirmation_email_display_image', __( 'Display generated product image in order emails', 'staggs' ) )
			->set_help_text( __( 'Displays product image in WooCommerce order emails when enabled. Applies to all products.', 'staggs' ) ),
	))
	->add_tab( __( 'Advanced', 'staggs' ), array(
		Field::make( 'checkbox', 'sgg_admin_display_edit_links', __( 'Enable inline edit links in attribute titles', 'staggs' ) )
			->set_help_text( __( 'Display edit links in attribute title in configurator for site administrators to edit attributes quickly.', 'staggs' ) ),
		Field::make( 'set', 'sgg_analytics_order_statusses', __( 'Analytics Order statusses', 'staggs' ) )
			->set_help_text( __( 'Define the order statusses that analytics should display.', 'staggs' ) )
			->add_options( 'get_all_woocommerce_order_statusses' )
			->set_default_value( array( 'wc-processing', 'wc-completed' )),
		Field::make( 'textarea', 'sgg_custom_css', __( 'Custom Styles', 'staggs' ) )
			->set_classes( 'cf-header-scripts-custom-css' )
			->set_help_text( __( 'If you need to add custom styling, you should enter the CSS here.', 'staggs' ) ),
	));
