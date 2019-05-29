<?php
if (!defined('ABSPATH')) exit;

global $mpsl_settings;

// Clear old fonts cache (new vendor/googlefonts/webfonts.json available)
wp_cache_delete('mpsl_gfonts');

// Replace "random" with "rand" in slider "post_order_by" option variants
global $wpdb;

$sql = sprintf('SELECT id, options FROM %s', $mpsl_settings['sliders_table']);
$sql .= ' WHERE options LIKE \'%"post_order_by":"random"%\''; // % in LIKE expression will emit PHP warning "Too few arguments"
$sliders = $wpdb->get_results($sql, ARRAY_A);

if ($sliders) {
    foreach ($sliders as $slider) {
        $id = (int)$slider['id'];
        $options = json_decode($slider['options'], true);

        if (isset($options['post_order_by'])) {
            $options['post_order_by'] = 'rand';
            $options = json_encode_slashed($options);

            if ($options !== false) {
                $wpdb->update(
                    $mpsl_settings['sliders_table'],
                    array('options' => $options),
                    array('id' => $id),
                    array('%s'),
                    array('%d')
                );
            }
        }

    } // foreach
} // if $sliders
