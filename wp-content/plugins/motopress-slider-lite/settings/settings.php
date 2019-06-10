<?php
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$mpsl_settings = array();
global $wpdb, $wp_version;

$mpsl_settings['debug'] = false;
$mpsl_settings['prefix'] = 'mpsl_';
$mpsl_settings['alt_prefix'] = 'mpsl-';
$mpsl_settings['admin_url'] = get_admin_url();
$mpsl_settings['plugin_root'] = WP_PLUGIN_DIR;
$mpsl_settings['plugin_root_url'] = plugins_url();
$mpsl_settings['plugin_file'] = $mpsl_plugin_file;
$mpsl_settings['plugin_name'] = 'motopress-slider';
$mpsl_settings['plugin_real_dir_name'] = basename(dirname($mpsl_plugin_file));
$mpsl_settings['plugin_symlink_dir_name'] = isset($plugin) ? basename(dirname($plugin)) : $mpsl_settings['plugin_real_dir_name'];
$mpsl_settings['plugin_dir_path'] = plugin_dir_path($mpsl_plugin_file);
$pluginData = get_plugin_data($mpsl_settings['plugin_dir_path'] . $mpsl_settings['plugin_name'] . '.php', false, false);
$mpsl_settings['plugin_version'] = $pluginData['Version'];
$mpsl_settings['plugin_author'] = $pluginData['Author'];
if (version_compare($wp_version, '3.9', '<')) {
	$mpsl_settings['plugin_dir_url'] = plugin_dir_url($mpsl_settings['plugin_symlink_dir_name'] . '/' . basename($mpsl_plugin_file));
} else {
	$mpsl_settings['plugin_dir_url'] = plugin_dir_url($mpsl_plugin_file);
}
$mpsl_settings['sliders_table'] = $wpdb->prefix . 'mpsl_sliders';
$mpsl_settings['slides_table'] = $wpdb->prefix . 'mpsl_slides';
$mpsl_settings['preview_slides_table'] = $wpdb->prefix . 'mpsl_slides_preview';
$mpsl_settings['core_version'] = '2.1.0';
$mpsl_settings['canjs_version'] = '2.3.22';
$mpsl_settings['codemirror_version'] = '3.12';
$mpsl_settings['spectrum_version'] = '1.7.1';
$mpsl_settings['shortcode_name'] = 'mpsl';
$mpsl_settings['versions_to_update'] = array('1.1.0', '1.2.0', '2.0.0', '2.1.0');

$wpVersion = get_bloginfo('version');
$wpVersion = (double) $wpVersion;
$mpsl_settings['is_new_wp_version'] = ($wpVersion >= 3.5) ? true : false;

$mpsl_settings['license_type'] = "Lite";
$mpsl_settings['edd_mpsl_store_url'] = $pluginData['PluginURI'];
$mpsl_settings['edd_mpsl_item_name'] = $pluginData['Name'] . ' ' . $mpsl_settings['license_type'];
$mpsl_settings['edd_mpsl_item_id'] = 74440;
$mpsl_settings['renew_url'] = $pluginData['PluginURI'];

$mpsl_settings['lite_upgrade_url'] = $pluginData['PluginURI'];

$GLOBALS['mpsl_settings'] = $mpsl_settings;