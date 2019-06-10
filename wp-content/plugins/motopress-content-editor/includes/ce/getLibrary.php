<?php
if (!isset($motopressCERequirements)) $motopressCERequirements = new MPCERequirements();
if (!isset($motopressCELang)) $motopressCELang = motopressCEGetLanguageDict();

function motopressCEGetLibrary() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/Library.php';

    $motopressCELibrary = new MPCELibrary();
    do_action_ref_array('mp_library', array(&$motopressCELibrary));
    wp_send_json($motopressCELibrary->getData());
    exit;
}