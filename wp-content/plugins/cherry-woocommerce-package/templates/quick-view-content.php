<?php	
/**
 * Quick view content template
 *
 * @author 		Cherry Team
 * @category 	Core
 * @package 	cherry-woocommerce-package/templates
 * @version     1.1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product, $woocommerce, $post;
?>
<div class="cherry-quick-view-wrap">
	<?php
		/**
		 * Hook fires before main quick view content
		 * @since 1.1.0
		 */
		do_action( 'cherry_quick_view_content_start' );
	?>
	<div class="cherry-quick-view-images">
		<?php 
			/**
			 * cherry_quick_view_content_images hook
			 *
			 * @hooked cherry_wc_quick_view_sale
			 * @hooked cherry_wc_quick_view_images
			 */
			do_action( 'cherry_quick_view_content_images' );
		?>
	</div>
	<div class="cherry-quick-view-data">
		<?php 
			/**
			 * cherry_quick_view_content_data hook
			 *
			 * @hooked cherry_wc_quick_view_rating
			 * @hooked cherry_wc_quick_view_price
			 * @hooked cherry_wc_quick_view_title
			 * @hooked cherry_wc_quick_view_add_to_cart
			 * @hooked cherry_wc_quick_view_meta
			 */
			do_action( 'cherry_quick_view_content_data' );
		?>
	</div>
	<div class="cherry-quick-view-content">
		<?php 
			/**
			 * cherry_quick_view_content_description hook
			 *
			 * @hooked cherry_wc_quick_view_excerpt
			 */
			do_action( 'cherry_quick_view_content_description' );
		?>
	</div>
	<?php
		/**
		 * Hook fires after main quick view content
		 * @since 1.1.0
		 */
		do_action( 'cherry_quick_view_content_end' );
	?>
</div>