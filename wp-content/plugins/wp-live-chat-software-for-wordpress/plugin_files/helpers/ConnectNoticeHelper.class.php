<?php

require_once('LiveChatHelper.class.php');

class ConnectNoticeHelper extends LiveChatHelper
{
  public function render()
  {
    ?>
    <div class="notice notice-info" id="lc-connect-notice">
      <p>
        <?php _e('Please connect your LiveChat account to start chatting with your customers.', 'wp-live-chat-software-for-wordpress'); ?> <a href="admin.php?page=livechat_settings">
          <?php _e('Connect', 'wp-live-chat-software-for-wordpress'); ?> &rarr;
        </a>
      </p>
    </div>
    <?php
  }
}