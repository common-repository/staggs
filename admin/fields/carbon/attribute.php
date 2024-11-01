<?php

/**
 * Provide a admin-facing view for the Attribute Fields form of the plugin
 *
 * This file is used to markup the admin-facing attribute form of the plugin admin side.
 *
 * @link       https://staggs.app
 * @since      1.4.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin/fields
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$item_labels = array(
	'plural_name' => __( 'Attribute items', 'staggs' ),
	'singular_name' => __( 'Attribute item', 'staggs' ),
);

Container::make( 'post_meta', __( 'Attribute details', 'staggs' ) )
	->where( 'post_type', '=', 'sgg_attribute' )
	->set_classes( 'carbon_fields_staggs_attribute_details' )
	->add_tab( __( 'General', 'staggs' ), array(
		Field::make( 'text', 'sgg_step_title', __( 'Title', 'staggs' ) )
			->set_required( true )
			->set_help_text( __( 'This title as shown in the product configurator.', 'staggs' ) ),
		Field::make( 'select', 'sgg_step_template', __( 'Template', 'staggs' ) )
			->set_classes( 'sgg_step_template' )
			->set_help_text( __( 'Select how your attributes will be displayed.', 'staggs' ) )
			->set_default_value( 'image' )
			->add_options( array(
				'dropdown' => __( 'Dropdown', 'staggs' ),
				'options' => __( 'Options list', 'staggs' ),
				'cards' => __( 'Cards', 'staggs' ),
				'icons' => __( 'Image swatches', 'staggs' ),
				'swatches' => __( 'Color swatches', 'staggs' ),
				'button-group' => __( 'Button group', 'staggs' ),
				'tickboxes' => __( 'Tickboxes', 'staggs' ),
				'true-false' => __( 'True / False', 'staggs' ),
				'text-input' => __( 'Text field', 'staggs' ),
				'number-input' => __( 'Numeric field', 'staggs' ),
				'measurements' => __( 'Measurements', 'staggs' ),
			)),
		Field::make( 'select', 'sgg_attribute_type', __( 'Type', 'staggs' ) )
			->set_classes( 'sgg_attribute_type' )
			->set_help_text( __( 'Select attribute type to be used in the template. <a href="https://staggs.app/attribute-support-table" target="_blank">View attribute support table</a>', 'staggs' ) )
			->add_options( array(
				'image' => __( 'Image', 'staggs' ),
				'input' => __( 'Input', 'staggs' ),
				'color' => __( 'Color', 'staggs' ),
				'font'  => __( 'Font Family', 'staggs' ),
				'url'   => __( 'URL', 'staggs' ),
			)),
		Field::make( 'select', 'sgg_step_field_required', __( 'Required field', 'staggs' ) )
			->set_help_text( __( 'Select if the attribute is required or optional.', 'staggs' ) )
			->add_options( array(
				'no' => __( 'Optional', 'staggs' ),
				'yes' => __( 'Required', 'staggs' ),
			)),
		Field::make( 'text', 'sgg_step_field_placeholder', __( 'Dropdown placeholder', 'staggs' ) )
			->set_help_text( __( 'Optionally override placeholder text. Defaults to "- field label -"', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'dropdown'
			))),
		Field::make( 'complex', 'sgg_attribute_items', __( 'Items', 'staggs' ) )
			->set_max( 30 )
			->set_help_text( __( 'We support max. 30 options per attribute. Please install and migrate to ACF PRO if you want more options.', 'staggs' ) )
			->setup_labels( $item_labels )
			->add_fields( array(

				Field::make( 'text', 'sgg_option_label', __( 'Label', 'staggs' ) )
					->set_required( true )
					->set_help_text( __( 'Main attribute label', 'staggs' ) ),
				Field::make( 'text', 'sgg_option_note', __( 'Note', 'staggs' ) )
					->set_help_text( __( 'Note is displayed underneath the label', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_step_template',
						'value' => array( 'options', 'cards', 'icons', 'swatches', 'tickboxes', 'text-input', 'number-input', 'measurements', 'image-upload' ),
						'compare' => 'IN',
					))),
				Field::make( 'image', 'sgg_option_image', __( 'Thumbnail', 'staggs' ) )
					->set_help_text( __( 'Note: please keep image sizes lower than 2 MB to maintain the configurator performance.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_step_template',
						'value' => array( 'options', 'cards', 'icons' ),
						'compare' => 'IN',
					), array( 
						'field' => 'parent.sgg_step_show_image',
						'value' => 'show',
					))),
				Field::make( 'select', 'sgg_option_field_type', __( 'Input Type', 'staggs' ) )
					->set_classes( 'sgg_option_field_type' )
					->set_help_text( __( 'Select your input type ', 'staggs' ))
					->add_options( array(
						'text'     => 'Text',
						'textarea' => 'Textarea',
						'date'     => 'Date picker',
						'number'   => 'Number',
						'range'    => 'Range slider',
					))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					))),
				Field::make( 'select', 'sgg_option_field_required', __( 'Required field', 'staggs' ) )
					->set_help_text( __( 'Select if the input field is required or optional.', 'staggs' ) )
					->add_options( array(
						'no' => __( 'Optional', 'staggs' ),
						'yes' => __( 'Required', 'staggs' ),
					))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					))),
				Field::make( 'text', 'sgg_option_page_url', __( 'Page URL', 'staggs' ) )
					->set_help_text( __( 'Enter full URL of page you want to link to.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'url',
					))),
				Field::make( 'select', 'sgg_option_enable_preview', __( 'Enable Preview', 'staggs' ) )
					->set_help_text( __( 'If enabled, the input value/image will be displayed/updated in the product preview.', 'staggs' ) )
					->add_options( array( 
						'yes' => 'Yes',
						'no' => 'No',
					) ),
				Field::make( 'media_gallery', 'sgg_option_preview', __( 'Main Images', 'staggs' ) )
					->set_duplicates_allowed( false )
					->set_type( array( 'image' ) )
					->set_help_text( __( 'Choose main images to show when this step option is selected. Note: please keep image sizes lower than 2 MB to maintain the configurator performance.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => array( 'image', 'color', 'link' ),
						'compare' => 'IN',
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_preview_top', __( 'Preview Top Position', 'staggs' ) )
					->set_help_text( __( 'Set default preview position from top. Use CSS Units.', 'staggs' ) )
					->set_attribute( 'placeholder', '50%' )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_preview_left', __( 'Preview Left Position', 'staggs' ) )
					->set_help_text( __( 'Set default preview position from left. Use CSS Units.', 'staggs' ) )
					->set_attribute( 'placeholder', '50%' )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_preview_width', __( 'Preview Width', 'staggs' ) )
					->set_help_text( __( 'Optionally set a preview width. Use CSS Units.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_field_type',
						'value' => 'file',
						'compare' => '!=',
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'select', 'sgg_option_preview_overflow', __( 'Preview overflow', 'staggs' ) )
					->set_help_text( __( 'Set preview overflow behaviour.', 'staggs' ) )
					->add_options( array(
						'hidden' => __( 'Hide text', 'staggs' ),
						'fittext' => __( 'Fit text within container', 'staggs' ),
					))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					), array(
						'field' => 'sgg_option_field_type',
						'value' => 'file',
						'compare' => '!=',
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'select', 'sgg_option_preview_custom_mobile', __( 'Change preview settings for mobile', 'staggs' ) )
					->set_help_text( __( 'Change preview settings for mobile devices.', 'staggs' ) )
					->add_options( array(
						'no' => 'No',
						'yes' => 'Yes',
					))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_preview_top_mobile', __( 'Preview Top (Mobile)', 'staggs' ) )
					->set_help_text( __( 'Set default preview position from top. Use CSS Units.', 'staggs' ) )
					->set_attribute( 'placeholder', '50%' )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_preview_custom_mobile',
						'value' => 'yes',
					), array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_preview_left_mobile', __( 'Preview Left (Mobile)', 'staggs' ) )
					->set_help_text( __( 'Set default preview position from left. Use CSS Units.', 'staggs' ) )
					->set_attribute( 'placeholder', '50%' )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_preview_custom_mobile',
						'value' => 'yes',
					), array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_preview_width_mobile', __( 'Preview Width (mobile)', 'staggs' ) )
					->set_help_text( __( 'Optionally set a preview width. Use CSS Units.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_preview_custom_mobile',
						'value' => 'yes',
					), array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_field_type',
						'value' => 'file',
						'compare' => '!=',
					), array(
						'field' => 'sgg_option_enable_preview',
						'value' => 'yes',
					))),
				Field::make( 'text', 'sgg_option_font_source', __( 'Font Stylesheet URL', 'staggs' ) )
					->set_help_text( __( 'Link to Google Fonts or Adobe Typekit font stylesheet.', 'staggs' ))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'font',
					))),
				Field::make( 'text', 'sgg_option_font_family', __( 'Font Family', 'staggs' ) )
					->set_help_text( __( 'Font family as defined in CSS (e.g. "Helvetica", sans-serif")', 'staggs' ))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'font',
					))),
				Field::make( 'text', 'sgg_option_font_weight', __( 'Font Weight', 'staggs' ) )
					->set_help_text( __( 'Font weight as numeric value (e.g. 400, 700, 900)', 'staggs' ))
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'font',
					))),

				Field::make( 'text', 'sgg_option_preview_ref_selector', __( 'Preview Reference Selector', 'staggs' ) )
					->set_help_text( __( 'Enter corresponding CSS property values.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_step_preview_ref',
						'value' => '',
						'compare' => '!=',
					))),

				Field::make( 'text', 'sgg_option_field_key', __( 'Field key', 'staggs' ) )
					->set_help_text( __( 'Unique identifier for given field. Can be used for price calculations or input value previews.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_gallery_type',
						'value' => 'image',
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					))),

				Field::make( 'text', 'sgg_option_range_increments', __( 'Range increments', 'staggs' ) )
					->set_help_text( __( 'Amount of increments per step. Defaults to 1.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					), array(
						'field' => 'sgg_option_field_type',
						'value' => array( 'range', 'number' ),
						'compare' => 'IN',
					))),

				Field::make( 'text', 'sgg_option_datepicker_format', __( 'Date format', 'staggs' ) )
					->set_help_text( __( 'Set a date format for the date picker. Defaults to mm/dd/yy.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					), array(
						'field' => 'sgg_option_field_type',
						'value' => 'date',
					))),

				Field::make( 'text', 'sgg_option_field_min', __( 'Field min', 'staggs' ) )
					->set_help_text( __( 'Minimum text length, date or numeric value for given field.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					))),
				Field::make( 'text', 'sgg_option_field_max', __( 'Field max', 'staggs' ) )
					->set_help_text( __( 'Maximum text length, date or numeric value for given field.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					))),
				Field::make( 'text', 'sgg_option_field_unit', __( 'Field unit', 'staggs' ) )
					->set_help_text( __( 'Optionally display unit for given field (e.g. cm, inch, gallon, liters)', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					), array(
						'field' => 'sgg_option_field_type',
						'value' => array( 'number', 'range' ),
						'compare' => 'IN',
					))),

				Field::make( 'text', 'sgg_option_field_placeholder', __( 'Placeholder', 'staggs' ) )
					->set_help_text( __( 'Optionally set a placeholder for the input.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_field_type',
						'value' => array( 'range', 'file' ),
						'compare' => 'NOT IN',
					))),
				Field::make( 'text', 'sgg_option_field_value', __( 'Value', 'staggs' ) )
					->set_help_text( __( 'Optionally set a default value for the input.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input'
					), array(
						'field' => 'sgg_option_field_type',
						'value' => 'date',
						'compare' => '!=',
					))),

				Field::make( 'select', 'sgg_option_datepicker_show_icon', __( 'Show datepicker icon', 'staggs' ) )
					->add_options( array( 
						'yes' => 'Yes',
						'no' => 'No',
					) )
					->set_help_text( __( 'Icon can be managed under Settings.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_field_type',
						'value' => 'date',
					))),
				Field::make( 'select', 'sgg_option_datepicker_show_inline', __( 'Show datepicker inline', 'staggs' ) )
					->set_help_text( __( 'Replaces the input field with the inline datepicker.', 'staggs' ) )
					->add_options( array( 
						'yes' => 'Yes',
						'no' => 'No',
					) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_field_type',
						'value' => 'date',
					))),

				Field::make( 'color', 'sgg_option_field_color', __( 'Color', 'staggs' ) )
					->set_help_text( __( 'Pick a color.', 'staggs' ) )
					->set_required( true )
					->set_conditional_logic( array(
						'relation' => 'OR',
						array(
							'field' => 'parent.sgg_attribute_type',
							'value' => 'color',
						),
						array(
							'field' => 'parent.sgg_step_template',
							'value' => 'swatches',
						)
					)),

				Field::make( 'select', 'sgg_option_calc_price_type', __( 'Price type', 'staggs' ) )
					->set_help_text( __( 'Items marked with option support user selection fields. Items marked with input support user input fields.', 'staggs' ) )
					->add_options( array(
						'single' => 'Fixed price (option, input)',
						'unit' => 'Unit price (input)',
					)),

				Field::make( 'html', 'sgg_option_price_message' )
					->set_html( __( 'This combination of attribute type and price type is not supported.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_calc_price_type',
						'value' => 'unit'
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
						'compare' => '!=',
					))),

				Field::make( 'text', 'sgg_option_calc_price_value', __( 'Price per unit', 'staggs' ) )
					->set_help_text( __( 'Define fixed price per unit value. Important! Units should match.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_calc_price_type',
						'value' => 'unit'
					), array(
						'field' => 'parent.sgg_attribute_type',
						'value' => 'input',
					))),
				Field::make( 'text', 'sgg_option_price', __( 'Price', 'staggs' ) )
					->set_help_text( __( 'Additional cost for this attribute item. Leave empty for no additional cost.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_calc_price_type',
						'value' => 'single',
					))),
				Field::make( 'text', 'sgg_option_sale_price', __( 'Sale Price', 'staggs' ) )
					->set_help_text( __( 'Discounted additional cost for this attribute item. Must be lower than price.', 'staggs' ) )
					->set_conditional_logic( array( array(
						'field' => 'sgg_option_calc_price_type',
						'value' => 'single',
					))),
		) )
		->set_header_template( '
			<% if (sgg_option_label) { %>
				<%- sgg_option_label %>
			<% } %>
		' )
	) )
	->add_tab( __( 'Presentation', 'staggs' ), array(

		Field::make( 'rich_text', 'sgg_step_short_description', __( 'Short description', 'staggs' ) )
			->set_help_text( __( 'Short description as shown below the title', 'staggs' ) ),
		Field::make( 'rich_text', 'sgg_step_description', __( 'Long Description (Panel):', 'staggs' ) )
			->set_help_text( __( 'Extensive description that will be displayed in popout panel.', 'staggs' ) ),
		Field::make( 'text', 'sgg_step_custom_class', __( 'Custom class', 'staggs' ) )
			->set_help_text( __( 'Optionally enter custom HTML class(es) for the attribute', 'staggs' ) ),
		Field::make( 'text', 'sgg_step_shared_group', __( 'Shared group name', 'staggs' ) )
			->set_help_text( __( 'Share attribute options with other attributes with same name.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'options', 'cards', 'icons', 'swatches', 'button-group' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_field_info', __( 'Show field info', 'staggs' ) )
			->set_help_text( __( 'Show min/max field value info label underneath input fields.', 'staggs' ) )
			->set_options( array(
				'yes' => __( 'Display field info labels', 'staggs' ),
				'no' => __( 'Hide field info labels', 'staggs' ),
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_attribute_type',
				'value' => 'input',
			))),
		Field::make( 'select', 'sgg_step_style', __( 'Style', 'staggs' ) )
			->set_help_text( __( 'Modify the style setting for individual attributes display', 'staggs' ) )
			->add_options( array(
				'inherit' => __( 'Inherit from theme', 'staggs' ),
				'rounded' => __( 'Rounded', 'staggs' ),
				'squared' => __( 'Squared', 'staggs' ),
			)),
		Field::make( 'select', 'sgg_step_show_image', __( 'Display thumbnails', 'staggs' ) )
			->set_help_text( __( 'Display thumbnails in options', 'staggs' ) )
			->add_options( array(
				'show' => __( 'Show thumbnails', 'staggs' ),
				'hide' => __( 'Hide thumbnails', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'options', 'cards' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_option_layout', __( 'Cards width', 'staggs' ) )
			->set_help_text( __( 'Choose to show 2 or 3 cards next to each other.', 'staggs' ) )
			->add_options( array(
				'two' => __( 'Two cards per row', 'staggs' ),
				'three' => __( 'Three cards per row', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'cards',
			))),
		Field::make( 'select', 'sgg_step_card_template', __( 'Card template', 'staggs' ) )
			->set_help_text( __( 'Choose to show image inside card or not.', 'staggs' ) )
			->add_options( array(
				'default' => __( 'Show image inside card', 'staggs' ),
				'full' => __( 'Show image on top of card', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'cards',
			), array(
				'field' => 'sgg_step_show_image',
				'value' => 'show',
			))),
		Field::make( 'select', 'sgg_step_show_summary', __( 'Display summary', 'staggs' ) )
			->set_help_text( __( 'Display summary of selected option', 'staggs' ) )
			->add_options( array(
				'show' => __( 'Show summary', 'staggs' ),
				'hide' => __( 'Hide summary', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'dropdown', 'icons', 'swatches' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_show_tick_all', __( 'Enable tick all options', 'staggs' ) )
			->set_help_text( __( 'Display button to (de)select all options at once', 'staggs' ) )
			->add_options( array(
				'hide' => __( 'Hide tick all option', 'staggs' ),
				'show' => __( 'Show tick all option', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'tickboxes'
			))),
		Field::make( 'text', 'sgg_step_tick_all_label', __( 'Tick all options label', 'staggs' ) )
			->set_help_text( __( 'Label to display in tick all option.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_show_tick_all',
				'value' => 'show',
			),array(
				'field' => 'sgg_step_template',
				'value' => 'tickboxes'
			))),
		Field::make( 'select', 'sgg_step_show_option_price', __( 'Display option price', 'staggs' ) )
			->set_help_text( __( 'Optionally choose to hide prices for the customer.', 'staggs' ) )
			->add_options( array(
				'show' => __( 'Show prices', 'staggs' ),
				'hide' => __( 'Hide prices', 'staggs' ),
			) ),
		Field::make( 'select', 'sgg_step_swatch_size', __( 'Choose swatch size', 'staggs' ) )
			->set_help_text( __( 'Choose the size of your swatches', 'staggs' ) )
			->add_options( array(
				'inherit' => __( 'Default', 'staggs' ),
				'small' => __( 'Small', 'staggs' ),
				'regular' => __( 'Regular', 'staggs' ),
				'medium' => __( 'Medium', 'staggs' ),
				'large' => __( 'Large', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'icons', 'swatches' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_swatch_style', __( 'Swatch border position', 'staggs' ) )
			->set_help_text( __( 'Choose to show border inside or outside when checked', 'staggs' ) )
			->add_options( array(
				'outside' => __( 'Outside', 'staggs' ),
				'inside' => __( 'Inside', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'icons', 'swatches' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_show_swatch_label', __( 'Display inline swatch label', 'staggs' ) )
			->set_help_text( __( 'Shows label of swatch inline', 'staggs' ) )
			->add_options( array(
				'hide' => __( 'Hide inline labels', 'staggs' ),
				'show' => __( 'Show inline labels', 'staggs' ),
				'show_note' => __( 'Show inline notes', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'icons', 'swatches' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_show_tooltip', __( 'Display swatch tooltip', 'staggs' ) )
			->set_help_text( __( 'Show tooltip with value on hover', 'staggs' ) )
			->add_options( array(
				'show' => __( 'Show tooltip', 'staggs' ),
				'hide' => __( 'Hide tooltip', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'icons', 'swatches' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_tooltip_template', __( 'Tooltip display', 'staggs' ) )
			->set_help_text( __( 'Choose what is included in the tooltip', 'staggs' ) )
			->add_options( array(
				'simple' => __( 'Show label and price', 'staggs' ),
				'extended' => __( 'Show icon, label and price', 'staggs' ),
				'text' => __( 'Show label, note and price', 'staggs' ),
				'full' => __( 'Show icon, label, note and price', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'icons', 'swatches' ),
				'compare' => 'IN',
			), array(
				'field' => 'sgg_step_show_tooltip',
				'value' => 'show',
			))),
		Field::make( 'select', 'sgg_step_enable_zoom', __( 'Enable zoom function', 'staggs' ) )
			->set_help_text( __( 'Show zoom icon on hover to enlarge the image', 'staggs' ) )
			->add_options( array(
				'hide' => __( 'No zoom function', 'staggs' ),
				'show' => __( 'Enable zoom function', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'options', 'cards', 'icons' ),
				'compare' => 'IN',
			))),
		Field::make( 'select', 'sgg_step_button_view', __( 'Button view', 'staggs' ) )
			->set_help_text( __( 'Select how you want the item to be displayed', 'staggs' ) )
			->set_options( array(
				'button' => __( 'Button', 'staggs' ),
				'toggle' => __( 'Toggle', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'true-false',
			))),
		Field::make( 'text', 'sgg_step_button_add', __( 'Button add text', 'staggs' ) )
			->set_help_text( __( 'Enter text when option is not selected.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'true-false',
			))),
		Field::make( 'text', 'sgg_step_button_del', __( 'Button remove text', 'staggs' ) )
			->set_help_text( __( 'Enter text when option is selected.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => 'true-false',
			))),
		Field::make( 'select', 'sgg_step_calc_price_type', __( 'Price type', 'staggs' ) )
			->set_help_text( __( 'Choose matrix to pick a price based on two values (pro)', 'staggs' ) )
			->add_options( array(
				'none'    => 'No calculation',
				'formula' => 'Formula',
			))
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'measurements', 'number-input' ),
				'compare' => 'IN',
			))),
		Field::make( 'text', 'sgg_step_price_formula', __( 'Formula', 'staggs' ) )
			->set_help_text( __( 'Describe formula of field keys for price calculation (e.g. A + B * C)', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_step_template',
				'value' => array( 'measurements', 'number-input' ),
				'compare' => 'IN',
			), array(
				'field' => 'sgg_step_calc_price_type',
				'value' => 'formula',
			))),
		Field::make( 'select', 'sgg_gallery_type', __( 'Gallery type', 'staggs' ) )
			->set_help_text( __( 'Define configurator gallery type.', 'staggs' ) )
			->set_default_value( 'image' )
			->add_options( array(
				'image' => __( 'Image', 'staggs' ),
			)),
		Field::make( 'text', 'sgg_step_preview_order', __( 'Image Layer Order:', 'staggs' ) )
			->set_help_text( __( 'Higher numbers will be displayed on top.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			))),
		Field::make( 'text', 'sgg_step_preview_index', __( 'Display Images On Slides:', 'staggs' ) )
			->set_attribute( 'placeholder', 'e.g. 1, 2, 5' )
			->set_help_text( __( 'Slides the main image will appear on. Defaults to first slide (1).', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			))),
		Field::make( 'text', 'sgg_step_preview_slide', __( 'Make Slide Active:', 'staggs' ) )
			->set_help_text( __( 'Index of product image slide to show when viewing this step.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			))),
		Field::make( 'text', 'sgg_step_preview_ref', __( 'Gallery Preview Reference:', 'staggs' ) )
			->set_help_text( __( 'Gallery preview reference. Use for text or image preview updates (supports comma separated values)', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			))),
		Field::make( 'text', 'sgg_step_preview_ref_props', __( 'Gallery Preview Properties:', 'staggs' ) )
			->set_help_text( __( 'Enter preview CSS properties, like color, width or height (supports comma separated values)', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			))),
		Field::make( 'select', 'sgg_step_preview_bundle', __( 'Gallery Preview Bundle:', 'staggs' ) )
			->set_help_text( __( 'This will bundle all input previews together in the first defined position.', 'staggs' ) )
			->set_options( array(
				'no' => __( 'No', 'staggs' ),
				'yes' => __( 'Yes', 'staggs' ),
			) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			), array(
				'field' => 'sgg_attribute_type',
				'value' => 'input',
			))),
		Field::make( 'text', 'sgg_step_preview_height', __( 'Gallery Preview Height:', 'staggs' ) )
			->set_help_text( __( 'Optionally define the allowed height of all input previews together. Use CSS units.', 'staggs' ) )
			->set_conditional_logic( array( array(
				'field' => 'sgg_gallery_type',
				'value' => 'image',
			), array(
				'field' => 'sgg_attribute_type',
				'value' => 'input',
			), array(
				'field' => 'sgg_step_preview_bundle',
				'value' => 'yes',
			))),
	));