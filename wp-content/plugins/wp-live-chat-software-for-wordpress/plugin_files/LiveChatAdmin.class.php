<?php

require_once('LiveChat.class.php');

final class LiveChatAdmin extends LiveChat
{
	/**
	 * Plugin's version
	 */
	protected $plugin_version = null;

	/**
	 * Returns true if "Advanced settings" form has just been submitted,
	 * false otherwise
	 *
	 * @return bool
	 */
	protected $changes_saved = false;

	/**
	 * Timestamp from which review notice timeout count starts from
	 */
	protected $review_notice_start_timestamp = null;

	/**
	 * Timestamp offset
	 */
	protected $review_notice_start_timestamp_offset = null;

	/**
	 * Returns true if review notice was dismissed
	 *
	 * @return bool
	 */
	protected $review_notice_dismissed = false;

	/**
	 * Starts the plugin
	 */
	protected function __construct()
	{
		parent::__construct();

		add_action('init', array($this, 'load_translations'));
		add_action('init', array($this, 'load_menu_icon_styles'));

		// notice action
		if($this->check_review_notice_conditions()) {
			add_action('init', array($this, 'load_review_scripts'));
			add_action('wp_ajax_lc_review_dismiss', array($this, 'ajax_review_dismiss'));
			add_action('wp_ajax_lc_review_postpone', array($this, 'ajax_review_postpone'));
			add_action('admin_notices', array($this, 'show_review_notice'));
		}

    if(!$this->is_installed() && !(array_key_exists('page', $_GET) && $_GET['page'] === 'livechat_settings')) {
      add_action('admin_notices', array($this, 'show_connect_notice'));
    }

		add_action('init', array($this, 'load_scripts'));

		add_action('admin_menu', array($this, 'admin_menu'));

		// tricky error reporting
		if (defined('WP_DEBUG') && WP_DEBUG == true)
		{
			add_action('init', array($this, 'error_reporting'));
		}

		if (array_key_exists('reset', $_GET) && $_GET['reset'] == '1' && $_GET['page'] === 'livechat_settings')
		{
			$this->reset_options();
		}
		else if ((
			array_key_exists('REQUEST_METHOD', $_SERVER) &&
			$_SERVER['REQUEST_METHOD'] === 'POST' &&
			array_key_exists('HTTP_REFERER', $_SERVER) &&
			strpos($_SERVER['HTTP_REFERER'], 'livechat_settings') !== false
		) || (
			!array_key_exists('HTTP_REFERER', $_SERVER) &&
			array_key_exists('REQUEST_METHOD', $_SERVER) &&
			$_SERVER['REQUEST_METHOD'] === 'POST'
		)) {
			echo $this->update_options($_POST);
		}
	}

	public static function get_instance()
	{
		if (!isset(self::$instance))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	/**
	 * Make translation ready
	 */
	public function load_translations()
	{
		load_plugin_textdomain(
			'wp-live-chat-software-for-wordpress',
			false,
			'wp-live-chat-software-for-wordpress/languages'
		);
	}

	/**
	 * Fix CSS for icon in menu
	 */
	public function load_menu_icon_styles()
	{
		wp_enqueue_style('livechat-menu', $this->get_plugin_url().'css/livechat-menu.css', false, $this->get_plugin_version());
	}

	/**
	 * Set error reporting for debugging purposes
	 */
	public function error_reporting()
	{
		error_reporting(E_ALL & ~E_USER_NOTICE);
	}

	/**
	 * Returns this plugin's version
	 *
	 * @return string
	 */
	public function get_plugin_version()
	{
		if (is_null($this->plugin_version))
		{
			if (!function_exists('get_plugins'))
			{
				require_once(ABSPATH.'wp-admin/includes/plugin.php');
			}

			$plugin_folder = get_plugins('/'.plugin_basename(dirname(__FILE__).'/..'));
			if(count($plugin_folder) === 0)
				$plugin_folder['livechat.php'] = get_plugin_data(dirname(__FILE__).'/../livechat.php');
			$this->plugin_version = $plugin_folder['livechat.php']['Version'];
		}

		return $this->plugin_version;
	}

	protected function get_review_notice_start_timestamp()
	{
		if (is_null($this->review_notice_start_timestamp))
		{
			$timestamp = get_option('livechat_review_notice_start_timestamp');
			// if timestamp was not set on install
			if (!$timestamp) {
				$timestamp = time();
				update_option('livechat_review_notice_start_timestamp', $timestamp); // set timestamp if not set on install
			}

			$this->review_notice_start_timestamp = $timestamp;
		}

		return $this->review_notice_start_timestamp;
	}

	protected function get_review_notice_start_timestamp_offset()
	{
		if (is_null($this->review_notice_start_timestamp_offset))
		{
			$offset = get_option('livechat_review_notice_start_timestamp_offset');
			// if offset was not set on install
			if (!$offset) {
				$offset = 15;
				update_option('livechat_review_notice_start_timestamp_offset', $offset); // set shorter offset
			}

			$this->review_notice_start_timestamp_offset = $offset;
		}

		return $this->review_notice_start_timestamp_offset;
	}

	protected function check_if_review_notice_was_dismissed()
	{
		if (!$this->review_notice_dismissed)
		{
			$this->review_notice_dismissed = get_option('livechat_review_notice_dismissed');
		}

		return $this->review_notice_dismissed;
	}

	public function load_scripts()
	{
		wp_enqueue_script('livechat', $this->get_plugin_url().'js/livechat.js', 'jquery', $this->get_plugin_version(), true);
		wp_enqueue_style('livechat', $this->get_plugin_url().'css/livechat.css', false, $this->get_plugin_version());
	}

	public function load_review_scripts()
	{
		wp_enqueue_script('livechat-review', $this->get_plugin_url().'js/livechat-review.js', 'jquery', $this->get_plugin_version(), true);
	}

	public function admin_menu()
	{
		add_menu_page(
			'LiveChat',
			'LiveChat',
			'administrator',
			'livechat',
			array($this, 'livechat_settings_page'),
			$this->get_plugin_url().'images/livechat-icon.svg'
		);

		add_submenu_page(
			'livechat',
			__('Settings', 'wp-live-chat-software-for-wordpress'),
			__('Settings', 'wp-live-chat-software-for-wordpress'),
			'administrator',
			'livechat_settings',
			array($this, 'livechat_settings_page')
		);

		// remove the submenu that is automatically added
		if (function_exists('remove_submenu_page'))
		{
			remove_submenu_page('livechat', 'livechat');
		}

		// Settings link
		add_filter('plugin_action_links', array($this, 'livechat_settings_link'), 10, 2);
	}

	/**
	 * Displays settings page
	 */
	public function livechat_settings_page()
	{
		$this->get_helper('Settings');
	}

	public function changes_saved()
	{
		return $this->changes_saved;
	}

	public function livechat_settings_link($links, $file)
	{
		if (basename($file) !== 'livechat.php')
		{
			return $links;
		}

		$settings_link = sprintf('<a href="admin.php?page=livechat_settings">%s</a>', __('Settings'));
		array_unshift ($links, $settings_link);
		return $links;
	}

	protected function reset_options()
	{
		delete_option('livechat_license_number');
		delete_option('livechat_email');
		delete_option('livechat_review_notice_start_timestamp');
		delete_option('livechat_review_notice_start_timestamp_offset');
		delete_option('livechat_disable_mobile');
		delete_option('livechat_disable_guests');
	}

	protected function check_review_notice_conditions()
	{
		if(!$this->check_if_review_notice_was_dismissed()) {
			if ($this->is_installed() && $this->check_if_license_is_active($this->get_license_number())) {
				$secondsInDay = 60 * 60 * 24;
				$noticeTimeout = time() - $this->get_review_notice_start_timestamp();
				$timestampOffset = $this->get_review_notice_start_timestamp_offset();
				if ($noticeTimeout >= $secondsInDay * $timestampOffset) {
					return true;
				}
			}
		}

		return false;
	}

	protected function check_if_license_is_active($license_number)
	{
		$url = 'https://api.livechatinc.com/v2/license/' . $license_number;
		try {
			if(function_exists('curl_init')) {
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($curl);
				$code     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);

				if ($code === 200) {
					return json_decode($response)->license_active;
				} else {
					throw new Exception($code);
				}
			} else if(ini_get('allow_url_fopen') === '1' || strtolower(ini_get('allow_url_fopen')) === 'on') {
				$options = array(
					'http' => array(
						'method'  => 'GET'
					),
				);
				$context  = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				return json_decode($result)->license_active;
			}
		} catch(Exception $exception) {
			error_log(
				'check_if_license_is_active() error ' .
				$exception->getCode() .
				': ' .
				$exception->getMessage()
			);
		}
		return false;
	}

	public function show_review_notice()
	{
		$this->get_helper('ReviewNotice');
	}

  public function show_connect_notice()
  {
    $this->get_helper('ConnectNotice');
  }

	public function ajax_review_dismiss()
	{
		update_option('livechat_review_notice_dismissed', true);
		echo "OK";
		wp_die();
	}

	public function ajax_review_postpone()
	{
		update_option('livechat_review_notice_start_timestamp', time());
		update_option('livechat_review_notice_start_timestamp_offset', 7);
		echo "OK";
		wp_die();
	}

	protected function update_options($data)
	{
		if (!isset($data['licenseEmail']) || !isset($data['licenseNumber']))
		{
			if(array_key_exists('disableMobile', $data) || array_key_exists('disableGuests', $data)) {
				$disableMobile = array_key_exists('disableMobile', $data) ? (int) $data['disableMobile'] : 0;
				$disableGuests = array_key_exists('disableGuests', $data) ? (int) $data['disableGuests'] : 0;

				update_option('livechat_disable_mobile', $disableMobile);
				update_option('livechat_disable_guests', $disableGuests);

				$array = array(
					'message' => 'success'
				);

				echo json_encode($array);
				die;
			} else {
				return false;
			}

		} else {

			$license_number = isset($data['licenseNumber']) ? (int) $data['licenseNumber'] : 0;
			$email          = isset($data['licenseEmail']) ? (string) $data['licenseEmail'] : '';

			update_option('livechat_license_number', $license_number);
			update_option('livechat_email', $email);

			update_option('livechat_review_notice_start_timestamp', time());
			update_option('livechat_review_notice_start_timestamp_offset', 45);

			update_option('livechat_disable_mobile', 0);
			update_option('livechat_disable_guests', 0);

			if (isset($data['changes_saved']) && $data['changes_saved'] == '1') {
				$this->changes_saved = true;
			}
		}
	}
}