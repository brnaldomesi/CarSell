<?php
/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */
if(!function_exists('optionsframework_options')) {
	function optionsframework_options() {
// Fonts

			// Get menus
			$menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

			$options_menus = array(
				'0' => __( 'Select menu', CURRENT_THEME )
			);

			foreach ($menus as $menu) {
				$options_menus[$menu->term_id] = $menu->name;
			}

			global $typography_mixed_fonts;
			$typography_mixed_fonts = array_merge(options_typography_get_os_fonts() , options_typography_get_google_fonts());
			asort($typography_mixed_fonts);

			$per_page_options = array( '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '10' => 10, '11' => 11, '12' => 12, '13' => 13, '14' => 14, '15' => 15, '16' => 16, '17' => 17, '18' => 18, '19' => 19, '20' => 20);
			$yes_no_options = array(
				'yes' => __( 'Yes', CURRENT_THEME ),
				'no' => __( 'No', CURRENT_THEME )
			);

			$options = array();
// ---------------------------------------------------------
// General
// ---------------------------------------------------------
			$options['general'] = array( "name" => theme_locals('general'),
								"type" => "heading");
			// Background Defaults
			$background_defaults = array(
				'color' => '', 
				'image' => '', 
				'repeat' => 'repeat',
				'position' => 'top center',
				'attachment'=>'scroll'
			);
			$options['body_background'] = array(
								"id" => "body_background",
								"std" => $background_defaults);

			$options['main_layout'] = array(
								"id" => "main_layout",
								"std" => "fullwidth");

			$options['main_background'] = array(
								"id" => "main_background",
								"std" => "#fafafa");

			$header_bg_defaults = array(
				'color' => '', 
				'image' => '', 
				'repeat' => 'repeat',
				'position' => 'top center',
				'attachment'=>'scroll'
			);
			$options['header_background'] = array( 
								"id" => "header_background",
								"std" => $header_bg_defaults);
			
			$options['links_color'] = array(
								"id" => "links_color",
								"std" => "#1c1c1c");

			$options['links_color_hover'] = array(
								"id" => "links_color_hover",
								"std" => "#fc4241");
								
			$options['google_mixed_3'] = array(
								'id' => 'google_mixed_3',
								'std' => array( 'size' => '14px', 'lineheight' => '23px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#1c1c1c'));
								
			$options['h1_heading'] = array(
								'id' => 'h1_heading',
								'std' => array( 'size' => '23px', 'lineheight' => '32px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#333333'));

			$options['h2_heading'] = array(
								'id' => 'h2_heading',
								'std' => array( 'size' => '22px', 'lineheight' => '22px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#333333'));
								
			$options['h3_heading'] = array(
								'id' => 'h3_heading',
								'std' => array( 'size' => '17px', 'lineheight' => '23px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#1c1c1c'));

			$options['h4_heading'] = array(
								'id' => 'h4_heading',
								'std' => array( 'size' => '14px', 'lineheight' => '18px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#333333'));

			$options['h5_heading'] = array(
								'id' => 'h5_heading',
								'std' => array( 'size' => '12px', 'lineheight' => '18px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#333333'));

			$options['h6_heading'] = array(
								'id' => 'h6_heading',
								'std' => array( 'size' => '12px', 'lineheight' => '18px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#333333'));

			$options['g_search_box_id'] = array(
								"id" => "g_search_box_id",
								"std" => "yes");

			$options['g_breadcrumbs_id'] = array(
								"id" => "g_breadcrumbs_id",
								"std" => "yes");

			$options['custom_css'] = array(
								"id" => "custom_css",
								"std" => "");

// ---------------------------------------------------------
// Logo & Favicon
// ---------------------------------------------------------
			$options["logo_favicon"] = array( "name" => theme_locals('logo_favicon'),
								"type" => "heading");

			$options['logo_type'] = array(
								"id" => "logo_type",
								"std" => "image_logo");

			$options['logo_typography'] = array(
								'id' => 'logo_typography',
								'std' => array( 'size' => '55px', 'lineheight' => '55px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#1c1c1c'));

			$options['logo_url'] = array(
								"id" => "logo_url",
								"std" => CHILD_URL . "/images/logo.png");
								
			$options['favicon'] = array(
								"id" => "favicon",
								"std" => CHILD_URL . "/favicon.ico");

// ---------------------------------------------------------
// Navigation
// ---------------------------------------------------------
			$options['navigation'] = array( "name" => theme_locals('navigation'),
								"type" => "heading");

			$options['menu_typography'] = array(
								'id' => 'menu_typography',
								'std' => array( 'size' => '17px', 'lineheight' => '1em', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#333333'));

			$options['sf_delay'] = array(
								"id" => "sf_delay",
								"std" => "1000");

			$options['sf_f_animation'] = array(
								"id" => "sf_f_animation",
								"std" => "show");

			$options['sf_sl_animation'] = array(
								"id" => "sf_sl_animation",
								"std" => "show");

			$options['sf_speed'] = array(
								"id" => "sf_speed",
								"std" => "normal");

			$options['sf_arrows'] = array(
								"id" => "sf_arrows",
								"std" => "true");

			$options['mobile_menu_label'] = array(
								"id" => "mobile_menu_label",
								"std" => theme_locals('mobile_menu_std'));
        $options['stickup_menu'] = array(
                "id" => "stickup_menu",
                "std" => "true",
           );

// ---------------------------------------------------------
// Slider
// ---------------------------------------------------------
			$options['slider'] = array( "name" => theme_locals('slider'),
								"type" => "heading");

	// Slider type
			$options['sl_type'] = array(
								"id" => "slider_type",
								"std" => "none_slider");
	// ---------------------------------------------------------
	// Camera Slider
	// ---------------------------------------------------------
			$options['sl_effect'] = array(
								"id" => "sl_effect",
								"std" => "simpleFade");

			$options['sl_columns'] = array(
								"id" => "sl_columns",
								"std" => "12");

			$options['sl_rows'] = array(
								"id" => "sl_rows",
								"std" => "8");

			$options['sl_banner'] = array(
								"id" => "sl_banner",
								"std" => "fadeIn");

			$options['sl_pausetime'] = array(
								"id" => "sl_pausetime",
								"std" => "7000");

			$options['sl_animation_speed'] = array(
								"id" => "sl_animation_speed",
								"std" => "1500");

			$options['sl_slideshow'] = array(
								"id" => "sl_slideshow",
								"std" => "true");

			$options['sl_thumbnails'] = array(
								"id" => "sl_thumbnails",
								"std" => "true"); // set "disabled" => "true" when only text in Slider posts

			$options['sl_control_nav'] = array(
								"id" => "sl_control_nav",
								"std" => "true");

			$options['sl_dir_nav'] = array(
								"id" => "sl_dir_nav",
								"std" => "true");

			$options['sl_dir_nav_hide'] = array(
								"id" => "sl_dir_nav_hide",
								"std" => "false");

			$options['sl_play_pause_button'] = array(
								"id" => "sl_play_pause_button",
								"std" => "true");
			$options['sl_loader'] = array(
								"id" => "sl_loader",
								"std" => "no");
   // ---------------------------------------------------------
    // Parallax Slider
	// ---------------------------------------------------------
            $options['px_slider'] = array(
								"name" => __( 'Parallax Slider', CURRENT_THEME ),
								"type" => "heading"
			);

			$options['px_slider_visibility'] = array(
								"name" => __( 'Display Parallax Slider?', CURRENT_THEME ),
								"desc"    => __( 'Display Parallax Slider?', CURRENT_THEME ),
								"id"      => "px_slider_visibility",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

			$options['px_slider_parallax_effect'] = array(
								"name" => __( 'Parallax effect', CURRENT_THEME ),
								"desc"    => __( 'Select parallax effect.', CURRENT_THEME ),
								"id"      => "px_slider_parallax_effect",
								"type"    => "select",
								"std"     => "parallax_effect_normal",
								"options" => array(
									"parallax_effect_none" => __( 'None', CURRENT_THEME ),
									"parallax_effect_low" => __( 'Low', CURRENT_THEME ),
									"parallax_effect_normal" => __( 'Normal', CURRENT_THEME ),
									"parallax_effect_high" => __( 'High', CURRENT_THEME ),
									"parallax_effect_fixed" => __( 'Fixed', CURRENT_THEME ),
								),
			);

			$options['px_slider_invert'] = array(
								"name" => __( 'Invert Parallax Slider', CURRENT_THEME ),
								"desc"    => __( 'Invert Parallax Slider', CURRENT_THEME ),
								"id"      => "px_slider_invert",
								"type"    => "radio",
								"std"     => "false",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

            $options['px_slider_effect'] = array(
								"name" => __( 'Sliding effect', CURRENT_THEME ),
								"desc"    => __( 'Select your animation type.', CURRENT_THEME ),
								"id"      => "px_slider_effect",
								"type"    => "select",
								"std"     => "simple-fade-eff",
								"options" => array(
									"simple-fade-eff" => __( 'Simple Fade', CURRENT_THEME ),
									"zoom-fade-eff" => __( 'Zoom Fade', CURRENT_THEME ),
									"slide-top-eff" => __( 'Slide Top', CURRENT_THEME ),
								),
			);

			$options['px_slider_auto'] = array(
								"name" => __( 'Slideshow', CURRENT_THEME ),
								"desc"    => __( 'Animate slider automatically?', CURRENT_THEME ),
								"id"      => "px_slider_auto",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

			$options['px_slider_pause'] = array(
								"name" => __( 'Pause time', CURRENT_THEME ),
								"desc"    => __( 'Pause time (ms).', CURRENT_THEME ),
								"id"      => "px_slider_pause",
								"type"    => "text",
								"std"     => "10000",
			);

			$options['px_slider_speed'] = array(
								"name" => __( 'Animation speed', CURRENT_THEME ),
								"desc"    => __( 'Animation speed (ms).', CURRENT_THEME ),
								"id"      => "px_slider_speed",
								"type"    => "text",
								"std"     => "1500",
			);

			$options['px_slider_scrolling_description'] = array(
								"name" => __( 'Scrolling description', CURRENT_THEME ),
								"desc"    => __( 'Scrolling description', CURRENT_THEME ),
								"id"      => "px_slider_scrolling_description",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);

			$options['px_slider_pags'] = array(
								"name" => __( 'Pagination', CURRENT_THEME ),
								"desc"    => __( 'Display pagination?', CURRENT_THEME ),
								"id"      => "px_slider_pags",
								"type"    => "radio",
								"std"     => "false",
								"options" => array(
												"buttons_pagination" => __( 'Buttons Pagination', CURRENT_THEME ),
												"images_pagination" => __( 'Images Pagination', CURRENT_THEME ),
												"none_pagination" => theme_locals("no"),
											),
			);

			$options['px_slider_navs'] = array(
								"name" => __( 'Next & Prev navigation', CURRENT_THEME ),
								"desc"    => __( 'Display next & prev navigation?', CURRENT_THEME ),
								"id"      => "px_slider_navs",
								"type"    => "radio",
								"std"     => "true",
								"options" => array(
									"true" => theme_locals("yes"),
									"false" => theme_locals("no"),
								),
			);
	// ---------------------------------------------------------
	// Accordion Slider
	// ---------------------------------------------------------
			$multicheck_defaults = array( '43' => 0,  '49' => 0, '50' => 0, '51' => 0, '52' => 0);
			$options['acc_show_post'] = array(
					"id" => "acc_show_post",
					"std" => $multicheck_defaults);

			$options['acc_slideshow'] = array(
								"id" => "acc_slideshow",
								"std" => "false");

			$options['acc_hover_pause'] = array(
								"id" => "acc_hover_pause",
								"std" => "true");

			$options['acc_pausetime'] = array(
								"id" => "acc_pausetime",
								"std" => "6000");

			$options['acc_animation_speed'] = array(
								"id" => "acc_animation_speed",
								"std" => "600");

			$options['acc_easing'] = array(
								"id" => "acc_easing",
								"std" => "easeOutCubic");

			$options['acc_trigger'] = array(
								"id" => "acc_trigger",
								"std" => "mouseover");

			$options['acc_starting_slide'] = array(
								"id" => "acc_starting_slide",
								"std" => "0");
// ---------------------------------------------------------
// Blog
// --------------------------------------------------------
			$options['blog'] = array( "name" => theme_locals('blog'),
								"type" => "heading");

			$options['blog_text'] = array(
								"id" => "blog_text",
								"std" => theme_locals('blog'));

			$options['blog_related'] = array(
								"id" => "blog_related",
								"std" => theme_locals('posts_std'));

			$options['blog_sidebar_pos'] = array(
								"id" => "blog_sidebar_pos",
								"std" => "right");

			$options['post_image_size'] = array(
								"id" => "post_image_size",
								"std" => "large");

			$options['single_image_size'] = array(
								"id" => "single_image_size",
								"std" => "large");

			$options['post_meta'] = array(
								"id" => "post_meta",
								"std" => "true");

			$options['post_excerpt'] = array(
								"id" => "post_excerpt",
								"std" => "true");

// ---------------------------------------------------------
// Portfolio
// ---------------------------------------------------------
			$options['portfolio'] = array(
								"name" => theme_locals("portfolio"),
								"type" => "heading");

			$options['folio_filter'] = array(
								"id" => "folio_filter",
								"std" => "cat");

			$options['folio_title'] = array(
								"id" => "folio_title",
								"std" => "yes");

			$options['folio_excerpt'] = array(
								"id" => "folio_excerpt",
								"std" => "yes");

			$options['folio_excerpt_count'] = array(
								"id" => "folio_excerpt_count",
								"std" => "20");

			$options['folio_btn'] = array(
								"id" => "folio_btn",
								"std" => "yes");

			$options['folio_meta'] = array(
								"id" => "folio_meta",
								"std" => "yes");

			$options['layout_mode'] = array(
								"id" => "layout_mode",
								"std" => "fitRows");

			$options['items_count2'] = array(
								"id" => "items_count2",
								"std" => "8");

			$options['items_count3'] = array(
								"id" => "items_count3",
								"std" => "9");

			$options['items_count4'] = array(
								"id" => "items_count4",
								"std" => "12");


// ---------------------------------------------------------
// Shop
// ---------------------------------------------------------		
		$options['shop'] = array( "name" => __( "Shop settings", CURRENT_THEME ),
							"id" => "shop",
							"type" => "heading");

		$options[] = array( "name" => __( "Empty cart message", CURRENT_THEME ),
					"desc" => __( "Empty cart message (for drop-down cart in header)", CURRENT_THEME ),
					"id" => "empty_cart_mess",
					"std" => "No products in the cart.",
					"class" => "tiny",
					"type" => "text");


		$options['catalog_info'] = array(
					"desc" => __( "Options for catalog page:", CURRENT_THEME ),
					"id" => "catalog_info",
					"type" => "info");

		$options['prod_per_page'] = array( "name" => __( "Products per page", CURRENT_THEME ),
					"desc" => __( "Number of producrs per catalog page", CURRENT_THEME ),
					"id" => "prod_per_page",
					"std" => "6",
					"type" => "select",
					"options" =>$per_page_options
				);

		$options['cat_title_length_limit'] = array( "name" => __( "Product title length limit", CURRENT_THEME ),
					"desc" => __( "Enter max number of words, which will shown in product title on catalog page (leave empty for displaying full title)", CURRENT_THEME ),
					"id" => "cat_title_length_limit",
					"std" => "",
					"class" => "tiny",
					"type" => "text");

		$options['cat_show_desc'] = array( "name" => __( "Product description", CURRENT_THEME ),
					"desc" => __( "Show/hide product description on catalog page", CURRENT_THEME ),
					"id" => "cat_show_desc",
					"std" => "yes",
					"type" => "radio",
					"options" =>$yes_no_options
				);

		$options['cat_show_details'] = array( "name" => __( "Show details button on catalog page", CURRENT_THEME ),
					"desc" => __( "Show/hide product details button on catalog page", CURRENT_THEME ),
					"id" => "cat_show_details",
					"std" => "no",
					"type" => "radio",
					"options" => $yes_no_options
				);

		$options['product_page_info'] = array(
					"desc" => __( "Options for product page:", CURRENT_THEME ),
					"id" => "product_page_info",
					"type" => "info");

		$options['video_tab_label'] = array( "name" => __( "Product Video tab title", CURRENT_THEME ),
					"desc" => __( "Enter Video tab title for product page", CURRENT_THEME ),
					"id" => "video_tab_label",
					"std" => "Video",
					"class" => "tiny",
					"type" => "text");

		$options['acc_dropdown'] = array(
							"desc" => __( "Options for account dropdown in header:", CURRENT_THEME ),
							"id" => "authentication_info",
							"type" => "info");

		$options['show_account'] = array( "name" => __( "Show account dropdown in header", CURRENT_THEME ),
					"desc" => __( "Show/hide product details button on catalog page", CURRENT_THEME ),
					"id" => "show_account",
					"std" => "show",
					"type" => "radio",
					"options" => array(
						"show" => "Show",
						"hide"	=> "Hide"
					)
				);

		$options['logged_in_label'] = array( "name" => __( "Account dropdown label (for logged in users)", CURRENT_THEME ),
					"desc" => __( "Enter account dropdown label for logged in users", CURRENT_THEME ),
					"id" => "logged_in_label",
					"std" => "My Account",
					"class" => "tiny",
					"type" => "text");

		$options['not_logged_in_label'] = array( "name" => __( "Account dropdown label (for guests)", CURRENT_THEME ),
					"desc" => __( "Enter account dropdown label for guests", CURRENT_THEME ),
					"id" => "not_logged_in_label",
					"std" => "My Account",
					"class" => "tiny",
					"type" => "text");

		$options['account_list_menu'] = array( "name" => __( "Account dropdown menu", CURRENT_THEME ),
					"desc" => __( "Select menu to show in account dropdown", CURRENT_THEME ),
					"id" => "account_list_menu",
					"std" => "",
					"type" => "select",
					"options" => $options_menus
					);

		$options['show_login_register'] = array( "name" => __( "Show login/logout link in account dropdown", CURRENT_THEME ),
					"desc" => __( "Show or hide login/logout links in account dropdown", CURRENT_THEME ),
					"id" => "show_login_register",
					"std" => "show",
					"type" => "radio",
					"options" => array(
						"show" => "Show",
						"hide"	=> "Hide"));
									
		$options['login_label'] = array( "name" => __( "Login label", CURRENT_THEME ),
					"desc" => __( "Please input label for login link in account dropdown", CURRENT_THEME ),
					"id" => "login_label",
					"std" => "Log In/Register",
					"class" => "tiny",
					"type" => "text");
					
		$options['logout_label'] = array( "name" => __( "Logout label.", CURRENT_THEME ),
					"desc" => __( "Please input label for logout link in account dropdown.", CURRENT_THEME ),
					"id" => "logout_label",
					"std" => "Logout",
					"class" => "tiny",
					"type" => "text");			


								
//End shop
// ---------------------------------------------------------
// Footer
// ---------------------------------------------------------
			$options['footer'] = array( "name" => theme_locals("footer"),
								"type" => "heading");

			$options['footer_text'] = array(
								"id" => "footer_text",
								"std" => "");

			$options['ga_code'] = array(
								"id" => "ga_code",
								"std" => "");

			$options['feed_url'] = array(
								"id" => "feed_url",
								"std" => "");

			$options['footer_menu'] = array(
								"id" => "footer_menu",
								"std" => "true");

			$options['footer_menu_typography'] = array(
								'id' => 'footer_menu_typography',
								'std' => array( 'size' => '12px', 'lineheight' => '18px', 'face' => 'Raleway', 'style' => 'normal', 'character'  => 'latin', 'color' => '#0088CC'));

			$options['foo'] = array( "name" => "foo",
								"type" => "heading");
		return $options;
	}
}
?>
