<?php
/**
 * Include template functions
 *
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/functions
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

add_action( 'cherry_quick_view_content_images', 'cherry_wc_quick_view_sale', 5 );
add_action( 'cherry_quick_view_content_images', 'cherry_wc_quick_view_images', 10 );

add_action( 'cherry_quick_view_content_data', 'cherry_wc_quick_view_rating', 5 );
add_action( 'cherry_quick_view_content_data', 'cherry_wc_quick_view_price', 10 );
add_action( 'cherry_quick_view_content_data', 'cherry_wc_quick_view_title', 15 );
add_action( 'cherry_quick_view_content_data', 'cherry_wc_quick_view_add_to_cart', 20 );
add_action( 'cherry_quick_view_content_data', 'cherry_wc_quick_view_meta', 25 );

add_action( 'cherry_quick_view_content_description', 'cherry_wc_quick_view_excerpt', 5 );

add_action( 'woocommerce_share', 'cherry_wc_share_buttons' );

add_action( 'wp_head', 'cherry_wc_product_og_tags' );


/**
 * Show sale flash
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_sale() {
	cherry_wc_get_template_part( 'single-product/sale-flash', true );
}

/**
 * Show product images
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_images() {
	cherry_wc_get_template_part( 'quick-view-content-image' );
}

/**
 * Show product rating
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_rating() {
	cherry_wc_get_template_part( 'quick-view-content-rating' );
}

/**
 * Show product title
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_title() {
	cherry_wc_get_template_part( 'single-product/title', true );
}

/**
 * Show product price
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_price() {
	cherry_wc_get_template_part( 'single-product/price', true );
}

/**
 * Show product meta
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_meta() {
	cherry_wc_get_template_part( 'single-product/meta', true );
}

/**
 * Trigger the single product add to cart action.
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_add_to_cart() {
	echo '<div class="cherry-quick-view-add-to-cart">';
		cherry_wc_get_template_part( 'loop/add-to-cart', true );
	echo '</div>';
}

/**
 * Show product excerpt
 *
 * @since 1.1.0
 */
function cherry_wc_quick_view_excerpt() {
	echo get_the_excerpt();
}

/**
 * Use own template for product images
 *
 * @since  1.2.0
 *
 * @param  string  $template       full template path
 * @param  string  $template_name  template name
 * @param  string  $template_path  default relative template path
 */
function cherry_wc_get_image_template( $template, $template_name, $template_path ) {

	if ( 'single-product/product-image.php' == $template_name ) {
		global $cherry_woocommerce;
		$child_template  = get_stylesheet_directory() . '/wocommerce/' . $template_name;
		$plugin_template = $cherry_woocommerce->dir( 'templates/' ) . $template_name;
		$template = file_exists( $child_template ) ? $child_template : $plugin_template;
	}

	return $template;
}
add_filter( 'woocommerce_locate_template', 'cherry_wc_get_image_template', 10, 3 );


if ( ! function_exists( 'cherry_wc_placeholder' ) ) {

	/**
	 * Show placeholder for single product image
	 *
	 * @since 1.2.0
	 */
	function cherry_wc_placeholder() {

		echo '<div class="product-thumbnails">';
		echo '<div class="product-thumbnails_list">';
		for ($i=0; $i < 4; $i++) {
			echo '<div class="product-thumbnails_item"><div class="cherry-wc-placeholder placeholder-thumb"></div></div>';
		}
		echo '</div>';
		echo '</div>';
		echo '<div class="product-large-image"><div class="cherry-wc-placeholder placeholder-large"><i class="icon-picture"></i></div></div>';
	}

}

/**
 * Get thumbnails placeholders if product has < 4 thumbnails
 *
 * @since 1.2.0
 *
 * @param  integer $thumbs_count product thumbs count
 */
function cherry_wc_get_missed_thumb( $thumbs_count = 0 ) {

	if ( ! $thumbs_count || 4 <= $thumbs_count ) {
		return;
	}

	$missed_count = 4 - $thumbs_count;

	for ($i=0; $i < $missed_count; $i++) {
		echo '<div class="product-thumbnails_item"><div class="cherry-wc-placeholder placeholder-thumb"><i class="icon-picture"></i></div></div>';
	}
}

/**
 * Show share buttons for product
 *
 * @since 1.2.0
 */
function cherry_wc_share_buttons() {

	$sharedata = array(
		'facebook'    => 'https://www.facebook.com/sharer/sharer.php?u=%1$s',
		'twitter'     => 'https://twitter.com/intent/tweet?url=%1$s&status=%2$s',
		'google-plus' => 'https://plus.google.com/share?url=%1$s',
		'pinterest'   => 'https://pinterest.com/pin/create/bookmarklet/?media=%3$s&url=%1$s&is_video=false&description=%2$s'
	);

	$format = apply_filters( 'cherry_wc_share_button_format', '<div class="share-buttons_item"><a href="#" data-url="%2$s" class="share-buttons_link link-%1$s"><i class="icon-%1$s"></i></a></div>' );

	$url   = urlencode( get_permalink() );
	$text  = urlencode( get_the_title() . ' - ' . get_permalink() );
	$media = false;
	if ( has_post_thumbnail() ) {
		$media = wp_get_attachment_url( get_post_thumbnail_id() );
		$media = urlencode( $media );
	}

	echo '<div class="share-buttons">';
	foreach ( $sharedata as $net => $link ) {
		$link = sprintf( $link, $url, $text, $media );
		printf( $format, $net, $link );
	}
	echo '</div>';
}

/**
 * Add OpenGraph tags to single product page
 *
 * @since 1.2.0
 */
function cherry_wc_product_og_tags() {

	if ( ! is_singular( 'product' ) ) {
		return;
	}

	$site_title  = get_bloginfo( 'name' );
	$url         = get_permalink();
	$page_title  = get_the_title() . ' - ' . $site_title;
	$description = strip_tags( get_the_excerpt() );
	$image       = false;

	if ( has_post_thumbnail() ) {
		$image = wp_get_attachment_url( get_post_thumbnail_id() );
	}


	?>
	<meta property="og:title" content="<?php echo $page_title; ?>">
	<meta property="og:url" content="<?php echo $url; ?>">
	<meta property="og:type" content="product">
	<meta property="og:site_name" content="<?php echo $site_title; ?>">
	<meta property="og:description" content="<?php echo $description; ?>">
	<meta property="og:image" content="<?php echo $image; ?>">
	<?php
}