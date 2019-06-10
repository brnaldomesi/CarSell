<?php
/**
 * Accordion dropdown
 *
 * add accordion dropdown functionality
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/function
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Menu badges management class
 *
 * @since 1.0.0
 */
class cherry_wc_account_dropdown {

	public $account_options = array();

	function __construct() {

		$this->account_options = array( 
			'show_account'        => 'show', 
			'not_logged_in_label' => __( 'My Account', 'cherry-woocommerce-package' ), 
			'logged_in_label'     => __( 'My Account', 'cherry-woocommerce-package' ),
			'account_list_menu'   => '', 
			'show_login_register' => 'show', 
			'login_label'         => __( 'Log In/Register', 'cherry-woocommerce-package' ), 
			'logout_label'        => __( 'Logout', 'cherry-woocommerce-package' )
		);

		add_action( 'cherry_woocommerce_account', array( $this, 'dropdown_frontend' ) );
	}

	/**
	 * show dropdown account content
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function dropdown_frontend() {
		$this->prepare_options();
		if ( 'show' != $this->account_options['show_account'] ) {
			return;
		}
		cherry_wc_get_template_part( 'account-dropdown' );
	}

	/**
	 * prepare account dropdown options
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function prepare_options() {

		foreach ( $this->account_options as $option_name => $option_val ) {
			$this->account_options[$option_name] = cherry_wc_get_option( $option_name, $option_val );
		}

	}

	/**
	 * show account items list
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function show_account_list() {
		if ( !$this->account_options['account_list_menu'] ) {
			$this->default_account_list();
			return;
		}

	   /**
		* Displays a navigation menu
		* @param array $args Arguments
		*/
		$args = apply_filters( 'cherry_wc_account_menu_args', array(
			'menu'       => $this->account_options['account_list_menu'],
			'menu_class' => 'cherry-wc-account_list',
			'depth'      => -1
		) );
		
		wp_nav_menu( $args );

	}

	/**
	 * show default account list (if menu in options not selected)
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function default_account_list() {
		$orders_page = get_option( 'woocommerce_myaccount_page_id' );
		if ( $orders_page ) {
			$orders_link = get_permalink( $orders_page );
		} else {
			$orders_link = '';
		}

		$items = array(
			'orders' => array(
				'link'  => esc_url( $orders_link ),
				'label' => __( 'Orders', 'cherry-woocommerce-package' )
			)
		);

		if ( defined( 'YITH_WOOCOMPARE' ) ) {
			$items['cherry-compare'] = array(
				'link'  => '#',
				'label' => __( 'Compare', 'cherry-woocommerce-package' )
			);
		}

		if ( defined( 'YITH_WCWL' ) ) {
			$wishlist_page = get_option( 'yith_wcwl_wishlist_page_id' );
			
			if ( $wishlist_page ) {
				$wishlist_link = get_permalink( $wishlist_page );
			} else {
				$wishlist_link = '';
			}

			$items['cherry-wishlist'] = array(
				'link'  => $wishlist_link,
				'label' => __( 'Wishlist', 'cherry-woocommerce-package' )
			);
		}

		if ( !$items ) {
			return;
		}
		?>
		<ul class="cherry-wc-account_list">
		<?php
		foreach ( $items as $item_class => $item_data ) {
			if ( empty($item_data) ) {
				continue;
			}
			echo '<li class="cherry-wc-account_list_item ' . $item_class . '"><a href="' . $item_data["link"] . '">' . $item_data["label"] . '</a></li>';
		}
		?>
		</ul>
		<?php
	}

	/**
	 * show default account auth links
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function show_account_auth() {
		
		if ( 'hide' == $this->account_options['show_login_register'] ) {
			return;
		}

		$account_page = get_option( 'woocommerce_myaccount_page_id' );

		if ( !$account_page ) {
			return;
		}

		echo '<div class="cherry-wc-account_auth">';
			$link_url   = get_permalink( $account_page );
			$link_text  = $this->account_options['login_label'];
			$link_class = 'not-logged';
			if ( is_user_logged_in() ) {
				$link_url   = wp_logout_url( get_permalink( $account_page ) );
				$link_text  = $this->account_options['logout_label'];
				$link_class = 'logged';
			}
			echo apply_filters( "cherry_wc_account_auth_html", "<a href='$link_url' class='$link_class'>$link_text</a>", $link_url, $link_text, $link_class );
		echo '</div>';

	}

}

$GLOBALS['cherry_wc_account_dropdown'] = new cherry_wc_account_dropdown();