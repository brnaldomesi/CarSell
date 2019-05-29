<?php
/**
 * Shop Options Managment 
*/

//Init shop options BackUp post type
add_action( 'init', 'initOptionsPost', 10);
function initOptionsPost() {
	register_post_type( 'shop_options',
		array( 
			'label'               => 'shop_options', 
			'singular_label'      => 'shop_options',
			'exclude_from_search' => true, // Exclude from Search Results
			'capability_type'     => 'page',
			'public'              => true, 
			'show_ui'             => false,
			'show_in_nav_menus'   => false,
			'supports'  => array('title', 'custom-fields')
		)
	);
}

//Create post for shop options
add_action( 'woocommerce_update_options', 'createOptionsPost', 40);
function createOptionsPost() {
	$args = array( 'posts_per_page' => 1, 'post_type'=> 'shop_options' );
	$opt_post = get_posts( $args );
	if ( !$opt_post ) {
		$post = array(
		  'post_name' => 'shop-options',
		  'post_title' => 'shop-options',
		  'post_type' => 'shop_options',
		  'post_status' => 'publish'
		);
		$post_id = wp_insert_post( $post );
	}

}

//First time write options top post
add_action( 'woocommerce_update_options', 'addOptions', 50);
function addOptions() {
	$args = array( 'posts_per_page' => 1, 'post_type'=> 'shop_options' );
	$opt_post = get_posts( $args );
	$opt_to_rewrite = array( 'woocommerce_enable_myaccount_registration', 'yith_woocompare_is_button', 'yith_woocompare_compare_button_in_products_list', 'yith_woocompare_auto_open', 'yith_woocompare_price_end', 'yith_woocompare_add_to_cart_end', 'yith_wcwl_use_cookie', 'yith_wcwl_wishlist_title', 'yith_wcwl_use_button', 'yith_wcwl_socials_title', 'woocommerce_default_country', 'woocommerce_currency', 'woocommerce_shop_page_id', 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_pay_page_id', 'woocommerce_thanks_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_edit_address_page_id', 'woocommerce_view_order_page_id', 'woocommerce_change_password_page_id', 'woocommerce_logout_page_id', 'woocommerce_lost_password_page_id', 'woocommerce_default_catalog_orderby');
	$s_opt_to_rewrite = array( 'yith_woocompare_image_size','shop_catalog_image_size', 'shop_single_image_size', 'shop_thumbnail_image_size');
	if ( $opt_post ) {
		$all_options = wp_load_alloptions();
		foreach ( $opt_post as $post ) {
			foreach( $all_options as $name => $value ) {
				if( in_array($name, $opt_to_rewrite)) {
					$already_exist = get_post_meta( $post->ID, $name );
					if( empty( $already_exist ) ) {
						add_post_meta($post->ID, $name, $value, true);
					} 
				} elseif ( in_array($name, $s_opt_to_rewrite)) {
					$already_exist = get_post_meta( $post->ID, $name );
					if( empty( $already_exist ) ) {
						$value = unserialize($value);
						add_post_meta($post->ID, $name, $value, true);
					}
				}
			}
		}
	}
}

//Update shop options backup in post meta
add_action( 'woocommerce_update_options', 'updateOptions', 60);
function updateOptions() {

	$args = array( 'posts_per_page' => 1, 'post_type'=> 'shop_options' );
	$opt_post = get_posts( $args );
	$opt_to_rewrite = array('woocommerce_enable_myaccount_registration', 'yith_woocompare_is_button', 'yith_woocompare_compare_button_in_products_list', 'yith_woocompare_auto_open', 'yith_woocompare_price_end', 'yith_woocompare_add_to_cart_end', 'yith_wcwl_use_cookie', 'yith_wcwl_wishlist_title', 'yith_wcwl_use_button', 'yith_wcwl_socials_title', 'woocommerce_default_country', 'woocommerce_currency', 'woocommerce_shop_page_id', 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_pay_page_id', 'woocommerce_thanks_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_edit_address_page_id', 'woocommerce_view_order_page_id', 'woocommerce_change_password_page_id', 'woocommerce_logout_page_id', 'woocommerce_lost_password_page_id', 'woocommerce_default_catalog_orderby');
	$s_opt_to_rewrite = array('yith_woocompare_image_size','shop_catalog_image_size', 'shop_single_image_size', 'shop_thumbnail_image_size');
	if ( $opt_post ) {
		$all_options = wp_load_alloptions();
		foreach ( $opt_post as $post ) {
			foreach( $all_options as $name => $value ) {
				if ( in_array($name, $opt_to_rewrite)) {
					update_post_meta($post->ID, $name, $value);
				} elseif ( in_array($name, $s_opt_to_rewrite)) {
					$value = unserialize($value);
					update_post_meta($post->ID, $name, $value);
				}
			}
		}
	}
		
}

//Copy category images ID's to postmeta
add_action('edit_term', 'saveCategoryThumb', 60, 3);
function saveCategoryThumb() {
	global $wpdb;
	$args = array( 'posts_per_page' => 1, 'post_type'=> 'shop_options' );
	$opt_post = get_posts( $args );
	if ( $opt_post ) {
		foreach ( $opt_post as $post ) {
			$prod_cats = get_terms( 'product_cat', $args );
			if ( $prod_cats ) {
				$thumbs_array = array();
				foreach ( $prod_cats as $cat ) {
					$cat_thumb = $wpdb->get_row("SELECT * FROM $wpdb->woocommerce_termmeta WHERE woocommerce_term_id = $cat->term_id AND meta_key = 'thumbnail_id'", ARRAY_A);
					$thumbs_array[$cat->slug] = $cat_thumb['meta_value'];
				}
				update_post_meta($post->ID, 'shop_cat_thumbs', $thumbs_array);
			}
		}
	}
}

//Remove old shop pages when new are imported
add_action( 'cherry_plugin_start_import', 'removeShopPages');
function removeShopPages() {
	$shop_pages = array('woocommerce_shop_page_id', 'woocommerce_terms_page_id', 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_pay_page_id', 'woocommerce_thanks_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_edit_address_page_id', 'woocommerce_view_order_page_id', 'woocommerce_change_password_page_id', 'woocommerce_logout_page_id', 'woocommerce_lost_password_page_id');
	$pages_removed = get_option( 'pages_removed' );
	if ( ( false != $shop_pages ) && ( false === $pages_removed ) ) {
		foreach($shop_pages as $page) {
			$page_id = get_option($page);
			wp_delete_post( $page_id, true );
		}
		update_option( 'pages_removed', 'removed' );
	}
}

//Rewrite shop options on import
add_action( 'cherry_plugin_import_post_meta', 'extractOptions', 30, 3);
function extractOptions($post_id, $key, $value) {
	$args = array( 'posts_per_page' => 1, 'post_type'=> 'shop_options' );
	$opt_post = get_posts( $args );
	$opt_to_rewrite = array('woocommerce_enable_myaccount_registration', 'yith_woocompare_is_button', 'yith_woocompare_compare_button_in_products_list', 'yith_woocompare_auto_open', 'yith_woocompare_price_end', 'yith_woocompare_add_to_cart_end', 'yith_wcwl_use_cookie', 'yith_wcwl_wishlist_title', 'yith_wcwl_use_button', 'yith_wcwl_socials_title', 'woocommerce_default_country', 'woocommerce_currency', 'woocommerce_shop_page_id', 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_pay_page_id', 'woocommerce_thanks_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_edit_address_page_id', 'woocommerce_view_order_page_id', 'woocommerce_change_password_page_id', 'woocommerce_logout_page_id', 'woocommerce_lost_password_page_id', 'woocommerce_default_catalog_orderby');
	$s_opt_to_rewrite = array('yith_woocompare_image_size', 'shop_catalog_image_size', 'shop_single_image_size', 'shop_thumbnail_image_size');
	if ( $opt_post ) {
		foreach ( $opt_post as $post ) {
			$meta_options = get_post_meta( $post->ID );
			if ($post_id == $post->ID) {
				if( in_array($key, $opt_to_rewrite)) {
					update_option( $key, $value );
					//echo $name . "-" . $value[0];
				} elseif ( in_array($key, $s_opt_to_rewrite)) {
					$single_meta = get_post_meta( $post->ID, $key, true );
					//$value = unserialize($value[0]);
					//echo $name . ":";
					$new_values = array();

					if ( !is_array($single_meta) ) {
						continue;
					}

					foreach ($single_meta as $new_key => $new_value) {
						$new_values[$new_key] = $new_value;
					}
					//$name = '_'.$name;
					update_option( $key, $new_values );
				}
			}
		}
	}
}

//Delete needed options on import start
add_action( 'cherry_plugin_start_import', 'tm_clear_data_before_import' );
function tm_clear_data_before_import() {
	delete_option( 'menu_seted' );
	delete_option( 'tm_content_imported' );
}

//Activate shop menu after data import
function set_top_menu_on_import() {
    $imported = get_option( 'tm_content_imported' );
    $menu_seted = get_option( 'menu_seted', 0 );

    if ( 'imported' == $imported && 0 == $menu_seted ) {
		$menus = get_terms('nav_menu');
		$menu_seted++;
		$save = array();
		foreach($menus as $menu) {
			if ($menu->name == 'Shop menu') {
		    	$save['shop_menu'] = $menu->term_id;
		    } elseif ($menu->name == 'Header Menu') {
		        $save['header_menu'] = $menu->term_id;
		    } elseif ($menu->name == 'Footer Menu') {
		        $save['footer_menu'] = $menu->term_id;
		    }
		}
		if($save){
			remove_theme_mod( 'nav_menu_locations' );
		    set_theme_mod( 'nav_menu_locations', array_map( 'absint', $save ) );
		}
		$menu_seted++;
		update_option( 'menu_seted', $menu_seted );
	}

}
add_action( 'init', 'set_top_menu_on_import' );


function tm_content_imported() {
	update_option( 'tm_content_imported', 'imported' );
}
add_action( 'cherry_plugin_set_settings', 'tm_content_imported', 80 );

//Import product attributes fix for woocommerce
add_action('cherry_plugin_start_import', 'tm_import_start', 10);
function tm_import_start() {
	global $wpdb;

	if (!isset($_POST['import_id'])) return;
	if (!class_exists('WXR_Parser')) return;

	$id = (int) $_POST['import_id'];
	$file = get_attached_file( $id );

	$parser = new WXR_Parser();
	$import_data = $parser->parse( $file );

	if (isset($import_data['posts'])) :
		$posts = $import_data['posts'];

		if ($posts && sizeof($posts)>0) foreach ($posts as $post) :

			if ($post['post_type']=='product') :

				if ($post['terms'] && sizeof($post['terms'])>0) :

					foreach ($post['terms'] as $term) :

						$domain = $term['domain'];

						if (strstr($domain, 'pa_')) :

							// Make sure it exists!
							if (!taxonomy_exists( $domain )) :

								$nicename = strtolower(sanitize_title(str_replace('pa_', '', $domain)));

								$exists_in_db = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_id FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $nicename ) );

								if (!$exists_in_db) :

									// Create the taxonomy
									$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", array( 'attribute_name' => $nicename, 'attribute_label' => $nicename, 'attribute_type' => 'select', 'attribute_orderby' => 'menu_order' ), array( '%s', '%s', '%s' ) );

								endif;

								// Register the taxonomy now so that the import works!
								register_taxonomy( $domain,
							        apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array('product') ),
							        apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
							            'hierarchical' => true,
							            'show_ui' => false,
							            'query_var' => true,
							            'rewrite' => false,
							        ) )
							    );

							endif;

						endif;

					endforeach;

				endif;

			endif;

		endforeach;

	endif;

}

// Update product gallery img
add_action( 'cherry_plugin_update_featured_images', 'tm_update_product_gall' );
function tm_update_product_gall() {
	if ( isset($_SESSION['processed_posts']) && !empty($_SESSION['processed_posts']) ) {
		$attachments = $_SESSION['processed_posts'];
		$products_args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1
		);
		$prod_query = new WP_Query( $products_args );
		$new_galleries = array();
		if ( $prod_query->have_posts() ) {
			while ($prod_query->have_posts()) {
				$prod_query->the_post();
				$prod_id = get_the_id();
				$prod_gallery = get_post_meta( $prod_id, '_product_image_gallery', true );
				$prod_gallery_array = explode(',', $prod_gallery);
				$new_gallery = array();
				if ( is_array($prod_gallery_array) ) {
					foreach ($prod_gallery_array as $img) {
						$new_gallery[] = $attachments[$img];
					}
				}
				$new_gallery = implode(',', $new_gallery);
				update_post_meta( $prod_id, '_product_image_gallery', $new_gallery );
				$new_galleries[] = $new_gallery;
			}
			wp_reset_postdata();
		} else {
			update_option( 'tm_new_galleries', 'no_posts_finded' );
		}
	}
}

//Check if Woocommerce is not activated on import start
add_action( 'check_shop_activation', 'check_woo' );
function check_woo() {
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		echo "<div class='note'>" . theme_locals('woocommerce_attention') . "</div>";
	}
}
?>