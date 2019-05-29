<?php
if (!defined('ABSPATH')) exit;

global $wpdb;

$slidesTable = $wpdb->prefix . 'mpsl_slides';

$slides = $wpdb->get_results("SELECT id, options FROM {$slidesTable}", 'ARRAY_A');

foreach ($slides as $slide) {
    $options = json_decode($slide['options'], true);
    if ($options) {
        if (!array_key_exists('bg_types', $options)) {
            // Change
            $bgType = isset($options['bg_type']) ? $options['bg_type'] : 'color';
            $options['bg_types'] = array($bgType);
            if ($bgType === 'color') {
                $options['bg_color_type'] = $bgType;
            }
            unset($options['bg_type']);

            // Update
            $options = json_encode_slashed($options);
            if ($options !== false) {
                $updResult = $wpdb->update(
                    $slidesTable,
                    array('options' => $options),
                    array('ID' => (int) $slide['id']),
                    array('%s'),
                    array('%d')
                );
            }
        }
    }
}