<?php
/**
 * Privacy class; added to let customer export personal data
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.2.2
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCWL_Privacy' ) ) {
	/**
	 * YITH WCWL Exporter
	 *
	 * @since 2.2.2
	 */
	class YITH_WCWL_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCWL_Privacy
		 * @since 2.2.2
		 */
		public function __construct() {

			parent::__construct( 'YITH WooCommerce Wishlist' );

			// set up wishlist data exporter
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

			// set up wishlist data eraser
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Retrieves privacy example text for wishlist plugin
		 *
		 * @return string Privacy message
		 * @since 2.2.2
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch( $section ){
				case 'collect_and_store':
					$content =  '<p>' . __( 'While you visit our site, we’ll track:', 'yith-woocommerce-wishlist' ) . '</p>' .
					            '<ul>' .
					            '<li>' . __( 'Products you’ve added to the wishlist: we’ll use this to show you and other users your favourite products, and to create targeted email campaigns.', 'yith-woocommerce-wishlist' ) . '</li>' .
					            '<li>' . __( 'Wishlists you’ve created: we’ll keep track of the wishlists you create, and make them visible to the store’s staff', 'yith-woocommerce-wishlist' ) . '</li>' .
					            '</ul>' .
					            '<p>' . __( 'We’ll also use cookies to keep track of wishlist contents while you’re browsing our site.', 'yith-woocommerce-wishlist' ) . '</p>';
					break;
				case 'has_access':
					$content =  '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-wishlist' ) . '</p>' .
					            '<ul>' .
					            '<li>' . __( 'Wishlist details, such as products added, date of addition, name and privacy settings of your wishlists', 'yith-woocommerce-wishlist' ) . '</li>' .
					            '</ul>' .
					            '<p>' . __( 'Our team members have access to this information to offer you better deals for the products you love.', 'yith-woocommerce-wishlist' ) . '</p>';
					break;
				case 'share':
				case 'payments':
				default:
					break;
			}

			return apply_filters( 'yith_wcwl_privacy_policy_content', $content, $section );
		}

		/**
		 * Register exporters for wishlist plugin
		 *
		 * @param $exporters array Array of currently registered exporters
		 * @return array Array of filtered exporters
		 * @since 2.2.2
		 */
		public function register_exporter( $exporters ) {
			$exporters['yith_wcwl_exporter'] = array(
				'exporter_friendly_name' => __( 'Customer Wishlists', 'yith-woocommerce-wishlist' ),
				'callback' => array( $this, 'wishlist_data_exporter' )
			);

			return $exporters;
		}

		/**
		 * Register eraser for wishlist plugin
		 *
		 * @param $erasers array Array of currently registered erasers
		 * @return array Array of filtered erasers
		 * @since 2.2.2
		 */
		public function register_eraser( $erasers ) {
			$erasers['yith_wcwl_eraser'] = array(
				'eraser_friendly_name' => __( 'Customer Wishlists', 'yith-woocommerce-wishlist' ),
				'callback' => array( $this, 'wishlist_data_eraser' )
			);

			return $erasers;
		}

		/**
		 * Export user wishlists (only available for authenticated users' wishlist)
		 *
		 * @param $email_address string Email of the users that requested export
		 * @param $page int Current page processed
		 * @return array Array of data to export
		 * @since 2.2.2
		 */
		public function wishlist_data_exporter( $email_address, $page ) {
			$done           = true;
			$page           = (int) $page;
			$offset         = 10 * ( $page -1 );
			$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
			$data_to_export = array();

			if ( $user instanceof WP_User ) {
				$wishlists = YITH_WCWL()->get_wishlists( array(
					'limit'   => 10,
					'offset'  => $offset,
					'user_id' => $user->ID,
					'orderby' => 'ID',
					'order'   => 'ASC'
				) );

				if ( 0 < count( $wishlists ) ) {
					foreach ( $wishlists as $wishlist ) {
						$data_to_export[] = array(
							'group_id'    => 'yith_wcwl_wishlist',
							'group_label' => __( 'Wishlists', 'yith-woocommerce-wishlist' ),
							'item_id'     => 'wishlist-' . $wishlist['ID'],
							'data'        => $this->get_wishlist_personal_data( $wishlist ),
						);
					}
					$done = 10 > count( $wishlists );
				} else {
					$done = true;
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Deletes user wishlists (only available for authenticated users' wishlist)
		 *
		 * @param $email_address string Email of the users that requested export
		 * @param $page int Current page processed
		 * @return array Result of the operation
		 * @since 2.2.2
		 */
		public function wishlist_data_eraser( $email_address, $page ) {
			global $wpdb;

			$page            = (int) $page;
			$offset          = 10 * ( $page -1 );
			$user            = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
			$response        = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			if( defined( 'YITH_WCWL_PREMIUM' ) ) {

				$wishlists = YITH_WCWL()->get_wishlists( array(
					'limit'   => 10,
					'offset'  => $offset,
					'user_id' => $user->ID,
					'orderby' => 'ID',
					'order'   => 'ASC'
				) );

				if ( 0 < count( $wishlists ) ) {
					foreach ( $wishlists as $wishlist ) {
						if ( apply_filters( 'yith_wcwl_privacy_erase_wishlist_personal_data', true, $wishlist ) ) {
							do_action( 'yith_wcwl_privacy_before_remove_wishlist_personal_data', $wishlist );

							YITH_WCWL_Premium()->remove_wishlist( $wishlist['ID'] );

							do_action( 'yith_wcwl_privacy_remove_wishlist_personal_data', $wishlist );

							/* Translators: %s Order number. */
							$response['messages'][]    = sprintf( __( 'Removed wishlist %s.', 'yith-woocommerce-wishlist' ), $wishlist['wishlist_token'] );
							$response['items_removed'] = true;
						} else {
							/* Translators: %s Order number. */
							$response['messages'][]     = sprintf( __( 'Wishlist %s has been retained.', 'yith-woocommerce-wishlist' ), $wishlist['wishlist_token'] );
							$response['items_retained'] = true;
						}
					}
					$response['done'] = 10 > count( $wishlists );
				} else {
					$response['done'] = true;
				}
			} else {
				$count = $wpdb->delete( $wpdb->yith_wcwl_items, array( 'user_id' => $user->ID ) );

				$response['messages'][] = __( 'Removed default user\'s wishlist', 'yith-woocommerce-wishlist' );
				$response['items_removed'] = $count;
				$response['done'] = true;
			}

			return $response;
		}

		/**
		 * Retrieves data to export for each user's wishlist
		 *
		 * @param $wishlist array Wishlist processed
		 * @return array Data to export
		 * @since 2.2.2
		 */
		protected function get_wishlist_personal_data( $wishlist ) {
			$personal_data   = array();
			$props_to_export = apply_filters( 'yith_wcwl_privacy_export_wishlist_personal_data_props', array(
				'wishlist_token'   => __( 'Token', 'yith-woocommerce-wishlist' ),
				'wishlist_url'     => __( 'Wishlist url', 'yith-woocommerce-wishlist' ),
				'wishlist_name'    => __( 'Title', 'yith-woocommerce-wishlist' ),
				'dateadded'        => _x( 'Created on', 'date wishlist was created', 'yith-woocommerce-wishlist' ),
				'wishlist_privacy' => __( 'Visibility', 'yith-woocommerce-wishlist' ),
				'items'            => __( 'Items Added', 'yith-woocommerce-wishlist' ),
			), $wishlist );

			foreach ( $props_to_export as $prop => $name ) {
				$value = '';

				switch ( $prop ) {
					case 'items':
						$item_names = array();
						$items = YITH_WCWL()->get_products( array(
							'wishlist_id' => $wishlist['ID']
						) );

						foreach ( $items as $item ) {
							$product = wc_get_product( $item['prod_id'] );

							if( ! $product ){
								continue;
							}

							$item_name = $product->get_name() . ' x ' . $item['quantity'];

							if( $item['dateadded'] ){
								$item_name .= ' (on: ' . date_i18n( 'd F Y', strtotime( $item['dateadded'] ) ) . ')';
							}

							$item_names[] = $item_name;
						}

						$value = implode( ', ', $item_names );
						break;
					case 'wishlist_url':
						$wishlist_url = YITH_WCWL()->get_wishlist_url( 'view/' . $wishlist['wishlist_token'] );

						$value = sprintf( '<a href="%1$s">%1$s</a>', $wishlist_url );
						break;
					case 'wishlist_name':
						$wishlist_name = $wishlist['wishlist_name'];

						$value = $wishlist_name ? $wishlist_name : get_option( 'yith_wcwl_wishlist_title' );
						break;
					case 'dateadded':
						$date = $wishlist['dateadded'];

						$value = date_i18n( 'd F Y', strtotime( $date ) );
						break;
					case 'wishlist_privacy':
						$privacy = $wishlist['wishlist_privacy'];

						if( $privacy == 1 ){
							$privacy_label = apply_filters( 'yith_wcwl_shared_wishlist_visibility', __( 'Shared', 'yith-woocommerce-wishlist' ) );
						}
						elseif( $privacy == 2 ){
							$privacy_label = apply_filters( 'yith_wcwl_private_wishlist_visibility', __( 'Private', 'yith-woocommerce-wishlist' ) );
						}
						else{
							$privacy_label = apply_filters( 'yith_wcwl_public_wishlist_visibility', __( 'Public', 'yith-woocommerce-wishlist' ) );
						}

						$value = $privacy_label;
						break;
					default:
						if ( isset( $wishlist[ $prop ] ) ) {
							$value = $wishlist[ $prop ];
						}
						break;
				}

				$value = apply_filters( 'yith_wcwl_privacy_export_wishlist_personal_data_prop', $value, $prop, $wishlist );

				if ( $value ) {
					$personal_data[] = array(
						'name'  => $name,
						'value' => $value,
					);
				}
			}

			$personal_data = apply_filters( 'yith_wcwl_privacy_export_wishlist_personal_data', $personal_data, $wishlist );

			return $personal_data;
		}
	}
}