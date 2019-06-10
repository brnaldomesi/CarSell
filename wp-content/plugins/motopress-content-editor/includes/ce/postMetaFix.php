<?php
function motopressCEPocketFix($post_id, $post_type) {
    if ($post_type == 'pockets'){
        $single_pocket_layout = get_post_meta( $post_id, 'single_pocket_layout', true);
        if ( empty( $single_pocket_layout ) ){
            update_post_meta($post_id, 'single_pocket_layout', 'pocket-layout-wide');
        }
    }
}
add_action('mp_post_meta', 'motopressCEPocketFix', 10, 2);

function motopressCEAddHeadwayFix($post_id, $tmp_post_id, $post_type) {
    if (defined('HEADWAY_VERSION')) {
        global $wpdb;
        if (
            version_compare(HEADWAY_VERSION, '3.7.10', '>=') ||
            property_exists($wpdb, 'hw_wrappers')
        ) {
            global $wp_query;
            $originalWpQuery = $wp_query;
            $key = ($post_type === 'page') ? 'page_id' : 'p';
            $wp_query = new WP_Query($key . '=' . $post_id);
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                }
            }

            $layoutId = HeadwayLayout::get_current_in_use();
            $wrappers = HeadwayWrappersData::get_wrappers_by_layout($layoutId);
//            if ($wrappers[0]['id'] !== 'default') {
            if (!array_key_exists('default', $wrappers)) {
                $sep = preg_quote(HeadwayLayout::$sep);
                if (preg_match('/^single' . $sep . '[a-z]+' . $sep . '\d+$/is', $layoutId)) {
                    $layoutTmpId = str_replace($post_id, $tmp_post_id, $layoutId);
                } else {
                    $layoutTmpId = 'single' . HeadwayLayout::$sep . $post_type . HeadwayLayout::$sep . $tmp_post_id;
                }

                update_option('motopress-ce-hw-layout', $layoutTmpId);

                foreach ($wrappers as $wrapper_id => $wrapper) {
                    $wrapperId = HeadwayWrappersData::add_wrapper($layoutTmpId, $wrapper);
                    $blocks = HeadwayBlocksData::get_blocks_by_wrapper($layoutId, $wrapper_id);
                    foreach ($blocks as $block_id => &$block) {
                        $block['wrapper_id'] = $wrapperId;
                        $block['wrapper'] = $wrapperId;
                        HeadwayBlocksData::add_block($layoutTmpId, $block);
                    }
                    unset($block);
                }

                $transient_id_customized_layouts = 'hw_customized_layouts_template_' . HeadwayOption::$current_skin;
                $customized_layouts = get_transient($transient_id_customized_layouts);
                if (!$customized_layouts) {
                    $customized_layouts = array_unique($wpdb->get_col($wpdb->prepare("SELECT layout FROM $wpdb->hw_blocks WHERE template = '%s'", HeadwayOption::$current_skin)));
                }
                if (!in_array($layoutTmpId, $customized_layouts)) {
                    $customized_layouts[] = $layoutTmpId;
                    set_transient($transient_id_customized_layouts, $customized_layouts);
                }
            }

            wp_reset_postdata();
            $wp_query = $originalWpQuery;
        } else {
            $layout = get_option('headway_layout_options_' . $post_id);
            if ($layout) {
                update_option('headway_layout_options_' . $tmp_post_id, $layout);
            } else {
                delete_option('headway_layout_options_' . $tmp_post_id);
            }
        }
    }
}
add_action('mp_theme_fix', 'motopressCEAddHeadwayFix', 10, 3);

function motopressCERemoveHeadwayFix() {
    if (defined('HEADWAY_VERSION')) {
        global $wpdb;
        if (
            version_compare(HEADWAY_VERSION, '3.7.10', '>=') ||
            property_exists($wpdb, 'hw_wrappers')
        ) {
            $layoutTmpId = get_option('motopress-ce-hw-layout');
            if ($layoutTmpId) {
                HeadwayWrappersData::delete_by_layout($layoutTmpId);

                $transient_id_customized_layouts = 'hw_customized_layouts_template_' . HeadwayOption::$current_skin;
                $customized_layouts = get_transient($transient_id_customized_layouts);
                if ($customized_layouts) {
                    $index = array_search($layoutTmpId, $customized_layouts);
                    if ($index !== false) {
                        unset($customized_layouts[$layoutTmpId]);
                        set_transient($transient_id_customized_layouts, $customized_layouts);
                    }
                }

                delete_option('motopress-ce-hw-layout');
            }
        }
    }
}

function motopressCEWPMLFix($post_id, $tmp_post_id, $post_type) {
    if (defined('ICL_SITEPRESS_VERSION')) {
        global $wpdb, $sitepress;
        $content_type = 'post_' . $post_type;

        // fix #169
        if ($sitepress->is_translated_post_type( $post_type )) {
            require_once WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php';
            if (!wpml_get_content_trid($content_type, $tmp_post_id)) {
                $content_type_pattern = 'post_%';
                $translation_id = $wpdb->get_var( $wpdb->prepare( "SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type LIKE %s", array( $tmp_post_id, $content_type_pattern ) ) );
                if ($translation_id) {
                    $wpdb->update( $wpdb->prefix . 'icl_translations', array( 'element_type' => $content_type ), array( 'translation_id' => $translation_id ) );
                }
            }
        }

        $language_code = $wpdb->get_var($wpdb->prepare('SELECT language_code FROM '.$wpdb->prefix.'icl_translations WHERE element_id = %d', $post_id));
        if (!is_null($language_code)) {
            require_once WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php';

            if (wpml_get_content_trid($content_type, $tmp_post_id)) {
                wpml_update_translatable_content($content_type, $tmp_post_id, $language_code);
            } else {
                wpml_add_translatable_content($content_type, $tmp_post_id, $language_code);
            }
        }
    }
}
add_action('mp_theme_fix', 'motopressCEWPMLFix', 10, 3);