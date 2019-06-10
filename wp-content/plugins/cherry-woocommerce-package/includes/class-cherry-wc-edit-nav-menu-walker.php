<?php
/**
 * Custom Walker for Nav Menu Editor
 *
 * We're separating this class from the plugin file because Walker_Nav_Menu_Edit
 * is only loaded on the wp-admin/nav-menus.php page.
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/class
 * @version     1.0.0
 */

/**
 * Menu item custom fields walker
 *
 * @since 1.0.0
 */
class cherry_woo_menu_item_custom_fields_walker extends Walker_Nav_Menu_Edit {

	/**
	* Start the element output.
	*
	* We're injecting our custom fields after the div.submitbox
	*
	* @see Walker_Nav_Menu::start_el()
	* @since 0.1.0
	*
	* @param string $output Passed by reference. Used to append additional content.
	* @param object $item Menu item data object.
	* @param int $depth Depth of menu item. Used for padding.
	* @param array $args Menu item args.
	* @param int $id Nav menu ID.
	*/
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';

		parent::start_el( $item_output, $item, $depth, $args, $id );

		$position = '<p class="field-move';
		$extra = $this->get_fields( $item, $depth, $args, $id );
		$output .= str_replace( $position, $extra . $position, $item_output );
	}

	/**
	 * Get custom fields
	 *
	 * @access protected
	 * @since 1.0.0
	 *
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Menu item args.
	 * @param int $id Nav menu ID.
	 *
	 * @return string Form fields
	 */
	protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
		
		ob_start();
		
		/**
		 * Get menu item custom fields from plugins/themes
		 *
		 * @since 1.0.0
		 *
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param array $args Menu item args.
		 * @param int $id Nav menu ID.
		 */
		do_action( 'wp_nav_menu_item_custom_fields', $id, $item, $depth, $args );

		return ob_get_clean();

	}
}