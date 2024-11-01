<?php

/**
 * The main hooks of this plugin.
 *
 * @link       https://staggs.app
 * @since      1.3.0
 *
 * @package    Staggs
 * @subpackage Staggs/includes
 */

/**
 * General
 */

add_action( 'staggs_before_single_product', 'staggs_product_configurator_wrapper', 10 );
add_action( 'staggs_after_single_product', 'staggs_product_configurator_wrapper_close', 10 );

add_action( 'staggs_before_single_product_content', 'staggs_output_content_wrapper', 10 );
add_action( 'staggs_after_single_product_content', 'staggs_output_content_wrapper_close', 10 );

/**
 * Configurator Single Product Page Gallery
 */

add_action( 'staggs_single_product_gallery', 'staggs_output_preview_gallery_wrapper', 10 );
add_action( 'staggs_single_product_gallery', 'staggs_output_preview_gallery', 20 );
add_action( 'staggs_single_product_gallery', 'staggs_output_preview_gallery_nav', 30 );
add_action( 'staggs_single_product_gallery', 'staggs_output_preview_gallery_wrapper_close', 40 );

/**
 * Configurator Single Product Page Options
 */

add_action( 'staggs_before_single_product_options', 'staggs_output_options_wrapper', 10 );
add_action( 'staggs_before_single_product_options', 'staggs_output_options_form', 20 );

add_action( 'staggs_single_product_summary', 'staggs_product_single_options_back_button', 1 );
add_action( 'staggs_single_product_summary', 'staggs_product_single_options_logo', 5 );

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	add_action( 'staggs_single_product_summary', 'woocommerce_template_single_title', 10 );
	add_action( 'staggs_single_product_summary', 'woocommerce_template_single_rating', 15 );
	add_action( 'staggs_single_product_summary', 'woocommerce_template_single_excerpt', 30 );
	add_action( 'staggs_single_product_summary', 'woocommerce_template_single_sharing', 40 );
} else {
	add_action( 'staggs_single_product_summary', 'staggs_product_single_title', 10 );
	add_action( 'staggs_single_product_summary', 'staggs_product_single_description', 30 );
}

add_action( 'staggs_single_product_options', 'staggs_output_single_product_options', 10 );
add_action( 'staggs_single_product_options', 'staggs_output_options_form_close', 20 );

add_action( 'staggs_after_single_product_options', 'staggs_output_options_wrapper_close', 20 );

/**
 * Configurator Single Configurator Option Group
 */

add_action( 'staggs_single_option_group', 'staggs_output_option_group_header', 10 );
add_action( 'staggs_single_option_group', 'staggs_output_option_group_content', 20 );
add_action( 'staggs_single_option_group', 'staggs_output_option_group_summary', 30 );

add_action( 'staggs_tab_option_group', 'staggs_output_option_tab_content', 20 );

add_action( 'staggs_after_single_option_group', 'staggs_option_group_description_panel', 10 );

/**
 * Configurator Totals
 */

add_action( 'woocommerce_before_add_to_cart_quantity', 'staggs_output_add_to_cart_wrapper', 10 );
add_action( 'woocommerce_after_add_to_cart_button', 'staggs_output_add_to_cart_wrapper_close', 10 );

add_action( 'staggs_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 10 );

add_action( 'staggs_single_product_options_totals', 'staggs_single_product_options_totals_wrapper', 5 );
add_action( 'staggs_single_product_options_totals', 'staggs_output_product_totals_list', 20 );
add_action( 'staggs_single_product_options_totals', 'staggs_output_product_main_button', 30 );
add_action( 'staggs_single_product_options_totals', 'staggs_output_options_credit', 50 );
add_action( 'staggs_single_product_options_totals', 'staggs_single_product_options_totals_wrapper_close', 65 );

/**
 * Configurator Popup Topbar
 */

add_action( 'staggs_single_product_popup_topbar', 'staggs_output_topbar_wrapper', 10 );
add_action( 'staggs_single_product_popup_topbar', 'staggs_output_topbar_product_title', 20 );
add_action( 'staggs_single_product_popup_topbar', 'staggs_output_topbar_wrapper_close', 50 );

/**
 * Configurator Popup Bottom bar
 */

add_action( 'staggs_single_product_popup_bottom_bar', 'staggs_output_bottom_bar_wrapper', 10 );
add_action( 'staggs_single_product_popup_bottom_bar', 'staggs_output_popup_bottom_bar', 30 );
add_action( 'staggs_single_product_popup_bottom_bar', 'staggs_output_bottom_bar_wrapper_close', 40 );

/**
 * Configurator Splitter Template Bottom bar
 */

add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_bottom_bar_wrapper', 10 );
add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_bottom_bar_info_wrapper', 20 );
add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_product_secondary_buttons', 30 );
add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_bottom_bar_info_wrapper_close', 50 );
add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_bottom_bar_totals', 60 );
add_action( 'staggs_splitter_product_bottom_bar', 'staggs_output_bottom_bar_wrapper_close', 70 );

add_action( 'staggs_before_bottom_bar_totals', 'staggs_output_product_totals_list', 10 );
add_action( 'staggs_bottom_bar_totals', 'staggs_output_product_main_button', 20 );

/**
 * Classic Configurator Sticky Bar
 */

add_action( 'staggs_after_single_product_options', 'staggs_output_product_sticky_bar', 10 );
