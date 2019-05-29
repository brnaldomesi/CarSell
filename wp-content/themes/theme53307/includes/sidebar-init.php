<?php
function elegance_widgets_init() {
	// Sidebar Widget
	// Location: the sidebar
	register_sidebar(array(
		'name'          => theme_locals("sidebar"),
		'id'            => 'main-sidebar',
		'description'   => theme_locals("sidebar_desc"),
		'before_widget' => '<div id="%1$s" class="widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>'
	));

	// Cart Holder Widget
	// Location: the sidebar
	register_sidebar(array(
		'name'          => __( "Cart Holder", "themeWoo" ),
		'id'            => 'cart-holder',
		'description'   => __( "Widget for cart in Header", "themeWoo" ),
		'before_widget' => '<div id="%1$s" class="cart-holder">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>'
	));

	// Product page widget area
	// Location: bottom of the product page
	register_sidebar(array(
		'name'          => __( "Product Page", "themeWoo" ),
		'id'            => 'product-page',
		'description'   => __( "Product page widget area", "themeWoo" ),
		'before_widget' => '<div class="product-page">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>'
	));


	// Footer Widget Area 1
	// Location: at the top of the footer, above the copyright
	register_sidebar(array(
		'name'          => theme_locals("footer_1"),
		'id'            => 'footer-sidebar-1',
		'description'   => theme_locals("footer_desc"),
		'before_widget' => '<div id="%1$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	));
	// Footer Widget Area 2
	// Location: at the top of the footer, above the copyright
	register_sidebar(array(
		'name'          => theme_locals("footer_2"),
		'id'            => 'footer-sidebar-2',
		'description'   => theme_locals("footer_desc"),
		'before_widget' => '<div id="%1$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	));
	// Footer Widget Area 3
	// Location: at the top of the footer, above the copyright
	register_sidebar(array(
		'name'          => theme_locals("footer_3"),
		'id'            => 'footer-sidebar-3',
		'description'   => theme_locals("footer_desc"),
		'before_widget' => '<div id="%1$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	));
	// Footer Widget Area 4
	// Location: at the top of the footer, above the copyright
	register_sidebar(array(
		'name'          => theme_locals("footer_4"),
		'id'            => 'footer-sidebar-4',
		'description'   => theme_locals("footer_desc"),
		'before_widget' => '<div id="%1$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>'
	));
    // Location: the footer
     register_sidebar(array(
      'name'     => __( "Google map", CURRENT_THEME ),
      'id'       => 'google',
      'description'   => __( "Google map", CURRENT_THEME ),
      'before_widget' => '<div id="%1$s" class="google-map">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>',
     ));

}
/** Register sidebars by running elegance_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'elegance_widgets_init' );
?>