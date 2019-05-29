<?php

if (!defined('ABSPATH')) exit;
global $mpsl_settings;
$sliderPresets = (include $mpsl_settings['plugin_dir_path'] . 'settings/slider-presets.php');
?>
<div id="mpsl-slider-preset-wrapper" class="hide-slider-preset-wrapper">
    <div class="container">
        <?php
        if (isset($sliderPresets)) {
        ?>
        <form action="<?php echo admin_url('admin.php?import=mpsl-importer&step=2&action=preset'); ?>" method="POST">
            <?php wp_nonce_field('mpsl-import', 'mpsl-import-nonce'); ?>
            <input type="hidden" name="mpsl-import-type" value="manual"/>

            <div class="mpsl-presets-tables-wrapper">

            <table class="widefat mpsl-templates-table mpsl-templates-group-main">
                <thead>
                <tr>
                   <th colspan="2"><?php _e('Choose Slider Type:', 'motopress-slider-lite'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sliderPresets['main'] as $key => $template) { ?>
                    <tr>
                        <td class="mpsl-templates-table-option">
                            <input type="radio" name="preset_id"
                                <?php checked($template['selected'], true); ?>
                                   value="<?php echo $template['id']; ?>"
                                   id="mpsl-preset-<?php echo $template['id']; ?>"
                                   data-id="<?php echo $template['id']; ?>"
                                   data-type="<?php echo $template['slider_type']; ?>"/>
                        </td>
                        <td>
                            <label
                                for="mpsl-preset-<?php echo $template['id']; ?>"><b><?php echo $template['label']; ?></b>
                            <?php if (isset($template['description']) && $template['description']) : ?>
                                <p class="description"><?php echo $template['description']; ?></p>
							</label>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                } ?>
                </tbody>
                <?php } ?>
            </table>

            <table class="widefat mpsl-templates-table mpsl-templates-group-sample">
                <thead>
                <tr>
                    <th colspan="3"><?php _e('Import Sample:', 'motopress-slider-lite'); ?></th>
                </tr>
                </thead>
                <tbody><tr>
                <?php foreach ($sliderPresets['sample'] as $key => $template) { ?>
                    <td>
                        <img src="<?php echo $template['screenshot'];  ?>" alt="" width="274"  height="156" >
                        <input type="radio" name="preset_id"
                                <?php checked($template['selected'], true); ?>
                                   value="<?php echo $template['id']; ?>"
                                   id="mpsl-preset-<?php echo $template['id']; ?>"
                                   data-id="<?php echo $template['id']; ?>"
                                   data-type="<?php echo $template['slider_type']; ?>"/>
                        <label
                            for="mpsl-preset-<?php echo $template['id']; ?>"><b><?php echo $template['label']; ?></b></label>
                        <?php if (isset($template['description']) && $template['description']) : ?>
                            <p class="description"><?php echo $template['description']; ?></p>
                        <?php endif; ?>
                    </td>
                    <?php
                } ?>
                </tr>
                </tbody>
            </table>

            </div>

            <div class="mpsl-slider-preset-footer">
                <input id="mpsl-create-slider-preset" type="submit" class="button-primary" value="<?php _e('Create Slider', 'motopress-slider-lite'); ?>">
            </div>

        </form>
    </div>
</div>







