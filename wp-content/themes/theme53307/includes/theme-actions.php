<?php
/**
 * Custom template actions 
 */

/*==============
  Catalog Page 
================*/

//Product short description
add_action( 'woocommerce_after_shop_loop_item', 'tm_catalog_product_description', 5 );
function tm_catalog_product_description() {
	$cat_show_desc = of_get_option( 'cat_show_desc' );
	if ( 'yes' == $cat_show_desc ) {
		if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		global $post;
		if ( ! $post->post_excerpt ) return;
		?>
		<div class="short_desc">
			<?php echo $post->post_excerpt; ?>
		</div>
<?php 
	}
}

// Catalog details button
add_action( 'woocommerce_after_shop_loop_item', 'tm_catalog_product_details', 15 );
function tm_catalog_product_details() {
	$cat_show_details = of_get_option( 'cat_show_details' );
	if ( 'yes' == $cat_show_details ) {
		if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		global $post, $product;
		if ( ('variable' != $product->product_type) && ('external' != $product->product_type) ) {
			echo "<a href='" . get_permalink() . "' class='btn'>" . __( "Details", CURRENT_THEME ) . "</a>";
		}
	}
}

// Add to wishlist in product list
add_action( 'woocommerce_after_shop_loop_item', 'tm_add_to_wishlist', 25 );
function tm_add_to_wishlist() {
	
	if ( !defined( 'YITH_WCWL' ) ) {
		return;
	}
	
	echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
}

// add wrapper for aaditional buttons
add_action( 'woocommerce_after_shop_loop_item', 'tm_add_buttons_wrap_open', 16 );
add_action( 'woocommerce_after_shop_loop_item', 'tm_add_buttons_wrap_close', 99 );
function tm_add_buttons_wrap_open() {
	echo '<div class="product-list-buttons">';
}
function tm_add_buttons_wrap_close() {
	echo '</div>';
}

// add clearing div to single product page
add_action( 'woocommerce_single_product_summary', 'tm_add_product_clearing_div', 31 );
function tm_add_product_clearing_div() {
	echo '<div class="clear"></div>';
}

?>