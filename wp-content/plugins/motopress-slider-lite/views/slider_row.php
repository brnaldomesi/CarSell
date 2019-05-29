<?php
if (!defined('ABSPATH')) exit;

global $mpsl_settings;
$menuUrl = admin_url( "admin.php?page=".$mpsl_settings['plugin_name']);
$sliderEditUrl = add_query_arg(array('view' => 'slider','id' => $slider['id']), $menuUrl);
$sliderEditTitle = 'Edit Slides';

$sliderType = $slider['options']['slider_type'];
if ($sliderType === 'custom') {
	$slidesEditUrl = add_query_arg(array('view' => 'slides', 'id' => $slider['id']), $menuUrl);
} else {
	$slidesEditUrl = add_query_arg(array('view' => 'slide', 'id' => $this->getTemplateId($slider['id'])), $menuUrl);
	$sliderEditTitle = 'Edit Template';
}


$sliderPreviewUrl = add_query_arg(array('view' => 'preview', 'id'=> $slider['id'], $menuUrl));

$visibleFrom = empty($slider['options']['visible_from']) ? '-' : $slider['options']['visible_from'] . 'px';
$visibleTill = empty($slider['options']['visible_till']) ? '-' : $slider['options']['visible_till'] . 'px';
?>
<tr>
    <td><?php echo $slider['id']; ?></td>
    <td class="mpsl-slider-name-wrapper"><a class="mpsl-slider-name" href="<?php echo $slidesEditUrl; ?>"><?php echo $slider['title']; ?></a></td>
    <td><?php echo '[' . $mpsl_settings['shortcode_name'] . ' ' . $slider['alias'] . ']'; ?></td>
    <td><?php echo $visibleFrom . ' / ' . $visibleTill; ?></td>
    <td class="btn-group" role="group">
        <a class="button-secondary" href="<?php echo $sliderEditUrl; ?>"><?php _e('Settings', 'motopress-slider-lite');?></a>
        <a class="button-primary" href="<?php echo $slidesEditUrl; ?>"><?php _e($sliderEditTitle, 'motopress-slider-lite');?></a>
		<?php if ($sliderType != 'custom') { ?>
			<a class="button-link" href="<?php echo $sliderEditUrl . '#mpsl-slider-settings-post_settings'; ?>"><?php _e('Content', 'motopress-slider-lite');?></a>
        <?php } ?>
		<a class="mpsl-preview-slider-btn button-link" data-mpsl-slider-id="<?php echo $slider['id']; ?>"  href="#"><?php _e('Preview', 'motopress-slider-lite');?></a>
        <a class="mpsl-duplicate-slider-btn button-link" data-mpsl-slider-id="<?php echo $slider['id']; ?>" href="#"><?php _e('Duplicate', 'motopress-slider-lite');?></a>
        <a class="mpsl-delete-slider-btn button-link" data-mpsl-slider-id="<?php echo $slider['id']; ?>" href="#"><?php _e('Delete', 'motopress-slider-lite');?></a>
    </td>
</tr>