<?php

require_once('LiveChatHelper.class.php');

class SettingsHelper extends LiveChatHelper
{
    public function render()
    {

        $license_email = LiveChat::get_instance()->get_login();
	    $license_id = LiveChat::get_instance()->get_license_number();
        $settings = LiveChat::get_instance()->get_settings();
        $user = LiveChat::get_instance()->get_user_data();

        if (isset($_GET['actionType']) && $_GET['actionType'] === 'install') { ?>
            <div class="updated installed">
                <p>
                    <?php _e('LiveChat is now installed on your website!', 'wp-live-chat-software-for-wordpress'); ?>
                </p>
                <span id="installed-close">x</span>
            </div>
        <?php } ?>
            <div id="wordpress-livechat-container">
        <?php if (!LiveChat::get_instance()->is_installed()) : ?>
            <div class="wordpress-livechat-column-left">
                <div class="login-box-header">
                    <img src="<?php echo plugins_url('wp-live-chat-software-for-wordpress').'/plugin_files/images/livechat-wordpress@2x.png'; ?>" alt="LiveChat + Wordpress" class="logo">
                </div>
                <div id="useExistingAccount">
                    <p class="login-with-livechat"><br>
                        <iframe id="login-with-livechat" src="https://addons.livechatinc.com/sign-in-with-livechat/wordpress/?linkLabel=Connect+with+LiveChat&popupRoute=signup%2Fcredentials&a=wordpress&utm_source=wordpress.org&utm_medium=integration&utm_campaign=wordpress_plugin&email=<?php echo urlencode($user['email']); ?>&name=<?php echo urlencode($user['name']); ?>" > </iframe>
                    </p>
                    <form id="licenseForm" action="?page=livechat_settings&actionType=install" method="post">
                        <input type="hidden" name="licenseEmail" id="licenseEmail">
                        <input type="hidden" name="licenseNumber" id="licenseNumber">
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if (LiveChat::get_instance()->is_installed()): ?>
            <div class="wordpress-livechat-column-left">
                <div class="account">
                    <?php _e('Currently you are using your', 'wp-live-chat-software-for-wordpress'); ?><br>
                    <strong><?php echo $license_email ?></strong><br>
                    <?php _e('LiveChat account.', 'wp-live-chat-software-for-wordpress'); ?>
                </div>
                <p class="webapp">
                    <a href="https://my.livechatinc.com/?utm_source=wordpress.org&utm_medium=integration&utm_campaign=wordpress_plugin" target="_blank">
                        <?php _e('Open web application', 'wp-live-chat-software-for-wordpress'); ?>
                    </a>
                </p>
                <div class="settings">
                    <p class="login-with-livechat"><br>
                        <iframe id="login-with-livechat" src="https://addons.livechatinc.com/sign-in-with-livechat" > </iframe>
                    </p>
                    <div>
                        <div class="title">
                            <span><?php _e('Hide chat on mobile', 'wp-live-chat-software-for-wordpress'); ?></span>
                        </div>
                        <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="disableMobile" <?php echo ($settings['disableMobile']) ? 'checked': '' ?>>
                            <label class="onoffswitch-label" for="disableMobile">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <div class="title">
                            <span><?php _e('Hide chat for Guest visitors', 'wp-live-chat-software-for-wordpress'); ?></span>
                        </div>
                        <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="disableGuests" <?php echo ($settings['disableGuests']) ? 'checked': '' ?>>
                            <label class="onoffswitch-label" for="disableGuests">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <p class="disconenct">
                    <?php _e('Something went wrong?', 'wp-live-chat-software-for-wordpress'); ?> <a id="resetAccount" href="?page=livechat_settings&reset=1" style="display: inline-block">
                        <?php _e('Disconect your account.', 'wp-live-chat-software-for-wordpress'); ?>
                    </a>
                </p>
                <script>
                    var lcDetails = {
                        license: <?php echo $license_id ?>,
                        email: '<?php echo $license_email ?>'
                    }
                </script>
            </div>
        <?php endif; ?>
            <div class="wordpress-livechat-column-right">
            <p><img src="<?php echo plugins_url('wp-live-chat-software-for-wordpress').'/plugin_files/images/livechat-app.png'; ?>" alt="LiveChat apps" class="livechat-app"></p>
            <p class="apps-link">
                <?php _e('Check out our apps for', 'wp-live-chat-software-for-wordpress'); ?> <a href="https://www.livechatinc.com/applications/?utm_source=wordpress.org&utm_medium=integration&utm_campaign=wordpress_plugin" target="_blank" class="a-important">
                    <?php _e('desktop or mobile!', 'wp-live-chat-software-for-wordpress'); ?>
                </a>
            </p>
            </div>
        </div>
        <?php
    }
}