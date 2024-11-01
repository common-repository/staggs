<?php

/**
 * The file that defines the core plugin theme class
 *
 * @link       https://staggs.app
 * @since      1.4.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define core Theme post type and maintain its data.
 *
 * @since      1.4.0
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */

class Staggs_Theme {

	/**
	 * Registers the Theme for the admin area.
	 *
	 * @since    1.4.0
	 */
	public function register() {
		$labels = array(
			'name'               => __( 'Staggs Templates', 'staggs' ),
			'singular_name'      => __( 'Template', 'staggs' ),
			'menu_name'          => __( 'Staggs', 'staggs' ),
			'name_admin_bar'     => __( 'Template', 'staggs' ),
			'add_new'            => __( 'New template', 'staggs' ),
			'add_new_item'       => __( 'Add new template', 'staggs' ),
			'new_item'           => __( 'New template', 'staggs' ),
			'edit_item'          => __( 'Edit template', 'staggs' ),
			'view_item'          => __( 'View template', 'staggs' ),
			'all_items'          => __( 'Templates', 'staggs' ),
			'search_items'       => __( 'Search templates', 'staggs' ),
			'parent_item_colon'  => __( 'Parent template:', 'staggs' ),
			'not_found'          => __( 'No templates found.', 'staggs' ),
			'not_found_in_trash' => __( 'No templates found in bin.', 'staggs' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=sgg_attribute',
			'menu_position'      => 57,
			'query_var'          => false,
			'rewrite'            => false,
			'with_front'         => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-icons-staggs',
			'supports'           => array( 'title' ),
		);

		register_post_type( 'sgg_theme', $args );
	}

	/**
	 * Clears saved theme transients.
	 *
	 * @since    1.11.1
	 */
	public function clear_transients($post_id)
	{
		delete_transient('sgg_configurator_page_template_' . $post_id);
		delete_transient('sgg_configurator_view_layout_' . $post_id);
	}

	/**
	 * Adds attribute columns to configurators list view.
	 *
	 * @since    1.12.5
	 */
	public function add_theme_columns($columns)
	{
		unset($columns['date']);

		$new_columns = array(
			'viewtype'   => __('Gallery type', 'staggs'),
			'template'   => __('Template', 'staggs'),
			'color'      => __('Color theme', 'staggs'),
			'buttontype' => __('Button type', 'staggs'),
			'date'       => __('Date', 'staggs'),
		);

		$columns = array_merge($columns, $new_columns);

		return $columns;
	}

	/**
	 * Fills the configurator list view attribute column values.
	 *
	 * @since    1.12.5
	 */
	public function fill_theme_columns($column, $post_id)
	{
		if ('template' === $column) {
			if ( 'popup' === staggs_get_post_meta($post_id, 'sgg_configurator_view') ) {
				echo '<img src="' . esc_url( STAGGS_BASE_URL ) . 'admin/img/popup-' . esc_attr( staggs_get_post_meta($post_id, 'sgg_configurator_popup_type') ) . '.png" width="120">';
			} else {
				echo '<img src="' . esc_url( STAGGS_BASE_URL ) . 'admin/img/' . esc_attr( staggs_get_post_meta($post_id, 'sgg_configurator_view') ) . '.png" width="120">';
			}
		} else if ('viewtype' === $column) {
			echo esc_attr( 'regular' === staggs_get_post_meta($post_id, 'sgg_configurator_gallery_type') ? __( 'Image', 'staggs' ) : __( '3D model', 'staggs' ) );
		} else if ('color' === $column) {
			echo esc_attr( ucfirst(staggs_get_post_meta($post_id, 'sgg_configurator_theme')) . ' ' . __( 'theme', 'staggs' ) );
		} else if ('buttontype' === $column) {
			$type = staggs_get_post_meta($post_id, 'sgg_configurator_button_type');
			switch($type) {
				case 'cart': echo esc_attr( __( 'Add to Cart', 'staggs' ) ); break;
				case 'invoice': echo esc_attr( __( 'Request Quote', 'staggs' ) ); break;
				case 'email': echo esc_attr( __( 'Send configuration via email', 'staggs' ) ); break;
				case 'pdf': echo esc_attr( __( 'Download PDF', 'staggs' ) ); break;
				case 'none': echo esc_attr( __( 'No action', 'staggs' ) ); break;
			}
		}
	}
}