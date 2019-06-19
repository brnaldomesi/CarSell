<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	add_action( 'wp_ajax_nopriv_wvs_get_available_variations', 'wvs_get_available_product_variations' );
	
	add_action( 'wp_ajax_wvs_get_available_variations', 'wvs_get_available_product_variations' );
	
	add_filter( 'product_attributes_type_selector', 'wvs_product_attributes_types' );
	
	add_action( 'init', 'wvs_settings', 2 );
	
	add_action( 'admin_init', 'wvs_add_product_taxonomy_meta' );
	
	// From WC 3.6+
	if ( defined( 'WC_VERSION' ) && version_compare( '3.6', WC_VERSION, '<=' ) ) {
		add_action( 'woocommerce_product_option_terms', 'wvs_product_option_terms', 20, 3 );
	} else {
		add_action( 'woocommerce_product_option_terms', 'wvs_product_option_terms_old', 20, 2 );
	}
	
	// Support Dokan Multi Vendor
	add_action( 'dokan_product_option_terms', 'wvs_product_option_terms', 20, 2 );
	
	add_filter( 'woocommerce_ajax_variation_threshold', 'wvs_ajax_variation_threshold', 8 );
	
	add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'wvs_variation_attribute_options_html', 200, 2 );
	
	// Add WooCommerce Default Image
	add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
		
		$classes = (array) explode( ' ', $attr[ 'class' ] );
		
		array_push( $classes, 'wp-post-image' );
		
		$attr[ 'class' ] = implode( ' ', array_unique( $classes ) );
		
		return $attr;
	}, 9 );
	
	if ( ! class_exists( 'Woo_Variation_Swatches_Pro' ) ) {
		add_filter( 'woocommerce_product_data_tabs', 'add_wvs_pro_preview_tab' );
		
		add_filter( 'woocommerce_product_data_panels', 'add_wvs_pro_preview_tab_panel' );
	}