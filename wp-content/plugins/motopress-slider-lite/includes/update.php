<?php
if (!defined('ABSPATH')) exit;

function mpslDoUpdate() {
    global $mpsl_settings;

    $currentDBVersion = get_option('mpsl_db_version');
	$versionsToUpdate = $mpsl_settings['versions_to_update'];

	foreach ($versionsToUpdate as $versionToUpdate) {
		if (version_compare($currentDBVersion, $versionToUpdate, '<')) {
	        include($mpsl_settings['plugin_dir_path'] . "includes/updates/{$versionToUpdate}.php");
	        update_option('mpsl_db_version', $versionToUpdate);
	    }
	}

    update_option('mpsl_db_version', $mpsl_settings['plugin_version']);
}