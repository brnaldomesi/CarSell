<?php
global $motopressCESettings;
require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
$motopressCELang = motopressCEGetLanguageDict();
$defaultText = $motopressCELang->CEContentDefault;

$descriptionContent = <<<CONTENT
[mp_row]

[mp_span col="12"]

[mp_text]
{$defaultText}
[/mp_text]

[/mp_span]

[/mp_row]

[mp_row]

[mp_span col="12" classes="motopress-space"]

[mp_space]

[/mp_span]

[/mp_row]

[mp_row]

[mp_span col="12"]

[mp_image size="full" link_type="custom_url" link="#" target="false" align="left"]

[/mp_span]

[/mp_row]

[mp_row]

[mp_span col="12" classes="motopress-space"]

[mp_space]

[/mp_span]

[/mp_row]

[mp_row]

[mp_span col="6"]

[mp_row_inner]

[mp_span_inner col="12"]

[mp_text]
{$defaultText}
[/mp_text]

[/mp_span_inner]

[/mp_row_inner]

[mp_row_inner]

[mp_span_inner col="12"]

[mp_button text="Button" link="#" target="false" align="left" mp_style_classes="motopress-btn-color-silver motopress-btn-size-middle motopress-btn-rounded"]

[/mp_span_inner]

[/mp_row_inner]

[/mp_span]

[mp_span col="6"]

[mp_row_inner]

[mp_span_inner col="12"]

[mp_text]
{$defaultText}
[/mp_text]

[/mp_span_inner]

[/mp_row_inner]

[mp_row_inner]

[mp_span_inner col="12"]

[mp_button text="Button" link="#" target="false" align="left" mp_style_classes="motopress-btn-color-silver motopress-btn-size-middle motopress-btn-rounded"]

[/mp_span_inner]

[/mp_row_inner]

[/mp_span]

[/mp_row]
CONTENT;
