<?php
if (!defined('ABSPATH')) exit;

global $mpsl_settings;
$menuUrl = menu_page_url($mpsl_settings['plugin_name'], false);
?>
<p><?php _e('Please install and activate WooCommerce plugin.', 'motopress-slider-lite'); ?></p>
<a class="button-secondary mpsl-button" href="<?php echo $menuUrl ?>"><?php _e('Close', 'motopress-slider-lite'); ?></a>