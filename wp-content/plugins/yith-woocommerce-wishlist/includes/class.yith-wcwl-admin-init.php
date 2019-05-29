<?php
/**
 * Admin init class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Admin_Init' ) ) {
	/**
	 * Initiator class. Create and populate admin views.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Admin_Init {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Admin_Init
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Wishlist panel
		 *
		 * @var string Panel hookname
		 * @since 2.0.0
		 */
		protected $_panel = null;

		/**
		 * Tab name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $tab;

		/**
		 * Various links
		 *
		 * @var string
		 * @access public
		 * @since 1.0.0
		 */
		public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-wishlist/';
		public $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-wishlist/';
		public $live_demo_url = 'https://plugins.yithemes.com/yith-woocommerce-wishlist/';

		/**
		 * Plugin options
		 *
		 * @var array
		 * @since 1.0.0
		 */
		public $options;

		/**
		 * List of available tab for wishlist panel
		 *
		 * @var array
		 * @access public
		 * @since 2.0.0
		 */
		public $available_tabs = array();

		/**
		 * Default tab to show when no selected
		 *
		 * @var string
		 * @access public
		 * @since 2.0.0
		 */
		public $default_tab = 'settings';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Admin_Init
		 * @since 2.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor of the class
		 *
		 * @return \YITH_WCWL_Admin_Init
		 * @since 2.0.0
		 */
		public function __construct(){
			// init premium features for admin panel
			if( function_exists( 'YITH_WCWL_Admin_Premium' ) ){
				YITH_WCWL_Admin_Premium();
			}

			$this->options = $this->_plugin_options();

			if ( ! defined( 'DOING_AJAX' ) ) {
				$this->install();
			}

			add_action( 'init', array( $this, 'init' ), 0 );

			// enqueue scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 20 );

			// add plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCWL_DIR . 'init.php' ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 5 );

			// saves panel options
            add_filter( 'woocommerce_admin_settings_sanitize_option_yith_wcwl_color_panel', array( $this, 'update_color_options' ) );

			// handles custom wc option type
			add_action( 'woocommerce_admin_field_yith_wcwl_color_panel', array( $this, 'print_color_panel' ) );

			// register wishlist panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_wcwl_premium_tab', array( $this, 'print_premium_tab' ) );

			// register pointer methods
			add_action( 'admin_init', array( $this, 'register_pointer' ) );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri(){
			return defined( 'YITH_REFER_ID' ) ? $this->premium_landing_url . '?refer_id=' . YITH_REFER_ID : $this->premium_landing_url . '?refer_id=1030585';
		}

		/* === INITIALIZATION SECTION === */

		/**
		 * Initiator method. Initiate properties.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		public function init() {
			$this->tab     = __( 'Wishlist', 'yith-woocommerce-wishlist' );
			$this->available_tabs = apply_filters( 'yith_wcwl_available_admin_tabs', array(
				'settings' => __( 'Settings', 'yith-woocommerce-wishlist' ),
				'colors' => __( 'Colors', 'yith-woocommerce-wishlist' ),
				'premium' => __( 'Premium Version', 'yith-woocommerce-wishlist' )
			) );
			$this->default_tab = apply_filters( 'yith_wcwl_default_admin_tab', $this->default_tab );

			wp_register_style( 'yith-wcwl-admin', YITH_WCWL_URL . 'assets/css/admin.css', array(), YITH_WCWL_Init()->version );
			wp_register_script( 'yith-wcwl-admin', YITH_WCWL_URL . 'assets/js/admin/yith-wcwl.js', array(), YITH_WCWL_Init()->version );
		}

		/**
		 * Run the installation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function install() {
			$stored_db_version = get_option( 'yith_wcwl_db_version' );

			if( version_compare( $stored_db_version,  '1.0.0', '<=' ) ){
				add_action( 'init', array( YITH_WCWL_Install(), 'update' ) );
				add_action( 'init', 'flush_rewrite_rules' );
				YITH_WCWL_Install()->default_options( $this->options );

				// Plugin installed
				do_action( 'yith_wcwl_installed' );
				do_action( 'yith_wcwl_updated' );
			}
			elseif( version_compare( $stored_db_version, '2.0.0', '<=' ) ){
			    add_action( 'init', array( YITH_WCWL_Install(), 'update' ) );

				// Plugin installed
				do_action( 'yith_wcwl_installed' );
				do_action( 'yith_wcwl_updated' );
            }
			elseif ( YITH_WCWL_Init()->db_version != $stored_db_version || ! YITH_WCWL_Install()->is_installed() ) {
				add_action( 'init', array( YITH_WCWL_Install(), 'init' ) );
				add_action( 'init', 'flush_rewrite_rules' );
				YITH_WCWL_Install()->default_options( $this->options );

				// Plugin installed
				do_action( 'yith_wcwl_installed' );
			}
		}

		/**
		 * Update plugin color options.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_color_options( $value = false ) {
			global $pagenow;

			$colors_options = array();

			foreach ( YITH_WCWL_Init()->colors_options as $name => $option ) {
				foreach ( $option as $id => $color ) {
					$default_value = isset( $colors_options[$name][$id] ) ? $colors_options[$name][$id] : '';
					if( isset( $_POST['yith_wcwl_color_' . $name . '_' . $id] ) && ! empty( $_POST['yith_wcwl_color_' . $name . '_' . $id] ) ){
						$colors_options[$name][$id] = wc_format_hex( $_POST['yith_wcwl_color_' . $name . '_' . $id] );
					}
					else{
						$colors_options[$name][$id] = $default_value;
					}
				}
			}

			update_option( 'yith_wcwl_frontend_css_colors', maybe_serialize( $colors_options ) );

			return null;
		}

		/**
		 * Print color panel.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_color_panel() {
			?>
			<div id="yith_wcwl_styles_colors">
				<h3><?php _e( 'Colors', 'yith-woocommerce-wishlist' ) ?></h3>
				<?php $this->_styles_options() ?>
			</div> <?php
		}

		/**
		 * action_links function.
		 *
		 * @access public
		 *
		 * @param mixed $links
		 * @return array
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcwl_panel', defined( 'YITH_WCWL_PREMIUM' ) );
			return $links;
		}

		/**
		 * Adds plugin row meta
		 *
		 * @param $plugin_meta array
		 * @param $plugin_file string
		 * @return array
		 * @since 2.0.0
		 */
		public function add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCWL_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']      = 'yith-woocommerce-wishlist';

			}

			if ( defined( 'YITH_WCWL_PREMIUM' ) ) {
				$new_row_meta_args['is_premium'] = true;

			}

			return $new_row_meta_args;
		}

		/**
		 * Plugin options and tabs.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		private function _plugin_options() {
			$icons = array(
				'fa-glass' => 'Glass',
				'fa-music' => 'Music',
				'fa-search' => 'Search',
				'fa-envelope-o' => 'Envelope O',
				'fa-heart' => 'Heart',
				'fa-star' => 'Star',
				'fa-star-o' => 'Star O',
				'fa-user' => 'User',
				'fa-film' => 'Film',
				'fa-th-large' => 'Th Large',
				'fa-th' => 'Th',
				'fa-th-list' => 'Th List',
				'fa-check' => 'Check',
				'fa-remove' => 'Remove',
				'fa-search-plus' => 'Search Plus',
				'fa-search-minus' => 'Search Minus',
				'fa-power-off' => 'Power Off',
				'fa-signal' => 'Signal',
				'fa-cog' => 'Cog',
				'fa-trash-o' => 'Trash O',
				'fa-home' => 'Home',
				'fa-file-o' => 'File O',
				'fa-clock-o' => 'Clock O',
				'fa-road' => 'Road',
				'fa-download' => 'Download',
				'fa-arrow-circle-o-down' => 'Arrow Circle O Down',
				'fa-arrow-circle-o-up' => 'Arrow Circle O Up',
				'fa-inbox' => 'Inbox',
				'fa-play-circle-o' => 'Play Circle O',
				'fa-repeat' => 'Repeat',
				'fa-refresh' => 'Refresh',
				'fa-list-alt' => 'List Alt',
				'fa-lock' => 'Lock',
				'fa-flag' => 'Flag',
				'fa-headphones' => 'Headphones',
				'fa-volume-off' => 'Volume Off',
				'fa-volume-down' => 'Volume Down',
				'fa-volume-up' => 'Volume Up',
				'fa-qrcode' => 'Qrcode',
				'fa-barcode' => 'Barcode',
				'fa-tag' => 'Tag',
				'fa-tags' => 'Tags',
				'fa-book' => 'Book',
				'fa-bookmark' => 'Bookmark',
				'fa-print' => 'Print',
				'fa-camera' => 'Camera',
				'fa-font' => 'Font',
				'fa-bold' => 'Bold',
				'fa-italic' => 'Italic',
				'fa-text-height' => 'Text Height',
				'fa-text-width' => 'Text Width',
				'fa-align-left' => 'Align Left',
				'fa-align-center' => 'Align Center',
				'fa-align-right' => 'Align Right',
				'fa-align-justify' => 'Align Justify',
				'fa-list' => 'List',
				'fa-dedent' => 'Dedent',
				'fa-indent' => 'Indent',
				'fa-video-camera' => 'Video Camera',
				'fa-picture-o' => 'Photo',
				'fa-pencil' => 'Pencil',
				'fa-map-marker' => 'Map Marker',
				'fa-adjust' => 'Adjust',
				'fa-tint' => 'Tint',
				'fa-edit' => 'Edit',
				'fa-share-square-o' => 'Share Square O',
				'fa-check-square-o' => 'Check Square O',
				'fa-arrows' => 'Arrows',
				'fa-step-backward' => 'Step Backward',
				'fa-fast-backward' => 'Fast Backward',
				'fa-backward' => 'Backward',
				'fa-play' => 'Play',
				'fa-pause' => 'Pause',
				'fa-stop' => 'Stop',
				'fa-forward' => 'Forward',
				'fa-fast-forward' => 'Fast Forward',
				'fa-step-forward' => 'Step Forward',
				'fa-eject' => 'Eject',
				'fa-chevron-left' => 'Chevron Left',
				'fa-chevron-right' => 'Chevron Right',
				'fa-plus-circle' => 'Plus Circle',
				'fa-minus-circle' => 'Minus Circle',
				'fa-times-circle' => 'Times Circle',
				'fa-check-circle' => 'Check Circle',
				'fa-question-circle' => 'Question Circle',
				'fa-info-circle' => 'Info Circle',
				'fa-crosshairs' => 'Crosshairs',
				'fa-times-circle-o' => 'Times Circle O',
				'fa-check-circle-o' => 'Check Circle O',
				'fa-ban' => 'Ban',
				'fa-arrow-left' => 'Arrow Left',
				'fa-arrow-right' => 'Arrow Right',
				'fa-arrow-up' => 'Arrow Up',
				'fa-arrow-down' => 'Arrow Down',
				'fa-share' => 'Share',
				'fa-expand' => 'Expand',
				'fa-compress' => 'Compress',
				'fa-plus' => 'Plus',
				'fa-minus' => 'Minus',
				'fa-asterisk' => 'Asterisk',
				'fa-exclamation-circle' => 'Exclamation Circle',
				'fa-gift' => 'Gift',
				'fa-leaf' => 'Leaf',
				'fa-fire' => 'Fire',
				'fa-eye' => 'Eye',
				'fa-eye-slash' => 'Eye Slash',
				'fa-warning' => 'Warning',
				'fa-plane' => 'Plane',
				'fa-calendar' => 'Calendar',
				'fa-random' => 'Random',
				'fa-comment' => 'Comment',
				'fa-magnet' => 'Magnet',
				'fa-chevron-up' => 'Chevron Up',
				'fa-chevron-down' => 'Chevron Down',
				'fa-retweet' => 'Retweet',
				'fa-shopping-cart' => 'Shopping Cart',
				'fa-folder' => 'Folder',
				'fa-folder-open' => 'Folder Open',
				'fa-arrows-v' => 'Arrows V',
				'fa-arrows-h' => 'Arrows H',
				'fa-bar-chart' => 'Bar Chart',
				'fa-twitter-square' => 'Twitter Square',
				'fa-facebook-square' => 'Facebook Square',
				'fa-camera-retro' => 'Camera Retro',
				'fa-key' => 'Key',
				'fa-cogs' => 'Cogs',
				'fa-comments' => 'Comments',
				'fa-thumbs-o-up' => 'Thumbs O Up',
				'fa-thumbs-o-down' => 'Thumbs O Down',
				'fa-star-half' => 'Star Half',
				'fa-heart-o' => 'Heart O',
				'fa-sign-out' => 'Sign Out',
				'fa-linkedin-square' => 'Linkedin Square',
				'fa-thumb-tack' => 'Thumb Tack',
				'fa-external-link' => 'External Link',
				'fa-sign-in' => 'Sign In',
				'fa-trophy' => 'Trophy',
				'fa-github-square' => 'Github Square',
				'fa-upload' => 'Upload',
				'fa-lemon-o' => 'Lemon O',
				'fa-phone' => 'Phone',
				'fa-square-o' => 'Square O',
				'fa-bookmark-o' => 'Bookmark O',
				'fa-phone-square' => 'Phone Square',
				'fa-twitter' => 'Twitter',
				'fa-facebook' => 'Facebook',
				'fa-github' => 'Github',
				'fa-unlock' => 'Unlock',
				'fa-credit-card' => 'Credit Card',
				'fa-rss' => 'Rss',
				'fa-hdd-o' => 'Hdd O',
				'fa-bullhorn' => 'Bullhorn',
				'fa-bell' => 'Bell',
				'fa-certificate' => 'Certificate',
				'fa-hand-o-right' => 'Hand O Right',
				'fa-hand-o-left' => 'Hand O Left',
				'fa-hand-o-up' => 'Hand O Up',
				'fa-hand-o-down' => 'Hand O Down',
				'fa-arrow-circle-left' => 'Arrow Circle Left',
				'fa-arrow-circle-right' => 'Arrow Circle Right',
				'fa-arrow-circle-up' => 'Arrow Circle Up',
				'fa-arrow-circle-down' => 'Arrow Circle Down',
				'fa-globe' => 'Globe',
				'fa-wrench' => 'Wrench',
				'fa-tasks' => 'Tasks',
				'fa-filter' => 'Filter',
				'fa-briefcase' => 'Briefcase',
				'fa-arrows-alt' => 'Arrows Alt',
				'fa-group' => 'Group',
				'fa-link' => 'Link',
				'fa-cloud' => 'Cloud',
				'fa-flask' => 'Flask',
				'fa-cut' => 'Cut',
				'fa-copy' => 'Copy',
				'fa-paperclip' => 'Paperclip',
				'fa-save' => 'Save',
				'fa-square' => 'Square',
				'fa-navicon' => 'Navicon',
				'fa-list-ul' => 'List Ul',
				'fa-list-ol' => 'List Ol',
				'fa-strikethrough' => 'Strikethrough',
				'fa-underline' => 'Underline',
				'fa-table' => 'Table',
				'fa-magic' => 'Magic',
				'fa-truck' => 'Truck',
				'fa-pinterest' => 'Pinterest',
				'fa-pinterest-square' => 'Pinterest Square',
				'fa-google-plus-square' => 'Google Plus Square',
				'fa-google-plus' => 'Google Plus',
				'fa-money' => 'Money',
				'fa-caret-down' => 'Caret Down',
				'fa-caret-up' => 'Caret Up',
				'fa-caret-left' => 'Caret Left',
				'fa-caret-right' => 'Caret Right',
				'fa-columns' => 'Columns',
				'fa-unsorted' => 'Unsorted',
				'fa-sort-down' => 'Sort Down',
				'fa-sort-up' => 'Sort Up',
				'fa-envelope' => 'Envelope',
				'fa-linkedin' => 'Linkedin',
				'fa-undo' => 'Undo',
				'fa-legal' => 'Legal',
				'fa-dashboard' => 'Dashboard',
				'fa-comment-o' => 'Comment O',
				'fa-comments-o' => 'Comments O',
				'fa-bolt' => 'Bolt',
				'fa-sitemap' => 'Sitemap',
				'fa-umbrella' => 'Umbrella',
				'fa-paste' => 'Paste',
				'fa-lightbulb-o' => 'Lightbulb O',
				'fa-exchange' => 'Exchange',
				'fa-cloud-download' => 'Cloud Download',
				'fa-cloud-upload' => 'Cloud Upload',
				'fa-user-md' => 'User Md',
				'fa-stethoscope' => 'Stethoscope',
				'fa-suitcase' => 'Suitcase',
				'fa-bell-o' => 'Bell O',
				'fa-coffee' => 'Coffee',
				'fa-cutlery' => 'Cutlery',
				'fa-file-text-o' => 'File Text O',
				'fa-building-o' => 'Building O',
				'fa-hospital-o' => 'Hospital O',
				'fa-ambulance' => 'Ambulance',
				'fa-medkit' => 'Medkit',
				'fa-fighter-jet' => 'Fighter Jet',
				'fa-beer' => 'Beer',
				'fa-h-square' => 'H Square',
				'fa-plus-square' => 'Plus Square',
				'fa-angle-double-left' => 'Angle Double Left',
				'fa-angle-double-right' => 'Angle Double Right',
				'fa-angle-double-up' => 'Angle Double Up',
				'fa-angle-double-down' => 'Angle Double Down',
				'fa-angle-left' => 'Angle Left',
				'fa-angle-right' => 'Angle Right',
				'fa-angle-up' => 'Angle Up',
				'fa-angle-down' => 'Angle Down',
				'fa-desktop' => 'Desktop',
				'fa-laptop' => 'Laptop',
				'fa-tablet' => 'Tablet',
				'fa-mobile' => 'Mobile',
				'fa-circle-o' => 'Circle O',
				'fa-quote-left' => 'Quote Left',
				'fa-quote-right' => 'Quote Right',
				'fa-spinner' => 'Spinner',
				'fa-circle' => 'Circle',
				'fa-reply' => 'Reply',
				'fa-github-alt' => 'Github Alt',
				'fa-folder-o' => 'Folder O',
				'fa-folder-open-o' => 'Folder Open O',
				'fa-smile-o' => 'Smile O',
				'fa-frown-o' => 'Frown O',
				'fa-meh-o' => 'Meh O',
				'fa-gamepad' => 'Gamepad',
				'fa-keyboard-o' => 'Keyboard O',
				'fa-flag-o' => 'Flag O',
				'fa-flag-checkered' => 'Flag Checkered',
				'fa-terminal' => 'Terminal',
				'fa-code' => 'Code',
				'fa-reply-all' => 'Reply All',
				'fa-star-half-o' => 'Star Half O',
				'fa-location-arrow' => 'Location Arrow',
				'fa-crop' => 'Crop',
				'fa-code-fork' => 'Code Fork',
				'fa-chain-broken' => 'Chain Broken',
				'fa-question' => 'Question',
				'fa-info' => 'Info',
				'fa-exclamation' => 'Exclamation',
				'fa-superscript' => 'Superscript',
				'fa-subscript' => 'Subscript',
				'fa-eraser' => 'Eraser',
				'fa-puzzle-piece' => 'Puzzle Piece',
				'fa-microphone' => 'Microphone',
				'fa-microphone-slash' => 'Microphone Slash',
				'fa-shield' => 'Shield',
				'fa-calendar-o' => 'Calendar O',
				'fa-fire-extinguisher' => 'Fire Extinguisher',
				'fa-rocket' => 'Rocket',
				'fa-maxcdn' => 'Maxcdn',
				'fa-chevron-circle-left' => 'Chevron Circle Left',
				'fa-chevron-circle-right' => 'Chevron Circle Right',
				'fa-chevron-circle-up' => 'Chevron Circle Up',
				'fa-chevron-circle-down' => 'Chevron Circle Down',
				'fa-html5' => 'Html5',
				'fa-css3' => 'Css3',
				'fa-anchor' => 'Anchor',
				'fa-unlock-alt' => 'Unlock Alt',
				'fa-bullseye' => 'Bullseye',
				'fa-ellipsis-h' => 'Ellipsis H',
				'fa-ellipsis-v' => 'Ellipsis V',
				'fa-rss-square' => 'Rss Square',
				'fa-play-circle' => 'Play Circle',
				'fa-ticket' => 'Ticket',
				'fa-minus-square' => 'Minus Square',
				'fa-minus-square-o' => 'Minus Square O',
				'fa-level-up' => 'Level Up',
				'fa-level-down' => 'Level Down',
				'fa-check-square' => 'Check Square',
				'fa-pencil-square' => 'Pencil Square',
				'fa-external-link-square' => 'External Link Square',
				'fa-share-square' => 'Share Square',
				'fa-compass' => 'Compass',
				'fa-caret-square-o-down' => 'Caret Square O Down',
				'fa-caret-square-o-up' => 'Caret Square O Up',
				'fa-caret-square-o-right' => 'Caret Square O Right',
				'fa-eur' => 'Eur',
				'fa-gbp' => 'Gbp',
				'fa-usd' => 'Usd',
				'fa-inr' => 'Inr',
				'fa-jpy' => 'Jpy',
				'fa-rub' => 'Rub',
				'fa-krw' => 'Krw',
				'fa-btc' => 'Btc',
				'fa-file' => 'File',
				'fa-file-text' => 'File Text',
				'fa-sort-alpha-asc' => 'Sort Alpha Asc',
				'fa-sort-alpha-desc' => 'Sort Alpha Desc',
				'fa-sort-amount-asc' => 'Sort Amount Asc',
				'fa-sort-amount-desc' => 'Sort Amount Desc',
				'fa-sort-numeric-asc' => 'Sort Numeric Asc',
				'fa-sort-numeric-desc' => 'Sort Numeric Desc',
				'fa-thumbs-up' => 'Thumbs Up',
				'fa-thumbs-down' => 'Thumbs Down',
				'fa-youtube-square' => 'Youtube Square',
				'fa-youtube' => 'Youtube',
				'fa-xing' => 'Xing',
				'fa-xing-square' => 'Xing Square',
				'fa-youtube-play' => 'Youtube Play',
				'fa-dropbox' => 'Dropbox',
				'fa-stack-overflow' => 'Stack Overflow',
				'fa-instagram' => 'Instagram',
				'fa-flickr' => 'Flickr',
				'fa-adn' => 'Adn',
				'fa-bitbucket' => 'Bitbucket',
				'fa-bitbucket-square' => 'Bitbucket Square',
				'fa-tumblr' => 'Tumblr',
				'fa-tumblr-square' => 'Tumblr Square',
				'fa-long-arrow-down' => 'Long Arrow Down',
				'fa-long-arrow-up' => 'Long Arrow Up',
				'fa-long-arrow-left' => 'Long Arrow Left',
				'fa-long-arrow-right' => 'Long Arrow Right',
				'fa-apple' => 'Apple',
				'fa-windows' => 'Windows',
				'fa-android' => 'Android',
				'fa-linux' => 'Linux',
				'fa-dribbble' => 'Dribbble',
				'fa-skype' => 'Skype',
				'fa-foursquare' => 'Foursquare',
				'fa-trello' => 'Trello',
				'fa-female' => 'Female',
				'fa-male' => 'Male',
				'fa-gratipay' => 'Gratipay',
				'fa-sun-o' => 'Sun O',
				'fa-moon-o' => 'Moon O',
				'fa-archive' => 'Archive',
				'fa-bug' => 'Bug',
				'fa-vk' => 'Vk',
				'fa-weibo' => 'Weibo',
				'fa-renren' => 'Renren',
				'fa-pagelines' => 'Pagelines',
				'fa-stack-exchange' => 'Stack Exchange',
				'fa-arrow-circle-o-right' => 'Arrow Circle O Right',
				'fa-arrow-circle-o-left' => 'Arrow Circle O Left',
				'fa-caret-square-o-left' => 'Caret Square O Left',
				'fa-dot-circle-o' => 'Dot Circle O',
				'fa-wheelchair' => 'Wheelchair',
				'fa-vimeo-square' => 'Vimeo Square',
				'fa-try' => 'Try',
				'fa-plus-square-o' => 'Plus Square O',
				'fa-space-shuttle' => 'Space Shuttle',
				'fa-slack' => 'Slack',
				'fa-envelope-square' => 'Envelope Square',
				'fa-wordpress' => 'Wordpress',
				'fa-openid' => 'Openid',
				'fa-university' => 'University',
				'fa-graduation-cap' => 'Graduation Cap',
				'fa-yahoo' => 'Yahoo',
				'fa-google' => 'Google',
				'fa-reddit' => 'Reddit',
				'fa-reddit-square' => 'Reddit Square',
				'fa-stumbleupon-circle' => 'Stumbleupon Circle',
				'fa-stumbleupon' => 'Stumbleupon',
				'fa-delicious' => 'Delicious',
				'fa-digg' => 'Digg',
				'fa-pied-piper' => 'Pied Piper',
				'fa-pied-piper-alt' => 'Pied Piper Alt',
				'fa-drupal' => 'Drupal',
				'fa-joomla' => 'Joomla',
				'fa-language' => 'Language',
				'fa-fax' => 'Fax',
				'fa-building' => 'Building',
				'fa-child' => 'Child',
				'fa-paw' => 'Paw',
				'fa-spoon' => 'Spoon',
				'fa-cube' => 'Cube',
				'fa-cubes' => 'Cubes',
				'fa-behance' => 'Behance',
				'fa-behance-square' => 'Behance Square',
				'fa-steam' => 'Steam',
				'fa-steam-square' => 'Steam Square',
				'fa-recycle' => 'Recycle',
				'fa-car' => 'Car',
				'fa-taxi' => 'Taxi',
				'fa-tree' => 'Tree',
				'fa-spotify' => 'Spotify',
				'fa-deviantart' => 'Deviantart',
				'fa-soundcloud' => 'Soundcloud',
				'fa-database' => 'Database',
				'fa-file-pdf-o' => 'File Pdf O',
				'fa-file-word-o' => 'File Word O',
				'fa-file-excel-o' => 'File Excel O',
				'fa-file-powerpoint-o' => 'File Powerpoint O',
				'fa-file-image-o' => 'File Image O',
				'fa-file-archive-o' => 'File Archive O',
				'fa-file-audio-o' => 'File Audio O',
				'fa-file-video-o' => 'File Video O',
				'fa-file-code-o' => 'File Code O',
				'fa-vine' => 'Vine',
				'fa-codepen' => 'Codepen',
				'fa-jsfiddle' => 'Jsfiddle',
				'fa-life-ring' => 'Life Ring',
				'fa-circle-o-notch' => 'Circle O Notch',
				'fa-rebel' => 'Rebel',
				'fa-empire' => 'Empire',
				'fa-git-square' => 'Git Square',
				'fa-git' => 'Git',
				'fa-hacker-news' => 'Hacker News',
				'fa-tencent-weibo' => 'Tencent Weibo',
				'fa-qq' => 'Qq',
				'fa-weixin' => 'Weixin',
				'fa-paper-plane' => 'Paper Plane',
				'fa-paper-plane-o' => 'Paper Plane O',
				'fa-history' => 'History',
				'fa-circle-thin' => 'Circle Thin',
				'fa-header' => 'Header',
				'fa-paragraph' => 'Paragraph',
				'fa-sliders' => 'Sliders',
				'fa-share-alt' => 'Share Alt',
				'fa-share-alt-square' => 'Share Alt Square',
				'fa-bomb' => 'Bomb',
				'fa-futbol-o' => 'Futbol O',
				'fa-tty' => 'Tty',
				'fa-binoculars' => 'Binoculars',
				'fa-plug' => 'Plug',
				'fa-slideshare' => 'Slideshare',
				'fa-twitch' => 'Twitch',
				'fa-yelp' => 'Yelp',
				'fa-newspaper-o' => 'Newspaper O',
				'fa-wifi' => 'Wifi',
				'fa-calculator' => 'Calculator',
				'fa-paypal' => 'Paypal',
				'fa-google-wallet' => 'Google Wallet',
				'fa-cc-visa' => 'Cc Visa',
				'fa-cc-mastercard' => 'Cc Mastercard',
				'fa-cc-discover' => 'Cc Discover',
				'fa-cc-amex' => 'Cc Amex',
				'fa-cc-paypal' => 'Cc Paypal',
				'fa-cc-stripe' => 'Cc Stripe',
				'fa-bell-slash' => 'Bell Slash',
				'fa-bell-slash-o' => 'Bell Slash O',
				'fa-trash' => 'Trash',
				'fa-copyright' => 'Copyright',
				'fa-at' => 'At',
				'fa-eyedropper' => 'Eyedropper',
				'fa-paint-brush' => 'Paint Brush',
				'fa-birthday-cake' => 'Birthday Cake',
				'fa-area-chart' => 'Area Chart',
				'fa-pie-chart' => 'Pie Chart',
				'fa-line-chart' => 'Line Chart',
				'fa-lastfm' => 'Lastfm',
				'fa-lastfm-square' => 'Lastfm Square',
				'fa-toggle-off' => 'Toggle Off',
				'fa-toggle-on' => 'Toggle On',
				'fa-bicycle' => 'Bicycle',
				'fa-bus' => 'Bus',
				'fa-ioxhost' => 'Ioxhost',
				'fa-angellist' => 'Angellist',
				'fa-cc' => 'Cc',
				'fa-ils' => 'Ils',
				'fa-meanpath' => 'Meanpath',
				'fa-buysellads' => 'Buysellads',
				'fa-connectdevelop' => 'Connectdevelop',
				'fa-dashcube' => 'Dashcube',
				'fa-forumbee' => 'Forumbee',
				'fa-leanpub' => 'Leanpub',
				'fa-sellsy' => 'Sellsy',
				'fa-shirtsinbulk' => 'Shirtsinbulk',
				'fa-simplybuilt' => 'Simplybuilt',
				'fa-skyatlas' => 'Skyatlas',
				'fa-cart-plus' => 'Cart Plus',
				'fa-cart-arrow-down' => 'Cart Arrow Down',
				'fa-diamond' => 'Diamond',
				'fa-ship' => 'Ship',
				'fa-user-secret' => 'User Secret',
				'fa-motorcycle' => 'Motorcycle',
				'fa-street-view' => 'Street View',
				'fa-heartbeat' => 'Heartbeat',
				'fa-venus' => 'Venus',
				'fa-mars' => 'Mars',
				'fa-mercury' => 'Mercury',
				'fa-transgender' => 'Transgender',
				'fa-transgender-alt' => 'Transgender Alt',
				'fa-venus-double' => 'Venus Double',
				'fa-mars-double' => 'Mars Double',
				'fa-venus-mars' => 'Venus Mars',
				'fa-mars-stroke' => 'Mars Stroke',
				'fa-mars-stroke-v' => 'Mars Stroke V',
				'fa-mars-stroke-h' => 'Mars Stroke H',
				'fa-neuter' => 'Neuter',
				'fa-facebook-official' => 'Facebook Official',
				'fa-pinterest-p' => 'Pinterest P',
				'fa-whatsapp' => 'Whatsapp',
				'fa-server' => 'Server',
				'fa-user-plus' => 'User Plus',
				'fa-user-times' => 'User Times',
				'fa-bed' => 'Bed',
				'fa-viacoin' => 'Viacoin',
				'fa-train' => 'Train',
				'fa-subway' => 'Subway',
				'fa-medium' => 'Medium',
				'fa-pull-left' => 'Pull Left',
				'fa-pull-right' => 'Pull Right',
				'fa-feed' => 'Feed',
				'fa-pied-piper-pp' => 'Pied Piper Pp',
				'fa-resistance' => 'Resistance',
				'fa-y-combinator-square' => 'Y Combinator Square',
				'fa-yc-square' => 'Yc Square',
				'fa-intersex' => 'Intersex',
				'fa-yc' => 'Yc',
				'fa-y-combinator' => 'Y Combinator',
				'fa-optin-monster' => 'Optin Monster',
				'fa-opencart' => 'Opencart',
				'fa-expeditedssl' => 'Expeditedssl',
				'fa-battery-4' => 'Battery 4',
				'fa-battery' => 'Battery',
				'fa-battery-full' => 'Battery Full',
				'fa-battery-3' => 'Battery 3',
				'fa-battery-three-quarters' => 'Battery Three Quarters',
				'fa-battery-2' => 'Battery 2',
				'fa-battery-half' => 'Battery Half',
				'fa-battery-1' => 'Battery 1',
				'fa-battery-quarter' => 'Battery Quarter',
				'fa-battery-0' => 'Battery 0',
				'fa-battery-empty' => 'Battery Empty',
				'fa-mouse-pointer' => 'Mouse Pointer',
				'fa-i-cursor' => 'I Cursor',
				'fa-object-group' => 'Object Group',
				'fa-object-ungroup' => 'Object Ungroup',
				'fa-sticky-note' => 'Sticky Note',
				'fa-sticky-note-o' => 'Sticky Note O',
				'fa-cc-jcb' => 'Cc Jcb',
				'fa-cc-diners-club' => 'Cc Diners Club',
				'fa-clone' => 'Clone',
				'fa-balance-scale' => 'Balance Scale',
				'fa-hourglass-o' => 'Hourglass O',
				'fa-hourglass-1' => 'Hourglass 1',
				'fa-hourglass-start' => 'Hourglass Start',
				'fa-hourglass-2' => 'Hourglass 2',
				'fa-hourglass-half' => 'Hourglass Half',
				'fa-hourglass-3' => 'Hourglass 3',
				'fa-hourglass-end' => 'Hourglass End',
				'fa-hourglass' => 'Hourglass',
				'fa-hand-grab-o' => 'Hand Grab O',
				'fa-hand-rock-o' => 'Hand Rock O',
				'fa-hand-stop-o' => 'Hand Stop O',
				'fa-hand-paper-o' => 'Hand Paper O',
				'fa-hand-scissors-o' => 'Hand Scissors O',
				'fa-hand-lizard-o' => 'Hand Lizard O',
				'fa-hand-spock-o' => 'Hand Spock O',
				'fa-hand-pointer-o' => 'Hand Pointer O',
				'fa-hand-peace-o' => 'Hand Peace O',
				'fa-trademark' => 'Trademark',
				'fa-registered' => 'Registered',
				'fa-creative-commons' => 'Creative Commons',
				'fa-gg' => 'Gg',
				'fa-gg-circle' => 'Gg Circle',
				'fa-tripadvisor' => 'Tripadvisor',
				'fa-odnoklassniki' => 'Odnoklassniki',
				'fa-odnoklassniki-square' => 'Odnoklassniki Square',
				'fa-get-pocket' => 'Get Pocket',
				'fa-wikipedia-w' => 'Wikipedia W',
				'fa-safari' => 'Safari',
				'fa-chrome' => 'Chrome',
				'fa-firefox' => 'Firefox',
				'fa-opera' => 'Opera',
				'fa-internet-explorer' => 'Internet Explorer',
				'fa-tv' => 'Tv',
				'fa-television' => 'Television',
				'fa-contao' => 'Contao',
				'fa-500px' => '500px',
				'fa-amazon' => 'Amazon',
				'fa-calendar-plus-o' => 'Calendar Plus O',
				'fa-calendar-minus-o' => 'Calendar Minus O',
				'fa-calendar-times-o' => 'Calendar Times O',
				'fa-calendar-check-o' => 'Calendar Check O',
				'fa-industry' => 'Industry',
				'fa-map-pin' => 'Map Pin',
				'fa-map-signs' => 'Map Signs',
				'fa-map-o' => 'Map O',
				'fa-map' => 'Map',
				'fa-commenting' => 'Commenting',
				'fa-commenting-o' => 'Commenting O',
				'fa-houzz' => 'Houzz',
				'fa-vimeo' => 'Vimeo',
				'fa-black-tie' => 'Black Tie',
				'fa-fonticons' => 'Fonticons',
				'fa-reddit-alien' => 'Reddit Alien',
				'fa-edge' => 'Edge',
				'fa-credit-card-alt' => 'Credit Card Alt',
				'fa-codiepie' => 'Codiepie',
				'fa-modx' => 'Modx',
				'fa-fort-awesome' => 'Fort Awesome',
				'fa-usb' => 'Usb',
				'fa-product-hunt' => 'Product Hunt',
				'fa-mixcloud' => 'Mixcloud',
				'fa-scribd' => 'Scribd',
				'fa-pause-circle' => 'Pause Circle',
				'fa-pause-circle-o' => 'Pause Circle O',
				'fa-stop-circle' => 'Stop Circle',
				'fa-stop-circle-o' => 'Stop Circle O',
				'fa-shopping-bag' => 'Shopping Bag',
				'fa-shopping-basket' => 'Shopping Basket',
				'fa-hashtag' => 'Hashtag',
				'fa-bluetooth' => 'Bluetooth',
				'fa-bluetooth-b' => 'Bluetooth B',
				'fa-percent' => 'Percent',
				'fa-gitlab' => 'Gitlab',
				'fa-wpbeginner' => 'Wpbeginner',
				'fa-wpforms' => 'Wpforms',
				'fa-envira' => 'Envira',
				'fa-universal-access' => 'Universal Access',
				'fa-wheelchair-alt' => 'Wheelchair Alt',
				'fa-question-circle-o' => 'Question Circle O',
				'fa-blind' => 'Blind',
				'fa-audio-description' => 'Audio Description',
				'fa-volume-control-phone' => 'Volume Control Phone',
				'fa-braille' => 'Braille',
				'fa-assistive-listening-systems' => 'Assistive Listening Systems',
				'fa-asl-interpreting' => 'Asl Interpreting',
				'fa-american-sign-language-interpreting' => 'American Sign Language-interpreting',
				'fa-deafness' => 'Deafness',
				'fa-hard-of-hearing' => 'Hard Of Hearing',
				'fa-deaf' => 'Deaf',
				'fa-glide' => 'Glide',
				'fa-glide-g' => 'Glide G',
				'fa-signing' => 'Signing',
				'fa-sign-language' => 'Sign Language',
				'fa-low-vision' => 'Low Vision',
				'fa-viadeo' => 'Viadeo',
				'fa-viadeo-square' => 'Viadeo Square',
				'fa-snapchat' => 'Snapchat',
				'fa-snapchat-ghost' => 'Snapchat Ghost',
				'fa-snapchat-square' => 'Snapchat Square',
				'fa-first-order' => 'First Order',
				'fa-yoast' => 'Yoast',
				'fa-themeisle' => 'Themeisle',
				'fa-google-plus-circle' => 'Google Plus Circle',
				'fa-google-plus-official' => 'Google Plus Official',
				'fa-fa' => 'Fa',
				'fa-font-awesome' => 'Font Awesome',
				'fa-handshake-o' => 'Handshake O',
				'fa-envelope-open' => 'Envelope Open',
				'fa-envelope-open-o' => 'Envelope Open O',
				'fa-linode' => 'Linode',
				'fa-address-book' => 'Address Book',
				'fa-address-book-o' => 'Address Book O',
				'fa-vcard' => 'Vcard',
				'fa-address-card' => 'Address Card',
				'fa-vcard-o' => 'Vcard O',
				'fa-address-card-o' => 'Address Card O',
				'fa-user-circle' => 'User Circle',
				'fa-user-circle-o' => 'User Circle O',
				'fa-user-o' => 'User O',
				'fa-id-badge' => 'Id Badge',
				'fa-drivers-license' => 'Drivers License',
				'fa-id-card' => 'Id Card',
				'fa-drivers-license-o' => 'Drivers License O',
				'fa-id-card-o' => 'Id Card O',
				'fa-quora' => 'Quora',
				'fa-free-code-camp' => 'Free Code Camp',
				'fa-telegram' => 'Telegram',
				'fa-thermometer-4' => 'Thermometer 4',
				'fa-thermometer' => 'Thermometer',
				'fa-thermometer-full' => 'Thermometer Full',
				'fa-thermometer-3' => 'Thermometer 3',
				'fa-thermometer-three-quarters' => 'Thermometer Three Quarters',
				'fa-thermometer-2' => 'Thermometer 2',
				'fa-thermometer-half' => 'Thermometer Half',
				'fa-thermometer-1' => 'Thermometer 1',
				'fa-thermometer-quarter' => 'Thermometer Quarter',
				'fa-thermometer-0' => 'Thermometer 0',
				'fa-thermometer-empty' => 'Thermometer Empty',
				'fa-shower' => 'Shower',
				'fa-bathtub' => 'Bathtub',
				'fa-s15' => 'S15',
				'fa-bath' => 'Bath',
				'fa-podcast' => 'Podcast',
				'fa-window-maximize' => 'Window Maximize',
				'fa-window-minimize' => 'Window Minimize',
				'fa-window-restore' => 'Window Restore',
				'fa-times-rectangle' => 'Times Rectangle',
				'fa-window-close' => 'Window Close',
				'fa-times-rectangle-o' => 'Times Rectangle O',
				'fa-window-close-o' => 'Window Close O',
				'fa-bandcamp' => 'Bandcamp',
				'fa-grav' => 'Grav',
				'fa-etsy' => 'Etsy',
				'fa-imdb' => 'Imdb',
				'fa-ravelry' => 'Ravelry',
				'fa-eercast' => 'Eercast',
				'fa-microchip' => 'Microchip',
				'fa-snowflake-o' => 'Snowflake O',
				'fa-superpowers' => 'Superpowers',
				'fa-wpexplorer' => 'Wpexplorer',
				'fa-meetup' => 'Meetup'
			);

			ksort( $icons );

			$message = __( 'Enable all plugin features','yith-woocommerce-wishlist' );
			$message2 = __( 'Be sure to select at least one option in the Wishlist page menu in this settings page', 'yith-woocommerce-wishlist' );
			$message3 = _x( 'Also, please read the plugin', '[Part of]: Also, please read the plugin documentation', 'yith-woocommerce-wishlist' );
			$documentation = _x( 'documentation', '[Part Of]: Also, please read the plugin documentation', 'yith-woocommerce-wishlist' );
			$url = esc_url( $this->doc_url );

			$options['general_settings'] = array(

				'section_general_settings_videobox' => array(
					'name'    => __( 'Upgrade to the PREMIUM VERSION', 'yith-woocommerce-wishlist' ),
					'type'    => 'videobox',
					'default' => array(
						'plugin_name'               => 'YITH WooCommerce Wishlist',
						'title_first_column'        => __( 'Discover the Advanced Features', 'yith-woocommerce-wishlist' ),
						'description_first_column'  => __( 'Upgrade to the PREMIUM VERSION of YITH WOOCOMMERCE WISHLIST to benefit from all features!', 'yith-woocommerce-wishlist' ),
						'video'                     => array(
							'video_id'          => '118797844',
							'video_image_url'   => YITH_WCWL_URL . '/assets/images/video-thumb.jpg',
							'video_description' => '',
						),
						'title_second_column'       => __( 'Get Support and Pro Features', 'yith-woocommerce-wishlist' ),
						'description_second_column' => __( 'By purchasing the premium version of the plugin, you will take advantage of the advanced features of the product and you will get one year of free updates and support through our platform available 24h/24.', 'yith-woocommerce-wishlist' ),
						'button'                    => array(
							'href'  => $this->get_premium_landing_uri(),
							'title' => 'Get Support and Pro Features'
						)
					),
					'id'      => 'yith_wcwl_general_videobox'
				),

				'general_section_start' => array(
					'name' => __( 'General Settings', 'yith-woocommerce-wishlist' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcwl_general_settings'
				),

				'wishlist_enable' => array(
					'name'    => __( 'Enable YITH Wishlist', 'yith-woocommerce-wishlist' ),
					'desc'    => sprintf( '%s. <strong>%s.</strong> %s <a href="%s" target="_blank">%s</a>.', $message, $message2, $message3, $url, $documentation ),
					'id'      => 'yith_wcwl_enabled',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'default_wishlist_title' => array(
					'name'    => __( 'Default wishlist title', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_wishlist_title',
					'std'     => sprintf( __( 'My wishlist on %s', 'yith-woocommerce-wishlist' ), get_bloginfo( 'name' ) ), // for woocommerce < 2.0
					'default' => sprintf( __( 'My wishlist on %s', 'yith-woocommerce-wishlist' ), get_bloginfo( 'name' ) ), // for woocommerce >= 2.0
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'wishlist_page' => array(
					'name'     => __( 'Wishlist Page', 'yith-woocommerce-wishlist' ),
					'desc'     => __( 'Page contents: [yith_wcwl_wishlist]', 'yith-woocommerce-wishlist' ),
					'id'       => 'yith_wcwl_wishlist_page_id',
					'type'     => 'single_select_page',
					'default'  => '',
					'class'    => 'chosen_select_nostd',
					'css'      => 'min-width:300px;',
					'desc_tip' => false,
				),
				'add_to_wishlist_position' => array(
					'name'     => __( 'Position', 'yith-woocommerce-wishlist' ),
					'desc'     => __( 'You can add the button in variable products only after the "Add to Cart" button or using the shortcode [yith_wcwl_add_to_wishlist].', 'yith-woocommerce-wishlist' ),
					'id'       => 'yith_wcwl_button_position',
					'type'     => 'select',
					'class'    => 'chosen_select',
					'css'      => 'min-width:300px;',
					'options'  => array(
						'add-to-cart' => __( 'After "Add to cart"', 'yith-woocommerce-wishlist' ),
						'thumbnails'  => __( 'After thumbnails', 'yith-woocommerce-wishlist' ),
						'summary'     => __( 'After summary', 'yith-woocommerce-wishlist' ),
						'shortcode'   => __( 'Use shortcode', 'yith-woocommerce-wishlist' )
					),
					'desc_tip' => true
				),
				'redirect_to_cart' => array(
					'name'    => __( 'Redirect to cart', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Redirect to cart page if "Add to cart" button is clicked in the wishlist page.', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_redirect_cart',
					'default' => 'no',
					'type'    => 'checkbox'
				),
				'remove_after_add_to_cart' => array(
					'name'    => __( 'Remove if added to the cart', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Remove the product from the wishlist if it has been added to the cart.', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_remove_after_add_to_cart',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'add_to_wishlist_text' => array(
					'name'    => __( '"Add to Wishlist" text', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_add_to_wishlist_text',
					'default' => __( 'Add to Wishlist', 'yith-woocommerce-wishlist' ),
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'browse_wishlist_text' => array(
					'name'    => __( '"Browse wishlist" text', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_browse_wishlist_text',
					'default' => __( 'Browse Wishlist', 'yith-woocommerce-wishlist' ),
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'already_in_wishlist_text' => array(
					'name'    => __( '"Product already in wishlist" text', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_already_in_wishlist_text',
					'default' => __( 'The product is already in the wishlist!', 'yith-woocommerce-wishlist' ),
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'product_added_text' => array(
					'name'    => __( '"Product added" text', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_product_added_text',
					'default' => __( 'Product added!', 'yith-woocommerce-wishlist' ),
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'add_to_cart_text' => array(
					'name'    => __( '"Add to Cart" text', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_add_to_cart_text',
					'default' => __( 'Add to Cart', 'yith-woocommerce-wishlist' ),
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'show_unit_price' => array(
					'name'    => __( 'Show Unit price', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show unit price for each product in wishlist', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_price_show',
					'default' => 'yes',
					'type'    => 'checkbox',
					'css'     => 'min-width:300px;',
				),
				'show_add_to_cart' => array(
					'name'    => __( 'Show "Add to Cart" button', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "Add to Cart" button for each product in wishlist', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_add_to_cart_show',
					'default' => 'yes',
					'type'    => 'checkbox',
					'css'     => 'min-width:300px;',
				),
				'show_stock_status' => array(
					'name'    => __( 'Show Stock status', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "In stock" or "Out of stock" label for each product in wishlist', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_stock_show',
					'default' => 'yes',
					'type'    => 'checkbox',
					'css'     => 'min-width:300px;',
				),
				'show_dateadded' => array(
					'name'    => __( 'Show Date of addition', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show the date when users have added a product to the wishlist', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_show_dateadded',
					'default' => 'no',
					'type'    => 'checkbox',
					'css'     => 'min-width:300px;',
				),
				'repeat_remove_button' => array(
					'name'    => __( 'Add second remove button', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Add a second remove button in the last column, with extended label', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_repeat_remove_button',
					'default' => 'no',
					'type'    => 'checkbox',
					'css'     => 'min-width:300px;',
				),

				'general_section_end' => array(
					'type' => 'sectionend',
					'id' => 'yith_wcwl_general_settings'
				)
			);

			$options['styles'] = array(
				'styles_section_start' => array(
					'name' => __( 'Styles', 'yith-woocommerce-wishlist' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcwl_styles'
				),

				'use_buttons' => array(
					'name'    => __( 'Use buttons', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Use buttons instead of simple anchors.', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_use_button',
					'default' => 'no',
					'type'    => 'checkbox'
				),
				'custom_css' => array(
					'name'    => __( 'Custom CSS', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_custom_css',
					'css'     => 'width:100%; height: 75px;',
					'default' => '',
					'type'    => 'textarea'
				),
				'use_theme_style' => array(
					'name'    => __( 'Use theme style', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Use the theme style.', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_frontend_css',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'rounded_buttons' => array(
					'name'    => __( 'Rounded buttons', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Make button corners rounded', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_rounded_corners',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'add_to_wishlist_icon' => array(
					'name'     => __( '"Add to Wishlist" icon', 'yith-woocommerce-wishlist' ),
					'desc'     => __( 'Add an icon to the "Add to Wishlist" button', 'yith-woocommerce-wishlist' ),
					'id'       => 'yith_wcwl_add_to_wishlist_icon',
					'css'      => 'min-width:300px;width:300px;',
					'default'  => apply_filters( 'yith_wcwl_add_to_wishlist_std_icon', 'none' ),
					'type'     => 'select',
					'class'    => 'chosen_select',
					'desc_tip' => true,
					'options'  => array( 'none' => 'None' ) + $icons
				),
				'add_to_cart_icon' => array(
					'name'     => __( '"Add to Cart" icon', 'yith-woocommerce-wishlist' ),
					'desc'     => __( 'Add an icon to the "Add to Cart" button', 'yith-woocommerce-wishlist' ),
					'id'       => 'yith_wcwl_add_to_cart_icon',
					'css'      => 'min-width:300px;width:300px;',
					'default'  => apply_filters( 'yith_wcwl_add_to_cart_std_icon', 'fa-shopping-cart' ),
					'type'     => 'select',
					'class'    => 'chosen_select',
					'desc_tip' => true,
					'options'  => array( 'none' => 'None' ) + $icons
				),

				'styles_section_end' => array(
					'type' => 'sectionend',
					'id' => 'yith_wcwl_styles'
				)
			);

			$options['socials_share'] = array(
				'socials_section_start' => array(
					'name' => __( 'Social Networks & Share', 'yith-woocommerce-wishlist' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcwl_socials_share'
				),

				'share_on_facebook' => array(
					'name'    => __( 'Share on Facebook', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "Share on Facebook" button', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_share_fb',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'share_on_twitter' => array(
					'name'    => __( 'Tweet on Twitter', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "Tweet on Twitter" button', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_share_twitter',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'share_on_pinterest' => array(
					'name'    => __( 'Pin on Pinterest', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "Pin on Pinterest" button', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_share_pinterest',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'share_on_googleplus' => array(
					'name'    => __( 'Share on Google+', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "Share on Google+" button', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_share_googleplus',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
				'share_by_email' => array(
					'name'    => __( 'Share by Email', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'Show "Share by Email" button', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_share_email',
					'default' => 'yes',
					'type'    => 'checkbox'
				),
                'share_by_whatsapp' => array(
                    'name'    => __( 'Share by WhatsApp', 'yith-woocommerce-wishlist' ),
                    'desc'    => __( 'Show "Share by WhatsApp" button ( only on phones )', 'yith-woocommerce-wishlist' ),
                    'id'      => 'yith_wcwl_share_whatsapp',
                    'default' => 'yes',
                    'type'    => 'checkbox'
                ),
				'socials_title' => array(
					'name'    => __( 'Social title', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_socials_title',
					'default' => sprintf( __( 'My wishlist on %s', 'yith-woocommerce-wishlist' ), get_bloginfo( 'name' ) ),
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),
				'socials_text' =>  array(
					'name'    => __( 'Social text', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'It will be used by Twitter and Pinterest. Use <strong>%wishlist_url%</strong> where you want to show the URL of your wishlist.', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_socials_text',
					'css'     => 'width:100%; height: 75px;',
					'default' => '',
					'type'    => 'textarea'
				),
				'socials_image' => array(
					'name'    => __( 'Social image URL', 'yith-woocommerce-wishlist' ),
					'desc'    => __( 'It will be used by Pinterest.', 'yith-woocommerce-wishlist' ),
					'id'      => 'yith_wcwl_socials_image_url',
					'default' => '',
					'type'    => 'text',
					'css'     => 'min-width:300px;',
				),

				'socials_section_end' => array(
					'type' => 'sectionend',
					'id' => 'yith_wcwl_styles'
				)
			);

			$yith_wfbt_thickbox = YITH_WCWL_URL . 'assets/images/landing/yith-wfbt-slider.jpg';
			$yith_wfbt_promo = sprintf( __( 'If you want to take advantage of this feature, you could consider to purchase the %s.', 'yith-woocommerce-wishlist' ), '<a href="https://yithemes.com/themes/plugins/yith-woocommerce-frequently-bought-together/">YITH WooCommerce Frequently Bought Together Plugin</a>' );

			$options['yith_wfbt_integration'] = array(

				'yith_wfbt_start' => array(
					'name' => __( 'YITH WooCommerce Frequently Bought Together Integration', 'yith-woocommerce-wishlist' ),
					'type' => 'title',
					'desc' => '',
					'id' => 'yith_wcwl_yith_wfbt'
				),

				'yith_wfbt_enable_integration' => array(
					'name'    => __( 'Enable slider in wishlist', 'yith-woocommerce-wishlist' ),
					'desc'    => sprintf( __( 'Choose to enable product slider in wishlist page with linked products (<a href="%s" class="thickbox">Example</a>). %s', 'yith-woocommerce-wishlist' ), $yith_wfbt_thickbox,  ( ! ( defined( 'YITH_WFBT' ) && YITH_WFBT ) ) ? $yith_wfbt_promo : '' ),
					'id'      => 'yith_wfbt_enable_integration',
					'default' => 'yes',
					'type'    => 'checkbox',
					'custom_attributes' => ( ! ( defined( 'YITH_WFBT' ) && YITH_WFBT ) ) ? array( 'disabled' => 'disabled' ) : false
				),

				'yith_wfbt_end' => array(
					'type' => 'sectionend',
					'id' => 'yith_wcwl_yith_wfbt'
				)

			);

			return apply_filters( 'yith_wcwl_admin_options', $options );
		}

		/**
		 * Add colors options to the panel.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		private function _styles_options() {
			$colors = maybe_unserialize( get_option( 'yith_wcwl_frontend_css_colors' ) );

			foreach ( YITH_WCWL_Init()->colors_options as $color => $attrs ) {
				if ( ! isset( $colors[$color] ) ) {
					$colors[$color] = $attrs;
				}
			}

			?>
			<div class="color-panel">
				<div class="clear"></div>
				<h4><?php _e( '"Add to wishlist" button', 'yith-woocommerce-wishlist' ) ?></h4>
				<?php

				yith_frontend_css_color_picker( __( 'Background', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_wishlist_background', $colors['add_to_wishlist']['background'] );
				yith_frontend_css_color_picker( __( 'Text', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_wishlist_color', $colors['add_to_wishlist']['color'] );
				yith_frontend_css_color_picker( __( 'Border', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_wishlist_border_color', $colors['add_to_wishlist']['border_color'] );

				?>
				<div class="clear" style="height:10px;"></div>
				<?php

				// hover
				yith_frontend_css_color_picker( __( 'Background (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_wishlist_hover_background', $colors['add_to_wishlist_hover']['background'] );
				yith_frontend_css_color_picker( __( 'Text (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_wishlist_hover_color', $colors['add_to_wishlist_hover']['color'] );
				yith_frontend_css_color_picker( __( 'Border (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_wishlist_hover_border_color', $colors['add_to_wishlist_hover']['border_color'] );

				?>
				<div class="clear" style="height:30px;"></div>
				<h4><?php _e( '"Add to Cart" button', 'yith-woocommerce-wishlist' ) ?></h4>
				<?php

				yith_frontend_css_color_picker( __( 'Background', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_cart_background', $colors['add_to_cart']['background'] );
				yith_frontend_css_color_picker( __( 'Text', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_cart_color', $colors['add_to_cart']['color'] );
				yith_frontend_css_color_picker( __( 'Border', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_cart_border_color', $colors['add_to_cart']['border_color'] );

				?>
				<div class="clear" style="height:10px;"></div>
				<?php

				// hover
				yith_frontend_css_color_picker( __( 'Background (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_cart_hover_background', $colors['add_to_cart_hover']['background'] );
				yith_frontend_css_color_picker( __( 'Text (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_cart_hover_color', $colors['add_to_cart_hover']['color'] );
				yith_frontend_css_color_picker( __( 'Border (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_add_to_cart_hover_border_color', $colors['add_to_cart_hover']['border_color'] );

				?>
				<div class="clear" style="height:30px;"></div>
				<h4><?php _e( '"Style 1" button', 'yith-woocommerce-wishlist' ) ?></h4>
				<?php

				yith_frontend_css_color_picker( __( 'Background', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_1_background', $colors['button_style_1']['background'] );
				yith_frontend_css_color_picker( __( 'Text', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_1_color', $colors['button_style_1']['color'] );
				yith_frontend_css_color_picker( __( 'Border', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_1_border_color', $colors['button_style_1']['border_color'] );

				?>
				<div class="clear" style="height:10px;"></div>
				<?php

				// hover
				yith_frontend_css_color_picker( __( 'Background (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_1_hover_background', $colors['button_style_1_hover']['background'] );
				yith_frontend_css_color_picker( __( 'Text (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_1_hover_color', $colors['button_style_1_hover']['color'] );
				yith_frontend_css_color_picker( __( 'Border (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_1_hover_border_color', $colors['button_style_1_hover']['border_color'] );

				?>
				<div class="clear" style="height:30px;"></div>
				<h4><?php _e( '"Style 2" button', 'yith-woocommerce-wishlist' ) ?></h4>
				<?php

				yith_frontend_css_color_picker( __( 'Background', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_2_background', $colors['button_style_2']['background'] );
				yith_frontend_css_color_picker( __( 'Text', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_2_color', $colors['button_style_2']['color'] );
				yith_frontend_css_color_picker( __( 'Border', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_2_border_color', $colors['button_style_2']['border_color'] );

				?>
				<div class="clear" style="height:10px;"></div>
				<?php

				// hover
				yith_frontend_css_color_picker( __( 'Background (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_2_hover_background', $colors['button_style_2_hover']['background'] );
				yith_frontend_css_color_picker( __( 'Text (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_2_hover_color', $colors['button_style_2_hover']['color'] );
				yith_frontend_css_color_picker( __( 'Border (hover)', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_button_style_2_hover_border_color', $colors['button_style_2_hover']['border_color'] );

				?>
				<div class="clear" style="height:30px;"></div>
				<h4><?php _e( 'Wishlist table', 'yith-woocommerce-wishlist' )?></h4>
				<?php

				yith_frontend_css_color_picker( __( 'Background', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_wishlist_table_background', $colors['wishlist_table']['background'] );
				yith_frontend_css_color_picker( __( 'Text', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_wishlist_table_color', $colors['wishlist_table']['color'] );
				yith_frontend_css_color_picker( __( 'Border', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_wishlist_table_border_color', $colors['wishlist_table']['border_color'] );

				?>
				<div class="clear" style="height:30px;"></div>
				<h4><?php _e( 'Headers', 'yith-woocommerce-wishlist' ) ?></h4>
				<?php

				yith_frontend_css_color_picker( __( 'Background color', 'yith-woocommerce-wishlist' ), 'yith_wcwl_color_headers_background', $colors['headers']['background'] );

				do_action( 'yith_wcwl_admin_color_pickers' );

				?>
				<div class="clear"></div>
			</div>
			<div class="clear" style="height:30px;"></div>
		<?php
		}

		/* === WISHLIST SUBPANEL SECTION === */

		/**
		 * Register wishlist panel
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_panel() {

			$args = array(
				'create_menu_page' => true,
				'parent_slug'   => '',
				'page_title'    => __( 'Wishlist', 'yith-woocommerce-wishlist' ),
				'menu_title'    => __( 'Wishlist', 'yith-woocommerce-wishlist' ),
				'capability'    => apply_filters( 'yith_wcwl_settings_panel_capability', 'manage_options' ),
				'parent'        => '',
				'parent_page'   => 'yith_plugin_panel',
				'page'          => 'yith_wcwl_panel',
				'admin-tabs'    => $this->available_tabs,
				'options-path'  => YITH_WCWL_DIR . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCWL_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Load admin style.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			global $woocommerce, $pagenow;

			if( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcwl_panel' ) {
				wp_enqueue_style( 'yith-wcwl-admin' );
				wp_enqueue_script( 'yith-wcwl-admin' );
			}
		}

		/**
		 * Prints tab premium of the plugin
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function print_premium_tab() {
			$premium_tab = YITH_WCWL_DIR . 'templates/admin/wishlist-panel-premium.php';

			if( file_exists( $premium_tab ) ){
				include( $premium_tab );
			}
		}

		/* === POINTER SECTION === */

		/**
		 * Register pointers for notify plugin updates to user
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_pointer(){

			if( ! class_exists( 'YIT_Pointers' ) ){
				include_once( 'plugin-fw/lib/yit-pointers.php' );
			}

			$args[] = array(
				'screen_id'     => 'plugins',
				'pointer_id' => 'yith_wcwl_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'Wishlist Activated', 'yith-woocommerce-wishlist' ),
					apply_filters( 'yith_wcwl_activated_pointer_content', sprintf( __( 'In the YIT Plugin tab you can find the Wishlist options. With this menu, you can access to all the settings of our plugins that you have activated. Wishlist is available in an outstanding PREMIUM version with many new options, <a href="%s">discover it now</a>.', 'yith-woocommerce-wishlist' ), $this->get_premium_landing_uri() ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'  => YITH_WCWL_INIT
			);

			$args[] = array(
				'screen_id'     => 'update',
				'pointer_id' => 'yith_wcwl_panel',
				'target'     => '#toplevel_page_yit_plugin_panel',
				'content'    => sprintf( '<h3> %s </h3> <p> %s </p>',
					__( 'Wishlist Updated', 'yith-woocommerce-wishlist' ),
					apply_filters( 'yith_wcwl_updated_pointer_content', sprintf( __( 'From now on, you can find all the options of Wishlist under YIT Plugin -> Wishlist instead of WooCommerce -> Settings -> Wishlist, as in the previous version. When one of our plugins is updated, a new voice will be added to this menu. Wishlist has been updated with new available options, <a href="%s">discover the PREMIUM version.</a>', 'yith-woocommerce-wishlist' ), $this->get_premium_landing_uri() ) )
				),
				'position'   => array( 'edge' => 'left', 'align' => 'center' ),
				'init'  => YITH_WCWL_INIT
			);

			YIT_Pointers()->register( $args );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Admin_Init class
 *
 * @return \YITH_WCWL_Admin_Init
 * @since 2.0.0
 */
function YITH_WCWL_Admin_Init(){
	return YITH_WCWL_Admin_Init::get_instance();
}