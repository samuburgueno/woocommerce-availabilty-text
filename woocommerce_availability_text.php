<?php
/**
 * Plugin Name: Availability Text for WooCommerce
 * Version: 1.0.0
 * Plugin URI: https://github.com/samuburgueno/woocommerce-availabilty-text
 * Description: Change Out of stock availability display, with defined custom text.
 * Author URI: https://samuburgueno.com
 * Domain Path: /languages/
 * Text Domain: woocommerce-abailability-text
 */

function woocommerce_abailability_text_load_plugin_textdomain() {
    load_plugin_textdomain( 
    	'woocommerce-abailability-text', FALSE, basename( dirname( __FILE__ ) ) . '/languages' 
    );
}
add_action( 'plugins_loaded', 'woocommerce_abailability_text_load_plugin_textdomain' );

/**
 * Change product availability text
 * https://pluginrepublic.com/add-custom-fields-woocommerce-product/
 */
add_filter( 'woocommerce_get_availability_text', 'filter_product_availability_text', 10, 2);
function filter_product_availability_text( $availability, $product ) {
	if ( $product->get_backorders() != 'no' ) {
		$availability .= '<p class="stock">' . $product->get_meta( 'wat_availability_text' ) . '</p>';
	} else {
		$availability .= '<p class="stock"><a class="button alt" target="_self" href="' . $product->get_meta( 'wat_contact_link' ) . '?product_name=' . $product->get_name() . '&product_id=' . $product->get_id() . '">' . __( 'Check availability', 'woocommerce-abailability-text' ) . '</a></p>';
	}

    return $availability;
}

/**
 * Add custom fields to woocommerce product data
 * https://pluginrepublic.com/add-custom-fields-woocommerce-product
 */
function wat_custom_fields() {
	$nostock = array(
		'id' => 'wat_contact_link',
		'label' => __( 'Contact link', 'woocommerce-abailability-text' ),
		'desc_tip' => false,
		'description' => __( 'Enter the contact page link for unavailable products. Visible when backorders disabled.', 'woocommerce-abailability-text' ),
		'data_type' => 'url'
	);
	
	woocommerce_wp_text_input( $nostock );

	$availability = array(
		'id' => 'wat_availability_text',
		'label' => __( 'Availability text', 'woocommerce-abailability-text' ),
		'desc_tip' => false,
		'description' => __( 'Enter a text to know when the product will be available. Example: "Available in 15 days". Visible when backorders enabled.', 'woocommerce-abailability-text' ),
	);
	
	woocommerce_wp_text_input( $availability );
}
add_action( 'woocommerce_product_options_inventory_product_data', 'wat_custom_fields' );

/**
 * Save data
 */
function wat_save_custom_fields( $post_id ) {
	$product = wc_get_product( $post_id );
	// Save contact link
	$contact_link = isset( $_POST['wat_contact_link'] ) ? $_POST['wat_contact_link'] : '';
	$product->update_meta_data( 'wat_contact_link', sanitize_text_field( $contact_link ) );
	// Save availability text
	$availability_text = isset( $_POST['wat_availability_text'] ) ? $_POST['wat_availability_text'] : '';
	$product->update_meta_data( 'wat_availability_text', sanitize_text_field( $availability_text ) );
	
	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'wat_save_custom_fields' );