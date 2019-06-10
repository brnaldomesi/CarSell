<?php
$motopressCELibrary = null;

add_filter('wp_revisions_to_keep', 'motopressCEDisableRevisions', 10, 2);

function motopressCERenderContent() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../Requirements.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';
    require_once dirname(__FILE__).'/postMetaFix.php';
    require_once dirname(__FILE__).'/ThemeFix.php';

    $content = trim($_POST['data']);
    $post_id = (int) $_POST['post_id'];

    global $motopressCESettings;
    global $motopressCELang;
    $errors = array();

    global $motopressCELibrary;
    $motopressCELibrary = new MPCELibrary();
    do_action_ref_array('mp_library', array(&$motopressCELibrary));

    $content = stripslashes($content);
    $content = motopressCECleanupShortcode($content);
    if (!empty($content)) {
        $content = motopressCEWrapOuterCode($content);
    }

    $output = motopressCEParseObjectsRecursive($content);

    $tmp_post_id = motopressCECreateTemporaryPost($post_id, $output);
    if ($tmp_post_id !== 0) {
        $themeFix = new MPCEThemeFix(MPCEThemeFix::DEACTIVATE);

        $src = get_permalink($tmp_post_id);

        //@todo: fix protocol for http://codex.wordpress.org/Administration_Over_SSL
        //fix different site (WordPress Address) and home (Site Address) url for iframe security
        $siteUrl = get_site_url();
        $homeUrl = get_home_url();

        $siteUrlArr = parse_url($siteUrl);
        $homeUrlArr = parse_url($homeUrl);

        if ($homeUrlArr['scheme'] !== $siteUrlArr['scheme'] || $homeUrlArr['host'] !== $siteUrlArr['host']) {
            $src = str_replace($homeUrl, $siteUrl, $src);
        }

        $result = array(
            'post_id' => $tmp_post_id,
            'src' => $src,
            'headway_themes' => $themeFix->isHeadwayTheme(),
        );
        wp_send_json($result);
    } else {
        $errors[] = $motopressCELang->CECreateTemporaryPostError;
    }

    if (!empty($errors)) {
        if ($motopressCESettings['debug']) {
            print_r($errors);
        } else {
            motopressCESetError($motopressCELang->CECreateTemporaryPostError);
        }
    }
    exit;
}

function motopressCEParseObjectsRecursive($matches) {
    global $motopressCELibrary;
    $regex = '/' . motopressCEGetMPShortcodeRegex() . '/';

    if (is_array($matches)) {
        $grid = $motopressCELibrary->getGridObjects();
        $parameters_str =' ' . MPCEShortcode::$attributes['parameters'];
        $unwrap = '';
        $atts = shortcode_parse_atts($matches[3]);
        $atts = (array) $atts;

        $list= $motopressCELibrary->getObjectsList();

        $parameters = $list[ $matches[2] ]['parameters'];

        $group = $list[$matches[2]]['group'];

        //set parameters of shortcode
        if (!empty($parameters)) {
            foreach($parameters as $name => $param) {
                if (array_key_exists($name, $atts)) {
                    //$value = $atts[$name];
                    //$parameters[$name]['value'] = str_replace(array('\'', '"'), array('&#039;', '&quot;'), $value);
                    $value = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $atts[$name]);
                    $parameters[$name]['value'] = htmlentities($value, ENT_QUOTES, 'UTF-8');
                } else {
                    $parameters[$name] = new stdClass();
                }
            }
            $jsonParameters = (version_compare(PHP_VERSION, '5.4.0', '>=')) ? json_encode($parameters, JSON_UNESCAPED_UNICODE) : motopressCEJsonEncode($parameters);
            $parameters_str = " " . MPCEShortcode::$attributes['parameters'] . "='" . $jsonParameters . "'";
        }

        //set styles
        $styles = array();
        if (!empty(MPCEShortcode::$styles)) {
            foreach(MPCEShortcode::$styles as $name => $value) {
                if (array_key_exists($name, $atts)) {
                    //$value = $atts[$name];
                    //$styles[$name]['value'] = str_replace(array('\'', '"'), array('&#039;', '&quot;'), $value);
                    $value = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $atts[$name]);
                    $styles[$name]['value'] = htmlentities($value, ENT_QUOTES, 'UTF-8');
                } else {
                    $styles[$name] = new stdClass();
                }
            }

            if (!is_array($styles['mp_style_classes'])) {
                if (array_key_exists($matches[2], $motopressCELibrary->deprecatedParameters)) {
                    foreach (array_merge($motopressCELibrary->deprecatedParameters[$matches[2]], array('custom_class' => array('prefix' => ''))) as $key => $val){
                        if (array_key_exists($key, $atts)){
                            if (!is_array($styles['mp_style_classes'])){
                                $styles['mp_style_classes'] = array();
                                $styles['mp_style_classes']['value'] = '';
                            }
                            if ($matches[2] === MPCEShortcode::PREFIX . 'button') {
                                if ($key === 'color' && $atts[$key] === 'default') {
                                    $className = $val['prefix'] . 'silver';
                                } elseif ($key === 'size') {
                                    $className = ($atts[$key] === 'default') ? $val['prefix'] . 'middle' : $val['prefix'] . $atts[$key];
                                    $className .= ' motopress-btn-rounded';
                                } else {
                                    $className = $val['prefix'] . $atts[$key];
                                }
                            } else {
                                $className = $val['prefix'] . $atts[$key];
                            }
                            $styles['mp_style_classes']['value'] .=  $styles['mp_style_classes']['value'] === '' ? $className : ' ' . $className;
                        }
                    }
                }
            }

            $jsonStyles = (version_compare(PHP_VERSION, '5.4.0', '>=')) ? json_encode($styles, JSON_UNESCAPED_UNICODE) : motopressCEJsonEncode($styles);
            $styles_str = " " . MPCEShortcode::$attributes['styles'] . "='" . $jsonStyles . "'";
        }

        // set close-type of shortcode
        if (preg_match('/\[\/' . $matches[2] .'\]$/', $matches[0])===1){
            $endstr = '[/' . $matches[2] .']';
            $closeType = MPCEObject::ENCLOSED;
        } else {
            $endstr = '';
            $closeType = MPCEObject::SELF_CLOSED;
        }

        //wrap custom code
        $wrapCustomCodeRegex = substr_replace($regex, '\A(?:', 1, 0);
        $wrapCustomCodeRegex = substr_replace($wrapCustomCodeRegex, ')+\Z', -1, 0);
        if (isset($grid['span']['type']) && $grid['span']['type'] === 'multiple') {
            $spanShortcodes = array_merge($grid['span']['shortcode'], $grid['span']['inner']);
        } else {
            $spanShortcodes = array($grid['span']['shortcode'], $grid['span']['inner']);
        }
        if (
            ($matches[5] !== '') &&
            ($matches[5] !== '&nbsp;') &&
            (in_array($matches[2], $spanShortcodes)) &&
            (!preg_match($wrapCustomCodeRegex, $matches[5])) //$regex
        ) {
            $matches[5] = motopressCEWrapCustomCode($matches[5]);
        }

        // set system marking for "must-unwrap" code
        if ($matches[2] == 'mp_code') {
            if (!empty($matches[3])) {
                $atts = shortcode_parse_atts($matches[3]);
                if (isset($atts['unwrap']) && $atts['unwrap'] === 'true') {
                    $unwrap = ' ' . MPCEShortcode::$attributes['unwrap'] . ' = "true"';
                }
            }
        }
        $dataContent = '';

        //setting data-motopress-content for all objects except layout
        if (isset($grid['span']['type']) && $grid['span']['type'] === 'multiple') {
            $gridShortcodes = array_merge(array($grid['row']['shortcode'],$grid['row']['inner']), $grid['span']['shortcode'], $grid['span']['inner']);
        } else {
            $gridShortcodes = array($grid['row']['shortcode'],$grid['row']['inner'],$grid['span']['shortcode'],$grid['span']['inner']);
        }
        if (!in_array($matches[2] , $gridShortcodes)){
            $dataContent = motopressCEScreeningDataAttrShortcodes($matches[5]);
        }

        return '<div '.MPCEShortcode::$attributes['closeType'].'="' . $closeType . '" '.MPCEShortcode::$attributes['shortcode'].'="' . $matches[2] .'" '.MPCEShortcode::$attributes['group'].'="' . $group .'"' . $parameters_str . $styles_str . ' '.MPCEShortcode::$attributes['content'].'="' . htmlentities($dataContent, ENT_QUOTES, 'UTF-8') . '" ' . $unwrap . '>[' . $matches[2] . $matches[3] . ']' . preg_replace_callback($regex, 'motopressCEParseObjectsRecursive', $matches[5]) . $endstr . '</div>';
    }

    return preg_replace_callback($regex, 'motopressCEParseObjectsRecursive', $matches);
}

function motopressCEMoreHandlerBubbling( $content ){

    if ( preg_match('/(<section class="motopress-more-handler">.*?<\/section>)/', $content, $matches) ) {
        $content = preg_replace('/<section class="motopress-more-handler">.*?<\/section>/', '', $content);
        $content .= $matches[1];
    }

    return motopressCEClearEmptyRows($content);
}

function motopressCEClearEmptyRows( $content ){
    global $motopressCELibrary;
    $grid = $motopressCELibrary->getGridObjects();
    if (isset($grid['span']['type']) &&  $grid['span']['type'] === 'multiple') {
        $fullSpanShortcodeName = end($grid['span']['shortcode']);
        reset($grid['span']['shortcode']);
        $fullSpanShortcode = '\[' . $fullSpanShortcodeName .'\]';
        $fullSpanCloseShortcode = '\[\/'.$fullSpanShortcodeName.'\]';
    } else {
        $fullSpanShortcode = '\[' . $grid['span']['shortcode'].' '.$grid['span']['attr'].'="'.$grid['row']['col'] . '"\]';
        $fullSpanCloseShortcode = '\[\/'.$grid['span']['shortcode'].'\]';
    }
    return preg_replace('/\['.$grid['row']['shortcode'].'\]' . $fullSpanShortcode . $fullSpanCloseShortcode . '\[\/'.$grid['row']['shortcode'].'\]/', '', $content);
}

function motopressCEWrapOuterCode($content) {
        global $motopressCELibrary;
        $grid = $motopressCELibrary->getGridObjects();
        $content = stripslashes( $content );
        if (isset($grid['span']['type']) && $grid['span']['type'] === 'multiple') {
            $fullSpanShortcodeName = end($grid['span']['shortcode']);
            reset($grid['span']['shortcode']);
            $fullSpanShortcode = '[' . $fullSpanShortcodeName .']';
            $fullSpanCloseShortcode = '[/'.$fullSpanShortcodeName.']';
        } else {
            $fullSpanShortcode = '['.$grid['span']['shortcode'].' '.$grid['span']['attr'].'="'.$grid['row']['col'].'"]';
            $fullSpanCloseShortcode = '[/'.$grid['span']['shortcode'].']';
        }
        if (!preg_match('/.*?\['.$grid['row']['shortcode'].'\s?.*\].*\[\/'.$grid['row']['shortcode'].'\].*/s', $content)){
            $content = '['.$grid['row']['shortcode'].']' . $fullSpanShortcode  . $content . $fullSpanCloseShortcode . '[/'.$grid['row']['shortcode'].']';
        }
        preg_match('/(\A.*?)(\['.$grid['row']['shortcode'].'\s?.*\].*\[\/'.$grid['row']['shortcode'].'\])(.*\Z)/s', $content, $matches);
        $result = '';
        $beforeContent = !empty($matches[1]) ? '['.$grid['row']['shortcode'].']' . $fullSpanShortcode . $matches[1] . $fullSpanCloseShortcode . '[/'.$grid['row']['shortcode'].']' :'';
        $result .= motopressCEMoreHandlerBubbling($beforeContent);
        $result .= $matches[2];
        $afterContent = !empty($matches[3]) ? '['.$grid['row']['shortcode'].']' . $fullSpanShortcode . $matches[3] . $fullSpanCloseShortcode . '[/'.$grid['row']['shortcode'].']' :'';
        $result .= motopressCEMoreHandlerBubbling($afterContent);

        return $result;
}

function motopressCEGetMPShortcodeRegex(){
    global $motopressCELibrary;

    $shortcodes = $motopressCELibrary->getObjectsNames();

    $tagnames = array_values($shortcodes);
    $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

    $pattern  =
              '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . '(' . $tagregexp . ')'                     // 2: Shortcode name
            . '\\b'                              // Word boundary
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

    return $pattern;
}

/*
 * replacement of [ to [] for supression of incorect rendering
 */
function motopressCEScreeningDataAttrShortcodes($content){
    return htmlspecialchars_decode(preg_replace('/\[/', '[]', $content), ENT_QUOTES);
}

function motopressCEWrapCustomCode($content){
    return '[mp_code unwrap="true"]' . $content . '[/mp_code]';
}

/**
 * Create temporary post with motopress adapted content
 */
function motopressCECreateTemporaryPost($post_id, $content) {
    $post = get_post($post_id);
    $post->ID = '';
    $post->post_title = 'temporary';
    $post->post_content = '<div class="motopress-content-wrapper">' . $content . '</div>';
    $post->post_status = 'trash';

    $userRole = wp_get_current_user()->roles[0];
    $optionName = 'motopress_tmp_post_id_' . $userRole;
    $id = get_option($optionName);

    if ($id) {
        if (is_null(get_post($id))) {
            $id = wp_insert_post($post, false);
            update_option($optionName, $id);
        }
    } else {
        $id = wp_insert_post($post, false);
        add_option($optionName, $id);
    }

    $post->ID = (int) $id;

    global $wpdb;
    $wpdb->delete($wpdb->posts, array('post_parent' => $post->ID, 'post_type' => 'revision'), array('%d', '%s')); //@todo: remove in next version

    wp_update_post($post);
    wp_untrash_post($post->ID);
    motopressCEClonePostmeta($post_id, $post->ID);
    do_action('mp_post_meta', $post->ID, $post->post_type);
    do_action('mp_theme_fix', $post_id, $post->ID, $post->post_type);
    $pageTemplate = get_post_meta($post_id, '_wp_page_template', true);
    $pageTemplate = (!$pageTemplate or empty($pageTemplate)) ? 'default' : $pageTemplate;
    update_post_meta($post->ID, '_wp_page_template', $pageTemplate);

    return $post->ID;
}
//
function motopressCEClonePostmeta( $post_id_from, $post_id_to){
    motopressCEClearPostmeta($post_id_to);

    update_post_meta($post_id_to, 'motopress-ce-edited-post', $post_id_from);

    $all_post_meta = get_post_custom_keys($post_id_from);
    if (is_array($all_post_meta)){
        foreach( $all_post_meta as $post_meta_key){
            // fix of the issue with "Custom Permalinks" plugin http://atastypixel.com/blog/wordpress/plugins/custom-permalinks/
            if ($post_meta_key == "custom_permalink") continue;
            $values = get_post_custom_values($post_meta_key, $post_id_from);
            foreach ($values as $value){
                add_post_meta($post_id_to, $post_meta_key, maybe_unserialize($value));
            }
        }
    }
}

function motopressCEClearPostmeta( $post_id ) {

    $all_post_meta = get_post_custom_keys($post_id);

    if (is_array($all_post_meta)) {
        foreach( $all_post_meta as $post_meta_key){
            delete_post_meta($post_id, $post_meta_key);
        }
    }

}

function motopressCECleanupShortcode($content) {
    return strtr($content, array (
        '<p>[' => '[',
        '</p>[' => '[',
        ']<p>' => ']',
        ']</p>' => ']',
        ']<br />' => ']'
    ));
}

/**
 * Disable store revisions for tmpPost
 */
function motopressCEDisableRevisions($num, $post) {
    $tmpPostId = get_option('motopress_tmp_post_id_' . wp_get_current_user()->roles[0]);
    if ($tmpPostId && $post->ID == $tmpPostId) {
        $num = 0;
    }
    return $num;
}
