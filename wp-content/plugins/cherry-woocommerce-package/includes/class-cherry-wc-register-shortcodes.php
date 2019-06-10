<?php
/**
 * Register existing shortcodes
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/class
 * @version     1.0.0
 * @since       1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('cherry_wc_register_shortcodes') ) {
	
	/**
	 * Register shortcodes class
	 *
	 * @since 1.0.1
	 */
	class cherry_wc_register_shortcodes {

		function __construct() {
			$this->include_shortcodes();
			$this->register_shortcodes();
		}	

		/**
		 * Include shortcodes files
		 * 
		 * @since  1.0.1
		 * 
		 * @return void
		 */
		public function include_shortcodes() {
			require( 'shortcodes/shortcode-cherry-wc-product-carousel.php' );
		}

		/**
		 * Register included shortcodes
		 * 
		 * @since  1.0.1
		 * 
		 * @return void
		 */
		public function register_shortcodes() {
			add_shortcode( 'cherry_wc_product_carousel', 'cherry_wc_product_carousel_shortcode' );
		}

	}

	new cherry_wc_register_shortcodes();
}