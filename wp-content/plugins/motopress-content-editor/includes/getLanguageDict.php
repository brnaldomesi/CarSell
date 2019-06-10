<?php

function motopressCEGetLanguageDict() {
    global $motopressCESettings;

    if (isset($motopressCESettings)) {
        $langFile = $motopressCESettings['plugin_root'].'/'.$motopressCESettings['plugin_name'].'/lang/'.$motopressCESettings['lang']['mpce'];
    } else {
        $file = get_option('motopress-language', 'en.json');
        if ($file === 'sp.json') $file = str_replace('sp', 'es', $file); //for support versions less than 1.5 where Spanish lang file called sp.json
        $langFile = dirname(__FILE__) . '/../lang/' . $file;
    }

    $contents = json_decode(file_get_contents($langFile));

    return $contents->lang;
}