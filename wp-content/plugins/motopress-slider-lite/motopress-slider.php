<?php
/*
Plugin Name: MotoPress Slider Lite
Plugin URI: https://motopress.com/plugins/slider/
Description: Responsive MotoPress Slider for your WordPress theme. This plugin is all you need for creating beautiful slideshows, smooth transitions, effects and animations. Easy navigation, intuitive interface and responsive layout.
Version: 2.1.0
Author: MotoPress
Author URI: https://motopress.com/
Text Domain: motopress-slider-lite
Domain Path: /lang
License: GPLv2 or later
*/
if (!defined('ABSPATH')) exit;

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(!is_plugin_active('motopress-slider/motopress-slider.php')) {

global $wp_version;
if (version_compare($wp_version, '3.9', '<') && isset($network_plugin)) {
	$mpsl_plugin_file = $network_plugin;
} else {
	$mpsl_plugin_file = __FILE__;
}
$mpsl_plugin_dir_path = plugin_dir_path($mpsl_plugin_file);

require_once $mpsl_plugin_dir_path . 'includes/php-core-functions.php';
require_once $mpsl_plugin_dir_path . 'includes/functions.php';
require_once $mpsl_plugin_dir_path . 'settings/settings.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/Sharing.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/MPSLDB.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SliderAPI.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/MPSLLayout.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/MPSLOptions.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/OptionsFactory.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SliderOptions.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SlideOptions.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/List.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SlidersList.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SlidesList.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/YoutubeDataApi.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/VimeoOEmbedApi.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SliderPreview.php';
require_once $mpsl_plugin_dir_path . 'includes/classes/SliderWidget.php';

    if (is_admin()) {
    require_once $mpsl_plugin_dir_path . 'includes/classes/AdminSharing.php';
    
    require_once $mpsl_plugin_dir_path . 'includes/classes/PluginOptions.php';
    
}

class MPSLAdmin {
    const SLIDERS_TABLE = 'mpsl_sliders';
    const SLIDES_TABLE = 'mpsl_slides';
    const SLIDES_PREVIEW_TABLE = 'mpsl_slides_preview';
    const SLIDERS_PREVIEW_TABLE = 'mpsl_sliders_preview';
	/** @var MPSLSliderOptions | MPSLSlideOptions | MPSLSliderPreview | MPSLSlidersList | MPSLSlidesList */
    public $pageController;

    private $mpsl_settings;
//    private $isPluginPage;
    public $isPluginPage;
    private $view;
    private $menuHook;
	
    private $pluginDir;

    public function __construct() {
        global $mpsl_settings;

        $this->pluginDir = $mpsl_settings['plugin_dir_path'];
        $this->mpsl_settings = &$mpsl_settings;

	    $this->isPluginPage = isset($_REQUEST['page']) && $_REQUEST['page'] === $this->mpsl_settings['plugin_name'];
		$this->view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'sliders';

        
        $this->initPluginOptionsController();
        $this->addActions();
//        $this->initPageController();
    }

    private function initPageController(){
        if ($this->isPluginPage) {
            switch($this->view) {
                case 'slider' :
//                    require_once $this->pluginDir . 'includes/classes/SliderOptions.php';
                    $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
                    if (!is_null($id) && !MPSLSliderOptions::isIdExists($id)) {
                        wp_die(sprintf(__('Slider with id %s is not exists!', 'motopress-slider-lite'), $id));
                    }
                    $slider = new MPSLSliderOptions($id);
                    $this->pageController = $slider;
                    break;
                case 'slide' :
                    $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
                    if (!is_null($id) && !MPSliderDB::getInstance()->isSlideExists($id)) {
                        wp_die(sprintf(__('Slide with id %s is not exists!', 'motopress-slider-lite'), $id));
                    }
//                    $slider = new MPSLSlideOptions($id);
                    $slider = MPSLSlideOptions::getInstance($id);
                    $this->pageController = $slider;
                    break;
                case 'slides' :
                    $id = isset($_GET['id']) ? $_GET['id'] : null;
                    $slides = new MPSLSlidesList($id);
                    $this->pageController = $slides;
                    break;
                case 'preview' :
					$type = isset($_GET['type']) ? $_GET['type'] : null;
					$sliderId = isset($_GET['slider_id']) ? $_GET['slider_id'] : null;
                    $slideId = isset($_GET['slide_id']) ? $_GET['slide_id'] : null;
					if ($type === 'slide') MPSLSharing::$isPreviewPage = true;

                    $slider = new MPSLSliderPreview($type, $sliderId, $slideId);
                    $this->pageController = $slider;
                    break;
                case 'export' :
//                    add_action('admin_init', array($this, 'exportSliders'));
                    $this->exportSliders();
                break;
	            case 'sliders': default:
                    $sliders = new MPSLSlidersList();
                    $this->pageController = $sliders;
                    break;
            }
        }
    }

	

    private function initPluginOptionsController(){
        $this->pluginOptionsController = new MPSLPluginOptions();
    }

    public static function install($network_wide){
        global $wpdb;


	    

        if (is_multisite() && $network_wide) {
            // store the current blog id
            $current_blog = $wpdb->blogid;

            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blog_ids as $blog_id ) {
                switch_to_blog( $blog_id );
                self::createTables();
                restore_current_blog();
            }
        } else {
            self::createTables();
        }
    }

    public static function onCreateBlog($blog_id, $user_id, $domain, $path, $site_id, $meta){
        if ( is_plugin_active_for_network( 'motopress-slider/motopress-slider.php' ) ) {
            switch_to_blog( $blog_id );
            self::createTables();
            restore_current_blog();
        }
    }

    public static function onDeleteBlog($tables){
        global $wpdb;
        $tables[] = $wpdb->prefix . self::SLIDERS_TABLE;
        $tables[] = $wpdb->prefix . self::SLIDES_TABLE;
        return $tables;
    }

    public static function createTables() {
        global $wpdb;

	    $charsetCollate = '';
	    if (!empty($wpdb->charset)) $charsetCollate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	    if (!empty($wpdb->collate)) $charsetCollate .= " COLLATE {$wpdb->collate}";

        $slidersTableRes = $wpdb->query(sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                id int(9) NOT NULL AUTO_INCREMENT,
                title tinytext NOT NULL,
                alias tinytext NULL,
                options text NOT NULL,
                PRIMARY KEY (id)
            ) %s;',
            $wpdb->prefix . self::SLIDERS_TABLE,
	        $charsetCollate
        ));
        if (!$slidersTableRes) {
            //@todo show error message
//            MPSLMessages::error(printf(__('Table %1$s', 'motopress-slider-lite'), $this->mpsl_settings['sliders_table']));
        }

        $slidesTableRes = $wpdb->query(sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                id int(9) NOT NULL AUTO_INCREMENT,
                slider_id int(9) NOT NULL,
                slide_order int(11) NOT NULL,
                options text NOT NULL,
                layers text NOT NULL,
                PRIMARY KEY (id)
            ) %s;',
            $wpdb->prefix . self::SLIDES_TABLE,
	        $charsetCollate
        ));
        if (!$slidesTableRes) {
            //@todo show error message
        }

        $slidesPreviewTableRes = $wpdb->query(sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                id int(9) NOT NULL AUTO_INCREMENT,
                slider_id int(9) NOT NULL,
                slide_order int(11) NOT NULL,
                options text NOT NULL,
                layers text NOT NULL,
                PRIMARY KEY (id)
            ) %s;',
            $wpdb->prefix . self::SLIDES_PREVIEW_TABLE,
	        $charsetCollate
        ));
        if (!$slidesPreviewTableRes) {
            //@todo show error message
        }

        $slidersPreviewTableRes = $wpdb->query(sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                id int(9) NOT NULL AUTO_INCREMENT,
                title tinytext NOT NULL,
                alias tinytext NULL,
                options text NOT NULL,
                PRIMARY KEY (id)
            ) %s;',
            $wpdb->prefix . self::SLIDERS_PREVIEW_TABLE,
	        $charsetCollate
        ));

        if (!$slidersPreviewTableRes) {
            //@todo show error message
        }
    }

    public function fixAfterUpdate() {
        include_once($this->pluginDir . 'includes/fix-after-update.php');
        mpslFixAfterUpdate();
    }

    public function adminPrintStyles() {
        /*if (get_option('_mpsl_needs_update') == 1) {
            //wp_enqueue_style('mpsl-update', plugins_url($this->pluginDir . '/css/activation.css', dirname(__FILE__)));
            add_action('admin_notices', array($this, 'adminUpdateNotices'));
        }*/
        add_action('admin_notices', array($this, 'adminNotices'));
    }

    public function adminNotices() {
        if (get_option('_mpsl_needs_update') == 1) {
            include($this->pluginDir . 'includes/notices/update.php');

        } elseif (!empty($_GET['mpsl-updated'])) {
            include($this->pluginDir . 'includes/notices/updated.php');
        }

		if ($this->isPluginPage && $this->view === 'sliders') {
			include($this->pluginDir . 'includes/notices/upgrade-to-pro.php');
		}
    }

    /*public function adminUpdateNotices() {
        if (get_option('_mpsl_needs_update') == 1) {
            include($this->pluginDir . 'includes/notices/update.php');
        }
    }*/

    public function adminInit() {
        $this->initPageController();

        // Updater
        global $mpsl_settings;
        register_importer( 'mpsl-importer', $mpsl_settings['product_name'], sprintf(__( 'Import sliders and images from a %s export file.', 'motopress-slider-lite' ), $mpsl_settings['product_name']), array( $this, 'importPageRender' ) );
        if (!empty($_GET['mpsl_do_update'])) {
            include_once($this->pluginDir . 'includes/update.php');
            mpslDoUpdate();

            // Update complete
            delete_option('_mpsl_needs_update');

            wp_safe_redirect(admin_url("admin.php?page={$this->mpsl_settings['plugin_name']}&mpsl-updated=true"));
            exit;
        }
    }

    public static function addGlobalActions(){
        add_action('plugins_loaded', array('MPSLAdmin', 'loadTextdomain'));
        add_action('after_setup_theme', array('MPSLAdmin', 'setProductName'));
    }

    private function addActions(){
        global $mpsl_settings;

	    add_filter('tiny_mce_before_init', array($this, 'mpslTinyMceBeforeInit'), 10, 2);
        add_filter('after_wp_tiny_mce', array($this, 'regSliderListObj'));
        add_filter('mce_external_plugins', array($this, 'regSliderListPlugin'));
        add_filter('mce_buttons', array($this, 'regSliderListButton'));
        if ($this->isPluginPage) {
            add_filter('wp_default_editor', array($this, 'wpDefaultEditor'));
        }


        add_action('admin_menu', array($this, 'mpslMenu'), 11);
        if ($mpsl_settings['plugin_version'] && get_option('mpsl_version') != $mpsl_settings['plugin_version']) {
	        add_action('init', array($this, 'fixAfterUpdate'), 1);
        }
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_print_styles', array($this, 'adminPrintStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminStylesAndScripts'), 10);
        add_action('admin_enqueue_scripts', array($this, 'deregisterBadAdminStylesAndScripts'), 100);

        //AJAX
	    // Slider
        add_action('wp_ajax_mpsl_update_slider', array($this, 'updateSliderCallback'));
        add_action('wp_ajax_mpsl_create_slider', array($this, 'createSliderCallback'));
        add_action('wp_ajax_mpsl_delete_slider', array($this, 'deleteSliderCallback'));
        add_action('wp_ajax_mpsl_duplicate_slider', array($this, 'duplicateSliderCallback'));
        add_action('wp_ajax_mpsl_update_slide', array($this, 'updateSlideCallback'));
        add_action('wp_ajax_mpsl_create_slide', array($this, 'createSlideCallback'));
        add_action('wp_ajax_mpsl_delete_slide', array($this, 'deleteSlideCallback'));
        add_action('wp_ajax_mpsl_duplicate_slide', array($this, 'duplicateSlideCallback'));
        add_action('wp_ajax_mpsl_update_slides_order', array($this, 'updateSlidesOrderCallback'));
        add_action('wp_ajax_mpsl_check_alias_exists', array($this, 'checkAliasExistsCallback'));
        add_action('wp_ajax_mpsl_get_youtube_thumbnail', array($this, 'getYoutubeThumbnailCallback'));
        add_action('wp_ajax_mpsl_get_vimeo_thumbnail', array($this, 'getVimeoThumbnailCallback'));

        add_action('wp_ajax_mpsl_posts_preview', array($this, 'postsPreviewCallback'));
    }

    public function wpDefaultEditor($ed) {
        return 'tinymce';
    }

	public static function mpslTinyMceBeforeInit($settings, $editorId) {
		global $mpsl_settings;

		// HTML Layer content
		if ($editorId === $mpsl_settings['alt_prefix'] . 'text') {
			$settings = array_merge($settings, array(
				'wpautop' => false,
				'force_br_newlines' => true,
				'force_p_newlines' => false,
				'forced_root_block' => ''
			));
		}

		return $settings;
	}

    public function regSliderListObj(){
        $mpslLang = array(
            'title' => __('Slider', 'motopress-slider-lite')
        );

        $sliders = new MPSLSlidersList();
        printf('<script type="text/javascript" > var mpslSliderList = ' . json_encode_slashed($sliders->getSliderAliases()) . '; var mpslLang = ' . json_encode_slashed($mpslLang) .'; </script>');
    }

    public function regSliderListPlugin($plugin_array){
        global $mpsl_settings;
        $plugin_array['mpslTinymceSliderList'] = $mpsl_settings['plugin_dir_url'] . 'js/tinymce-button.js';
        $plugin_array['mpslTinymceMacrosList'] = $mpsl_settings['plugin_dir_url'] . 'js/macros-select.js';
        return $plugin_array;
    }

    public function regSliderListButton($buttons) {
        array_push($buttons, 'mpsl_slider_list_btn');
        array_push($buttons, 'mpsl_post_macros');
        return $buttons;
    }

	public function deregisterBadAdminStylesAndScripts($hook) {
		if (isset($this->menuHook) && $hook === $this->menuHook) {
			wp_deregister_style('jquery-ui');
			wp_deregister_style('wp-jquery-ui-dialog');
			wp_deregister_style('admin-interface');
		}
	}

    public static function setProductName() {
        global $mpsl_settings;
        $mpsl_settings['product_name'] = apply_filters('mpsl_product_name', 'MotoPress Slider');
    }

    public static function registerSliderWidget(){
        register_widget('MPSLWidget');
    }

    public function enqueueAdminStylesAndScripts($hook) {
        if (isset($this->menuHook) && $hook === $this->menuHook) {
            global $mpsl_settings;

	        // `wp-editor` plugin conflict
            wp_deregister_script('codemirror');
            wp_deregister_script('codemirror-css');
            wp_deregister_script('codemirror_css');
            wp_deregister_style('codemirror');
	        // End `wp-editor` plugin conflict

            wp_register_script('jquery-ui-touch', $mpsl_settings['plugin_dir_url'] . 'vendor/jqueryui/jquery-ui-touch/jquery.ui.touch-punch.min.js', array('jquery-ui-widget', 'jquery-ui-mouse'), '0.2.3');
            $page = $this->view;
            $prefix = (is_ssl()) ? 'https://' : 'http://';

            $deps = array('jquery');

            if (in_array($page, array('slider', 'slide', 'sliders'))) {
                wp_enqueue_script('jquery-ui-dialog');
            }

            if ($page === 'slides') {
                wp_enqueue_script('jquery-ui-sortable');
                if (wp_is_mobile()) {
                    wp_enqueue_script('jquery-ui-touch');
                    $deps[] = 'jquery-ui-touch';
                }
            }

            if ($page === 'slider') {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-widget');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('codemirror', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/lib/codemirror.js', array('jquery'), $mpsl_settings['codemirror_version']);
                wp_enqueue_style('codemirror', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/lib/codemirror.css', array(), $mpsl_settings['codemirror_version']);
                wp_enqueue_script('codemirror-css', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/mode/css/css.js', array('codemirror'), $mpsl_settings['codemirror_version']);
//                wp_enqueue_script('codemirror-js', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/mode/javascript/javascript.js', array('codemirror'), $mpsl_settings['codemirror_version']);
            }

            if ($page === 'slide') {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-widget');
                wp_enqueue_script('jquery-ui-mouse');
                wp_enqueue_script('jquery-ui-draggable');
                wp_enqueue_script('jquery-ui-droppable');
                wp_enqueue_script('jquery-ui-resizable');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-ui-datepicker');
	            wp_enqueue_script('codemirror', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/lib/codemirror.js', array('jquery'), $mpsl_settings['codemirror_version']);
                wp_enqueue_style('codemirror', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/lib/codemirror.css', array(), $mpsl_settings['codemirror_version']);
                wp_enqueue_script('codemirror-css', $mpsl_settings['plugin_dir_url'] . 'vendor/codemirror/mode/css/css.js', array('codemirror'), $mpsl_settings['codemirror_version']);
                wp_enqueue_style('spectrum', $mpsl_settings['plugin_dir_url'] . 'vendor/spectrum/spectrum.css', array(), $mpsl_settings['spectrum_version']);
                wp_enqueue_script('spectrum', $mpsl_settings['plugin_dir_url'] . 'vendor/spectrum/spectrum.js', array('jquery'), $mpsl_settings['spectrum_version']);
                if (wp_is_mobile()) {
                    wp_enqueue_script('jquery-ui-touch', $mpsl_settings['plugin_dir_url'] . 'vendor/jqueryui/jquery-ui-touch/jquery.ui.touch-punch.min.js', array(), $mpsl_settings['plugin_version']);
                    $deps[] = 'jquery-ui-touch';
                }
            }
            wp_enqueue_script('jquery-ui-button');
            $deps[] = 'jquery-ui-button';

            if ($page === 'slide') wp_enqueue_media();
            wp_enqueue_style('mpsl-jquery-ui-theme', $mpsl_settings['plugin_dir_url'] . 'vendor/jqueryui/ui-smoothness/jquery-ui.css', false, $mpsl_settings['plugin_version']);

            wp_enqueue_style('mpsl-admin', $mpsl_settings['plugin_dir_url'] . 'css/admin.css', array(), $mpsl_settings['plugin_version']);

            if ($page === 'slide') {
                wp_enqueue_style('mpsl-simplegrid', $mpsl_settings['plugin_dir_url'] . 'vendor/simplegrid/simplegrid.css', array(), $mpsl_settings['plugin_version']);
                wp_enqueue_style('mpsl-slide', $mpsl_settings['plugin_dir_url'] . 'css/slide.css', array(), $mpsl_settings['plugin_version']);
                $customPreloaderImageSrc = apply_filters('mpsl_preloader_src', false);
                if ($customPreloaderImageSrc) {
                    echo '<style type="text/css">.mpsl-preloader, .mpsl-global-preloader{background-image: url("' . esc_url($customPreloaderImageSrc) . '") !important;}</style>';
                }
            }
            wp_register_script('mpsl-canjs', $mpsl_settings['plugin_dir_url'] . 'vendor/canjs/can.custom.min.js', $deps, $mpsl_settings['canjs_version'], true);
            $deps[] = 'mpsl-canjs';

            wp_register_script('mpsl-functions', $mpsl_settings['plugin_dir_url'] . 'js/functions.js', $deps, $mpsl_settings['plugin_version'], true);
            $deps[] = 'mpsl-functions';

            if (in_array($page, array('slider', 'slide'))) {
                wp_enqueue_script('mpsl-controllers', $mpsl_settings['plugin_dir_url'] . "js/controls.js", $deps, $mpsl_settings['plugin_version'], true);
                $deps[] = 'mpsl-controllers';
            }

            if (file_exists($mpsl_settings['plugin_dir_path'] . "/js/$page.js")) {
                wp_register_script("mpsl-$page", $mpsl_settings['plugin_dir_url'] . "js/$page.js", $deps, $mpsl_settings['plugin_version'], true);
                wp_enqueue_script("mpsl-$page");
            }

            $jsVars = array();
            $jsVars['Vars'] = array(
                'ajax_url' => admin_url('admin-ajax.php'),
	            'page' => array(),
                'settings' => $mpsl_settings,
//                'options' => $this->pageController->getOptions(),
                'menu_url' => menu_page_url($mpsl_settings['plugin_name'], false),
                'lang' => $this->getLangStrings(),
                'nonces' => array(
                    'update_slider' => wp_create_nonce('wp_ajax_mpsl_update_slider'),
                    'create_slider' => wp_create_nonce('wp_ajax_mpsl_create_slider'),
                    'delete_slider' => wp_create_nonce('wp_ajax_mpsl_delete_slider'),
                    'duplicate_slider' => wp_create_nonce('wp_ajax_mpsl_duplicate_slider'),
                    'create_slide' => wp_create_nonce('wp_ajax_mpsl_create_slide'),
                    'update_slide' => wp_create_nonce('wp_ajax_mpsl_update_slide'),
                    'delete_slide' => wp_create_nonce('wp_ajax_mpsl_delete_slide'),
                    'duplicate_slide' => wp_create_nonce('wp_ajax_mpsl_duplicate_slide'),
                    'update_slides_order' => wp_create_nonce('wp_ajax_mpsl_update_slides_order'),
                    'check_alias_exists' => wp_create_nonce('wp_ajax_mpsl_check_alias_exists'),
                    'get_youtube_thumbnail' => wp_create_nonce('wp_ajax_mpsl_get_youtube_thumbnail'),
                    'get_vimeo_thumbnail' => wp_create_nonce('wp_ajax_mpsl_get_vimeo_thumbnail'),
                    'export_sliders' => wp_create_nonce('wp_ajax_mpsl_export_sliders'),
                    'import_sliders' => wp_create_nonce('wp_ajax_mpsl_import_sliders')
                )
            );
            if (in_array($page, array('slider', 'slide', 'slides'))) {
                $jsVars['Vars']['page'] = $this->pageController->getAttributes();
                $jsVars['Vars']['page']['type'] = $page;
            }
            if(!in_array($page, array('preview'))){
                $jsVars['Vars']['page']['grouped_options'] = $this->pageController->getOptions(true);
                $jsVars['Vars']['page']['options'] = $this->pageController->getOptions();
            }

            if ($page === 'slide') {
                $jsVars['Vars']['slider'] = $this->pageController->slider->getOptions();

	            // Get layouts
	            $layouts = $this->pageController->slider->getLayouts();
//	            $enabledLayouts = MPSLSliderOptions::filterLayoutsByEnabled($layouts);

                $jsVars['Vars']['layout'] = array(
	                'list' => $layouts,
	                'default' => MPSLLayout::DEFAULT_LAYOUT,
	                'options' => MPSLLayout::$OPTIONS,
	                'style_options' => MPSLLayout::$STYLE_OPTIONS,
                );
	            // End Get layouts

	            $jsVars['Vars']['layer'] = array(
		            'list' => $this->pageController->getLayers(),
		            'grouped_options' => $this->pageController->getLayerOptions(true),
		            'options' => $this->pageController->getLayerOptions(),
		            'layouted_defaults' => $this->pageController->getLayoutedOptionsDefaults('layer'),
		            'defaults' => $this->pageController->getOptionsDefaults('layer'),
		            'white_space_class_prefix' => MPSLSlideOptions::LAYER_WHITE_SPACE_CLASS_PREFIX
	            );
	            $jsVars['Vars']['preset'] = array(
		            'default_list' => $this->pageController->layerPresets->getDefaultPresets(),
		            'list' => $this->pageController->layerPresets->getPresets(),
		            'grouped_options' => $this->pageController->layerPresets->getOptions(true),
		            'options' => $this->pageController->layerPresets->getOptions(),
		            'defaults' => $this->pageController->layerPresets->getOptionsDefaults(),
		            'last_id' => $this->pageController->layerPresets->getLastPresetId(),
		            'last_private_id' => $this->pageController->layerPresets->getLastPrivatePresetId(),
		            'class_prefix' => MPSLLayerPresetOptions::PRESET_PREFIX,
		            'private_class_prefix' => MPSLLayerPresetOptions::PRIVATE_PRESET_PREFIX,
		            'layer_class' => MPSLLayerPresetOptions::LAYER_CLASS,
		            'layer_hover_class' => MPSLLayerPresetOptions::LAYER_HOVER_CLASS,
		            'font_list' => MPSLLayerPresetOptions::getFontList(),
		            'default_font_weight_list' => MPSLLayerPresetOptions::getDefaultFontWeightList()
	            );
            }

            //wp_localize_script("mpsl-$page", 'MPSL', $jsVars);
            wp_localize_script("jquery", 'MPSL', $jsVars);
        }
    }

    private function getLangStrings(){
        global $mpsl_settings;
        return array(
            'test' => __('test', 'motopress-slider-lite'),
            'emptyInputError' => __('%s require non empty value.', 'motopress-slider-lite'),
            'ajax_result_not_found' => __('In the AJAX response undisclosed result field.', 'motopress-slider-lite'),
            'validate_digitals_only' => __('%s must content digitals only.', 'motopress-slider-lite'),
            'validate_less_min' => __('%s could not be less then %d', 'motopress-slider-lite'),
            'validate_greater_max' => __('%s could not be greater then %d', 'motopress-slider-lite'),
            'aliasNotValidPattern' => __('Alias not valid. Alias could contents latin symbols, numbers, underscore and hyphen only.', 'motopress-slider-lite'),
            'aliasAlreadyExists' => __('This alias already exists. Alias must be unique.', 'motopress-slider-lite'),
            'validate_invalid_date_format' => __('"%s" invalid date format. Use datepicker.', 'motopress-slider-lite'),
            'validate_invalid_day' => __('"%s" invalid value for day: %day.', 'motopress-slider-lite'),
            'validate_invalid_month' => __('"%s" invalid value for month: %month.', 'motopress-slider-lite'),
            'validate_invalid_year' => __('"%s" Invalid value for year: %year - must be between %minYear and %maxYear.', 'motopress-slider-lite'),
            'validate_invalid_hour' => __('"%s" invalid value for hour: %hour.', 'motopress-slider-lite'),
            'validate_invalid_minute' => __('"%s" invalid value for minute: %minute.', 'motopress-slider-lite'),
            'delete' => __('Delete', 'motopress-slider-lite'),
            'cancel' => __('Cancel', 'motopress-slider-lite'),
            'choose' => __('Choose', 'motopress-slider-lite'),
            'none' => __('None', 'motopress-slider-lite'),

            'slider_updated' => __('Slider updated.', 'motopress-slider-lite'),
            'slider_update_error' =>  __('Slider update error:', 'motopress-slider-lite'),
            'slider_created' => __('Slider is created', 'motopress-slider-lite'),
            'slider_deleted' => __('Slider is deleted.', 'motopress-slider-lite'),
            'slider_deleted_id' => __('Slider %d deleted.', 'motopress-slider-lite'),
            'slider_duplicated' => __('Slider duplicated.', 'motopress-slider-lite'),
            'slider_want_delete_single' => __('Do you really want to delete \'%d\' ?', 'motopress-slider-lite'),

            'slide_created' => __('Slide created.', 'motopress-slider-lite'),
            'slide_created_error' => __('Slide is not created.', 'motopress-slider-lite'),
            'slide_updated' => __('Slide updated.', 'motopress-slider-lite'),
            'slide_update_error' => __('Slide update error: ', 'motopress-slider-lite'),
            'slide_deleted' => __('Slide deleted.', 'motopress-slider-lite'),
            'slide_duplicated' => __('Slide duplicated.', 'motopress-slider-lite'),
            'slide_want_delete_single' => __('Do you really want to delete \'%d\' ?', 'motopress-slider-lite'),

            'slides_sorted' => __('Slides sorted', 'motopress-slider-lite'),
            'slides_sorted_error' => __('Slides error when sorting', 'motopress-slider-lite'),

            'layer_want_delete_all' => __('Do you really want to delete all the layers?', 'motopress-slider-lite'),
            'import_export_dialog_title' => sprintf(__('%s Import and Export', 'motopress-slider-lite'), $mpsl_settings['product_name']),
            'template_dialog_title' => __('Create New Slider', 'motopress-slider-lite'),
            'preview_dialog_title' => __('Preview Slider', 'motopress-slider-lite'),
            'no_sliders_selected_to_export' => __('No sliders selected to export.', 'motopress-slider-lite'),
            'style_editor_dialog_title' => __('Style Editor', 'motopress-slider-lite'),
            'style_editor_dialog_presets_title' => __('Presets', 'motopress-slider-lite'),

            'animation-modal' => __('Transition Editor', 'motopress-slider-lite'),

            'layer_preset_delete' => __('Do you really want to delete preset "%s"?', 'motopress-slider-lite'),
            'layer_preset_rename' => __('Rename preset', 'motopress-slider-lite'),
            'layer_preset_enter_name' => __('Please enter name for new preset', 'motopress-slider-lite'),
            'layer_preset_not_selected' => __('No preset selected', 'motopress-slider-lite'),
            'layer_preset_private_name' => __('Element Style', 'motopress-slider-lite'),
            'layer_preset_default_name' => __('New preset', 'motopress-slider-lite'),

	        'macros' => array(
		        'general' => array(
			        'post_content' => 'Post Content',
			        'woo_content' => 'WooCommerce Content',
		        ),
		        'post' => array(
			        'title' => __('The post title', 'motopress-slider-lite'),
					'content' => __('The post content', 'motopress-slider-lite'),
					'excerpt' => __('The post excerpt', 'motopress-slider-lite'),
					'categories' => __('The post categories', 'motopress-slider-lite'),
					'tags' => __('The post tags', 'motopress-slider-lite'),
					'link' => __('The post link', 'motopress-slider-lite'),
					'author_name' => __('The author name', 'motopress-slider-lite'),
					'unique_id' => __('The unique ID of the post', 'motopress-slider-lite'),
					'image' => __('Post image', 'motopress-slider-lite'),
					'image_source' => __('Post image source', 'motopress-slider-lite'),
					'year' => __('The year of the post', 'motopress-slider-lite'),
					'numeric_month' => __('Numeric Month', 'motopress-slider-lite'),
					'month_name' => __('Month name', 'motopress-slider-lite'),
					'day_of_month' => __('Day of the month', 'motopress-slider-lite'),
					'weekday_name' => __('Weekday name', 'motopress-slider-lite'),
					'hour_minutes' => __('Hour:Minutes', 'motopress-slider-lite'),
					'publish_date' => __('The publish date', 'motopress-slider-lite'),
					'last_modified_date' => __('The last modified date', 'motopress-slider-lite'),
					'number_of_comments' => __('Number of comments', 'motopress-slider-lite')
		        ),
		        'woo' => array(
			        'add_to_cart' => __('Add To Cart', 'motopress-slider-lite'),
					'price' => __('Price', 'motopress-slider-lite'),
					'currency' => __('Currency', 'motopress-slider-lite'),
					'currency_price' => __('Currency + Price', 'motopress-slider-lite'),
					'regular_price' => __('Regular Price', 'motopress-slider-lite'),
					'sale_price' => __('Sale Price', 'motopress-slider-lite'),
					'in_stock_status' => __('In Stock Status', 'motopress-slider-lite'),
			        'in_stock_quantity' => __('In Stock Quantity', 'motopress-slider-lite'),
					'weight' => __('Weight', 'motopress-slider-lite'),
					'product_categories' => __('Product Categories', 'motopress-slider-lite'),
					'product_tags' => __('Product Tags', 'motopress-slider-lite'),
					'total_sales' => __('Total Sales', 'motopress-slider-lite'),
					'average_rating' => __('Average Rating', 'motopress-slider-lite'),
					'rating_count' => __('Rating Count', 'motopress-slider-lite')
		        )
	        )
        );
    }

    public static function loadTextdomain(){
        global $mpsl_settings;
        load_plugin_textdomain('mpsl', FALSE, $mpsl_settings['plugin_symlink_dir_name'] . '/lang/');
    }

    public function mpslMenu() {
        global $mpsl_settings;
        $isHideMenu = apply_filters('mpsl_hide_menu', false);
        if (!isMPSLDisabledForCurRole() && !$isHideMenu) {
			$menu_icon = (version_compare( $GLOBALS['wp_version'], '3.8', '<' )) ? '' : 'dashicons-slides';
            $this->menuHook = add_menu_page($mpsl_settings['product_name'], $mpsl_settings['product_name'], 'read', $this->mpsl_settings['plugin_name'], array($this, 'renderPage'), $menu_icon);
            $isHideOptionsMenu = apply_filters('mpsl_hide_options_page', false);
            if (!$isHideOptionsMenu) {
                $this->pluginOptionsController->addMenu();
            }
	        
        }
    }

    public function renderPage(){
        echo '<div class="mpsl-wrapper wrap">';
	    echo '<div class="mpsl-global-preloader"></div>';
        $this->pageController->render();
        echo '<div id="mpsl-info-box"></div>';
        echo '</div>';
    }

    public function importPageRender(){
        require_once $this->pluginDir . 'includes/classes/SliderImporter.php';
        $importer = new MPSLSliderImporter();
        $importer->renderImportPage();
    }

    public function autoImport($path, $isVerbose = false){
        require_once $this->pluginDir . 'includes/classes/SliderImporter.php';
        $importer = new MPSLSliderImporter();
        return $importer->importFromFile($path, $isVerbose);
    }

    // AJAX Callbacks

    public function postsPreviewCallback() {
        $options = isset($_POST['options']) ? json_decode(stripslashes($_POST['options']), true) : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;
        if ($options) {
            $db = MPSliderDB::getInstance();
	        $_posts = array();
	        $charLength = (!empty($options['post_excerpt_length'])) ? $options['post_excerpt_length'] : 0;

	        MPSLSharing::disableShortcodeRendering();

            $posts = $db->getPostsByOptions($options, $type);
	        if ($posts->have_posts()) {
		        while ($posts->have_posts()) {
			        $posts->the_post();
			        $_posts[] = array(
				        'ID' => get_the_ID(),
				        'title' => get_the_title(),
				        'url' => get_permalink(),
				        'image' => $db->getPostImageThumbnail($posts->post),
				        'excerpt' => $db->getExcerpt($posts->post, $charLength),
				        'date' => $db->getFormatDate('F j, Y', $posts->post)
			        );
		        }
	        }
	        wp_reset_postdata();

	        MPSLSharing::enableShortcodeRendering();

            wp_send_json(array('result' => true, 'posts' => $_posts));
        }

	    wp_send_json(array('result' => false));
    }

    public function updateSliderCallback() {
        mpslVerifyNonce();
        // Prepare data
        $preview = (isset($_POST['preview']) && $_POST['preview'] == 'true') ? true : false;
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        if (isset($_POST['options'])) {
            $options = stripslashes($_POST['options']);
            $options = json_decode($options, true);
        } else {
            $options = array();
        }
        $title = isset($options['main']['title']) ? $options['main']['title'] : null;
        $alias = isset($options['main']['alias']) ? $options['main']['alias'] : null;

        // TODO: Flash messages
        if (is_null($title)) return false;
        if (is_null($alias)) return false;
        if (!count($options)) return false;

        if (isset($_POST['id'])) {
            require_once $this->pluginDir . 'includes/classes/SliderOptions.php';
            $slider = new MPSLSliderOptions($id, $preview, false, true);
            $slider->setTitle($title);
            $oldAlias = $slider->getAlias();
            if ($preview || ($oldAlias === $alias) || !$slider->isAliasExists($alias)) {
                $slider->setAlias($alias);
                $slider->overrideOptions($options, true);
                $updated = $slider->update();
                if (false !== $updated) {
                    wp_send_json(array('result' => true, 'id' => $slider->getId()));
                } else {
                    global $wpdb;
                    mpslSetError(__('Slider is not updated. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
                }
            } else {
                mpslSetError(__('This alias already exists. Alias must be unique.', 'motopress-slider-lite'));
            }
        } else {
            mpslSetError(__('Id is not set.', 'motopress-slider-lite'));
        }
	}

    public function createSliderCallback(){
        mpslVerifyNonce();
        if (isset($_POST['options'])) {
            $options = stripslashes($_POST['options']);
            $options = json_decode($options, true);
        } else {
            $options = array();
        }
        require_once $this->pluginDir . 'includes/classes/SliderOptions.php';
//        $slider = new MPSLSliderOptions();
        $slider = new MPSLSliderOptions(array(
	        'grouped' => true,
	        'options' => $options
        ));
//        $slider->overrideOptions($options, true);
        if (!$slider->isAliasExists($slider->getAlias())) {
            if(!$slider->isNotValidOptions()){
                $id = $slider->create();
                if ($id) {
                    wp_send_json(array('result' => true, 'id' => $slider->getId(), 'template_id' => $slider->getTemplateId()));
                } else {
                    global $wpdb;
                    mpslSetError(__('Slider is not updated. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
                }
            } else {
                mpslSetError(__('Slider parameters are not valid.', 'motopress-slider-lite'));
            }
        } else {
            mpslSetError(__('This alias already exists. Alias must be unique.', 'motopress-slider-lite'));
        }

    }

    public function deleteSliderCallback(){
        mpslVerifyNonce();
        if (isset($_POST['id'])) {
            require_once $this->pluginDir . 'includes/classes/SliderOptions.php';
            $slider = new MPSLSliderOptions((int) $_POST['id']);
            $error = null;
            $result = $slider->delete();
            if (false !== $result) {
                wp_send_json(array('result' => true));
            } else {
                global $wpdb;
                mpslSetError(__('Slider is not deleted. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
            }
        } else {
            mpslSetError(__('Slider is not deleted. ID is not set.', 'motopress-slider-lite'));
        }
    }

    public function duplicateSliderCallback(){
        mpslVerifyNonce();
        if (isset($_POST['id'])) {
	        $layerPresetsObj = MPSLLayerPresetOptions::getInstance();
            $slider = new MPSLSliderOptions((int) $_POST['id']);
            $error = null;
            $slideRes = $slider->duplicate();

            if (false !== $slideRes['slide']) {
	            $id = $slideRes['slide_id'];
	            $presetResult = $layerPresetsObj->update();
		        $layerPresetsObj->updatePrivateStyles($id);

                $slidersList = new MPSLSlidersList();
                $html = $slidersList->getRowHtml($id);

                wp_send_json(array('result' => true, 'id' => $id, 'html' => $html));
            } else {
                global $wpdb;
                mpslSetError(__('Slider is not duplicated. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
            }
        } else {
            mpslSetError(__('Slider is not duplicated. Slider ID is not set.', 'motopress-slider-lite'));
        }
    }

    public function checkAliasExistsCallback(){
        mpslVerifyNonce();
        require_once $this->pluginDir . 'includes/classes/SliderOptions.php';
        $alias = $_POST['alias'];
        $result = array(
            'result' => MPSLSliderOptions::isAliasExists($alias)
        );
        wp_send_json($result);
    }

    function updateSlideCallback() {
        mpslVerifyNonce();
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;

        if (isset($_POST['options'])) {
            $options = stripslashes($_POST['options']);
            $options = json_decode($options, true);
        } else {
            $options = array();
        }

        if (isset($_POST['layers']) ){
            $layers = stripslashes($_POST['layers']);
            $layers = json_decode($layers, true);
        } else {
            $layers = array();
        }

	    $preview = isset($_POST['preview']) && $_POST['preview'] == 'true';

	    

        // TODO: Flash messages
        if (!count($options)) return false;

        if (!is_null($id)) {
            require_once $this->pluginDir . 'includes/classes/SlideOptions.php';
            $slide = new MPSLSlideOptions($id, $preview, true);
            $slide->overrideOptions($options, true);
            $slide->setLayers($layers);

	        

            $result = $slide->update();
	        $presetResult = $slide->layerPresets->update();
	        $slide->layerPresets->updatePrivateStyles($id);

            if (false !== $result) {
                wp_send_json(array('result' => $result, 'id' => $slide->getId()));
            } else {
                global $wpdb;
	            if ($preview) {
		            mpslSetError(__('Slider can\'t be previewed. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
	            } else {
		            mpslSetError(__('Slide is not updated. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
	            }
            }

        } else {
            mpslSetError(__('Slide ID is not set.', 'motopress-slider-lite'));
        }
        die();
    }

    function deleteSlideCallback() {
        mpslVerifyNonce();
        require_once $this->pluginDir . 'includes/classes/SlideOptions.php';
        $slide = new MPSLSlideOptions($_POST['id']);
        $result = $slide->delete();
        if (false !== $result) {
            wp_send_json(array('result' => $result));
        } else{
            global $wpdb;
            mpslSetError(__('Slide is not deleted. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
        }
        die();
    }

    function createSlideCallback() {
        mpslVerifyNonce();
        require_once $this->pluginDir . 'includes/classes/SlideOptions.php';
        if (isset($_POST['slider_id'])) {
            $sliderId = (int) $_POST['slider_id'];
            $slide = new MPSLSlideOptions();
            $result = $slide->create($sliderId);

            if($result !== false){
                wp_send_json(array('result' => $result, 'id' => $result));
            }else{
                global $wpdb;
                mpslSetError(__('Slide is not created. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
            }


        } else {
            mpslSetError(__('Slider ID is not set.', 'motopress-slider-lite'));
        }
        die();
    }

    function duplicateSlideCallback() {
        mpslVerifyNonce();
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
	        global $wpdb;
            $slide = new MPSLSlideOptions($id);
            $duplicateSlideRes = $slide->duplicateSlide($id);
//            if ($duplicateSlideRes === 'false') {
	        if ($duplicateSlideRes === false) {
	            mpslSetError(__('Slide is not duplicated. Error: ', 'motopress-slider-lite') . $wpdb->last_error);
	        } else {
		        $slide->layerPresets->update();
		        $slide->layerPresets->updatePrivateStyles();
		        wp_send_json(array('result' => $duplicateSlideRes, 'id' => $duplicateSlideRes));
	        }
        } else {
            mpslSetError(__('Slide ID is not set.', 'motopress-slider-lite'));
        }
        die();
    }

    function updateSlidesOrderCallback(){
        mpslVerifyNonce();
        if (isset($_POST['order'])) {
            $order = (array) $_POST['order'];
            $db = MPSliderDB::getInstance();
            $result = $db->updateSlidesOrder($order);
            if ( false !== $result ) {
                wp_send_json(array('result' => true));
            } else {
                global $wpdb;
                mpslSetError(__('Slides order update error: ' . $wpdb->last_error, 'motopress-slider-lite'));
            }
        } else {
            mpslSetError(__('Order is not set.', 'motopress-slider-lite'));
        }
        die();
    }

    function getYoutubeThumbnailCallback(){
        mpslVerifyNonce();
        if (isset($_GET['src'])) {
            $youtubeDataApi = MPSLYoutubeDataApi::getInstance();
            $thumbnail = $youtubeDataApi->getThumbnail($_GET['src']);
            if (false === $thumbnail) {
                $thumbnail = '';
            }
            wp_send_json(array('result' => $thumbnail));
        } else {
            mpslSetError(__('YouTube video source not setted.', 'motopress-slider-lite'));
        }
    }

    function getVimeoThumbnailCallback(){
        mpslVerifyNonce();
        if (isset($_GET['src'])) {
            $vimeoOEmbedApi = MPSLVimeoOEmbedApi::getInstance();
            $thumbnail = $vimeoOEmbedApi->getThumbnail($_GET['src']);
            if (false === $thumbnail) {
                $thumbnail = '';
            }
            wp_send_json(array('result' => $thumbnail));
        } else {
            mpslSetError(__('Vimeo video source not setted.', 'motopress-slider-lite'));
        }
    }

    function exportSliders() {
        global $mpsl_settings;
        if (isset($_POST['ids']) && !empty($_POST['ids'])) {
            if (check_admin_referer('export-mpsl-sliders')) {
	            require_once $this->pluginDir . 'includes/classes/SliderOptions.php';

                $uploads = wp_upload_dir();
                $exportData = array(
                    'info' => array(
                        'mpsl-ver' => $mpsl_settings['plugin_version'],
                        'base-upload' => $uploads['baseurl']
                    )
                );

	            $presetClasses = array();
                $internalResources = array();

                foreach($_POST['ids'] as $id) {
                    $slider = new MPSLSliderOptions((int) $id);
                    $error = null;
                    $slider_data = $slider->getExportSliderData($internalResources);
                    $exportData['sliders'][$id] = $slider_data['slider'];
	                $presetClasses = array_merge($presetClasses, $slider_data['preset_classes']);
                }
                $exportData['uploads']  = $internalResources;
                $siteTitle = str_replace( ' ', '', get_bloginfo('name') );
                $date = date("m-d-Y");

                if (count($exportData['sliders']) > 1) {
                    $sliderAlias = 'sliders';
                } else {
                    $firstSlider = reset($exportData['sliders']);
                    $sliderAlias = $firstSlider['options']['alias'];
                }

	            /* Presets */
	            $presetClasses = array_unique($presetClasses);
	            if (count($presetClasses)) {
		            $presets = get_option(MPSLLayerPresetOptions::PRESETS_OPT, array());
		            foreach ($presets as $pClass => $preset) {
			            if (!in_array($pClass, $presetClasses)) unset($presets[$pClass]);
		            }
	            } else {
		            $presets = array();
	            }
	            $exportData['presets'] = $presets;
	            /* End Presets */

                $exportFileName = $siteTitle . "-" . $sliderAlias . "-data-" . $date;
                $exportData = json_encode_slashed($exportData);

                header("Content-Type: application/force-download; charset=" . get_bloginfo('charset'));
                header("Content-Disposition: attachment; filename=$exportFileName.json");
                exit($exportData);
            }
        }
        exit();
    }
}

MPSLAdmin::addGlobalActions();

if (is_admin()) {
    global $mpslAdmin;
    $mpslAdmin = new MPSLAdmin();
    register_activation_hook($mpsl_plugin_file, array('MPSLAdmin', 'install'));
    add_action('wpmu_new_blog', array('MPSLAdmin','onCreateBlog'), 10, 6);
    add_filter('wpmu_drop_tables', array('MPSLAdmin', 'onDeleteBlog'));
}


//Widget
add_action('widgets_init', array('MPSLAdmin', 'registerSliderWidget'));

// Shortcode
function mpsl_shortcode($atts){
    global $mpsl_settings;
    $mp_plugin_active = is_plugin_active('motopress-content-editor/motopress-content-editor.php') || is_plugin_active('motopress-content-editor-lite/motopress-content-editor.php');

    $defaultAtts = array(
        'alias' => '',
        'edit_mode' => false
    );
    if ($mp_plugin_active) $defaultAtts = MPCEShortcode::addStyleAtts($defaultAtts);
    extract(shortcode_atts($defaultAtts, $atts, $mpsl_settings['shortcode_name']));

    if ($alias === '') {
        $alias = isset($atts[0]) ? $atts[0] : '';
    }
    $edit_mode = filter_var($edit_mode, FILTER_VALIDATE_BOOLEAN);

    $mpAtts = array();
    if ($mp_plugin_active) {
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $mpAtts = array(
            'mp_style_classes' => $mp_style_classes,
            'margin' => $margin,
			'mp_custom_style' => isset($mp_custom_style) ? $mp_custom_style : ''
        );
    }

    return get_mpsl_slider($alias, $edit_mode, null, $mpAtts);
}
add_shortcode($mpsl_settings['shortcode_name'], 'mpsl_shortcode');

function get_mpsl_slider($alias = '', $edit_mode = false, $slideId = null, $mpAtts = array()) {
	$result = '';
    if (!MPSLSharing::isShortcodeRendering() && $alias) {
	    MPSLSharing::disableShortcodeRendering();
//		$slider = new MPSLSliderOptions();
//	    $slider->loadByAlias($alias);
//        $slider = new MPSLSliderOptions(array('key' => 'alias', 'value' => $alias));
        $slider = new MPSLSliderOptions((string) $alias);
        $sliderOptions = $slider->getFullSliderData($slideId, $edit_mode);
        $result = get_mpsl_slider_by_options($slider->getId(), $sliderOptions, $edit_mode, $mpAtts);
	    MPSLSharing::enableShortcodeRendering();
    }
	return $result;
}

function get_mpsl_slider_by_options($sliderId, $sliderOptions, $edit_mode = false, $mpAtts = array()) {

	if ($sliderOptions['options']['slider_type'] === 'woocommerce' && !is_plugin_active('woocommerce/woocommerce.php')) {
		return __('Please install and activate WooCommerce plugin.', 'motopress-slider-lite');
	}

    if (isset($sliderOptions['slides']) && !empty($sliderOptions['slides'])) {
        mpsl_enqueue_core_scripts_styles($edit_mode);
	    global $mpsl_settings;
        $hasVisibleSlides = false; // will change to true if slider has at least one visible slide .
        ob_start();

        require_once $mpsl_settings['plugin_dir_path'] . 'includes/classes/MPSLShortcode.php';
        $shortcode = new MPSLShortcode($sliderOptions, $edit_mode);
        include $mpsl_settings['plugin_dir_path'] . 'views/shortcode.php';
        $result = ob_get_clean();
        return $hasVisibleSlides ? $result : null;
    }
}

function motoPressSlider($alias){
    echo get_mpsl_slider($alias);
}

function mpsl_enqueue_core_scripts_styles($edit_mode = false) {
	if (!MPSLSharing::$isScriptsStylesEnqueued) {
		global $mpsl_settings;

		wp_enqueue_style('mpsl-core', $mpsl_settings['plugin_dir_url'] . 'motoslider_core/styles/motoslider.css', array(), $mpsl_settings['core_version']);
		wp_enqueue_style('mpsl-object-style', $mpsl_settings['plugin_dir_url'] . 'css/theme.css', array('mpsl-core'), $mpsl_settings['plugin_version']);
		wp_enqueue_style('mpsl-object-gfonts', 'https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800', array('mpsl-object-style'), $mpsl_settings['plugin_version']);

		do_action('mpsl_slider_enqueue_style');

		if (!$edit_mode) {
			global $wp_version;
			if (version_compare($wp_version, '3.7', '>=') || !is_admin()) {
				wp_add_inline_style('mpsl-core', MPSLLayerPresetOptions::getAllCss());
			} else {
				wp_add_inline_style('mpsl-core', '<style type="text/css">' . MPSLLayerPresetOptions::getAllCss() . '</style>');
			}
		}

		wp_enqueue_script('mpsl-vendor', $mpsl_settings['plugin_dir_url'] . 'motoslider_core/scripts/vendor.js', array('jquery'), $mpsl_settings['core_version'], true);
		wp_enqueue_script('mpsl-core', $mpsl_settings['plugin_dir_url'] . 'motoslider_core/scripts/motoslider.js', array(), $mpsl_settings['core_version'], true);

		MPSLSharing::$isScriptsStylesEnqueued = true;
	}
}

add_action('wp_head','mpslPrintInlineHeadScripts');
add_action('admin_print_scripts','mpslPrintInlineHeadScripts');
function mpslPrintInlineHeadScripts() {
	global $mpsl_settings; ?>
	<script type="text/javascript">
		MPSLCore = {
			'path': "<?php echo $mpsl_settings['plugin_dir_url']. 'motoslider_core/'; ?>",
			'version': "<?php echo $mpsl_settings['core_version']; ?>"
		};
	</script>
<?php }

add_action('admin_print_footer_scripts','mpslPrintInlineFooterScripts');
function mpslPrintInlineFooterScripts() {
	// Print fonts
	if (MPSLAdminSharing::$gFontsUrl) { ?>
		<link rel="stylesheet" type="text/css" class="mpsl-admin-fonts-link" href="<?php echo MPSLAdminSharing::$gFontsUrl; ?>" />
	<?php }

	// Print presets
	?><div id="mpsl-preset-styles-wrapper"><?php

	if (is_array(MPSLAdminSharing::$defaultPresets)) {
		foreach (MPSLAdminSharing::$defaultPresets as $class => $preset) { ?>
			<style type="text/css" class="mpsl-preset-style" id="<?php echo $class; ?>"><?php echo $preset; ?></style>
		<?php }
	}

	if (is_array(MPSLAdminSharing::$presets)) {
		foreach (MPSLAdminSharing::$presets as $class => $preset) { ?>
			<style type="text/css" class="mpsl-preset-style" id="<?php echo $class; ?>"><?php echo $preset; ?></style>
		<?php }
	}

	// Print private presets
	if (is_array(MPSLAdminSharing::$privatePresets)) {
		foreach (MPSLAdminSharing::$privatePresets as $class => $preset) { ?>
			<style type="text/css" class="mpsl-preset-style" id="<?php echo $class; ?>"><?php echo $preset; ?></style>
		<?php }
	}

	?></div><?php
}

}
else {
	add_action('admin_notices', 'mpslLiteConflictNotice');
	if (is_multisite()) add_action('network_admin_notices', 'mpslLiteConflictNotice');
}
function mpslLiteConflictNotice() {
	$class = "error";
	$message = "<b>MotoPress Slider Lite</b> plugin and <b>MotoPress Slider</b> plugin do not work simultaneously. Deactivate <b>MotoPress Slider Lite</b> plugin.";
        echo"<div class=\"$class\"> <p>$message</p></div>";
}