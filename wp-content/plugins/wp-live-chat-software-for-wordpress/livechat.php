<?php
/*
Plugin Name: LiveChat
Plugin URI: https://www.livechatinc.com/addons/wordpress/
Description: Live chat software for live help, online sales and customer support. This plugin allows to quickly install LiveChat on any WordPress website.
Author: LiveChat
Author URI: https://www.livechatinc.com
Version: 3.7.2
Text Domain: wp-live-chat-software-for-wordpress
Domain Path: /languages
*/

if (is_admin())
{
	require_once(dirname(__FILE__).'/plugin_files/LiveChatAdmin.class.php');
	LiveChatAdmin::get_instance();
}
else
{
	require_once(dirname(__FILE__).'/plugin_files/LiveChat.class.php');
	LiveChat::get_instance();
}

