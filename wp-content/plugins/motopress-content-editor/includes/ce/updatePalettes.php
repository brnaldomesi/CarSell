<?php
function motopressCEupdatePalettes() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';

    global $motopressCELang;

    if ( isset( $_POST['palettes'] ) && !empty( $_POST['palettes'] ) ){
        $palettes = $_POST['palettes'];
        update_option('motopress-palettes', $palettes);
        wp_send_json_success(array('palettes' => $palettes));
    } else {
        motopressCESetError($motopressCELang->CEColorpickerPalettesError);
    }

    exit;
}