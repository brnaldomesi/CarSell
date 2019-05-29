<?php if (!defined('ABSPATH')) exit; ?>

<?php
$prefix = 'mpsl-layer-settings-';
$hideClasses = array();
?>
<div class="mpsl-layer-settings-tabs mpsl-layer-settings-wrapper mpsl_layers_wrapper">

	<!------------------ Header ------------------>
    <ul>
        <?php
        foreach ($this->layerOptions as $grpKey => $grp) {
            $hideClasses[$grpKey] = isset($grp['hidden']) && $grp['hidden'] ? 'hidden' : '';
            echo '<li class="'. $hideClasses[$grpKey] .'"><a href="#' . $prefix . $grpKey . '">' . $grp['title'] . '</a></li>';
        }
        ?>
    </ul>

	<!------------------ Content ------------------>
    <?php $opts = $this->layerOptions['content']['options']; ?>
    <div data-group="content" class="<?php echo $hideClasses['content']; ?>" id="<?php echo "{$prefix}content"; ?>">
	    <div class="grid">
		    <div class="col-10-12">
			    <div class="mpsl-option-wrapper mpsl-hidden">
		            <?php MPSLOptionsFactory::addControl($opts['type']); ?>
		        </div>
		        <div class="mpsl-option-wrapper">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['text']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['text']); ?>
		        </div>
		        <div class="mpsl-option-wrapper">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['button_text']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['button_text']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['button_link']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['button_link']); ?>
		        </div>
		        <?php if(isset($opts['button_autolink'])){?>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		<!--			<div class="label-wrapper">-->
		<!--			    --><?php //MPSLOptionsFactory::addLabel($opts['button_autolink']); ?>
		<!--			</div>-->
		            <?php MPSLOptionsFactory::addControl($opts['button_autolink']); ?>
		        </div>
		        <?php }?>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addControl($opts['button_target']); ?>
		        </div>
			    <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['image_id']); ?>
		            <?php MPSLOptionsFactory::addControl($opts['image_url']); ?>
		        </div>
		        <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['video_type']);?>
		        </div>
		        <!--<div class="mpsl-option-wrapper">-->
		            <?php // MPSLOptionsFactory::addControl($opts['video_id']);?>
		        <!--</div>-->
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['video_src_mp4']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['video_src_mp4']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['video_src_webm']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['video_src_webm']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['video_src_ogg']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['video_src_ogg']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
			        <?php MPSLOptionsFactory::addLabel($opts['vimeo_src']); ?>
		            <?php MPSLOptionsFactory::addControl($opts['vimeo_src']);?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
			        <?php MPSLOptionsFactory::addLabel($opts['youtube_src']); ?>
		            <?php MPSLOptionsFactory::addControl($opts['youtube_src']);?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addLabel($opts['video_preview_image']); ?>
		            <?php MPSLOptionsFactory::addControl($opts['video_preview_image']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['image_link']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['image_link']); ?>
		        </div>
		        <?php if(isset($opts['image_autolink'])){ ?>
			        <div class="mpsl-option-wrapper mpsl-option-offset-top">
			            <?php MPSLOptionsFactory::addControl($opts['image_autolink']); ?>
			        </div>
		        <?php }?>
				<div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addControl($opts['image_target']); ?>
		        </div>

	        </div>
		    <div class="col-2-12">
				<!-- Video controls -->
			    <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['video_autoplay']); ?>
					<?php MPSLOptionsFactory::addLabel($opts['video_autoplay']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
					<?php MPSLOptionsFactory::addControl($opts['video_loop']); ?>
					<?php MPSLOptionsFactory::addLabel($opts['video_loop']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addControl($opts['video_mute']); ?>
					<?php MPSLOptionsFactory::addLabel($opts['video_mute']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addControl($opts['video_html_hide_controls']); ?>
					<?php MPSLOptionsFactory::addLabel($opts['video_html_hide_controls']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addControl($opts['video_youtube_hide_controls']); ?>
					<?php MPSLOptionsFactory::addLabel($opts['video_youtube_hide_controls']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <?php MPSLOptionsFactory::addControl($opts['video_disable_mobile']); ?>
					<?php MPSLOptionsFactory::addLabel($opts['video_disable_mobile']); ?>
		        </div>
				<!-- End Video controls -->
		    </div>
	    </div>

    </div>
	<!------------------ End Content ------------------>

	<!------------------ Position & Size ------------------>
    <?php $opts = $this->layerOptions['position_size']['options']; ?>
    <div data-group="position_size" class="<?php echo $hideClasses['position_size']; ?>" id="<?php echo "{$prefix}position_size"; ?>">

	    <div class="grid">
		    <div class="col-4-12">
			    <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['align']); ?>
		        </div>
		    </div>
		    <div class="col-2-12">
			    <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['width']); ?>
		        </div>
		        <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['html_width']); ?>
		        </div>
		        <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['video_width']); ?>
		        </div>
		        <div class="mpsl-option-wrapper">
		            <?php MPSLOptionsFactory::addControl($opts['video_height']); ?>
		        </div>
		    </div>
		    <div class="col-6-12">
	            <div class="mpsl-option-wrapper">
	                <?php MPSLOptionsFactory::addControl($opts['resizable']); ?>
	            </div>
	            <div class="mpsl-option-wrapper mpsl-option-offset-top">
	                <?php MPSLOptionsFactory::addControl($opts['dont_change_position']); ?>
	            </div>
	            <div class="mpsl-option-wrapper mpsl-hide-width-wrapper mpsl-option-offset-top">
	                <?php MPSLOptionsFactory::addControl($opts['hide_width']); ?>
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['hide_width']); ?>
		            </div>
	            </div>
		    </div>
	    </div>

    </div>
	<!------------------ End Position & Size ------------------>

	<!------------------ Animation ------------------>
    <?php $opts = $this->layerOptions['animation']['options']; ?>
    <div data-group="animation" class="<?php echo $hideClasses['animation']; ?>" id="<?php echo "{$prefix}animation"; ?>">

	    <div class="grid">
		    <div class="col-2-12">
<!--			    <div class="mpsl-option-wrapper">-->
<!--			        <div class="mpsl-hide-display-wrapper">-->
<!--			            --><?php //MPSLOptionsFactory::addControl($opts['start']); ?>
<!--			            --><?php //MPSLOptionsFactory::addControl($opts['end']); ?>
<!--			        </div>-->
<!--			    </div>-->
			    <div class="mpsl-option-wrapper">
			        <div class="mpsl-hide-display-wrapper">
			            <?php MPSLOptionsFactory::addControl($opts['start']); ?>
			        </div>
			    </div>
			    <div class="mpsl-option-wrapper mpsl-option-offset-top">
			        <div class="mpsl-hide-display-wrapper">
			            <?php MPSLOptionsFactory::addControl($opts['end']); ?>
			        </div>
			    </div>
			    <div class="mpsl-option-wrapper">
			        <div class="mpsl-duration-info">
			            <?php echo __('Slide duration (ms):', 'motopress-slider-lite') . ' ' . $this->slider->options['slideshow']['options']['slider_delay']['value']; ?>
			        </div>
			    </div>
		    </div>
		    <div class="col-9-12">
		        <div class="mpsl-option-wrapper mpsl-group-animation-wrappers">
			        <div class="mpsl-animation-wrapper">
			            <?php MPSLOptionsFactory::addControl($opts['start_animation']); ?>
			        </div>
			        <div class="mpsl-timing-function-wrapper">
		                <?php MPSLOptionsFactory::addControl($opts['start_timing_function']); ?>
			        </div>
		            <div class="mpsl-duration-wrapper">
		                <?php MPSLOptionsFactory::addControl($opts['start_duration']); ?>
		            </div>
			        <div class="mpsl-animation-group">
				        <?php MPSLOptionsFactory::addControl($opts['start_animation_group']); ?>
			        </div>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-group-animation-wrappers">
			        <div class="mpsl-animation-wrapper">
			            <?php MPSLOptionsFactory::addControl($opts['end_animation']); ?>
			        </div>
			        <div class="mpsl-timing-function-wrapper">
		                <?php MPSLOptionsFactory::addControl($opts['end_timing_function']); ?>
			        </div>
		            <div class="mpsl-duration-wrapper">
		                <?php MPSLOptionsFactory::addControl($opts['end_duration']); ?>
		            </div>
			        <div class="mpsl-animation-group-wrapper">
				        <?php MPSLOptionsFactory::addControl($opts['end_animation_group']); ?>
			        </div>
		        </div>
		    </div>
	    </div>

    </div>
	<!------------------ End Animation ------------------>

	<!------------------ Style ------------------>
    <?php $opts = $this->layerOptions['style']['options']; ?>
    <div data-group="style" class="<?php echo $hideClasses['style']; ?>" id="<?php echo "{$prefix}style"; ?>">
	    <div class="grid">
		    <div class="col-3-12">
			    <div class="mpsl-option-wrapper mpsl-hidden">
		            <?php MPSLOptionsFactory::addControl($opts['private_styles']); ?>
		        </div>
			    <div class="mpsl-option-wrapper mpsl-hidden">
		            <?php MPSLOptionsFactory::addControl($opts['private_preset_class']); ?>
		        </div>
		        <div class="mpsl-option-wrapper mpsl-option-wrapper-preset">
		            <?php MPSLOptionsFactory::addControl($opts['preset']); ?>
		        </div>
	        </div>
		    <div class="col-4-12">
		        <div class="mpsl-style-classes-wrapper">
			        <div class="mpsl-option-wrapper mpsl-option-offset-bottom">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['classes']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['classes']); ?>
			        </div>
			        <div class="mpsl-option-wrapper">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['image_link_classes']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['image_link_classes']); ?>
			        </div>

			        <div class="mpsl-option-wrapper">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['html_style']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['html_style']); ?>
			        </div>
			        <div class="mpsl-option-wrapper">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['button_style']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['button_style']); ?>
			        </div>
		        </div>
	        </div>
		    <div class="col-5-12">
		        <!-- Font options -->
		        <div class="mpsl-layout-styles-wrapper">
			        <div class="mpsl-option-wrapper">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['font-size']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['font-size']); ?>
			        </div>
			        <div class="mpsl-option-wrapper">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['line-height']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['line-height']); ?>
			        </div>
			        <div class="mpsl-option-wrapper">
			            <div class="label-wrapper">
			                <?php MPSLOptionsFactory::addLabel($opts['text-align']); ?>
			            </div>
			            <?php MPSLOptionsFactory::addControl($opts['text-align']); ?>
			        </div>
		        </div>
				<!-- End Font options-->

			    <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="label-wrapper">
		                <?php MPSLOptionsFactory::addLabel($opts['white-space']); ?>
		            </div>
		            <?php MPSLOptionsFactory::addControl($opts['white-space']); ?>
		        </div>

			    <div class="mpsl-option-wrapper mpsl-option-offset-top">
		            <div class="mpsl-layout-style-header">
		                <i><?php _e('* Custom layer options for this particular screen size', 'motopress-slider-lite'); ?></i>
		            </div>
		        </div>

	        </div>
        </div>
    </div>
	<!------------------ End Style ------------------>

</div>