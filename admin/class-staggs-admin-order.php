<?php

/**
 * The file that defines the core plugin admin order class
 *
 * @link       https://staggs.app
 * @since      1.3.1
 *
 * @package    Staggs
 * @subpackage Staggs/admin
 */

/**
 * The core plugin admin order handler class.
 *
 * This is used to define all WooCommerce admin order hooks.
 *
 * @since      1.3.1.
 * @package    Staggs
 * @subpackage Staggs/admin
 * @author     Staggs <contact@staggs.app>
 */
class Staggs_Admin_Order {

	/**
	 * Remove loop add to cart links for configurable products.
	 *
	 * @since    1.3.1
	 */
	public function filter_admin_order_item_thumbnail( $image, $item_id, $item ) {
		$product = $item->get_product();

		if ( ! product_is_configurable( $product->get_id() ) ) {
			return $image;
		}

		$item_meta = $item->get_meta_data();
		if ( ! $item_meta || ! is_array( $item_meta ) || 0 === count( $item_meta ) ) {
			return $image;
		}
		
		$thumbnail_url = '';
		foreach ( $item_meta as $meta_obj ) {
			if ( '_product_thumbnail' === $meta_obj->key ) {
				$thumbnail_url = $meta_obj->value;
			}
		}

		$thumbnail_path = str_replace( trailingslashit( get_site_url() ), ABSPATH, $thumbnail_url );
		if ( '' === $thumbnail_url || ! file_exists( $thumbnail_path ) ) {
			return $image;
		}

		return '<img src="' . $thumbnail_url . '">';
	}

	/**
	 * Hide order item meta from admin view
	 * 
	 * @since    1.3.1
	 */
	public function hide_private_order_itemmeta( $keys ) {
		$keys[] = '_configurator_item_ids';
		$keys[] = '_configurator_sku_data';
		$keys[] = '_product_thumbnail';

		$keys = apply_filters( 'staggs_hide_order_item_meta', $keys );

		return $keys;
	}
}
