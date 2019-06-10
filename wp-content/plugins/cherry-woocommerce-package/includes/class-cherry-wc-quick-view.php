<?php
/**
 * Cherry WooCommerce quick view module
 *
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Menu badges management class
 *
 * @since 1.1.0
 */
class cherry_wc_quick_view {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 99 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'append_open_wrap' ), 0 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'append_close_wrap' ), 100 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'append_button' ), 99 );

		if ( is_admin() ) {
			add_action( 'wp_ajax_cherry_wc_quick_view', array( $this, 'process_ajax_request' ) );
			add_action( 'wp_ajax_nopriv_cherry_wc_quick_view', array( $this, 'process_ajax_request' ) );
		}
	}

	/**
	 * Enqueue necessary CSS and JS
	 *
	 * @since 1.1.0
	 */
	function assets() {
		global $cherry_woocommerce;
		if ( !wp_script_is( 'magnific-popup', 'enqueued' ) ) {
			wp_enqueue_script( 'magnific-popup', $cherry_woocommerce->url( 'jquery.magnific-popup.min.js' ), array( 'jquery' ), $cherry_woocommerce->version, true );
		}
		if ( !wp_style_is( 'magnific-popup', 'enqueued' ) ) {
			wp_enqueue_style( 'magnific-popup', $cherry_woocommerce->url( 'assets/css/magnific-popup.css' ), '', $cherry_woocommerce->version, 'all' );
		}
		if ( !wp_script_is( 'cherry_prettyPhoto', 'enqueued' ) ) {
			wp_enqueue_script( 'prettyPhoto', $cherry_woocommerce->url( 'assets/js/jquery.prettyPhoto.min.js' ), '', $cherry_woocommerce->version, 'all' );
		}
		if ( !wp_style_is( 'cherry_prettyPhoto_css', 'enqueued' ) ) {
			wp_enqueue_style( 'cherry_prettyPhoto_css', $cherry_woocommerce->url( 'assets/css/prettyPhoto.css' ), '', $cherry_woocommerce->version, 'all' );
		}
	}

	/**
	 * Append open wrapper for quick view
	 *
	 * @since 1.1.0
	 */
	function append_open_wrap() {
		echo '<div class="cherry-thumb-wrap">';
	}

	/**
	 * Append open wrapper for quick view
	 *
	 * @since 1.1.0
	 */
	function append_close_wrap() {
		echo '</div>';
	}

	/**
	 * Append quick view button to product listing template
	 *
	 * @since 1.1.0
	 */
	function append_button() {
		global $post, $product;

		$btn_tex = apply_filters( 'cherry_wc_quick_view_text', __( 'Quick view', 'cherry-woocommerce-package' ) );

		echo '<span class="btn cherry-quick-view" data-product="' . $product->id . '">' . $btn_tex . '</span>';

		wp_enqueue_script( 'prettyPhoto' );
	}

	/**
	 * Process AJAX request and return quick view popup content
	 *
	 * @since 1.1.0
	 */
	function process_ajax_request() {

		$verify_nonce = check_ajax_referer( 'cherry_wc_data', '_wpnonce', false );

		$error_message = array( 'content' => __( 'Error!', 'cherry-woocommerce-package' ) );

		if ( !$verify_nonce ) {
			wp_send_json( $error_message );
		}

		$product_id = isset( $_REQUEST['product'] ) ? $_REQUEST['product'] : false;

		if ( !$product_id ) {
			wp_send_json( $error_message );
		}

		if ( !class_exists( 'WC_Product_Factory' ) ) {
			wp_send_json( $error_message );
		}

		global $product, $woocommerce, $post;

		$product_factory = new WC_Product_Factory();

		$post    = get_post( $product_id );
		$product = $product_factory->get_product( $product_id );

		setup_postdata( $post );

		ob_start();
		cherry_wc_get_template_part( 'quick-view-content' );
		$result = ob_get_clean();

		wp_reset_postdata();

		wp_send_json( array( 'content' => $result ) );

	}

}

new cherry_wc_quick_view();