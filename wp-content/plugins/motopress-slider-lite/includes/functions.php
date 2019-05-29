<?php

function mpslSetError($message = '') {
    global $mpsl_settings;
    header('HTTP/1.1 500 Internal Server Error');
    wp_send_json(array(
        'debug' => $mpsl_settings ? $mpsl_settings['debug'] : false,
        'message' => $message
    ));
    exit;
}

function mpslVerifyNonce(){
    if (!isset($_REQUEST['nonce']) or empty($_REQUEST['nonce']) or !wp_verify_nonce($_REQUEST['nonce'], 'wp_ajax_'.$_REQUEST['action'])) {
        exit('Nonce error');
    }
}

/*
 * @return boolean
 */
function isMPSLDisabledForCurRole(){
    $disabledRoles = get_option('mpsl-disabled-roles', array());
    $currentUser = wp_get_current_user();
    $currentUserRoles = $currentUser->roles;

    if (is_super_admin()) return false;

    foreach ($currentUserRoles as $key => $role) {
        if ( !in_array($role, $disabledRoles)){
            return false;
        }
    }
    // in case if all user rules are disabled
    return true;
}