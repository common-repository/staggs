<?php

/**
 * Provide a admin-facing view for the Product Fields form of the plugin
 *
 * This file is used to markup the admin-facing product form of the plugin admin side.
 *
 * @link       https://staggs.app
 * @since      1.5.3
 *
 * @package    Staggs
 * @subpackage Staggs/admin/fields
 */

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

acf_add_local_field_group( array(
	'key' => 'group_6517f013b92b0',
	'title' => 'Product Details',
	'fields' => array(
		array(
			'key' => 'field_6517f01327a9e',
			'label' => 'Product gallery',
			'name' => 'sgg_product_gallery',
			'aria-label' => '',
			'type' => 'gallery',
			'instructions' => 'Add product gallery images. Note: please keep image sizes lower than 2 MB to maintain the configurator performance.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'id',
			'library' => 'all',
			'min' => '',
			'max' => '',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
			'insert' => 'append',
			'preview_size' => 'medium',
		),
		array(
			'key' => 'field_6517f04427aa0',
			'label' => 'Regular price',
			'name' => 'sgg_product_regular_price',
			'aria-label' => '',
			'type' => 'number',
			'instructions' => 'Product price. Numeric value expected (1999.00)',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'min' => '',
			'max' => '',
			'placeholder' => '',
			'step' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_6517f06e27aa1',
			'label' => 'Sale price',
			'name' => 'sgg_product_sale_price',
			'aria-label' => '',
			'type' => 'number',
			'instructions' => 'Product sale price. Numeric value expected (1999.00)',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'min' => '',
			'max' => '',
			'placeholder' => '',
			'step' => '',
			'prepend' => '',
			'append' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'sgg_product',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
) );
