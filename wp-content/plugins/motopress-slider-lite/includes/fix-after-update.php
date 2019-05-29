<?php
if (!defined('ABSPATH')) exit;

function mpslFixAfterUpdate() {
    global $mpsl_settings;

    // Queue upgrades
    $currentDBVersion = get_option('mpsl_db_version', null);
	$latestVersionToUpdate = end($mpsl_settings['versions_to_update']);

    // Fix for v1.0.2
    if ($currentDBVersion === null and version_compare($mpsl_settings['plugin_version'], '1.0.2', '>')) {
        global $wpdb;
        $slidesCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mpsl_slides");
        if ($slidesCount) {
            $currentDBVersion = '1.0.2';
//            update_option('mpsl_db_version', $currentDBVersion);
        }
    }

    // Write here max version that needs update
    if (version_compare($currentDBVersion, $latestVersionToUpdate, '<') and null !== $currentDBVersion) {
        update_option('_mpsl_needs_update', 1);
    } else {
        update_option('mpsl_db_version', $mpsl_settings['plugin_version']);
    }

    // Update version
    update_option('mpsl_version', $mpsl_settings['plugin_version']);
}