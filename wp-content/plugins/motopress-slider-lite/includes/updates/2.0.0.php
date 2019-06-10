<?php
if (!defined('ABSPATH')) exit;

global $mpsl_settings;
$pluginDir = $mpsl_settings['plugin_dir_path'];

require_once $pluginDir . 'includes/classes/LayerPresetOptions.php';
/*
require_once $pluginDir . 'includes/classes/update_fixes/MPSL_Fix_Factory.php';

$slidesTable = $wpdb->prefix . MPSLAdmin::SLIDES_TABLE;
$slides = $wpdb->get_results("SELECT id, layers FROM {$slidesTable}", 'ARRAY_A');
*/
/** @var MPSL_Fix_v2_0_0 $fixer */
/*$fixer = MPSL_Fix_Factory::getFixer('2.0.0');

foreach ($slides as $slide) {
    $layers = json_decode($slide['layers'], true);
    if ($layers && count($layers)) {

	    $layers = $fixer->fixLayers($layers);

	    // Update
	    $layers = json_encode_slashed($layers);
	    if ($layers !== false) {
		    $updResult = $wpdb->update(
			    $slidesTable,
			    array('layers' => $layers),
			    array('ID' => (int) $slide['id']),
			    array('%s'),
			    array('%d')
		    );
	    }

    }
}*/

// Update presets
/** Note: Need add !important to layouted hover styles */
$layerPresetsObj = MPSLLayerPresetOptions::getInstance();
$presetsUpdateResult = $layerPresetsObj->update();
$layerPresetsObj->updatePrivateStyles();