<?php if (!defined('ABSPATH')) exit; ?>
<h3><?php _e('Slides List: ', 'motopress-slider-lite');?></h3>
<?php if (!empty($slides)) { ?>
    <table class="table widefat mpsl-slides-table">
        <col width="20">
        <thead>
            <tr>
                <th><?php // _e('Order', 'motopress-slider-lite');?></th>
                <th><?php _e('ID', 'motopress-slider-lite'); ?></th>
                <th><?php _e('Title', 'motopress-slider-lite'); ?></th>
                <th><?php _e('Status', 'motopress-slider-lite'); ?></th>
                <th><?php _e('Visible for', 'motopress-slider-lite')?></th>
                <th><?php _e('Date From', 'motopress-slider-lite'); ?></th>
                <th><?php _e('Date Until', 'motopress-slider-lite'); ?></th>
                <th><?php _e('Action', 'motopress-slider-lite'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $menuUrl = menu_page_url($mpsl_settings['plugin_name'], false);
                foreach($slides as $slide) {
                    $slideEditUrl = add_query_arg(array('view' => 'slide','id' => $slide['id']), $menuUrl);
                    $slideDuplicateUrl = add_query_arg(array('view' => 'slide','id' => $slide['id']), $menuUrl);
                    $slideDeleteUrl = add_query_arg(array('view' => 'slide','id' => $slide['id']), $menuUrl);
                    ?>
                    <tr data-id="<?php echo $slide['id'] ?>">
                        <td class="mpsl-slide-sort-handle">
	                        <div class="mpsl-slide-sort-icon"></div>
                        </td>
                        <td><?php echo $slide['id']; ?></td>
                        <td class="mpsl-slide-name-wrapper"><a class="mpsl-slide-name" href="<?php echo $slideEditUrl; ?>"><?php echo ($slide['title']) ? $slide['title'] : '<i>' . __('not set', 'motopress-slider-lite') . '</i>';?></a></td>
                        <td><?php echo (isset($slide['options']['status']) && $slide['options']['status']) ? $slide['options']['status'] : 'published' ; ?></td>
                        <td><?php echo (isset($slide['options']['need_logged_in']) && $slide['options']['need_logged_in']) ? 'logged-in' : 'all'; ?></td>
                        <td><?php echo (empty($slide['options']['date_from'])) ? '-' : $slide['options']['date_from']; ?></td>
                        <td><?php echo (empty($slide['options']['date_until'])) ? '-' : $slide['options']['date_until']; ?></td>
                        <td class="btn-group" role="group">
                            <a href="<?php echo $slideEditUrl; ?>" class="button-secondary"><?php _e('Edit', 'motopress-slider-lite'); ?></a>
                            <a class="mpsl_duplicate_slide button-link" data-id="<?php echo $slide['id'] ?>" href="#"><?php _e('Duplicate', 'motopress-slider-lite'); ?></a>
                            <a class="mpsl_delete_slide button-link" data-id="<?php echo $slide['id'] ?>" href="#"><?php _e('Delete', 'motopress-slider-lite'); ?></a>
                        </td>
                    </tr>
                    <?php
                }
            ?>
        </tbody>
    </table>
<?php }?>
<div class="control-panel">
    <?php
    $menuUrl = menu_page_url($mpsl_settings['plugin_name'], false);
    $sliderSettingsPageUrl = add_query_arg(array('view'=>'slider', 'id'=> $this->getSliderId()), $menuUrl);
    ?>
    <button type="button" id="create_slide" class="button-primary mpsl-button" data-slider-id="<?php echo $_GET['id'] ?>"><?php _e('New Slide', 'motopress-slider-lite'); ?></button>
    <a id="slider_settings" class="button-secondary mpsl-button" href="<?php echo $sliderSettingsPageUrl;?>"><?php _e('Slider Settings', 'motopress-slider-lite'); ?></a>
    <a class="button-secondary mpsl-button" href="<?php echo $menuUrl ?>"><?php _e('Close', 'motopress-slider-lite'); ?></a>
</div>