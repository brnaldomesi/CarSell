<?php
require_once 'settings.php';

function motopressCESetError($message = '') {
    global $motopressCESettings;
    header('HTTP/1.1 500 Internal Server Error');
    wp_send_json(array(
        'debug' => $motopressCESettings ? $motopressCESettings['debug'] : false,
        'message' => $message
    ));
    exit;
}

function motopressCEMbEncodeNumericentity(&$item, $key, $options) {
    if (is_string($item)) {
        $item = mb_encode_numericentity($item, $options['convmap'], $options['encoding']);
    }
}

function motopressCEJsonEncode($array) {
    //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
    $options = array(
        'convmap' => array(0x80, 0xffff, 0, 0xffff),
        'encoding' => 'UTF-8'
    );
    array_walk_recursive($array, 'motopressCEMbEncodeNumericentity', $options);
    return mb_decode_numericentity(json_encode($array), $options['convmap'], $options['encoding']);
}