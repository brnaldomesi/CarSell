<?php
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    require_once dirname(__FILE__) . '/ce/Access.php';
    $ceAccess = new MPCEAccess();
    $access = $ceAccess->hasAccess($_POST['postID']);

    if (!$access) {
        require_once 'functions.php';
        require_once 'getLanguageDict.php';
        global $motopressCELang;
        $motopressCELang = motopressCEGetLanguageDict();
        motopressCESetError($motopressCELang->permissionDenied);
    }
}