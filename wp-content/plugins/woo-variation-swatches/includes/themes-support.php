<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	if ( ! function_exists( 'wvs_woo_layout_injector_script_override' ) ):
		function wvs_woo_layout_injector_script_override() {
			if ( function_exists( 'sb_et_woo_li_enqueue' ) ) :
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_dequeue_script( 'sb_et_woo_li_js' );
				wp_enqueue_script( 'sb_et_woo_li_js_override', woo_variation_swatches()->assets_uri( "/js/divi_woo_layout_injector{$suffix}.js" ), array( 'jquery' ), woo_variation_swatches()->version(), true );
			endif;
		}
		
		add_action( 'wp_enqueue_scripts', 'wvs_woo_layout_injector_script_override', 99999 );
	endif;
	