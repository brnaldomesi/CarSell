<?php
	// Loading child theme textdomain
	load_child_theme_textdomain( CURRENT_THEME, get_stylesheet_directory() . '/languages' );

	// Remove phone styles for IOS
	add_action( 'wp_head', 'tm_remove_phone_styles' );
	function tm_remove_phone_styles() {
		echo '<meta name="format-detection" content="telephone=no" />';
	}

	// Include scripts and styles for Child Theme
	add_action( 'wp_enqueue_scripts', 'tm_enqueue_assets', 40 );
	function tm_enqueue_assets() {
		global $wp_styles;
		wp_dequeue_style( 'woocommerce-smallscreen' );
		wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/js/custom-script.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'theme_ie', get_stylesheet_directory_uri() . '/css/ie.css' );
		$wp_styles->add_data( 'theme_ie', 'conditional', 'lt IE 9' );
	}

	//Layot change
	add_filter( 'cherry_layout_content_column', 'tm_content_column' );
	add_filter( 'cherry_layout_sidebar_column', 'tm_sidebar_column' );
	function tm_content_column() {
		return "span9";
	}
	function tm_sidebar_column() {
		return "span3";
	}

	//Change Slider Parameters
	add_filter( 'cherry_slider_params', 'tm_rewrite_slider_params' );
	function tm_rewrite_slider_params( $params ) {

		$params['height'] = "'47.4%'";
		$params['minHeight'] = "'100px'";

		return $params;
	}



	// Include additional files
	include_once( 'options-management.php' );
	include_once( 'shop-functions.php' );
    
    //stickmenu
    add_filter( 'cherry_stickmenu_selector', 'cherry_change_selector' );
     function cherry_change_selector($selector) {
      $selector = 'header .logo_box';
      return $selector;
    }
    
    //newslatter form
    add_action( 'after_setup_theme', 'after_cherry_child_setup' );
      function after_cherry_child_setup() {
        $nfu_options = get_option( 'nsu_form' );
        if ( !$nfu_options ) {
         $nfu_options_array = array();
         $nfu_options_array['email_label']         = '';
         $nfu_options_array['text_before_form']    = '';
         $nfu_options_array['email_default_value'] = 'Your email address';
         $nfu_options_array['submit_button']       = 'Sign in';
         update_option( 'nsu_form', $nfu_options_array );
      }
    }
    
    
    //Change product on catalog page
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
    add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_price', 50 );
    
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
    add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 60 );
    
    
    // google_api_map
if ( !function_exists('gmap_shortcode') ) {
   function gmap_shortcode( $atts, $content = null ) {
    extract(shortcode_atts(array(
      'lat_value'  => '37.7749300'
     , 'lng_value'  => '-122.4194200'
     , 'zoom_value'      => '8'
     , 'custom_class'  => ''
    ), $atts));

    $random_id       = uniqid();
    $lat_value        = floatval( $lat_value );
    $lng_value        = floatval( $lng_value );
    $zoom_value       = intval( $zoom_value );

    $output = '<div class="google-map-api '.$custom_class.'">';

    $output .= '<div id="map-canvas-'.$random_id.'" class="gmap"></div>';
    $output .= '</div>';


    $output .= '<script type="text/javascript">
        var map;
        var coordData = new google.maps.LatLng('.$lat_value.', '.$lng_value.');
        var marker;

        function initialize() {
         var mapOptions = {
          zoom: '.$zoom_value.',
          center: coordData,
          scrollwheel: false,
          panControl: false,
          zoomControl: false,
          mapTypeControl: false,
          scaleControl: false,
          streetViewControl: false,
          overviewMapControl: false,
          styles: [{"featureType":"administrative.locality","elementType":"all","stylers":[{"hue":"#2c2e33"},{"saturation":7},{"lightness":19},{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":31},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"hue":"#bbc0c4"},{"saturation":-93},{"lightness":-2},{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#e9ebed"},{"saturation":-90},{"lightness":-8},{"visibility":"simplified"}]},{"featureType":"transit","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":10},{"lightness":69},{"visibility":"on"}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#e9ebed"},{"saturation":-78},{"lightness":67},{"visibility":"simplified"}]}]
         }



           var map = new google.maps.Map(document.getElementById("map-canvas-'.$random_id.'"), mapOptions);
           map.panBy(0, 0);
           var markerIcon = { 
               url: "'.CHILD_URL.'/images/gmap_marker.png", 
               size: new google.maps.Size(47, 48), 
               origin: new google.maps.Point(0,0), 
               anchor: new google.maps.Point(39, 58) 
           };
           
           marker = new google.maps.Marker({
               map:map,
             draggable:false,
             position: coordData,
             icon: markerIcon

           });

        }
        google.maps.event.addDomListener(window, "load", initialize);
       </script>';
    return $output;
   }
   add_shortcode('gmap', 'gmap_shortcode');
  }
    
    
	// WP Pointers
	add_action('admin_enqueue_scripts', 'myHelpPointers');
	function myHelpPointers() {
	//First we define our pointers 
	$pointers = array(
	   	array(
	       'id' => 'xyz1',   // unique id for this pointer
	       'screen' => 'options-permalink', // this is the page hook we want our pointer to show on
	       'target' => '#submit', // the css selector for the pointer to be tied to, best to use ID's
	       'title' => theme_locals("submit_permalink"),
	       'content' => theme_locals("submit_permalink_desc"),
	       'position' => array( 
	                          'edge' => 'top', //top, bottom, left, right
	                          'align' => 'left', //top, bottom, left, right, middle
	                          'offset' => '0 5'
	                          )
	       ),

	    array(
	       'id' => 'xyz2',   // unique id for this pointer
	       'screen' => 'themes', // this is the page hook we want our pointer to show on
	       'target' => '#toplevel_page_options-framework', // the css selector for the pointer to be tied to, best to use ID's
	       'title' => theme_locals("import_sample_data"),
	       'content' => theme_locals("import_sample_data_desc"),
	       'position' => array( 
	                          'edge' => 'bottom', //top, bottom, left, right
	                          'align' => 'top', //top, bottom, left, right, middle
	                          'offset' => '0 -10'
	                          )
	       ),

	    array(
	       'id' => 'xyz3',   // unique id for this pointer
	       'screen' => 'toplevel_page_options-framework', // this is the page hook we want our pointer to show on
	       'target' => '#toplevel_page_options-framework', // the css selector for the pointer to be tied to, best to use ID's
	       'title' => theme_locals("import_sample_data"),
	       'content' => theme_locals("import_sample_data_desc_2"),
	       'position' => array( 
	                          'edge' => 'left', //top, bottom, left, right
	                          'align' => 'top', //top, bottom, left, right, middle
	                          'offset' => '0 18'
	                          )
	       )
	    // more as needed
	    );
		//Now we instantiate the class and pass our pointer array to the constructor 
		$myPointers = new WP_Help_Pointer($pointers); 
	};
?>
