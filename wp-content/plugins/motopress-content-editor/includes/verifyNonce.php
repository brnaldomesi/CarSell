<?php
if (!isset($_REQUEST['nonce']) or empty($_REQUEST['nonce']) or !wp_verify_nonce($_REQUEST['nonce'], 'wp_ajax_'.$_REQUEST['action'])) {
    exit('Nonce error');
}