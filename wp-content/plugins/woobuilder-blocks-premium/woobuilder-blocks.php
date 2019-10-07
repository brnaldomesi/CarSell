<?php

/*
 * Plugin Name: WooBuilder blocks
 * Plugin URI: http://pootlepress.com/
 * Description: Bring the power of WordPress' blocks builder to products for fully customizable product layouts.
 * Author: PootlePress
 * Version: 2.8.0
 * Author URI: http://pootlepress.com/
 * @developer shramee <shramee.srivastav@gmail.com>
 */
/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
if ( file_exists( __DIR__ . '/inc/class-pro.php' ) ) {
    /** Plugin Pro class */
    require 'inc/class-pro.php';
}
/**
 * WooBuilder blocks main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class WooBuilder_Blocks
{
    /** @var WooBuilder_Blocks Instance */
    private static  $_instance = null ;
    /** @var string Token */
    public static  $token ;
    /** @var string Version */
    public static  $version ;
    /** @var string Plugin main __FILE__ */
    public static  $file ;
    /** @var string Plugin directory url */
    public static  $url ;
    /** @var string Plugin directory path */
    public static  $path ;
    /** @var WooBuilder_Blocks_Admin Instance */
    public  $admin ;
    /** @var WooBuilder_Blocks_Public Instance */
    public  $public ;
    private  $templates = array() ;
    public static function templates( $reload = false )
    {
        if ( $reload || !self::instance()->templates ) {
            self::instance()->templates = apply_filters( 'woobuilder_templates', [] );
        }
        return self::instance()->templates;
    }
    
    public static function template( $id )
    {
        $tpls = self::templates();
        if ( isset( $tpls[$id] ) ) {
            return $tpls[$id];
        }
        return [];
    }
    
    public static function blocks()
    {
        return [
            'sale_counter',
            'related_products',
            'add_to_cart',
            'product_price',
            'tabs',
            'excerpt',
            'meta',
            'title',
            'rating',
            'reviews',
            'images',
            'images_carousel'
        ];
    }
    
    /**
     * Checks if WooBuilder is enabled on product.
     * @param int $product_id
     * @return string|int Template id or enabled
     */
    public static function template_id( $product_id = 0 )
    {
        if ( !$product_id ) {
            $product_id = get_the_ID();
        }
        return get_post_meta( $product_id, 'woobuilder', 'single' );
    }
    
    /**
     * Checks if WooBuilder is enabled on product.
     * @param int $product_id
     * @return bool Enabled
     */
    public static function enabled( $product_id = 0 )
    {
        return !!self::template_id( $product_id );
    }
    
    /**
     * Return class instance
     * @return WooBuilder_Blocks instance
     */
    public static function instance( $file = '' )
    {
        if ( null == self::$_instance ) {
            self::$_instance = new self( $file );
        }
        return self::$_instance;
    }
    
    /**
     * Constructor function.
     * @param string $file __FILE__ of the main plugin
     * @access  private
     * @since   1.0.0
     */
    private function __construct( $file )
    {
        self::$token = 'woobuilder-blocks';
        self::$file = $file;
        self::$url = plugin_dir_url( $file );
        self::$path = plugin_dir_path( $file );
        self::$version = '2.8.0';
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        
        if ( function_exists( 'caxton_fs' ) ) {
            // Caxton FS function exists and is executed
            $this->init_fs();
        } else {
            add_action( 'caxton_fs_loaded', [ $this, 'init_fs' ] );
        }
    
    }
    
    public function init()
    {
        
        if ( !class_exists( 'Caxton' ) ) {
            // Caxton not installed
            add_action( 'admin_notices', array( $this, 'caxton_required_notice' ) );
        } elseif ( !class_exists( 'WooCommerce' ) ) {
            // Caxton not installed
            add_action( 'admin_notices', array( $this, 'wc_required_notice' ) );
        } elseif ( $this->init_fs()->can_use_premium_code() || $this->init_fs()->has_secret_key() ) {
            // All clear! initiate admin and public code
            $this->_admin();
            //Initiate admin
            $this->_public();
            //Initiate public
        }
    
    }
    
    /**
     * Initiates FS SDK
     * No need to include the SDK, already done in Caxton
     * @return Freemius
     */
    public function init_fs()
    {
        global  $wb_fs ;
        
        if ( !isset( $wb_fs ) ) {
            require_once dirname( __FILE__ ) . '/inc/wp-sdk/start.php';
            try {
                $wb_fs = fs_dynamic_init( array(
                    'id'               => '3514',
                    'slug'             => 'woobuilder-blocks',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_c52effbb9158dc8c4098e44429e4a',
                    'is_premium'       => true,
                    'is_premium_only'  => true,
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => false,
                    'menu'             => array(
                    'first-path' => 'plugins.php',
                    'support'    => false,
                ),
                    'is_live'          => true,
                ) );
            } catch ( Freemius_Exception $e ) {
                error_log( 'Error ' . $e->getCode() . ': ' . $e->getMessage() );
            }
        }
        
        return $wb_fs;
    }
    
    public function caxton_required_notice()
    {
        echo  '<div class="notice is-dismissible error">
				<p>' . sprintf( __( '%s requires that you have our free plugin %s installed and activated.', 'sfp-blocks' ), '<b>WooBuilder Blocks</b>', '<a href="' . admin_url( 'plugin-install.php?s=caxton&tab=search&type=term' ) . '">Caxton</a>' ) . '</p>' . '<p><a style="background:#e25c4e;border-color:#d23c1e;text-shadow:none;box-shadow:0 1px 0 #883413;" href="' . admin_url( 'plugin-install.php?s=caxton&tab=search&type=term' ) . '" class="button-primary button-pootle">' . __( 'Install Caxton', 'sfp_blocks' ) . '</a></p>' . '</div>' ;
    }
    
    public function wc_required_notice()
    {
        echo  '<div class="notice is-dismissible error">
				<p>' . sprintf( __( '%s requires that you have our free plugin %s installed and activated.', 'sfp-blocks' ), '<b>WooBuilder Blocks</b>', '<a href="' . admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) . '">WooCommerce</a>' ) . '</p>' . '<p><a style="background:#e25c4e;border-color:#d23c1e;text-shadow:none;box-shadow:0 1px 0 #883413;" href="' . admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) . '" class="button-primary button-pootle">' . __( 'Install WooCommerce', 'sfp_blocks' ) . '</a></p>' . '</div>' ;
    }
    
    /**
     * Initiates admin class and adds admin hooks
     */
    private function _admin()
    {
        //Instantiating admin class
        $this->admin = WooBuilder_Blocks_Admin::instance();
        add_filter(
            'gutenberg_can_edit_post_type',
            [ $this->admin, 'enable_gutenberg_products' ],
            11,
            2
        );
        add_filter(
            'use_block_editor_for_post_type',
            [ $this->admin, 'enable_gutenberg_products' ],
            11,
            2
        );
        add_filter( 'block_editor_settings', array( $this->admin, 'block_editor_settings' ) );
        add_filter( 'save_post', array( $this->admin, 'save_post' ) );
        add_filter( 'dbx_post_sidebar', array( $this->admin, 'admin_footer' ) );
        add_filter( 'post_submitbox_misc_actions', array( $this->admin, 'product_meta_fields' ) );
        add_filter(
            'rest_request_after_callbacks',
            array( $this->admin, 'rest_request_after_callbacks' ),
            10,
            3
        );
        add_action( 'rest_api_init', array( $this->admin, 'rest_api_init' ) );
        add_filter( 'block_categories', array( $this->admin, 'block_categories' ) );
        add_action( 'enqueue_block_editor_assets', array( $this->admin, 'enqueue' ), 7 );
    }
    
    /**
     * Initiates public class and adds public hooks
     */
    private function _public()
    {
        //Instantiating public class
        $this->public = WooBuilder_Blocks_Public::instance();
        // Register blocks
        add_action( 'init', array( $this->public, 'setup_product_render' ) );
        add_action( 'init', array( $this->public, 'register_blocks' ) );
        add_action( 'wp_head', array( $this->public, 'maybe_setup_woobuilder_product' ) );
        add_filter( 'woocommerce_taxonomy_args_product_visibility', array( $this->public, 'enable_rest_taxonomy' ) );
        add_filter( 'woocommerce_taxonomy_args_product_cat', array( $this->public, 'enable_rest_taxonomy' ) );
        add_filter( 'woocommerce_taxonomy_args_product_tag', array( $this->public, 'enable_rest_taxonomy' ) );
        add_filter( 'woocommerce_taxonomy_args_product_shipping_class', array( $this->public, 'enable_rest_taxonomy' ) );
        //Enqueue front end JS and CSS
        add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue' ) );
    }

}
/** Intantiating main plugin class */
WooBuilder_Blocks::instance( __FILE__ );