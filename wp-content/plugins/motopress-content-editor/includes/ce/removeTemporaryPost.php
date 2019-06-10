<?php
function motopressCERemoveTemporaryPost() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../Requirements.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';
    require_once dirname(__FILE__).'/ThemeFix.php';
    require_once dirname(__FILE__).'/postMetaFix.php';

    global $motopressCESettings;
    global $motopressCELang;
    $errors = array();

    $post_id = $_POST['post_id'];
    $post = get_post($post_id);

    if (!is_null($post)) {
        motopressCERemoveHeadwayFix();

        $delete = wp_trash_post($post_id);

        new MPCEThemeFix(MPCEThemeFix::ACTIVATE);

        if ($delete === false) {
            $errors[] = $motopressCELang->CERemoveTemporaryPostError;
        }
    }

    if (!empty($errors)) {
        if ($motopressCESettings['debug']) {
            print_r($errors);
        } else {
            motopressCESetError($motopressCELang->CERemoveTemporaryPostError);
        }
    }
    exit();
}