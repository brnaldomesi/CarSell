<?php
if (!defined('ABSPATH')) exit;

/** @var $this MPSLSliderOptions */
$sliderId = $this->getId();
?>
<div class="mpsl-slider-settings-wrapper">
<?php
if (is_null($sliderId)) {
	$sliderType = isset($_REQUEST['slider_type']) ? $_REQUEST['slider_type'] : 'custom';
	$this->updateOption('main', 'slider_type', $sliderType);
	echo '<h3>' . __('New Slider Settings', 'motopress-slider-lite') . '</h3>';
} else {
	echo '<h3>' . __('Slider Settings', 'motopress-slider-lite') . '</h3>';
	$sliderOption = $this->getOption('main', 'slider_type');
	$sliderType = $sliderOption['value'];
}

?>

<div id="mpsl-slider-settings-tabs" class="mpsl-slider-settings-wrapper mpsl_options_wrapper">
    <?php $sliderSettingsPrefix = 'mpsl-slider-settings-'; ?>
    <ul>
    <?php foreach ($this->options as $groupKey => $group) {
        echo '<li><a href="#' . $sliderSettingsPrefix . $groupKey . '">' . $group['title'] . '</a></li>';
    } ?>
    </ul>
    <?php foreach ($this->options as $groupKey => $group) { ?>

    <div id="<?php echo $sliderSettingsPrefix . $groupKey; ?>">
        <table class="form-table">
            <tbody>
            <?php foreach ($group['options'] as $optionKey => $option) { ?>

                <tr class="mpsl-option-wrapper <?php echo ($option['type'] === 'hidden' || $option['hidden']) ? 'mpsl-option-wrapper-hidden' : ''; ?>">
                <?php if (isset($option['label'])) { ?>
                    <th>
                        <?php MPSLOptionsFactory::addLabel($option); ?>
                    </th>
                    <td data-group="<?php echo $groupKey; ?>">
                        <?php MPSLOptionsFactory::addControl($option); ?>
                    </td>
                <?php } else { ?>
                    <th data-group="<?php echo $groupKey; ?>" colspan="2" class="th-full">
                        <?php MPSLOptionsFactory::addControl($option); ?>
                    </th>
                <?php } ?>
                </tr>
            <?php } ?>
            <tbody>
        </table>
        <?php if (in_array($groupKey, array('post_settings', 'woocommerce_settings'))) {
            include $mpsl_settings['plugin_dir_path'] . 'views/preview-posts.php';
        } ?>
    </div>
    <?php } ?>
</div>




<div class="control-panel">
    <?php if (is_null($sliderId)) {
        echo '<button type="button" class="button-primary mpsl-button" id="create_slider">' . __('Create Slider', 'motopress-slider-lite') . '</button>';
        echo '<a class="button-secondary mpsl-button" href="' . add_query_arg(array('view' => 'sliders') ,menu_page_url($mpsl_settings['plugin_name'], false)) . '">' . __('Cancel', 'motopress-slider-lite') . '</a>';
    } else {
        echo '<button data-id="' . $sliderId . '" type="button" class="button-primary mpsl-button" id="update_slider">' . __('Save Settings', 'motopress-slider-lite') . '</button>';
//        echo '<button data-id="' . $sliderId . '" type="button" class="button-secondary mpsl-button" id="delete_slider">' . __('Delete Slider', 'motopress-slider-lite') . '</button>';

        if($sliderType !== 'custom'){
            echo '<a id="edit_slides_template" class="button-secondary mpsl-button" href="' . add_query_arg(array('view' => 'slide', 'id' => $this->getTemplateId()), menu_page_url($mpsl_settings['plugin_name'], false)) . '">' . __('Edit Template', 'motopress-slider-lite') . '</a>';
        }else{
            echo '<a id="edit_slides" class="button-secondary mpsl-button" href="' . add_query_arg(array('view' => 'slides', 'id' => $sliderId), menu_page_url($mpsl_settings['plugin_name'], false)) . '">' . __('Edit Slides', 'motopress-slider-lite') . '</a>';
        }

        echo '<a id="slider_preview" class="button-secondary mpsl-button" href="#" data-mpsl-slider-id="'. $sliderId .'" >' . __('Preview Slider', 'motopress-slider-lite') . '</a>';
        echo '<a class="button-secondary mpsl-button" href="' . add_query_arg(array('view' => 'sliders') ,menu_page_url($mpsl_settings['plugin_name'], false)) . '">' . __('Close', 'motopress-slider-lite') . '</a>';
    }
    ?>
</div>

</div>
<?php include $mpsl_settings['plugin_dir_path'] . 'views/preview-dialog.php'; ?>