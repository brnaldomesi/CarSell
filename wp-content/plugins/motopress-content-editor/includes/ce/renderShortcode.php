<?php
function motopressCERenderShortcode() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';
    require_once dirname(__FILE__).'/Shortcode.php';

    global $motopressCELang;
    $errorMessage = strtr($motopressCELang->CERenderError, array('%name%' => $motopressCELang->CEShortcode));

    if (
        isset($_POST['closeType']) && !empty($_POST['closeType']) &&
        isset($_POST['shortcode']) && !empty($_POST['shortcode'])
    ) {
        global $motopressCESettings;
        $errors = array();

        $closeType = $_POST['closeType'];
        $shortcode = $_POST['shortcode'];
        $parameters = null;
        if (isset($_POST['parameters']) && !empty($_POST['parameters'])) {
            $parameters = json_decode(stripslashes($_POST['parameters']));
            if (!$parameters) {
                $errors[] = $errorMessage;
            }
        }
        $styles = null;
        if (isset($_POST['styles']) && !empty($_POST['styles'])) {
            $styles = json_decode(stripslashes($_POST['styles']));
            if (!$styles) {
                $errors[] = $errorMessage;
            }
        }

        if (empty($errors)) {
            global $motopressCELibrary;
            $motopressCELibrary = new MPCELibrary();
            do_action_ref_array('mp_library', array(&$motopressCELibrary));
            do_action('motopress_render_shortcode', $shortcode); //for motopress-cherryframework plugin

            $s = new MPCEShortcode();
            $content = null;
            if (isset($_POST['content']) && !empty($_POST['content'])) {
                $content = stripslashes($_POST['content']);

                if (isset($_POST['wrapRender']) && $_POST['wrapRender'] === 'true') {
                    $content = motopressCECleanupShortcode($content);
                    $content = motopressCEParseObjectsRecursive($content);
                }
            }

            $str = $s->toShortcode($closeType, $shortcode, $parameters, $styles, $content);
            echo apply_filters('the_content', $str);
//            echo do_shortcode($str);
        }

        if (!empty($errors)) {
            if ($motopressCESettings['debug']) {
                print_r($errors);
            } else {
                motopressCESetError($errorMessage);
            }
        }
    } else {
        motopressCESetError($errorMessage);
    }
    exit;
}