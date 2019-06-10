<?php
/**
 * Include core functions
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
 * Get theme option by name
 * @since 1.0.0
 *
 * @param  string  $name    option name
 * @param  mixed   $default default option value
 * @return mixed            option value
 */
function cherry_wc_get_option( $name, $default = false ) {

	if ( function_exists( 'of_get_option' ) ) {
		return of_get_option( $name, $default );
	}

	$config = get_option( 'optionsframework' );

	if ( ! isset( $config['id'] ) ) {
		return $default;
	}

	$options = get_option( $config['id'] );

	if ( isset( $options[$name] ) ) {
		return $options[$name];
	}

	return $default;
}

/**
 * Get template part (for templates like the shop-loop).
 * @since 1.0.0
 * @since 1.0.1 added @param $from_wc - if true template will be searched in WooCommerce templates before other
 *
 * @param  string  $name  template name
 * @return void
 */
function cherry_wc_get_template_part( $name, $from_wc = false ) {

	if ( !$name ) {
		return false;
	}

	global $cherry_woocommerce;

	$template = '';

	// Look in yourtheme/name.php and yourtheme/woocommerce/name.php
	$template = locate_template( array( "{$name}.php", "/woocommerce/{$name}.php" ) );

	// look in woocommerce templates if needed
	if ( $from_wc && ! $template ) {
		$template = WC()->plugin_path() . "/templates/{$name}.php";
	}

	// Get template file from plugin templates
	if ( ! $template ) {
		$template = $cherry_woocommerce->dir( 'templates' ) . "/{$name}.php";
	}
	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'cherry_woocommerce_get_template_part', $template, $name );

	if ( $template && file_exists( $template ) ) {
		load_template( $template, false );
	}
}

/**
 * Temporary remove srcset attribute from single shop image.
 *
 * @since  1.0.0
 * @param  array  $atts       default attributes array.
 * @param  object $attachment image attachment post object.
 * @param  array  $size       image size array.
 * @return array
 */
function cherry_wc_remove_srcset( $atts, $attachment, $size ) {

	if ( ! isset( $atts['srcset'] )
		 || ! isset( $atts['data-is-shop-single'] )
		 || true !== $atts['data-is-shop-single'] ) {
		return $atts;
	}

	unset( $atts['srcset'] );

	return $atts;
}
add_filter( 'wp_get_attachment_image_attributes', 'cherry_wc_remove_srcset', 10 ,3 );
