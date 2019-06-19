<?php

require_once('LiveChatHelper.class.php');

class ReviewNoticeHelper extends LiveChatHelper
{
	public function render()
	{
	?>
		<div class="notice notice-info is-dismissible" id="lc-review-notice">
			<p><?php _e('Do you like LiveChat? Leave us a review and join our LiveChat Knowledge Journey!', 'wp-live-chat-software-for-wordpress'); ?></p>
			<p><?php _e('We do our best to create the best live chat software available out there. Leave us a review, help us grow LiveChat and join our exclusive Knowledge Journey community. Learn how to:', 'wp-live-chat-software-for-wordpress'); ?></p>
			<ul style="list-style-type:disc;list-style-position:inside;margin:0;padding:3px;">
				<li><?php _e('Bring people to your website', 'wp-live-chat-software-for-wordpress'); ?></li>
				<li><?php _e('Be the hero of your customers\' needs', 'wp-live-chat-software-for-wordpress'); ?></li>
				<li><?php _e('Sell like a boss with LiveChat', 'wp-live-chat-software-for-wordpress'); ?></li>
				<li><?php _e('Succeed in customer success', 'wp-live-chat-software-for-wordpress'); ?></li>
			</ul>
			<p>
				<a href="https://wordpress.org/support/plugin/wp-live-chat-software-for-wordpress/reviews/#new-post" target="_blank" style="text-decoration: none" id="lc-review-now">
					<span class="dashicons dashicons-thumbs-up"></span> <?php _e('Leave a review and join LiveChat Knowledge Journey!', 'wp-live-chat-software-for-wordpress'); ?>
				</a> |
				<a href="#" style="text-decoration: none" id="lc-review-postpone">
					<span class="dashicons dashicons-clock"></span> <?php _e('Maybe later', 'wp-live-chat-software-for-wordpress'); ?>
				</a> |
				<a href="#" style="text-decoration: none" id="lc-review-dismiss">
					<span class="dashicons dashicons-no-alt"></span><?php _e('I don’t use this app anymore', 'wp-live-chat-software-for-wordpress'); ?>
				</a>
			</p>
		</div>
	<?php
	}
}