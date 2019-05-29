<?php
/**
 * Additional shop functions
 */
if (!class_exists('Woocommerce')) {
	return;
}

include_once( 'theme-actions.php' );
include_once( 'child-shortcodes.php' );


add_filter( 'body_class', 'cherry_add_comapre_class_to_body' );
function cherry_add_comapre_class_to_body($classes) {
	
	if ( isset($_GET['action']) && 'yith-woocompare-view-table' == $_GET['action'] ) {
		$classes[] = 'woocompare_table';
	}

	return $classes;
}


// Product title length
add_filter( 'the_title', 'tm_product_title_length', 10, 2 );
function tm_product_title_length($title) {
	if (is_admin()) 
		return $title;
	global $post, $woocommerce_loop;
	$post_type = get_post_type( $post );
	if ($woocommerce_loop) {
		if ( 'product' == $post_type ) {
			$length_limit = intval( of_get_option('cat_title_length_limit') );
			if ( "" != $length_limit ) {
				$words = explode(' ', $title, ($length_limit + 1));
				if( count($words) > $length_limit ) {
					array_pop($words);
					$title = implode(' ', $words) . '... ';
				}
			}
		}
	}
	return $title;
}

// add woocommerce classes to body
add_filter('body_class','tm_add_plugin_name_to_body_class');
function tm_add_plugin_name_to_body_class($classes) {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$classes[] = 'has_woocommerce has_shop';
	}
	return $classes;
}

// Empty cart message
add_action( 'wp_footer', 'empty_cart', 80 );
function empty_cart() {
	$empty_cart_mess = of_get_option( 'empty_cart_mess' );
	?>
	<script>
	(function($) {
		$(window).load(function() {
			if ($('.widget_shopping_cart_content').is(':empty')) {
				$('.widget_shopping_cart_content').text('<?php echo $empty_cart_mess; ?>');
			}
		});
	})(jQuery);
	</script>
	<?php
}

// Products per page
add_filter( 'loop_shop_per_page', 'tm_product_per_page', 20 );
function tm_product_per_page() {
	$prod_number = of_get_option( 'prod_per_page' );
	if (!$prod_number) $prod_number = 8;
	return $prod_number;
}

//Related products limit
function tm_related_products_limit() {
	global $product;
	$orderby = '';
	$columns = 4;
	$related = $product->get_related( 4 );
	$args = array(
		'post_type' => 'product',
		'no_found_rows' => 1,
		'posts_per_page' => 3,
		'ignore_sticky_posts' => 1,
		'orderby' => $orderby,
		'post__in' => $related,
		'post__not_in' => array($product->id)
	);
	return $args;
}
add_filter( 'woocommerce_related_products_args', 'tm_related_products_limit' );

// Template Wrappers
function tm_open_shop_content_wrappers(){
	echo '<div class="motopress-wrapper content-holder clearfix woocommerce">
			<div class="container">
				<div class="row">
					<div class="span12" data-motopress-type="static" data-motopress-static-file="static/static-title.php">';
						echo get_template_part("static/static-title");
	echo 			'</div>
				</div>
				<div class="row">
					<div class="' . cherry_get_layout_class( 'content' ) . '" id="content">';
}
function tm_close_shop_content_wrappers(){
	echo			'</div>
					<div class="sidebar ' . cherry_get_layout_class( 'sidebar' ) . '" id="sidebar" data-motopress-type="static-sidebar"  data-motopress-sidebar-file="sidebar.php">';
						get_sidebar();
	echo			'</div>
				</div>
			</div>
		</div>';
}

function tm_prepare_shop_wrappers(){
	/* Woocommerce */
	remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5, 0);
	remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

	add_action('woocommerce_before_main_content', 'tm_open_shop_content_wrappers', 10);
	add_action('woocommerce_after_main_content', 'tm_close_shop_content_wrappers', 10);
	/* end Woocommerce */	
}
add_action('wp_head', 'tm_prepare_shop_wrappers');

// declare theme support for woocommerce
add_theme_support( 'woocommerce' );

// add share buttons to product page
add_action('woocommerce_share', 'tm_product_share');
function tm_product_share() {
	get_template_part( 'includes/post-formats/share-buttons' );
}

// Add widget area to product page
add_action( 'woocommerce_after_single_product_summary', 'tm_add_product_page_sidebar', 30 );
function tm_add_product_page_sidebar() {
	dynamic_sidebar( 'product-page' );
}

// Change columns number
// ---------------------
add_filter( 'loop_shop_columns', 'tm_product_columns', 5);
function tm_product_columns($columns) {
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		$columns = 3;
	}
	return $columns;
}

// add theme styles to compare box
add_action( 'wp_head', 'tm_add_compare_style' );
function tm_add_compare_style() {
	if ( !isset($_GET['action']) || 'yith-woocompare-view-table' != $_GET['action'] ) {
		return;
	}
	?>
	<link type="text/css" href="<?php echo get_stylesheet_directory_uri() . '/main-style.css'; ?>" rel="stylesheet">
	<?php
}

/**
 * Additional Compare functions
 */

add_action( 'init', 'tm_comapre_fuction', 10 );
function tm_comapre_fuction() {
	global $yith_woocompare;

	if ( !$yith_woocompare || !isset( $yith_woocompare->obj->products_list ) ) {
		return;
	}

	$products_num = is_array( $yith_woocompare->obj->products_list ) ? count( $yith_woocompare->obj->products_list ) : 0;
}

// add comare table URL to JS variables
add_action( 'wp_enqueue_scripts', 'tm_compare_table_url', 99 );
function tm_compare_table_url() {

	if ( !defined( 'YITH_WOOCOMPARE' ) ) {
		return;
	} 

	global $yith_woocompare;

	$data = array( 
		'table_url' => add_query_arg( array(
				'action' => $yith_woocompare->obj->action_view,
				'iframe' => 'true',
				'ver' => time()
			), site_url() ),
		'ajax_url' => admin_url( 'admin-ajax.php' )
    );
	wp_localize_script( 'custom-script', 'compare_data', $data );
}