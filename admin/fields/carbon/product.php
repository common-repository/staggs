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

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make( 'post_meta', __( 'Product Details', 'staggs' ) )
	->where( 'post_type', '=', 'sgg_product' )
	->add_fields( array(
		Field::make( 'media_gallery', 'sgg_product_gallery', __( 'Product gallery', 'staggs' ) )
			->set_help_text( __( 'Add product gallery images. Note: please keep image sizes lower than 2 MB to maintain the configurator performance.', 'staggs' ) ),
		Field::make( 'text', 'sgg_product_regular_price', __( 'Regular price', 'staggs' ) )
			->set_help_text( __( 'Product price. Numeric value expected (1999.00)', 'staggs' ) )
			->set_width( 50 ),
		Field::make( 'text', 'sgg_product_sale_price', __( 'Sale price', 'staggs' ) )
			->set_help_text( __( 'Product sale price. Numeric value expected (1999.00)', 'staggs' ) )
			->set_width( 50 ),
	));
