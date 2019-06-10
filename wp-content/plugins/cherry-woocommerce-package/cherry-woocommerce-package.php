<?php
/*
  Plugin Name: Cherry WooCommerce Package
  Version: 1.2.0
  Plugin URI: http://www.cherryframework.com/
  Description: Extend shop functionality for Cherry themes
  Author: Cherry Team.
  Author URI: http://www.cherryframework.com/
  Text Domain: cherry-woocommerce-package
  Domain Path: languages/
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Main class
 */
class cherry_woocommerce {

	/**
	 * current plugin version
	 *
	 * @var string
	 */
	public $version = '1.2.0';

	/**
	 * min Cherry framework version compatible with plugin current version
	 *
	 * @var string
	 */
	public $compatible_cherry_version = '3.1.5';

	function __construct() {

		if ( !$this->has_woocommerce() ) {
			return;
		}

		// Internationalize the text strings used.
		add_action( 'plugins_loaded', array( $this, 'lang' ), 2 );

		$this->include_files();

		add_action( 'wp_enqueue_scripts', array( $this, 'include_assets' ) );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since 1.2.0
	 */
	public function lang() {
		load_plugin_textdomain(
			'cherry-woocommerce-package',
			false, dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Enqueue scripts and styles
	 * @since  1.0.0
	 * @since  1.1.0 - added JS global variables via wp_localize_script
	 *
	 * @return void
	 */
	public function include_assets() {
		wp_enqueue_style(
			'cherry_woocommerce_style',
			$this->url( 'assets/css/style.css' ), '', $this->version, 'all'
		);

		if ( is_singular( 'product' ) ) {
			wp_enqueue_script(
				'cherry_woocommerce_cycle',
				$this->url( 'assets/js/jquery.cycle2.min.js' ), array( 'jquery' ), $this->version, true
			);
			wp_enqueue_script(
				'cherry_elevatezoom',
				$this->url( 'assets/js/jquery.elevatezoom.min.js' ), array( 'jquery' ), $this->version, true
			);
			wp_enqueue_script(
				'cherry_woocommerce_carousel',
				$this->url( 'assets/js/jquery.cycle2.carousel.min.js' ), array( 'jquery' ), $this->version, true
			);
		}

		wp_enqueue_script(
			'cherry_woocommerce_script',
			$this->url( 'assets/js/script.js' ), array( 'jquery' ), $this->version, true
		);

		$data = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'cherry_wc_data' ),
			'loading'  => __( 'Loading...', 'cherry-woocommerce-package' )
		);
		wp_localize_script( 'cherry_woocommerce_script', 'cherry_wc_data', $data );
	}

	/**
	 * include necessary files
	 * @since 1.0.0
	 * @since 1.1.0 - added quick view module
	 *
	 * @return void
	 */
	private function include_files() {
		require( 'includes/cherry-wc-functions-core.php' );
		require( 'includes/cherry-wc-functions-templates.php' );
		require( 'includes/class-cherry-wc-register-shortcodes.php' );
		require( 'includes/class-cherry-wc-menu-badges.php' );
		require( 'includes/class-cherry-wc-account-dropdown.php' );
		require( 'includes/class-cherry-wc-product-video-tab.php' );
		require( 'includes/class-cherry-wc-quick-view.php' );
	}

	/**
	 * get file URL inside plugin
	 * @since 1.0.0
	 *
	 * @param  string $path path to file inside plugin
	 * @return string       file URL
	 */
	public function url( $path = null ) {
		$base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
		if ( !$path ) {
			return $base_url;
		} else {
			return esc_url( $base_url . '/' . $path );
		}
	}

	/**
	 * get file dir inside plugin
	 * @since 1.0.0
	 *
	 * @param  string $path path to file inside plugin
	 * @return string       file dir
	 */
	public function dir( $path = null ) {
		$base_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
		if ( !$path ) {
			return $base_dir;
		} else {
			return $base_dir . '/' . $path;
		}
	}

	/**
	 * Check if WooCommerce plugin is active
	 * @since 1.0.0
	 *
	 * @return boolean true if WooCommerce active
	 */
	public function has_woocommerce() {
		return  in_array(
			'woocommerce/woocommerce.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		);
	}

}

global $cherry_woocommerce;
$cherry_woocommerce = new cherry_woocommerce();