<?php
if (!defined('ABSPATH')) exit;

/** @var MPSLShortcode $shortcode */

global $mpsl_settings;
$mpClasses = '';
if (!empty($mpAtts) and (is_plugin_active('motopress-content-editor/motopress-content-editor.php') || is_plugin_active('motopress-content-editor-lite/motopress-content-editor.php'))) {
    $mpClasses = MPCEShortcode::getBasicClasses($mpsl_settings['shortcode_name']) . MPCEShortcode::getMarginClasses($mpAtts['margin']) . $mpAtts['mp_style_classes'];
	if (method_exists('MPCEShortcode', 'handleCustomStyles')) {
		$mpClasses .= MPCEShortcode::handleCustomStyles($mpAtts['mp_custom_style'], $mpsl_settings['shortcode_name']);
	}
}

if ($edit_mode) {
    $layerPresets = MPSLLayerPresetOptions::getInstance();
}

$slider = $shortcode->getSliderSettings();

$aspect = $sliderOptions['options']['height'] / $sliderOptions['options']['width'];
$sliderWrapperId = 'motoslider_wrapper' . uniqid();
$sliderCustomStyles = trim($sliderOptions['options']['custom_styles']);
if (!empty($sliderCustomStyles)) {
    echo '<style type="text/css">' . $sliderCustomStyles . '</style>';
}

//$sliderFonts = array();
$wrapperInlineStyle = $edit_mode ? 'style="width:' . ($sliderOptions['options']['width']) . 'px"' : '';
?>
<div class="motoslider_wrapper <?php echo $mpClasses; ?>" id="<?php echo $sliderWrapperId; ?>" <?php echo $wrapperInlineStyle; ?>>
    <?php
    if (is_user_logged_in() && !is_admin() && !isMPSLDisabledForCurRole() && $sliderOptions['options']['edit_slider']) {
        $editLink = admin_url("admin.php?page={$mpsl_settings['plugin_name']}&view=slider&id={$sliderId}"); ?>
        <a href="<?php echo $editLink; ?>" target="_blank" style="display: none;" class="mpsl-edit-btn dashicons dashicons-admin-generic"></a>
    <?php } ?>
    <div data-motoslider style="height: <?php echo $sliderOptions['options']['height'] . 'px'; ?>; max-height: <?php echo $sliderOptions['options']['height'] . 'px'; ?>;">
    </div>
    <div class="motoslider" style="display: none;">
        <div id="settings" <?php echo $shortcode->stringifyAttributes($slider['settings']); ?>>
        </div>
        <div id="slides">
            <?php foreach ($slider['slides'] as $slide) {
                $slideDataAttrs = '';
                $hasVisibleSlides = true;
                $layerContent = '';

                $slideBGContainerDataAttrs = ''; // animations
                ?>
                <div class="slide" <?php echo $shortcode->stringifyAttributes($slide['settings']); ?>>
                    <div class="slide_bg" <?php echo $slideBGContainerDataAttrs; ?>>
                        <?php
                        if (isset($slide['backgrounds']['color'])) {
                            echo '<div ' . $shortcode->stringifyAttributes($slide['backgrounds']['color']) . '></div>';
                        }
                        if (isset($slide['backgrounds']['gradient'])) {
                            echo '<div ' . $shortcode->stringifyAttributes($slide['backgrounds']['gradient']) . '></div>';
                        }
                        if (isset($slide['backgrounds']['image'])) {
                            echo '<div ' . $shortcode->stringifyAttributes($slide['backgrounds']['image']) . '></div>';
                        }
                        if (isset($slide['backgrounds']['video'])) {
                            echo '<div ' . $shortcode->stringifyAttributes($slide['backgrounds']['video']) . '></div>';
                        }
                        ?>
                    </div>
                    <div class="layers">
                        <?php $layers = $slide['layers'];
                        if (!empty($layers)) {
                            foreach ($layers as $layer) {
                                if (in_array($layer['type'], array('html', 'button'))) {
                                    $layerContent = $layer['content'];
                                    $style = $layer['style'];
                                } else {
                                    $layerContent = '';
                                }

                                $attributes = $shortcode->stringifyAttributes($layer['attrs']);
                                if ($layer['type'] === 'image') { ?>
                                    <div class="layer" <?php echo $attributes; ?>>
                                        <?php  echo $layer['img']; ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="layer" <?php echo $attributes; ?>><?php echo $layerContent; ?></div>
                                <?php }
                            }
                        } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
$fontsUrl = $shortcode->getFontsUrl();
$hideScriptClasses = 'motopress-hide-script mpsl-hide-script';
if ($edit_mode) {
    MPSLAdminSharing::$gFontsUrl = $fontsUrl;
} elseif ($fontsUrl) { ?>
    <p class="<?php echo $hideScriptClasses; ?>">
        <script type="text/javascript" id='mpsl-slider-fonts-load-<?php echo $sliderWrapperId; ?>'>
            var font = document.createElement('link');
            font.rel = 'stylesheet';
            font.type = 'text/css';
            font.className = 'mpsl-fonts-link';
            font.href = '<?php echo $fontsUrl; ?>';
            document.getElementsByTagName('head')[0].appendChild(font);
        </script>
    </p>
<?php }

if (defined('DOING_AJAX') && DOING_AJAX) { ?>
    <p class="<?php echo $hideScriptClasses; ?>">
        <script type="text/javascript" id='mpsl-init-slider-<?php echo $sliderWrapperId; ?>'>
            jQuery(document).ready(function ($) {
                MPSLManager.initSlider($('#<?php echo $sliderWrapperId; ?>')[0]);
            });
        </script>
    </p>
<?php } else {
    if ($edit_mode) {
        MPSLAdminSharing::$defaultPresets = $layerPresets->compile($layerPresets->getDefaultPresets(), false, true);
        MPSLAdminSharing::$presets = $layerPresets->compile($layerPresets->getPresets(), false, true);
        MPSLAdminSharing::$privatePresets = $layerPresets->compile($shortcode->getPrivatePresets(), true, true);
    }
} ?>
<p class="<?php echo $hideScriptClasses; ?>">
    <script type="text/javascript" id='mpsl-fix-height-<?php echo $sliderWrapperId; ?>'>
        var aspect = <?php echo $aspect; ?>;
        var sliderWrapper = document.getElementById('<?php echo $sliderWrapperId; ?>');
        var outerWidth = sliderWrapper.offsetWidth;
        var curHeight = outerWidth * aspect;
        sliderWrapper.querySelector('[data-motoslider]').height = curHeight + 'px';
    </script>
</p>