<?php

/**
 * Provide a admin-facing view for the Product Builder Fields form of the plugin
 *
 * This file is used to markup the admin-facing product builder form of the plugin admin side.
 *
 * @link       https://staggs.app
 * @since      1.4.4
 *
 * @package    Staggs
 * @subpackage Staggs/admin/fields
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$step_labels = array(
	'plural_name' => __( 'Steps', 'staggs' ),
	'singular_name' => __( 'Step', 'staggs' ),
);
$attribute_labels = array(
	'plural_name' => __( 'New', 'staggs' ),
	'singular_name' => __( 'New', 'staggs' ),
);
$tab_labels = array(
	'plural_name' => __( 'Tabs', 'staggs' ),
	'singular_name' => __( 'Tab', 'staggs' ),
);

/**
 * Builder meta
 */

Container::make( 'post_meta', __( 'Staggs Configurator Builder', 'staggs' ) )
	->where( 'post_type', 'IN', array( 'product', 'sgg_product' ) )
	->set_classes( 'carbon_fields_staggs_configurator_builder' )
	->add_fields( array(

		Field::make( 'select', 'sgg_product_configurator_theme_id', __( 'Configurator Template', 'staggs' ) )
			->set_help_text( __( 'Choose your Staggs configurator template.', 'staggs' ) )
			->add_options( 'get_configurator_themes_options' ),

		Field::make( 'select', 'sgg_configurator_type', __( 'Configurator Type', 'staggs' ) )
			->set_help_text( __( 'Choose your configurator type', 'staggs' ) )
			->add_options( array(
				'image' => __( 'Image', 'staggs' ),
			) ),

		Field::make( 'text', 'sgg_configurator_min_price', __( 'Product Configurator Min Price', 'staggs' ) )
			->set_help_text( __( 'Optionally set fixed price (0.00) or enter comma separated list of attributes', 'staggs' ) ),
		Field::make( 'text', 'sgg_configurator_max_price', __( 'Product Configurator Max Price', 'staggs' ) )
			->set_help_text( __( 'Fixed price or list. Control display format in Staggs -> Settings -> WooCommerce', 'staggs' ) ),

		Field::make( 'complex', 'sgg_configurator_attributes', __( 'Product Attributes', 'staggs' ) )
			->setup_labels( $attribute_labels )
			->add_fields( 'separator', array(
				Field::make( 'text', 'sgg_step_separator_title', __( 'Attribute separator title', 'staggs' ) )
					->set_help_text( __( 'Title as displayed above attributes', 'staggs' ) ),
				Field::make( 'checkbox', 'sgg_step_separator_collapsible', __( 'Make attribute separator collapsible', 'staggs' ) )
					->set_help_text( __( 'Will add collapsible icon when enabled', 'staggs' ) ),
				Field::make( 'radio', 'sgg_step_collapsible_state', __( 'Choose default state', 'staggs' ) )
					->set_help_text( __( 'Choose to show or hide attribute group by default', 'staggs' ) )
					->add_options( array(
						'open' => __( 'Opened', 'staggs' ),
						'collapsed' => __( 'Collapsed', 'staggs' ),
					))
					->set_conditional_logic( array( array(
						'field' => 'sgg_step_separator_collapsible',
						'value' => true
					)))
			))
			->add_fields( 'tabs', array(
				Field::make( 'complex', 'sgg_step_tab_options', __( 'Tabs', 'staggs' ) )
					->set_help_text( __( 'Define the attribute tabs and link to the attribute', 'staggs' ) )
					->setup_labels( $tab_labels )
					->add_fields( array(
						Field::make( 'text', 'sgg_step_tab_title', __( 'Attribute Tab Title', 'staggs' ) )
							->set_width( 50 )
							->set_help_text( __( 'Title as displayed in the tab', 'staggs' ) )
							->set_classes( 'conditional-tab-title' ),
						Field::make( 'text', 'sgg_step_tab_preview_slide', __( 'Tab Make Slide Active', 'staggs' ) )
							->set_width( 50 )
							->set_help_text( __( 'Index of product image slide to show when clicking this tab', 'staggs' ) )
							->set_classes( 'conditional-tab-preview' ),
						Field::make( 'multiselect', 'sgg_step_tab_attribute', __( 'Tab linked attribute', 'staggs' ) )
							->set_help_text( __( 'Select your attribute group that will be shown when tab is active.', 'staggs' ) )
							->add_options( 'get_configurator_attribute_values' )
							->set_classes( 'conditional-tab-attribute' ),
					))
			))
			->add_fields( 'attribute', array(
				Field::make( 'select', 'sgg_step_attribute', __( 'Select attribute', 'staggs' ) )
					->set_help_text( __( 'Select your attribute group', 'staggs' ) )
					->set_classes( 'base-attribute-select' )
					->add_options( 'get_configurator_attribute_values' ),

				Field::make( 'checkbox', 'sgg_step_attribute_hidden', __( 'Hide attribute', 'staggs' ) )
					->set_help_text( __( 'Hides the attribute in configurator when enabled', 'staggs' ) ),

				Field::make( 'checkbox', 'sgg_step_attribute_default_value', __( 'Set default attribute option', 'staggs' ) )
					->set_help_text( __( 'Allows you to change the default attribute option for the given attribute', 'staggs' ) ),

				Field::make( 'select', 'sgg_step_attribute_value', __( 'Select default option', 'staggs' ) )
					->set_help_text( __( 'Select your attribute default option', 'staggs' ) )
					->set_classes( 'base-attribute-option-select' )
					->add_options( 'get_configurator_attribute_item_values' )
					->set_conditional_logic( array( array(
						'field' => 'sgg_step_attribute_default_value',
						'value' => true
					))),

				Field::make( 'checkbox', 'sgg_step_attribute_collapsible', __( 'Make attribute collapsible', 'staggs' ) )
					->set_help_text( __( 'Will add collapsible icon when enabled', 'staggs' ) ),

				Field::make( 'radio', 'sgg_step_attribute_state', __( 'Choose default state', 'staggs' ) )
					->set_help_text( __( 'Choose to show or hide attribute group by default', 'staggs' ) )
					->add_options( array(
						'open' => __( 'Opened', 'staggs' ),
						'collapsed' => __( 'Collapsed', 'staggs' ),
					))
					->set_conditional_logic( array( array(
						'field' => 'sgg_step_attribute_collapsible',
						'value' => true
					))),
			))
			->add_fields( 'html', array(
				Field::make( 'textarea', 'sgg_step_html', __( 'Custom HTML', 'staggs' ))
					->set_help_text( __( 'Enter any HTML contents', 'staggs' ) ),
			))
	));