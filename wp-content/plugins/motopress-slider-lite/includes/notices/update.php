<?php
if (!defined('ABSPATH')) exit;
?>
<div id="message" class="updated">
    <h4><?php _e('<strong>Slider Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'motopress-slider-lite'); ?></h4>
    <p class="submit"><a href="<?php echo add_query_arg( 'mpsl_do_update', 'true', admin_url('admin.php?page=motopress-slider')); ?>" class="mpsl-update-now button-primary"><?php _e('Run the updater', 'motopress-slider-lite'); ?></a></p>
</div>
<script type="text/javascript">
    jQuery('.mpsl-update-now').on('click', function() {
        var answer = confirm('<?php _e('It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'motopress-slider-lite'); ?>');
        return answer;
    });
</script>