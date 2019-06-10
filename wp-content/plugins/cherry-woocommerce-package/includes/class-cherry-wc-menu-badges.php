<?php
/**
 * Menu badges
 *
 * add menu badges functionality
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/class
 * @version     1.0.0
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Menu badges management class
 *
 * @since 1.0.0
 */
class cherry_wc_menu_badges {

	public $cherry_mega_menu_walker_filter = 'cherry_megamenu_walker_nav_menu_start_el';

	public $meta_keys = array(
		'text' => "_cherry_woo_badge_text",
		'type' => "_cherry_woo_badge_type"
	);

	public $badges = array();

	function __construct() {

		if ( is_admin() ) {
			
			$this->badges = apply_filters( 'cherry_woocommerce_menu_badges', array(
				'new'  => __( 'New', 'cherry-woocommerce-package' ),
				'hot'  => __( 'Hot', 'cherry-woocommerce-package' ),
				'sale' => __( 'Sale', 'cherry-woocommerce-package' ),
			));

			$this->backend();
		} else {
			$this->frontend();
		}

	}

	/**
	 * Run backend functions
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function backend() {
		
		if ( !$this->has_cherry_mega_menu() ) {
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'custom_walker' ) );
		}

		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'show_badge_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_badge_fields' ), 10, 3 );
	}

	/**
	 * Run frontend functions
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function frontend() {
		add_filter( 'nav_menu_link_attributes', array( $this, 'add_badge_atts' ), 10, 3 );
	}

	/**
	 * add badge atts to frontend
	 * @since 1.0.0
	 * 
	 * @param  array  $atts default menu item atts
	 * @param  object $item item object
	 * @param  array  $args default nav args
	 * @return array filtered atts
	 */
	public function add_badge_atts( $atts, $item, $args ) {

		/**
		 * Allow to override badges output from theme
		 * @var bollean
		 */
		$use_custom_output = apply_filters( 'cherry_woo_use_custom_badges_output', false, $atts, $item, $args );

		if ( $use_custom_output ) {
			return $atts;
		}

		$badge_text = get_post_meta( $item->ID, $this->meta_keys['text'], true );

		if ( !$badge_text ) {
			return $atts;
		}

		$badge_type = get_post_meta( $item->ID, $this->meta_keys['type'], true );

		$badge_type = empty($badge_type) ? 'hot' : $badge_type;

		$atts['data-badge-text'] = $badge_text;
		$atts['data-badge-type'] = $badge_type;

		$badge_class = ' cherry-badge ' . ' cherry-badge-' . $badge_type . ' ';

		if ( isset($atts['class']) ) {
			$atts['class'] .= $badge_class;
		} else {
			$atts['class'] = $badge_class;
		}

		return $atts;
	}

	/**
	 * Add custom walker for editor
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function custom_walker() {
		$walker = 'cherry_woo_menu_item_custom_fields_walker';
		if ( ! class_exists( $walker ) ) {
			require_once 'class-cherry-wc-edit-nav-menu-walker.php';
		}
		return $walker;
	}

	/**
	 * Badges control interface
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function show_badge_fields( $id, $item, $depth, $args ) {

		$text = get_post_meta( $item->ID, $this->meta_keys['text'], true );
		$type = get_post_meta( $item->ID, $this->meta_keys['type'], true );

		?>
		<div class="cherry_badge_controls" style="clear:both; padding:5px 0;">
			<div class="description description-thin">
				<label><?php _e( 'Menu bage text:', 'cherry-woocommerce-package' ); ?></label>
				<input type="text" value="<?php echo esc_attr( $text ); ?>" name="<?php echo $this->meta_keys['text']; ?>[<?php echo $item->ID; ?>]">
			</div>
			<div class="description description-thin">
				<label><?php _e( 'Menu bage type:', 'cherry-woocommerce-package' ); ?></label>
				<select style="width:100%;" name="<?php echo $this->meta_keys['type']; ?>[<?php echo $item->ID; ?>]">
					<optgroup>
						<option value=""><?php _e( 'Select badge type', 'cherry-woocommerce-package' ); ?></option>
					<?php
						foreach ( $this->badges as $badge_key => $badge_name ) {
							echo '<option value="' . esc_attr( $badge_key ) . '" ' . selected( $type, $badge_key, false ) . '>' . $badge_name . '</option>';
						}
					?>
					</optgroup>
				</select>
			</div>
			<div style="clear:both;"></div>
		</div>
		<?php
	}

	/**
	 * update badge data in database
	 * @since 1.0.0
	 * 
	 * @param  int   $menu_id         current menu ID
	 * @param  int   $menu_item_db_id current menu item ID
	 * @param  array $args            item args array
	 * @return void
	 */
	public function update_badge_fields( $menu_id, $menu_item_db_id, $args ) {
		foreach ( $this->meta_keys as $name => $key ) {
			$this->save_single_field( $menu_item_db_id, $key );
		}
	}

	/**
	 * save single badge meta field into database
	 * @since 1.0.0
	 * 
	 * @param  int    $menu_item_db_id current menu item ID
	 * @param  string $key             current field key
	 * @return void
	 */
	protected function save_single_field( $menu_item_db_id, $key ) {

		if ( !isset($_POST[$key]) || !isset($_POST[$key][$menu_item_db_id]) ) {
			return;
		}

		if ($_POST[$key][$menu_item_db_id]) {
			update_post_meta( $menu_item_db_id, $key, sanitize_text_field( $_POST[$key][$menu_item_db_id] ) );
		} else {
			delete_post_meta( $menu_item_db_id, $key );
		}

	}

	/**
	 * Check if Cherry Mega menu plugin inastalled and active
	 * 
	 * @since 1.0.0
	 * 
	 * @return boolean
	 */
	public function has_cherry_mega_menu() {
		return  in_array( 'cherry-megamenu/cherry-megamenu.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

}

new cherry_wc_menu_badges();