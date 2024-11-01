<?php

/**
 * The file that defines the core plugin product class
 *
 * @link       https://staggs.app
 * @since      1.5.2
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define core Product post type and maintain its data.
 *
 * @since      1.5.3
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */

class Staggs_Product {

	/**
	 * Registers the Product for the admin area.
	 *
	 * @since    1.5.3
	 */
	public function register() {
		$labels = array(
			'name'               => __( 'Staggs Products', 'staggs' ),
			'singular_name'      => __( 'Product', 'staggs' ),
			'menu_name'          => __( 'Staggs', 'staggs' ),
			'name_admin_bar'     => __( 'Product', 'staggs' ),
			'add_new'            => __( 'New product', 'staggs' ),
			'add_new_item'       => __( 'Add new product', 'staggs' ),
			'new_item'           => __( 'New product', 'staggs' ),
			'edit_item'          => __( 'Edit product', 'staggs' ),
			'view_item'          => __( 'View product', 'staggs' ),
			'all_items'          => __( 'Products', 'staggs' ),
			'search_items'       => __( 'Search products', 'staggs' ),
			'parent_item_colon'  => __( 'Parent product:', 'staggs' ),
			'not_found'          => __( 'No products found.', 'staggs' ),
			'not_found_in_trash' => __( 'No products found in bin.', 'staggs' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=sgg_attribute',
			'menu_position'      => 10,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'configurator' ),
			'with_front'         => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-icons-staggs',
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);

		register_post_type( 'sgg_product', $args );
	}
}
