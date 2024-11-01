<?php

/**
 * The file that defines the core plugin attribute class
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
 * This is used to define core Attribute post type and maintain its data.
 *
 * @since      1.4.0
 * @package    Staggs
 * @subpackage Staggs/includes
 * @author     Staggs <contact@staggs.app>
 */

class Staggs_Attribute
{

	/**
	 * Registers the Attribute for the admin area.
	 *
	 * @since    1.4.0
	 */
	public function register()
	{
		$labels = array(
			'name'               => __('Staggs Attributes', 'staggs'),
			'singular_name'      => __('Attribute', 'staggs'),
			'menu_name'          => __('Staggs', 'staggs'),
			'name_admin_bar'     => __('Attribute', 'staggs'),
			'add_new'            => __('New attribute', 'staggs'),
			'add_new_item'       => __('Add new attribute', 'staggs'),
			'new_item'           => __('New attribute', 'staggs'),
			'edit_item'          => __('Edit attribute', 'staggs'),
			'view_item'          => __('View attribute', 'staggs'),
			'all_items'          => __('Attributes', 'staggs'),
			'search_items'       => __('Search attributes', 'staggs'),
			'parent_item_colon'  => __('Parent attribute:', 'staggs'),
			'not_found'          => __('No attributes found.', 'staggs'),
			'not_found_in_trash' => __('No attributes found in bin.', 'staggs'),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_position'      => 57,
			'query_var'          => false,
			'rewrite'            => false,
			'with_front'         => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-icons-staggs',
			'supports'           => array('title'),
		);

		register_post_type('sgg_attribute', $args);
	}

	/**
	 * Registers the Attribute tags for grouping in the admin area.
	 *
	 * @since    1.7.1
	 */
	public function register_tag()
	{
		$labels = array(
			'name'              => __('Attribute tags', 'staggs'),
			'singular_name'     => __('Attribute tag', 'staggs'),
			'search_items'      => __('Search attribute tags', 'staggs'),
			'all_items'         => __('All attribute tags', 'staggs'),
			'edit_item'         => __('Edit attribute tag', 'staggs'),
			'update_item'       => __('Update attribute tag', 'staggs'),
			'add_new_item'      => __('Add new attribute tag', 'staggs'),
			'new_item_name'     => __('New attribute tag', 'staggs'),
			'menu_name'         => __('Attribute tags', 'staggs'),
			'not_found'          => __('No attribute tags found.', 'staggs'),
			'not_found_in_trash' => __('No attribute tags found in bin.', 'staggs'),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => false,
		);

		register_taxonomy('sgg_attribute_tag', array('sgg_attribute'), $args);
	}

	/**
	 * Registers the Attribute admin table filters
	 *
	 * @since    1.7.1
	 */
	public function add_taxonomy_filters()
	{
		global $typenow;
		if ( $typenow !== 'sgg_attribute' ) {
			return;
		}

		// an array of all the taxonomyies you want to display. Use the taxonomy name or slug
		$my_taxonomies = array('sgg_attribute_tag');
		switch ($typenow) {
			case 'sgg_attribute':
				foreach ($my_taxonomies as $tax_slug) {
					$tax_obj = get_taxonomy($tax_slug);
					$tax_name = strtolower( $tax_obj->labels->name );
					$terms = get_terms($tax_slug);
					if (count($terms) > 0) {
						echo wp_kses_normalize_entities("<select name='$tax_slug' id='$tax_slug' class=''>");
						echo wp_kses_normalize_entities("<option value=''>Show all $tax_name</option>");
						foreach ($terms as $term) {
							echo wp_kses_normalize_entities('<option value="' . $term->slug . '" ' . selected(@$_GET[$tax_slug] == $term->slug, true, false) . '>' . $term->name . ' (' . $term->count . ')</option>');
						}
						echo "</select>";
					}
				}
				break;
		}

		global $wpdb;
		$prefix = '_';
		if ( defined( 'STAGGS_ACF' ) && STAGGS_ACF ) {
			$prefix = '';
		}

		$templates = $wpdb->get_results( $wpdb->prepare(
			'SELECT meta_value, COUNT(*) as meta_count FROM `%1$s` WHERE meta_key = "%2$s" GROUP BY meta_value ORDER BY meta_value ASC',
			$wpdb->prefix . 'postmeta',
			$prefix . 'sgg_step_template'
		), ARRAY_A);

		if (count($templates) > 0) {
			$filter_template = isset($_GET['sgg_templates']) ? esc_attr( $_GET['sgg_templates'] ) : '';
			echo "<select name='sgg_templates' id='sgg_templates' class=''>";
			echo "<option value=''>" . esc_attr__( 'Show all templates', 'staggs' ) . "</option>";
			foreach ($templates as $template) {
				if (str_contains($template['meta_value'], 'item:')){
					continue;
				}
				echo wp_kses_normalize_entities( '<option value="' . $template['meta_value'] . '" ' . selected($filter_template == $template['meta_value'], true, false) . '>' . ucfirst($template['meta_value']) . ' (' . $template['meta_count'] . ')</option>' );
			}
			echo "</select>";
		}

		$types = $wpdb->get_results( $wpdb->prepare(
			'SELECT meta_value, COUNT(*) as meta_count FROM `%1$s` WHERE meta_key = "%2$s" GROUP BY meta_value ORDER BY meta_value ASC',
			$wpdb->prefix . 'postmeta',
			$prefix . 'sgg_attribute_type'
		), ARRAY_A);

		if (count($types) > 0) {
			$filter_type = isset($_GET['sgg_types']) ? $_GET['sgg_types'] : '';
			echo "<select name='sgg_types' id='sgg_types' class=''>";
			echo "<option value=''>" . esc_attr__( 'Show all types', 'staggs' ) . "</option>";
			foreach ($types as $type) {
				if (str_contains($type['meta_value'], 'item:')){
					continue;
				}
				echo wp_kses_normalize_entities( '<option value="' . $type['meta_value'] . '" ' . selected($filter_type == $type['meta_value'], true, false) . '>' . ucfirst($type['meta_value']) . ' (' . $type['meta_count'] . ')</option>' );
			}
			echo "</select>";
		}
	}

	/**
	 * Filters the attribute admin table list
	 *
	 * @since    1.7.4
	 */
	public function filter_admin_attribute_table_results( $query ) {
		global $post_type, $pagenow;

		if ( $pagenow != 'edit.php' ) {
			return $query;
		}
		if ( $post_type != 'sgg_attribute' ) {
			return $query;
		}

		$prefix = '_';
		if ( defined( 'STAGGS_ACF' ) && STAGGS_ACF ) {
			$prefix = '';
		}

		$meta_query = array();
		if ( isset( $query->query_vars['meta_query'] ) ) {
			$meta_query = $query->query_vars['meta_query'];
		}
		
		if ( isset( $_GET['sgg_templates'] ) ) {
			$template = esc_attr( $_GET['sgg_templates'] );
			if ( '' !== $template ) {
				array_push( $meta_query, array(
					'key' =>  $prefix . 'sgg_step_template',
					'value' => $template
				));
			}
		}

		if ( isset( $_GET['sgg_types'] ) ) {
			$type = esc_attr( $_GET['sgg_types'] );
			if ( '' !== $type ) {
				array_push( $meta_query, array(
					'key' =>  $prefix . 'sgg_attribute_type',
					'value' => $type
				));
			}
		}

		$query->query_vars['meta_query'] = $meta_query;
		
		return $query;
	}

	/**
	 * Adds attribute columns to configurators list view.
	 *
	 * @since    1.4.0
	 */
	public function add_attribute_columns($columns)
	{
		unset($columns['title']);
		unset($columns['date']);

		$new_columns = array(
			'title'    => __('Attribute', 'staggs'),
			'name'     => __('Title', 'staggs'),
			'template' => __('Template', 'staggs'),
			'type'     => __('Type', 'staggs'),
			'attrtags' => __('Tags', 'staggs'),
			'date'     => __('Date', 'staggs'),
		);

		$columns = array_merge($columns, $new_columns);

		return $columns;
	}

	/**
	 * Fills the configurator list view attribute column values.
	 *
	 * @since    1.4.0
	 */
	public function fill_attribute_columns($column, $post_id)
	{
		if ('name' === $column) {
			echo esc_attr( ucfirst(staggs_get_post_meta($post_id, 'sgg_step_title')) );
		} else if ('template' === $column) {
			echo esc_attr( ucfirst(staggs_get_post_meta($post_id, 'sgg_step_template')) );
		} else if ('type' === $column) {
			echo esc_attr( ucfirst(staggs_get_post_meta($post_id, 'sgg_attribute_type')) );
		} else if ('attrtags' === $column) {
			$terms = get_the_terms($post_id, 'sgg_attribute_tag');
			if ( is_array( $terms ) && count($terms) > 0) {
				$current = 0;
				foreach ($terms as $term) {
					echo wp_kses_normalize_entities(  '<a href="' . admin_url('edit.php?post_type=sgg_attribute&sgg_attribute_tag=' . $term->slug ) . '">', $term->name . '</a>' );
					if ( $current + 1 !== count( $terms ) ) {
						echo ', ';
					}
					$current++;
				}
			} else {
				echo '-';
			}
		}
	}

	/**
	 * Clears saved attribute items transients.
	 *
	 * @since    1.4.4
	 */
	public function clear_transients($post_id)
	{
		delete_transient('staggs_attribute_values');
		delete_transient('staggs_attribute_conditional_values');
		delete_transient('staggs_attribute_conditional_inputs');
		delete_transient('staggs_attribute_conditional_labels_' . $post_id);
		delete_transient('sgg_formatted_attribute_' . $post_id);
	}

	/**
	 * Clears saved builder attribute transients.
	 *
	 * @since    1.5.3
	 */
	public function clear_builder_transients($post_id)
	{
		// Attributes.
		$builder_attributes = staggs_get_post_meta($post_id, 'sgg_configurator_attributes');
		foreach ($builder_attributes as $builder_attr) {
			if (isset($builder_attr['sgg_step_attribute'])) {
				delete_transient('sgg_formatted_attribute_' . $builder_attr['sgg_step_attribute']);
			}
		}
		// Theme.
		delete_transient( 'sgg_product_configurator_theme_id_' . $post_id );
	}

	/**
	 * Imports a Configurator Attribute.
	 *
	 * @since    1.4.0
	 */
	public static function import($data, $group_id = '')
	{
		// Update Attribute.
		if (isset($data['id']) && $data['id'] && self::exists($data['id'])) {
			$post_id = self::update($data);
		} else {
			$post_id = self::create($data);
		}

		return $post_id;
	}

	/**
	 * Checks if a Configurator Attribute exists.
	 *
	 * @since    1.4.0
	 */
	public static function exists($id)
	{
		if (!$id) {
			return false;
		}

		global $wpdb;
		$columns = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM %s WHERE post_type = 'sgg_attribute' AND ID = %d", 
			array( $wpdb->prefix . 'posts', $id )
		) );

		return (is_array($columns) && count($columns) > 0);
	}

	/**
	 * Creates a Configurator Attribute.
	 *
	 * @since    1.4.0
	 */
	public static function create($data)
	{
		global $wpdb;

		if ( isset( $data['admin_title'] ) || isset( $data['title'] ) ) {
			$post_title = isset( $data['admin_title'] ) ? $data['admin_title'] : $data['title'];
			$post_id = $wpdb->get_var(
				sprintf(
					"SELECT `ID` FROM %s WHERE `post_title` = '%s' AND `post_status` = 'publish' AND `post_type` = 'sgg_attribute'",
					$wpdb->prefix . 'posts',
					$post_title
				)
			);

			if ( ! $post_id) {
				$post_id = wp_insert_post(
					array(
						'post_title'  => $post_title,
						'post_type'   => 'sgg_attribute',
						'post_status' => 'publish',
					)
				);
			}

			self::set_field_data($post_id, $data);

			return $post_id;
		}

		return false;
	}

	/**
	 * Updates a Configurator Attribute.
	 *
	 * @since    1.4.0
	 */
	public static function update($data)
	{
		$post_id = sanitize_key( $data['id'] );

		if ( isset( $data['admin_title'] ) ) {
			$post_id = wp_update_post(
				array(
					'ID'    => $data['id'],
					'title' => $data['admin_title'],
				)
			);
		}

		self::set_field_data($post_id, $data, true);

		return $post_id;
	}

	/**
	 * Sets a Configurator Attribute Field data.
	 *
	 * @since    1.4.0
	 */
	public static function set_field_data($post_id, $new_data, $update = false)
	{
		// Unset non cf field info.
		unset($new_data['id']);
		unset($new_data['admin_title']);

		if ( $update ) {
			// Prefill with existing data.
			$data['items'] = staggs_get_post_meta($post_id, 'sgg_attribute_items');
		} else {
			// Create. New data = data
			$data = $new_data;
		}

		/**
		 * Scan for image links and download 'em
		 */
		if (isset($new_data['items']) && is_array($new_data['items']) && count($new_data['items']) > 0) {
			foreach ($new_data['items'] as $data_item_index => $data_item) {
				foreach ($data_item as $item_key => $item_value) {
					if ('sgg_option_image' === $item_key) {
						if ($item_value && '' !== $item_value) {
							$image_id = '';
	
							if (!is_numeric($item_value)) {
								$image_id = self::set_or_download_image($item_value);
							} else {
								$image_id = (int) $item_value;
							}

							$data['items'][$data_item_index][$item_key] = $image_id;
						}
					} elseif ('sgg_option_preview' === $item_key) {
						$preview_urls = explode('|', $item_value);

						if ( is_array( $preview_urls ) && count( $preview_urls ) > 0 ) {
							$image_ids = array();

							foreach ($preview_urls as $preview_url) {
								if ($preview_url && '' !== $preview_url) {
									if (!is_numeric($preview_url)) {
										$image_ids[] = self::set_or_download_image($preview_url);
									} else {
										$image_ids[] = (int) $preview_url;
									}
								}
							}
	
							$data['items'][$data_item_index][$item_key] = $image_ids;
						}
					} else {
						$data['items'][$data_item_index][$item_key] = $item_value;
					}
				}
			}
		}

		/**
		 * Update new post data.
		 */
		foreach ($new_data as $field_key => $field_value) {
			staggs_set_post_meta($post_id, $field_key, $field_value);
		}

		staggs_set_post_meta($post_id, 'sgg_attribute_items', $data['items']);
	}

	/**
	 * Checks if an image exists by image name.
	 *
	 * @since    1.4.0
	 */
	public static function set_or_download_image($image_url)
	{
		global $wpdb;

		$file_name   = esc_attr(basename($image_url));
		$wp_filetype = wp_check_filetype($file_name, null);
		$image_name  = str_replace( '.' . $wp_filetype['ext'], '', $file_name);

		$query    = sprintf("SELECT `ID` FROM %s WHERE `post_name`='%s' AND `post_type`='attachment'", $wpdb->posts, $image_name);
		$image_id = intval($wpdb->get_var($query));
		if (0 === (int) $image_id) {
			$image_id = self::download_image($image_url);
		}

		return $image_id;
	}

	/**
	 * Sets a Configurator Attribute Field data.
	 *
	 * @since    1.4.0
	 */
	public static function download_image($imageurl)
	{
		include_once ABSPATH . 'wp-admin/includes/image.php';

		$filename   = basename($imageurl);
		$uploaddir  = wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' . $filename;
		$contents   = file_get_contents($imageurl);
		$savefile   = fopen($uploadfile, 'w');

		fwrite($savefile, $contents);
		fclose($savefile);

		$wp_filetype   = wp_check_filetype(basename($filename), null);
		$wp_file_url   = str_replace( ABSPATH, get_site_url(), $uploadfile );
		$wp_file_title = str_replace( '.' . $wp_filetype['ext'], '', $filename );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => $wp_file_title,
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => $wp_file_url,
		);

		$attach_id    = wp_insert_attachment($attachment, $uploadfile);
		$imagenew     = get_post($attach_id);
		$fullsizepath = get_attached_file($imagenew->ID);
		$attach_data  = wp_generate_attachment_metadata($attach_id, $fullsizepath);

		wp_update_attachment_metadata($attach_id, $attach_data);

		return $attach_id;
	}
}
