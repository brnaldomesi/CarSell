<?php if (!defined('ABSPATH')) exit; ?>
<div class="mpsl-slider-preview">
	<div class="mpsl-resolution-buttons-wrapper hidden">
		<div class="container">
			<?php
				$class = 'item';
				$class .= (version_compare( $GLOBALS['wp_version'], '3.8', '<' )) ? ' img-icon' : '';
			?>
			<div class="<?php echo $class; ?> desktop active"><span class="dashicons dashicons-desktop"></span></div>
			<div class="<?php echo $class; ?> tablet"><span class="dashicons dashicons-tablet"></span></div>
			<div class="<?php echo $class; ?> mobile"><span class="dashicons dashicons-smartphone"></span></div>
		</div>
	</div>
	<iframe src="" frameborder="0" id="mpsl-slider-preview" name="mpsl-slider-preview"></iframe>

	<div class="mpsl-slider-preview-footer-message hidden"><em><?php _e('The displayed preview may differ from the actual result.', 'motopress-slider-lite');?></em></div>
	<div class="mpsl-preloader" style="display: none;"></div>
</div>