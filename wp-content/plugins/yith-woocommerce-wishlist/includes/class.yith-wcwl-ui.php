<?php
/**
 * Shortcodes class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 1.1.5
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCWL_UI' ) ) {

    /**
     * YITH_WCWL_UI class, with methods used to print user interface
     *
     * @since 1.0.0
     */
    class YITH_WCWL_UI {

        /**
         * Build the popup message HTML/jQuery.
         *
         * @return string
         * @static
         * @since 1.0.0
         */
        public static function popup_message() {
            _deprecated_function( 'popup_message', '2.0.0', 'add-to-wishlist-button.php template' );
            ob_start() ?>

            <script>
                if( !jQuery( '#yith-wcwl-popup-message' ).length ) {
                    jQuery( 'body' ).prepend(
                        '<div id="yith-wcwl-popup-message" style="display:none;">' +
                            '<div id="yith-wcwl-message"></div>' +
                            '</div>'
                    );
                }
            </script>

            <?php
            return ob_get_clean();
        }

        /**
         * Build the "Add to Wishlist" HTML
         *
         * @deprecated
         * @param string $url
         * @param string $product_type
         * @param bool $exists
         * @return string
         * @static
         * @since 1.0.0
         */
        public static function add_to_wishlist_button( $url, $product_type, $exists ) {
            _deprecated_function( 'add_to_wishlist_button', '2.0.0', 'add-to-wishlist-button.php template' );

            global $yith_wcwl, $product;
            $product_id = yit_get_product_id( $product );

            $label_option = get_option( 'yith_wcwl_add_to_wishlist_text' );
            $localize_label = function_exists( 'icl_translate' ) ? icl_translate( 'Plugins', 'plugin_yit_wishlist_button', $label_option ) : $label_option;

            $label = apply_filters( 'yith_wcwl_button_label', $localize_label );
            $icon = get_option( 'yith_wcwl_add_to_wishlist_icon' ) != 'none' ? '<i class="fa ' . get_option( 'yith_wcwl_add_to_wishlist_icon' ) . '"></i>' : '';

            $classes = get_option( 'yith_wcwl_use_button' ) == 'yes' ? 'class="add_to_wishlist single_add_to_wishlist button alt"' : 'class="add_to_wishlist"';

            $html  = '<div class="yith-wcwl-add-to-wishlist">';
            $html .= '<div class="yith-wcwl-add-button';  // the class attribute is closed in the next row

            $html .= $exists ? ' hide" style="display:none;"' : ' show"';

            $html .= '><a href="' . esc_url( add_query_arg( 'add_to_wishlist', $product_id ) ) . '" data-product-id="' . $product_id . '" data-product-type="' . $product_type . '" ' . $classes . ' >' . $icon . $label . '</a>';
            $html .= '<img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />';
            $html .= '</div>';

            $html .= '<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><span class="feedback">' . __( 'Product added!','yith-woocommerce-wishlist' ) . '</span> <a href="' . esc_url( $url ) . '">' . apply_filters( 'yith-wcwl-browse-wishlist-label', __( 'Browse Wishlist', 'yith-woocommerce-wishlist' ) ) . '</a></div>';
            $html .= '<div class="yith-wcwl-wishlistexistsbrowse ' . ( $exists ? 'show' : 'hide' ) . '" style="display:' . ( $exists ? 'block' : 'none' ) . '"><span class="feedback">' . __( 'The product is already in the wishlist!', 'yith-woocommerce-wishlist' ) . '</span> <a href="' . esc_url( $url ) . '">' . apply_filters( 'yith-wcwl-browse-wishlist-label', __( 'Browse Wishlist', 'yith-woocommerce-wishlist' ) ) . '</a></div>';
            $html .= '<div style="clear:both"></div><div class="yith-wcwl-wishlistaddresponse"></div>';

            $html .= '</div>';
            $html .= '<div class="clear"></div>';

            return $html;
        }

        /**
         * Build the "Add to cart" HTML.
         *
         * @deprecated
         * @param string $url
         * @param string $stock_status
         * @param string $type
         * @return string
         * @static
         * @since 1.0.0
         */
        public static function add_to_cart_button( $product_id, $stock_status ) {
            _deprecated_function( 'add_to_cart_button', '2.0.0', 'wc_get_template( "loop/add-to-cart.php" )' );

            global $yith_wcwl, $product;

            if ( function_exists( 'get_product' ) )
                $product = get_product( $product_id );
            else
                $product = new WC_Product( $product_id );

            $url = $product->product_type == 'external' ? $yith_wcwl->get_affiliate_product_url( $product_id ) : $yith_wcwl->get_addtocart_url( $product_id );

            $label_option = get_option( 'yith_wcwl_add_to_cart_text' );
            $localize_label = function_exists( 'icl_translate' ) ? icl_translate( 'Plugins', 'plugin_yit_wishlist_button', $label_option ) : $label_option;

            $label = $product->product_type == 'variable' ? apply_filters( 'variable_add_to_cart_text', __('Select options', 'yith-woocommerce-wishlist') ) : apply_filters( 'yith_wcwl_add_to_cart_label', $localize_label );
            $icon = get_option( 'yith_wcwl_use_button' ) == 'yes' && get_option( 'yith_wcwl_add_to_cart_icon' ) != 'none' ? '<i class="fa ' . get_option( 'yith_wcwl_add_to_cart_icon' ) . '"></i>' : '';

            $cartlink = '';
            $redirect_to_cart = get_option( 'yith_wcwl_redirect_cart' ) == 'yes' && $product->product_type != 'variable' ? 'true' : 'false';
            $style = ''; //indicates the style (background-color and font color)

            if( get_option( 'yith_wcwl_use_button' ) == 'yes' ) {
                if( $product->product_type == 'external' ) {
                    $cartlink .= '<a target="_blank" class="add_to_cart button alt" href="' . $url . '"';
                } else {
                    $cartlink .= '<a class="add_to_cart add_to_cart_from_wishlist button alt" href="' . $url . '" data-stock-status="' . $stock_status . '" data-redirect-to-cart="' . $redirect_to_cart . '"';
                }

                $cartlink .= $style . '>' . $icon . $label . '</a>';
            } else {
                if( $product->product_type == 'external' ) {
                    $cartlink .= '<a target="_blank" class="add_to_cart button alt" href="' . $url . '">' . $icon . $label . '</a>';
                } else {
                    $cartlink .= '<a class="add_to_cart add_to_cart_from_wishlist button alt" href="' . $url . '" data-stock-status="' . $stock_status . '" data-redirect-to-cart="' . $redirect_to_cart . '">' . $icon . $label . '</a>';
                }
            }

            return $cartlink;
        }

        /**
         * Build share HTML.
         *
         * @deprecated
         * @param string $url
         * @return string $string
         * @static
         * @since 1.0.0
         */
        public static function get_share_links( $url ) {
            _deprecated_function( 'get_share_links', '2.0.0', 'share.php template' );

            $normal_url = $url;
            $url = urlencode( $url );
            $title = apply_filters( 'plugin_text', urlencode( get_option( 'yith_wcwl_socials_title' ) ) );
            $twitter_summary = str_replace( '%wishlist_url%', '', get_option( 'yith_wcwl_socials_text' ) );
            $summary = urlencode( str_replace( '%wishlist_url%', $normal_url, get_option( 'yith_wcwl_socials_text' ) ) );
            $imageurl = urlencode( get_option( 'yith_wcwl_socials_image_url' ) );

            $html  = '<div class="yith-wcwl-share">';
            $html .= apply_filters( 'yith_wcwl_socials_share_title', '<span>' . __( 'Share on:', 'yith-woocommerce-wishlist' ) . '</span>' );
            $html .= '<ul>';

            if( get_option( 'yith_wcwl_share_fb' ) == 'yes' )
            { $html .= '<li style="list-style-type: none; display: inline-block;"><a target="_blank" class="facebook" href="https://www.facebook.com/sharer.php?s=100&amp;p[title]=' . $title . '&amp;p[url]=' . $url . '&amp;p[summary]=' . $summary . '&amp;p[images][0]=' . $imageurl . '" title="' . __( 'Facebook', 'yith-woocommerce-wishlist' ) . '"></a></li>'; }

            if( get_option( 'yith_wcwl_share_twitter' ) == 'yes' )
            { $html .= '<li style="list-style-type: none; display: inline-block;"><a target="_blank" class="twitter" href="https://twitter.com/share?url=' . $url . '&amp;text=' . $twitter_summary . '" title="' . __( 'Twitter', 'yith-woocommerce-wishlist' ) . '"></a></li>'; }

            if( get_option( 'yith_wcwl_share_pinterest' ) == 'yes' )
            { $html .= '<li style="list-style-type: none; display: inline-block;"><a target="_blank" class="pinterest" href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;description=' . $summary . '&media=' . $imageurl . '" onclick="window.open(this.href); return false;"></a></li>'; }

            if( get_option( 'yith_wcwl_share_googleplus' ) == 'yes' )
            { $html .= '<li style="list-style-type: none; display: inline-block;"><a target="_blank" class="googleplus" href="https://plus.google.com/share?url=' . $url . '&amp;title=' . $title . '" title="' . $title . '" onclick=\'javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;\'></a></li>'; }

            if( get_option( 'yith_wcwl_share_email' ) == 'yes' )
            { $html .= '<li style="list-style-type: none; display: inline-block;"><a class="email" href="mailto:?subject='.urlencode( apply_filters( 'yith_wcwl_email_share_subject', 'I wanted you to see this site' ) ).'&amp;body= ' . $url . '&amp;title=' . __('email', 'yith-woocommerce-wishlist') . '" title="' . $title . '" ></a></li>'; }

            $html .= '</ul>';
            $html .= '</div>';

            return $html;
        }

        /**
         * Adds classes to add-to-cart button
         *
         * @param $button_html string
         * @param $product \WC_Product
         * @return string
         * @static
         * @since 2.0.0
         */
        public static function alter_add_to_cart_button( $button_html, $product ){
            // retrieve options
            $label_option = get_option( 'yith_wcwl_add_to_cart_text' );
            $label = $product->is_type( 'variable' ) ? apply_filters( 'variable_add_to_cart_text', __('Select options', 'yith-woocommerce-wishlist') ) : apply_filters( 'yith_wcwl_add_to_cart_label', $label_option );
	        $icon = '';

            if( get_option( 'yith_wcwl_frontend_css' ) != 'yes' && get_option( 'yith_wcwl_use_button' ) == 'yes' && get_option( 'yith_wcwl_add_to_cart_icon' ) != 'none' ) {
                $icon = '<i class="fa ' . get_option( 'yith_wcwl_add_to_cart_icon' ) . '"></i>';
            }

            // customize
            $match = array();
	        preg_match( '/<a.*class="([^"]*).*>.*<\/a>/', $button_html, $match );

            if( ! empty( $match ) && isset( $match[1] ) ){
                $button_html = str_replace( $match[1], $match[1] . ' add_to_cart button alt', $button_html );
            }

            preg_match( '/<a.*?>(.*)<\/a>/', $button_html, $match );

            if( ! empty( $match ) && isset( $match[1] ) ){
                $button_html = str_replace( '>' . $match[1] . '<', '>' . $icon . ' ' . $label . '<', $button_html );
            }

            return $button_html;
        }
    }
}