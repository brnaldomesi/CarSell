<?php
/**
 * Define product carousel shortcode and helper functions for it
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/shortcodes
 * @version     1.0.0
 * @since       1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Define product carousel shortcode
 *
 * @since  1.0.1
 *
 * @param  array  $atts    shortcode attributes
 * @param  string $content shortcode content
 * @return void
 */

function cherry_wc_product_carousel_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'featured'            => 'no',
		'sale'                => 'no',
		'items_total'         => 12,
		'items_visible'       => 4,
		'items_desktop'       => '1199,4',
		'items_desktop_small' => '979,3',
		'items_tablet'        => '768,2',
		'items_mobile'        => '479,1',
		'custom_class'        => ''
	), $atts, 'cherry_wc_product_carousel' ) );

	global $woocommerce_loop, $cherry_woocommerce;

	wp_enqueue_script( 'cherry_woocommerce_carousel', $cherry_woocommerce->url( 'assets/js/owl.carousel.min.js' ), array( 'jquery' ), $cherry_woocommerce->version, true );

	// Get products on sale
	$product_ids_on_sale = wc_get_product_ids_on_sale();

	$meta_query   = array();
	$tax_query    = array();
	$meta_query[] = WC()->query->visibility_meta_query();
	$meta_query[] = WC()->query->stock_status_meta_query();

	// add featured products to metaquey if needed
	if ( 'yes' == $featured && ! function_exists( 'wc_get_product_visibility_term_ids' ) ) {
		$meta_query[] = array(
			'key'   => '_featured',
			'value' => 'yes'
		);
	} elseif ( 'yes' == $featured && function_exists( 'wc_get_product_visibility_term_ids' ) )  {
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => $product_visibility_term_ids['featured'],
		);
	}

	$meta_query   = array_filter( $meta_query );

	$args = array(
		'posts_per_page' => $items_total,
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'meta_query'     => $meta_query,
		'tax_query'      => $tax_query,
	);

	// add on slae products to query if needed
	if ( 'yes' == $sale && function_exists( 'wc_get_product_ids_on_sale' ) ) {
		$product_ids_on_sale = wc_get_product_ids_on_sale();
		$args['post__in'] = array_merge( array( 0 ), $product_ids_on_sale );
	}

	$carousel_params_array = array(
		'items'             => "$items_visible",
		'itemsDesktop'      => "[$items_desktop]",
		'itemsDesktopSmall' => "[$items_desktop_small]",
		'itemsTablet'       => "[$items_tablet]",
		'itemsMobile'       => "[$items_mobile]",
		'navigation'        => "true",
		'navigationText'    => "[\"\",\"\"]",
		'pagination'        => "false"
	);

	$carousel_params_array = apply_filters( 'cherry_wc_product_carousel_script_params', $carousel_params_array, $atts );

	$carousel_params = cherry_wc_product_carousel_script_params( $carousel_params_array );

	ob_start();

	$products = new WP_Query( $args );

	$woocommerce_loop['columns'] = $items_visible;

	if ( $products->have_posts() ) : ?>

		<?php

			/**
			 * Hook cherry_wc_product_carousel_before_carousel
			 * fires before carousel markup output start
			 *
			 * @param  array $atts shortcode attributes
			 */

			do_action( 'cherry_wc_product_carousel_before_carousel', $atts );

			echo '<div class="cherry_wc_product_carousel ' . esc_attr( $custom_class ) . '"' . $carousel_params . '>';
			echo '<ul class="products owl-carousel">';

		?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php cherry_wc_get_template_part( 'content-product', true ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php

			echo '</ul>';
			echo '</div>';

			/**
			 * Hook cherry_wc_product_carousel_after_carousel
			 * fires after carousel markup output end
			 *
			 * @param  array $atts shortcode attributes
			 */

			do_action( 'cherry_wc_product_carousel_after_carousel', $atts );

		?>
	<?php endif;

	wp_reset_postdata();

	return '<div class="woocommerce">' . ob_get_clean() . '</div>';

}



/**
 * Combine product carousel params array into data attributes string
 *
 * @since  1.0.1
 *
 * @param  array  $params  input params array
 * @return string $result  combined data attributes string
 */
function cherry_wc_product_carousel_script_params( $params = array() ) {

	if ( !is_array($params) ) {
		return;
	}

	$result = ' data-params=\'{';
	$index  = 0;
	foreach ( $params as $param_name => $param_val ) {
		if ( 0 != $index ) {
			$result .= ', ';
		}
		$index++;
		$result .= '"' . $param_name . '":' . $param_val;
	}
	$result .= '}\'';
	return $result;
}
