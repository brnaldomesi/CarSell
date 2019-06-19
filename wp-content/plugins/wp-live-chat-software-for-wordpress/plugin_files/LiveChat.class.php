<?php

class LiveChat
{
	// singleton pattern
	protected static $instance;

	/**
	 * Absolute path to plugin files
	 */
	protected $plugin_url = null;

	/**
	 * LiveChat license parameters
	 */
	protected $login = null;
	protected $license_number = null;

	/**
	 * Remembers if LiveChat license number is set
	 */
	protected $license_installed = false;

	/**
	 * Starts the plugin
	 */
	protected function __construct()
	{
		add_action ('wp_head', array($this, 'tracking_code'));
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
	 * Returns plugin files absolute path
	 *
	 * @return string
	 */
	public function get_plugin_url()
	{
		if (is_null($this->plugin_url))
		{
			$this->plugin_url = plugin_dir_url( __FILE__ );
		}

		return $this->plugin_url;
	}

	/**
	 * Returns true if LiveChat license is set properly,
	 * false otherwise
	 *
	 * @return bool
	 */
	public function is_installed()
	{

		$this->license_installed = ($this->get_license_number() > 0);

		return $this->license_installed;
	}

	/**
	 * Returns LiveChat license number
	 *
	 * @return int
	 */
	public function get_license_number()
	{
		$this->license_number = get_option('livechat_license_number');

		// license_number must be >= 0
		// also, this prevents from NaN values
		$this->license_number = max(0, $this->license_number);

		return $this->license_number;
	}

	/**
	 * Returns LiveChat login
	 */
	public function get_login()
	{
		if (is_null($this->login))
		{
			$this->login = get_option('livechat_email');
		}

		return $this->login;
	}

	/**
	 * Returns LiveChat settings
	 *
	 * @return int
	 */
	public function get_settings()
	{
		$settings['disableMobile'] = get_option('livechat_disable_mobile');
		$settings['disableGuests'] = get_option('livechat_disable_guests');

		return $settings;
	}

	/**
	 * Injects tracking code
	 */
	public function tracking_code()
	{
		$this->get_helper('TrackingCode');
	}

	/**
	 * Echoes given helper
	 */
	public static function get_helper($class, $echo=true, $params=array())
	{
		$class .= 'Helper';

		if (class_exists($class) == false)
		{
			$path = dirname(__FILE__).'/helpers/'.$class.'.class.php';
			if (file_exists($path) !== true)
			{
				return false;
			}

			require_once($path);
		}

		$c = new $class;

		if ($echo)
		{
			echo $c->render($params);
			return true;
		}
		else
		{
			return $c->render($params);
		}
	}

	/**
	 * Checks if visitor is on mobile device.
	 * @return boolean
	 */
	public function check_mobile() {
		$userAgent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$regex = '/((Chrome).*(Mobile))|((Android).*)|((iPhone|iPod).*Apple.*Mobile)|((Android).*(Mobile))/i';
		return preg_match($regex, $userAgent);
	}

	/**
	 * Checks if visitor is logged in
	 * @return boolean
	 */
	public function check_logged() {
		if (property_exists(wp_get_current_user()->data, 'ID')) {
			return true;
		}
		return false;
	}

	/**
	 * Get visitor's name and email
	 * @return array
	 */
	public function get_user_data(){
		$currentUserData = wp_get_current_user()->data;

		$visitor_email = $visitor_name = '';

		if (property_exists($currentUserData, 'user_email')) {
			$visitor_email = $currentUserData->user_email;
		}
		if (property_exists($currentUserData, 'display_name')) {
			$visitor_name = $currentUserData->display_name;
		}

		return array(
			'name' => $visitor_name,
			'email' => $visitor_email
		);
	}
}