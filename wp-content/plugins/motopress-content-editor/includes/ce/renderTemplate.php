<?php
function motopressCERenderTemplate() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';

    global $motopressCELang;
    $errorMessage = strtr($motopressCELang->CERenderError, array('%name%' => $motopressCELang->CETemplate));

    if (isset($_POST['templateId']) && !empty($_POST['templateId'])) {
        global $motopressCESettings;
        $errors = array();

        $templateId = $_POST['templateId'];
        global $motopressCELibrary;
        $motopressCELibrary = new MPCELibrary();
        do_action_ref_array('mp_library', array(&$motopressCELibrary));
        $template = &$motopressCELibrary->getTemplate($templateId);
        if ($template) {
            $content = $template->getContent();
            $content = stripslashes($content);
            $content = motopressCECleanupShortcode($content);
            $content = preg_replace('/\][\s]*/', ']', $content);
            $content = motopressCEWrapOuterCode($content);
            $content = motopressCEParseObjectsRecursive($content);
            echo apply_filters('the_content', $content);
        } else {
            $errors[] = $errorMessage;
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