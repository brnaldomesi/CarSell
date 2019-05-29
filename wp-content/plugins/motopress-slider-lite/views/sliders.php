<?php if (!defined('ABSPATH')) exit; ?>
<div class="mpsl-sliders-list-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h3><?php _e('Sliders: ', 'motopress-slider-lite');?></h3>
                <?php if (!empty($sliders)) { ?>
                    <table class="widefat mpsl-sliders-table">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'motopress-slider-lite'); ?></th>
                                <th><?php _e('Name', 'motopress-slider-lite'); ?></th>
                                <th><?php _e('Shortcode', 'motopress-slider-lite'); ?>*</th>
                                <th><?php _e('Visible from/till', 'motopress-slider-lite'); ?></th>
                                <th><?php _e('Actions', 'motopress-slider-lite'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($sliders as $slider) {
                                include $mpsl_settings['plugin_dir_path'] . 'views/slider_row.php';
                            }?>
                        </tbody>
                    </table>
                    <div class="mpsl-shortcode-hint">
                        <i><?php echo "* From the page and/or post editor insert the shortcode from the sliders table. From the html use:";?></i><code><?php print("&lt;?php motoPressSlider( \"alias\" ) ?&gt;");  ?></code>
                    </div>
                <?php }?>

                <div class="mpsl_controls">
<!--                    <a class="button-primary" href="--><?php //echo $this->getSliderCreateUrl(); ?><!--">--><?php //_e('Create New Slider', 'motopress-slider-lite'); ?><!--</a>-->
<!--                    <a class="button-primary" href="--><?php //echo $this->getSliderCreateUrl(); ?><!--&slider_type=post">--><?php //_e('Create New Post Slider', 'motopress-slider-lite'); ?><!--</a>-->
<!--                    <a class="button-primary" href="--><?php //echo $this->getSliderCreateUrl(); ?><!--&slider_type=woocommerce">--><?php //_e('Create New WooCommerce Slider', 'motopress-slider-lite'); ?><!--</a>-->

                    <button class="button-primary" id="template-btn"><?php _e('Create Slider', 'motopress-slider-lite'); ?></button>
                    <button class="button-secondary" id="import-export-btn"><?php _e('Import & Export', 'motopress-slider-lite'); ?></button>

                </div>
                <?php include $mpsl_settings['plugin_dir_path'] . 'views/preview-dialog.php'; ?>
                <?php include $mpsl_settings['plugin_dir_path'] . 'views/import-export-dialog.php'; ?>
                <?php include $mpsl_settings['plugin_dir_path'] . 'views/slider-presets.php'; ?>
            </div>
        </div>
    </div>
</div>
