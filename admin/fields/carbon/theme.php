<?php

/**
 * Provide a admin-facing view for the Staggs Theme form of the plugin
 *
 * This file is used to markup the admin-facing theme form of the plugin admin side.
 *
 * @link       https://staggs.app
 * @since      1.4.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin/fields
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$usp_labels = array(
	'plural_name' => 'USPs',
	'singular_name' => 'USP',
);

/**
 * Settings meta
 */
Container::make( 'post_meta', __( 'Staggs Configurator Template', 'staggs' ) )
	->where( 'post_type', '=', 'sgg_theme' )
	->set_layout( 'tabbed-vertical' )
	->add_tab( __( 'Template', 'staggs' ), array(
		Field::make( 'separator', 'sgg_appearance_panel_general', __( 'Template', 'staggs' ) ),

		Field::make( 'select', 'sgg_configurator_gallery_type', __( 'Configurator Type', 'staggs' ) )
			->add_options( array(
				'regular' => __( 'Image', 'staggs' ),
			)),

		Field::make( 'radio', 'sgg_configurator_page_template', __( 'Staggs Configurator template', 'staggs' ) )
			->set_options( array(
				'staggs' => __( 'Staggs Product Configurator Template', 'staggs' ), // Let's go Staggs all the way
				'default' => __( 'WooCommerce Page Template', 'staggs' ), // Replaces Woo gallery and adds attributes to cart form
				'none' => __( 'Attributes Only Template', 'staggs' ), // Only adds attributes to cart form
			))
			->set_help_text( __( 'Set to WooCommerce Page Template to use your theme\'s product page or default WooCommerce Product Page template', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_disable_attribute_styles', __( 'Disable default Staggs configurator styles', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_page_template',
				'value' => 'staggs',
				'compare' => '!=',
			))),

		Field::make( 'radio_image', 'sgg_configurator_view', __( 'Template', 'staggs' ) )
			->add_options( array(
				'classic'  => STAGGS_BASE_URL . 'admin/img/classic.png',
				'floating' => STAGGS_BASE_URL . 'admin/img/floating.png',
				'full'     => STAGGS_BASE_URL . 'admin/img/full.png',
				'popup'    => STAGGS_BASE_URL . 'admin/img/popup.png',
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_page_template',
				'value' => 'staggs',
			))),

		Field::make( 'text', 'sgg_step_popup_button_text', __( 'Configurator Popup Button Text', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
				'compare' => '=',
			))),
		Field::make( 'checkbox', 'sgg_configurator_popup_mobile_inline', __( 'Show contents of popup inline on mobile', 'staggs' ) )
			->set_help_text( __( 'Disable sticky elements in popup on mobile view (natural scroll)', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
				'compare' => '=',
			))),

		Field::make( 'checkbox', 'sgg_step_set_included_option_text', __( 'Set Included Option Label', 'staggs' ) ),

		Field::make( 'text', 'sgg_step_included_text', __( 'Included Option Text', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_set_included_option_text',
				'value' => true,
			))),

		Field::make( 'checkbox', 'sgg_step_disable_default_option', __( 'Disable default option selection on load', 'staggs' ) )
			->set_help_text( __( 'Disables the automatic selection of the first available option on configurator load', 'staggs' ) ),

	))
	->add_tab( __( 'Template options', 'staggs' ), array(

		Field::make( 'separator', 'sgg_settings_panel_general', __( 'Template options', 'staggs' ) ),

		Field::make( 'radio', 'sgg_show_theme_header_footer', __( 'Header and Footer display', 'staggs' ) )
			->set_options( array(
				'none' => __( 'Hide header and footer', 'staggs' ),
				'both' => __( 'Show header and footer', 'staggs' ),
				'header' => __( 'Show header only', 'staggs' ),
				'footer' => __( 'Show footer only', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'floating', 'full' ),
				'compare' => 'IN',
			))),

		Field::make( 'radio', 'sgg_configurator_layout', __( 'Layout', 'staggs' ) )
			->add_options( array(
				'right' => __( 'Image Preview Right', 'staggs' ),
				'left' => __( 'Image Preview Left', 'staggs' ),
			))
			->set_width( 50 ),
		Field::make( 'radio', 'sgg_configurator_text_align', __( 'Text Align', 'staggs' ) )
			->add_options( array(
				'center' => __( 'Center', 'staggs' ),
				'left' => __( 'Left', 'staggs' ),
			))
			->set_width( 50 ),
		Field::make( 'radio', 'sgg_configurator_borders', __( 'Borders', 'staggs' ) )
			->add_options( array(
				'rounded' => __( 'Rounded', 'staggs' ),
				'squared' => __( 'Squared', 'staggs' ),
				'pill' => __( 'Circular', 'staggs' ),
			))
			->set_width( 50 ),
		Field::make( 'radio', 'sgg_configurator_step_description_type', __( 'Description type', 'staggs' ) )
			->add_options( array(
				'panel' => __( 'Panel', 'staggs' ),
				'popup' => __( 'Popup', 'staggs' ),
				'tooltip' => __( 'Tooltip', 'staggs' ),
			))
			->set_width( 50 ),

		Field::make( 'checkbox', 'sgg_show_logo', __( 'Show Company Logo', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_show_logo_options', __( 'Show company logo in options panel', 'staggs' ) )
			->set_help_text( __( 'Displays the company logo above the title.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_show_logo',
				'value' => true,
			), array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'classic', 'floating', 'full' ),
				'compare' => 'IN',
			))),

		Field::make( 'checkbox', 'sgg_configurator_disable_floating_notice', __( 'Disable Staggs floating add to cart notices', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_configurator_disable_product_price', __( 'Disable WooCommerce Product Price', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_configurator_disable_product_price_update', __( 'Disable updating the single WooCommerce Product Price', 'staggs' ) )
			->set_help_text( __( 'Updates the default WooCommerce Single Product price to match the total configuration price by default.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_disable_product_price',
				'value' => false,
			) ) ),
		Field::make( 'checkbox', 'sgg_configurator_disable_product_meta', __( 'Disable WooCommerce Product Meta (SKU, Category)', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_configurator_disable_product_tabs_override', __( 'Disable Staggs WooCommerce Product Tabs override', 'staggs' ) )
			->set_help_text( __( 'Enable this option if you do not want the tabs to be relocated.', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_configurator_disable_product_tabs', __( 'Disable WooCommerce Product Tabs', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_configurator_disable_template_override', __( 'Disable WooCommerce Template Override', 'staggs' ) )
			->set_help_text( __( 'Only applicable when you want to output the configurator contents using shortcode(s).', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_show_close_button', __( 'Show close button', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
				'compare' => '!=',
			))),
		Field::make( 'text', 'sgg_back_page_url', __( 'Back page URL', 'staggs' ) )
			->set_help_text( __( 'Configure logo / back button URL. Defaults to home URL.', 'staggs' ) )
			->set_conditional_logic( array(
				'relation' => 'OR',
				 array(
					'field' => 'sgg_show_logo',
					'value' => true,
				),
				array(
				   'field' => 'sgg_show_logo_options',
				   'value' => true,
			   ),
				array(
				   'field' => 'sgg_show_close_button',
				   'value' => true,
			   ),
			)),
		Field::make( 'checkbox', 'sgg_show_close_button_message', __( 'Show close button confirm message', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_show_close_button',
				'value' => true,
			))),
		Field::make( 'text', 'sgg_close_button_message', __( 'Close button confirm message', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_show_close_button_message',
				'value' => true,
			))),

	))
	->add_tab( __( 'Gallery', 'staggs' ), array(
		Field::make( 'separator', 'sgg_appearance_product_display', __( 'Gallery', 'staggs' ) ),

		Field::make( 'radio', 'sgg_configurator_template_height', __( 'Template height', 'staggs' ) )
			->add_options( array(
				'full_height' => __( 'Force full height of screen on desktop', 'staggs' ),
				'image_height' => __( 'Use image height as reference for configurator height', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),

		Field::make( 'checkbox', 'sgg_gallery_scale_mobile_display', __( 'Block mobile gallery image overflow', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_configurator_gallery_sticky', __( 'Make Gallery Image Sticky', 'staggs' ) )
			->set_help_text( __( 'Keep image preview in view when scrolling down the options list', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'classic',
			))),

		Field::make( 'radio', 'sgg_mobile_gallery_display', __( 'Gallery Display on Mobile Devices', 'staggs' ) )
			->add_options( array(
				'fixed' => __( 'Fixed', 'staggs' ),
				'inline' => __( 'Inline', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
				'compare' => '!=',
			))),

		Field::make( 'text', 'sgg_configurator_fixed_header_height', __( 'Fixed mobile header offset', 'staggs' ) )
			->set_help_text( __( 'Enter offset of mobile header in pixels. (Enter numeric value)', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_mobile_gallery_display',
				'value' => 'fixed',
			), array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
				'compare' => '!=',
			))),

		Field::make( 'radio', 'sgg_preview_image_type', __( 'Preview Type', 'staggs' ) )
			->add_options( array(
				'stacked' => __( 'Stacked Image Preview', 'staggs' ),
				'single' => __( 'Single Image Preview', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),

		Field::make( 'checkbox', 'sgg_use_product_image', __( 'Display product featured image', 'staggs' ) )
			->set_help_text( __( 'Use the product featured image as the base layer for the preview.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			), array(
				'field' => 'sgg_preview_image_type',
				'value' => 'stacked',
			))),

		Field::make( 'checkbox', 'sgg_configurator_generate_cart_image', __( 'Generate Active Configuration Image for Cart', 'staggs' ) )
			->set_help_text( __( 'Product featured image will be shown by default.', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_configurator_store_cart_image', __( 'Store Configuration Previews in Uploads Folder for Later Access', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_generate_cart_image',
				'value' => true,
			))),

	))
	->add_tab( __( 'Gallery background', 'staggs' ), array(
		Field::make( 'separator', 'sgg_background_product_display', __( 'Gallery background', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_show_bg_image', __( 'Add Background Image', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_page_template',
				'value' => 'staggs',
			), array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),

		Field::make( 'media_gallery', 'sgg_bg_image', __( 'Background Image', 'staggs' ) )
			->set_duplicates_allowed( false )
			->set_type( array( 'image' ) )
			->set_help_text( __( 'Select one or more background images. Note: please keep image sizes lower than 2 MB to maintain the configurator performance.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_page_template',
				'value' => 'staggs',
			), array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			), array(
				'field' => 'sgg_show_bg_image',
				'value' => true,
			))),

		Field::make( 'radio', 'sgg_bg_image_size', __( 'Background Image Size', 'staggs' ) )
			->add_options( array(
				'contain' => __( 'Contain', 'staggs' ),
				'cover' => __( 'Cover', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),

		Field::make( 'checkbox', 'sgg_stretch_bg_image', __( 'Stretch Background Image Behind Options', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'floating',
			), array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			), array(
				'field' => 'sgg_show_bg_image',
				'value' => true,
			))),
		Field::make( 'checkbox', 'sgg_capture_bg_image', __( 'Capture Background Image In Export', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_page_template',
				'value' => 'staggs',
			), array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			), array(
				'field' => 'sgg_show_bg_image',
				'value' => true,
			))),

	))
	->add_tab( __( 'Gallery controls', 'staggs' ), array(
		Field::make( 'separator', 'sgg_background_controls_display', __( 'Gallery controls', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_gallery_fullscreen_display', __( 'Display button to show gallery fullscreen', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_gallery_camera_display', __( 'Display button to capture configuration image', 'staggs' ) ),
		Field::make( 'checkbox', 'sgg_gallery_reset_display', __( 'Display button to reset configuration', 'staggs' ) ),

		Field::make( 'radio', 'sgg_configurator_thumbnails', __( 'Display Gallery Thumbnails', 'staggs' ) )
			->add_options( array(
				'none' => __( 'None', 'staggs' ),
				'thumbnails' => __( 'Thumbnails', 'staggs' ),
				'labels' => __( 'Labels', 'staggs' ),
			))
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),
		Field::make( 'radio', 'sgg_configurator_thumbnails_align', __( 'Gallery Thumbnails Align', 'staggs' ) )
			->add_options( array(
				'left' => __( 'Left', 'staggs' ),
				'center' => __( 'Center', 'staggs' ),
				'right' => __( 'Right', 'staggs' ),
			))
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),
		Field::make( 'radio', 'sgg_configurator_thumbnails_position', __( 'Gallery Thumbnails Position', 'staggs' ) )
			->add_options( array(
				'inside' => __( 'Inside main image', 'staggs' ),
				'under' => __( 'Underneath main image', 'staggs' ),
			))
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'classic',
			), array(
				'field' => 'sgg_configurator_thumbnails',
				'value' => 'thumbnails',
			), array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),
		Field::make( 'radio', 'sgg_configurator_thumbnails_layout', __( 'Gallery Thumbnails Layout', 'staggs' ) )
			->add_options( array(
				'inline' => __( 'Single row of images', 'staggs' ),
				'grid' => __( 'Image grid', 'staggs' ),
			))
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'classic',
			), array(
				'field' => 'sgg_configurator_thumbnails',
				'value' => 'thumbnails',
			), array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			), array(
				'field' => 'sgg_configurator_thumbnails_position',
				'value' => 'under',
			))),

		Field::make( 'text', 'sgg_configurator_thumbnail_labels', __( 'Gallery Thumbnail Labels', 'staggs' ) )
			->set_help_text( 'Enter labels in comma-separated format for the image views (e.g. front, back, inside, top)')
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			), array(
				'field' => 'sgg_configurator_thumbnails',
				'value' => 'labels',
			))),
		Field::make( 'radio_image', 'sgg_configurator_arrows', __( 'Preview arrow controls', 'staggs' ) )
			->add_options( array(
				'none' => STAGGS_BASE_URL . 'admin/img/boxed.png',
				'bottom_right' => STAGGS_BASE_URL . 'admin/img/arrow-bottom-right.png',
				'bottom_left' => STAGGS_BASE_URL . 'admin/img/arrow-bottom-left.png',
				'center' => STAGGS_BASE_URL . 'admin/img/arrow-center.png',
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_gallery_type',
				'value' => 'regular',
			))),
	))
	->add_tab( __( 'Colors', 'staggs' ), array(

		Field::make( 'separator', 'sgg_theme_panel_general', __( 'Colors', 'staggs' ) ),

		Field::make( 'radio_image', 'sgg_configurator_theme', __( 'Color theme', 'staggs' ) )
			->add_options( array(
				'light' => STAGGS_BASE_URL . 'admin/img/light.png',
				'dark' => STAGGS_BASE_URL . 'admin/img/dark.png',
				'custom' => STAGGS_BASE_URL . 'admin/img/custom.png',
			)),
		Field::make( 'color', 'sgg_primary_color', __( 'Primary Color', 'staggs' ) )
			->set_help_text( __( 'Options panel background color', 'staggs' ) )
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_theme',
				'value' => 'custom',
			))),
		Field::make( 'color', 'sgg_secondary_color', __( 'Secondary Color', 'staggs' ) )
			->set_help_text( __( 'Image view background color', 'staggs' ) )
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_theme',
				'value' => 'custom',
			))),
		Field::make( 'color', 'sgg_tertiary_color', __( 'Tertiary Color', 'staggs' ) )
			->set_help_text( __( 'Options cards background color', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_theme',
				'value' => 'custom',
			))),
		Field::make( 'color', 'sgg_heading_color', __( 'Heading Color', 'staggs' ) )
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_theme',
				'value' => 'custom',
			))),
		Field::make( 'color', 'sgg_text_color', __( 'Text Color', 'staggs' ) )
			->set_width( 50 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_theme',
				'value' => 'custom',
			))),
		Field::make( 'radio', 'sgg_configurator_icon_theme', __( 'Icon Theme', 'staggs' ) )
			->add_options( array(
				'light' => __( 'Light Icons', 'staggs' ),
				'dark' => __( 'Dark Icons', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_theme',
				'value' => 'custom',
			))),

		Field::make( 'color', 'sgg_accent_color', __( 'Accent Color', 'staggs' ) )
			->set_width( 50 ),
		Field::make( 'color', 'sgg_button_text_color', __( 'Button Text Color', 'staggs' ) )
			->set_width( 50 ),

	))
	->add_tab( __( 'Spacing', 'staggs' ), array(
		
		Field::make( 'radio', 'sgg_configurator_step_density', __( 'Step Density', 'staggs' ) )
			->add_options( array(
				'default' => __( 'Default', 'staggs' ),
				'compact' => __( 'Compact', 'staggs' ),
			)),

		Field::make( 'text', 'sgg_template_form_options_width', __( 'Configurator form width', 'staggs' ) )
			->set_help_text( __( 'Optionally override default form options width. Enter CSS units. Defaults to 540px', 'staggs' ) )
			->set_width(50),
		Field::make( 'text', 'sgg_template_form_options_width_tablet', __( 'Configurator form width (tablet)', 'staggs' ) )
			->set_help_text( __( 'Optionally override default form options width for tablets. Enter CSS units. Defaults to 400px', 'staggs' ) )
			->set_width(50),

		Field::make( 'text', 'sgg_template_form_options_padding', __( 'Configurator form spacing', 'staggs' ) )
			->set_help_text( __( 'Optionally override default form options spacing. Enter CSS units. Defaults to 50px', 'staggs' ) )
			->set_width(50),
		Field::make( 'text', 'sgg_template_form_options_padding_tablet', __( 'Configurator form spacing (tablet)', 'staggs' ) )
			->set_help_text( __( 'Optionally override default form options spacing for tablets. Enter CSS units. Defaults to 40px', 'staggs' ) )
			->set_width(50),

		Field::make( 'text', 'sgg_attribute_spacing_top', __( 'Attribute spacing top', 'staggs' ) )
			->set_help_text( __( 'Optionally override default attribute spacing top. Enter CSS units', 'staggs' ) )
			->set_width(50),
		Field::make( 'text', 'sgg_attribute_spacing_top_mobile', __( 'Attribute spacing top (mobile)', 'staggs' ) )
			->set_help_text( __( 'Optionally override default attribute spacing top for mobile. Enter CSS units', 'staggs' ) )
			->set_width(50),
		Field::make( 'text', 'sgg_attribute_spacing_bottom', __( 'Attribute spacing bottom', 'staggs' ) )
			->set_help_text( __( 'Optionally override default attribute spacing bottom. Enter CSS units', 'staggs' ) )
			->set_width(50),
		Field::make( 'text', 'sgg_attribute_spacing_bottom_mobile', __( 'Attribute spacing bottom (mobile)', 'staggs' ) )
			->set_help_text( __( 'Optionally override default attribute spacing bottom for mobile. Enter CSS units', 'staggs' ) )
			->set_width(50),

	))
	->add_tab( __( 'Fonts', 'staggs' ), array(
		
		Field::make( 'separator', 'sgg_configurator_template_fonts', __( 'Fonts', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_configurator_custom_font', __( 'Define your own configurator font', 'staggs' ) )
			->set_help_text( __( 'This setting overrides the default font settings defined in the Appearance page.', 'staggs' ) ),
		Field::make( 'text', 'sgg_font_family', __( 'Font Family', 'staggs' ) )
			->set_help_text( __( 'If you need a custom font, you should enter the Font Family name here.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_custom_font',
				'value' => true,
			))),
		Field::make( 'textarea', 'sgg_header_scripts', __( 'Font Scripts', 'staggs' ) )
			->set_help_text( __( 'If you need to add scripts to your header (like Google Font scripts), you should enter them here.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_custom_font',
				'value' => true,
			))),

		Field::make( 'checkbox', 'sgg_theme_disable_cart_styles', __( 'Disable add to cart button styles', 'staggs' ) )
			->set_help_text( __( 'This setting overrides the default Staggs template styles for add to cart button.', 'staggs' ) ),

	))
	->add_tab( __( 'Separator', 'staggs' ), array(

		Field::make( 'separator', 'sgg_settings_panel_general_options', __( 'Separator', 'staggs' ) ),

		Field::make( 'radio_image', 'sgg_configurator_step_indicator', __( 'Separator Template', 'staggs' ) )
			->add_options( array(
				'one'   => STAGGS_BASE_URL . 'admin/img/indicator-one.png',
				'two'   => STAGGS_BASE_URL . 'admin/img/indicator-two.png',
				'three' => STAGGS_BASE_URL . 'admin/img/indicator-three.png',
			)),

	))
	->add_tab( __( 'Summary', 'staggs' ), array(

		Field::make( 'separator', 'sgg_settings_panel_general_summary', __( 'Summary', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_configurator_display_summary', __( 'Display summary of selected configuration options', 'staggs' ) ),
		Field::make( 'text', 'sgg_configurator_summary_title', __( 'Summary title', 'staggs' ) )
			->set_help_text( __( 'Title that is displayed above the selected options list', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'Your configuration', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_display_summary',
				'value' => true,
			))),
		Field::make( 'text', 'sgg_configurator_summary_empty_message', __( 'Summary empty message', 'staggs' ) )
			->set_help_text( __( 'Message that appears when no option is selected', 'staggs' ) )
			->set_attribute( 'placeholder', __( 'No options selected', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_display_summary',
				'value' => true,
			))),
		Field::make( 'radio', 'sgg_configurator_summary_location', __( 'Summary location', 'staggs' ) )
			->set_help_text( __( 'Choose where the summary widget should be displayed', 'staggs' ) )
			->add_options( array(
				'before_totals' => __( 'Before configurator totals', 'staggs' ),
				'after_totals' => __( 'After configurator totals', 'staggs' ),
				'shortcode' => __( 'Anywhere using the shortcode', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_display_summary',
				'value' => true,
			))),
		Field::make( 'checkbox', 'sgg_configurator_summary_include_notes', __( 'Include attribute option notes in summary', 'staggs' ) )
			->set_help_text( __( 'Extend summary details by including option notes', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_display_summary',
				'value' => true,
			))),
		Field::make( 'text', 'sgg_configurator_summary_hidden_items', __( 'Summary hidden attributes', 'staggs' ) )
			->set_help_text( __( 'Optionally enter labels of attributes that should not be included in summary list (comma separated)', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_display_summary',
				'value' => true,
			))),

	))
	->add_tab( __( 'Configurator button', 'staggs' ), array(

		Field::make( 'separator', 'sgg_settings_panel_general_totals', __( 'Configurator button', 'staggs' ) ),

		Field::make( 'radio', 'sgg_configurator_button_type', __( 'Configurator Button Type', 'staggs' ) )
			->add_options( array(
				'cart' => __( 'Add to Cart', 'staggs' ),
				'invoice' => __( 'Request Invoice', 'staggs' ),
				'email' => __( 'Send configuration via email', 'staggs' ),
				'none' => __( 'No action', 'staggs' )
			)),

		Field::make( 'checkbox', 'sgg_configurator_button_close_popup', __( 'Close popup on totals configurator button click', 'staggs' ) )
			->set_help_text( __( 'Closes the popup and updates product page details based on configured product instead of adding to cart', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
			))),

		Field::make( 'checkbox', 'sgg_configurator_popup_replace_cart_form', __( 'Replace cart form with configure button', 'staggs' ) )
			->set_help_text( __( 'Replaces the single product page add to cart form with configure button', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'popup',
			), array(
				'field' => 'sgg_configurator_button_close_popup',
				'value' => false,
			))),

		Field::make( 'text', 'sgg_step_add_to_cart_text', __( 'Configurator Button Text', 'staggs' ) ),

		Field::make( 'checkbox', 'sgg_configurator_hide_totals_button', __( 'Hide total button', 'staggs' ) )
			->set_help_text( __( 'Hides the total button so no action is involved in the configurator. For configuration purposes only.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'invoice',
			))),
		Field::make( 'text', 'sgg_configurator_button_email_recipient', __( 'Email recipient', 'staggs' ) )
			->set_help_text( __( 'Optionally set the email adress of the receiver. Defaults to admin email.', 'staggs' ) )
			->set_width(50)
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'email',
			))),
		Field::make( 'text', 'sgg_configurator_button_email_subject', __( 'Email subject', 'staggs' ) )
			->set_help_text( __( 'Optionally set a default email subject.', 'staggs' ) )
			->set_width(50)
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'email',
			))),
		Field::make( 'checkbox', 'sgg_configurator_button_email_show_product_title', __( 'Include product title in email body', 'staggs' ) )
			->set_help_text( __( 'Includes product title in email contents', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'email',
			))),
		Field::make( 'checkbox', 'sgg_configurator_total_generate_image', __( 'Generate configuration image link', 'staggs' ) )
			->set_help_text( __( 'Generates a configuration image link to be used in form or email contents.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'invoice',
			))),

			Field::make( 'select', 'sgg_configurator_form_page', __( 'Form page', 'staggs' ) )
			->add_options( 'get_page_options' )
			->set_help_text( __( 'Page where the form is located (user is redirected to this page).', 'staggs') )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'invoice',
			))),

		Field::make( 'checkbox', 'sgg_configurator_sticky_button', __( 'Make total action button sticky', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'full', 'floating', 'classic' ),
				'compare' => 'IN'
			), array(
				'field' => 'sgg_configurator_step_totals_display',
				'value' => 'always',
			))),

		Field::make( 'checkbox', 'sgg_configurator_sticky_button_mobile', __( 'Make total action button sticky on mobile', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'steps',
			))),

	))
	->add_tab( __( 'Configurator price', 'staggs' ), array(

		Field::make( 'separator', 'sgg_settings_panel_general_prices', __( 'Configurator price', 'staggs' ) ),
	
		Field::make( 'radio', 'sgg_configurator_total_calculation', __( 'Configurator Total Price Calculation', 'staggs' ) )
			->add_options( array(
				'default' => __( 'Default calculation', 'staggs' ),
				'custom' => __( 'Custom calculation', 'staggs' )
			)),
		Field::make( 'text', 'sgg_configurator_total_price_formula', __( 'Total Price Formula', 'staggs' ) )
			->set_help_text( __( 'Insert custom formula to calculate total price', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'cart',
			))),

		Field::make( 'radio', 'sgg_configurator_display_pricing', __( 'Display Totals', 'staggs' ) )
			->add_options( array( 
				'show' => 'Show totals',
				'hide' => 'Hide totals',
			)),

		Field::make( 'radio', 'sgg_configurator_total_price_display', __( 'Total Price Display', 'staggs' ) )
			->add_options( array( 
				'within' => 'In button',
				'above' => 'Above button',
			))
			->set_conditional_logic( array( 
				'relation' => 'OR',
				array(
					'field' => 'sgg_configurator_button_type',
					'value' => 'cart',
				),
				array(
					'field' => 'sgg_configurator_display_pricing',
					'value' => 'show',
				)
			)),

		Field::make( 'radio', 'sgg_configurator_price_display_template', __( 'Price Display Template', 'staggs' ) )
			->add_options( array( 
				'total' => 'Only total configuration price',
				'summary' => 'Display total configuration price breakdown',
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_total_price_display',
				'value' => 'above',
			), array(
				'field' => 'sgg_configurator_display_pricing',
				'value' => 'show',
			), array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'classic', 'floating', 'full' ) ,
				'compare' => 'IN',
			))),
		Field::make( 'text', 'sgg_configurator_totals_product_label', __( 'Product total label', 'staggs' ) )
			->set_attribute( 'placeholder', 'Product total:' )
			->set_width( 33 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_total_price_display',
				'value' => 'above',
			), array(
				'field' => 'sgg_configurator_display_pricing',
				'value' => 'show',
			), array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'classic', 'floating', 'full' ),
				'compare' => 'IN',
			), array(
				'field' => 'sgg_configurator_price_display_template',
				'value' => 'summary',
			))),
		Field::make( 'text', 'sgg_configurator_totals_options_label', __( 'Options total label', 'staggs' ) )
			->set_attribute( 'placeholder', 'Options total:' )
			->set_width( 33 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_total_price_display',
				'value' => 'above',
			), array(
				'field' => 'sgg_configurator_display_pricing',
				'value' => 'show',
			), array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'classic', 'floating', 'full' ),
				'compare' => 'IN',
			), array(
				'field' => 'sgg_configurator_price_display_template',
				'value' => 'summary',
			))),
		Field::make( 'text', 'sgg_configurator_totals_combined_label', __( 'Grand total label', 'staggs' ) )
			->set_attribute( 'placeholder', 'Grand total:' )
			->set_width( 33 )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_total_price_display',
				'value' => 'above',
			), array(
				'field' => 'sgg_configurator_display_pricing',
				'value' => 'show',
			), array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'classic', 'floating', 'full' ),
				'compare' => 'IN',
			), array(
				'field' => 'sgg_configurator_price_display_template',
				'value' => 'summary',
			))),
		Field::make( 'text', 'sgg_configurator_totals_label', __( 'Total label', 'staggs' ) )
			->set_attribute( 'placeholder', 'Total:' )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_total_price_display',
				'value' => 'above',
			), array(
				'field' => 'sgg_configurator_display_pricing',
				'value' => 'show',
			))),

		Field::make( 'checkbox', 'sgg_configurator_price_summary_collapse', __( 'Collapse price summary on mobile', 'staggs' ) )
			->set_help_text( __( 'Make price summary collapsible on mobile devices to save space', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_total_price_display',
				'value' => 'above',
			), array(
				'field' => 'sgg_configurator_display_pricing',
				'value' => 'show',
			), array(
				'field' => 'sgg_configurator_view',
				'value' => array( 'classic', 'floating', 'full' ),
				'compare' => 'IN',
			), array(
				'field' => 'sgg_configurator_price_display_template',
				'value' => 'summary',
			))),

	))
	->add_tab( __( 'Configurator extras', 'staggs' ), array(

		Field::make( 'checkbox', 'sgg_configurator_display_qty_input', __( 'Display Product Quantity Input', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'cart',
			))),
		Field::make( 'text', 'sgg_configurator_qty_input_label', __( 'Product Quantity Input Name', 'staggs' ) )
			->set_help_text( __( 'Optionally enter name of input to function as the main product quantity input', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'cart',
			), array(
				'field' => 'sgg_configurator_display_qty_input',
				'value' => false,
			))),
		Field::make( 'checkbox', 'sgg_configurator_display_qty_total', __( 'Multiply total price by quantity', 'staggs' ) )
			->set_help_text( __( 'When checked the total configurator price will be shown as price * quantity', 'staggs') )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'cart',
			))),

		Field::make( 'checkbox', 'sgg_configurator_sticky_cart_bar', __( 'Display sticky add to cart bar', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_view',
				'value' => 'classic',
			), array(
				'field' => 'sgg_configurator_button_type',
				'value' => 'cart',
			))),
		Field::make( 'checkbox', 'sgg_configurator_sticky_cart_bar_usps', __( 'Display USPs in sticky bar', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_configurator_sticky_cart_bar',
				'value' => true
			))),

		Field::make( 'radio_image', 'sgg_configurator_usp_location', __( 'USPs location', 'staggs' ) )
			->add_options( array(
				'totals' => STAGGS_BASE_URL . 'admin/img/usp-cart.png',
				'gallery' => STAGGS_BASE_URL . 'admin/img/usp-gallery.png',
			)),
		Field::make( 'complex', 'sgg_step_usps', __( 'Unique Selling Points', 'staggs' ) )
			->set_max( 3 )
			->setup_labels( $usp_labels )
			->add_fields( 'options', array(
				Field::make( 'image', 'sgg_usp_icon', __( 'Icon', 'staggs' ) )
					->set_help_text( __( 'Note: please keep image sizes lower than 2 MB to maintain the configurator performance.', 'staggs' ) )
					->set_width( 50 ),
				Field::make( 'text', 'sgg_usp_title', __( 'Title', 'staggs' ) )
					->set_width( 50 )
			)),
	))
	->add_tab( __( 'Custom CSS', 'staggs' ), array(
		
		Field::make( 'separator', 'sgg_theme_panel_css', __( 'Custom CSS', 'staggs' ) ),

		Field::make( 'textarea', 'sgg_configurator_css', __( 'Custom Styles', 'staggs' ) )
			->set_classes( 'cf-header-scripts-custom-css' )
			->set_help_text( __( 'If you need to add custom configurator styling, you should enter the CSS here.', 'staggs' ) ),

	));