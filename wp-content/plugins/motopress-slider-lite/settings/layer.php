<?php
/** @var MPSLSlideOptions $this */
$sliderType = $this->getSliderType();

$result =  array(

	// --------------------- Content ---------------------
	'content' => array(
        'title' => __('Content', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
        	'type' => array(
                'type' => 'select',
                'layer_type' => 'all',
                'default' => 'html',
                'list' => array(
                    'html' => 'html',
                    'image' => 'image',
                    'button' => 'button',
                    'video' => 'video'
                ),
                'hidden' => true
            ),
        	'text' => array(
                'type' => 'tiny_mce',
	            'layer_type' => array('html'),
                'label' => __('Text/HTML', 'motopress-slider-lite'),
                'default' => __('lorem ipsum', 'motopress-slider-lite'),
                'plugins' => array(),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                )
            ),
            'button_text' => array(
                'type' => 'text',
	            'layer_type' => array('button'),
                'label' => __('Button Text', 'motopress-slider-lite'),
                'default' => __('Button', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
	        'button_link' => array(
                'type' => 'text',
	            'layer_type' => array('button'),
                'label' => __('Link:', 'motopress-slider-lite'),
                'default' => '#',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
            'button_autolink' => array(
                'type' => 'action_group',
	            'layer_type' => array('button'),
                'label' => __('To Post', 'motopress-slider-lite'),
                'default' => '',
                'list' => array(
                    'permalink' => __('#link to post', 'motopress-slider-lite')
                ),
	            'actions' => array(
                    'permalink' => array(
                        'button_link' => '%permalink%',
                    ),
                ),
                'classes' => 'button-link',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
            'button_target' => array(
                'type' => 'checkbox',
	            'layer_type' => array('button'),
                'label2' => __('Open in new window', 'motopress-slider-lite'),
                'default' => 'false',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
            'image_id' => array(
                'type' => 'library_image',
	            'layer_type' => array('image'),
//                'label2' => __('Image', 'motopress-slider-lite'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                ),
                'helpers' => array('image_url'),
                'button_label' => __('Select Image', 'motopress-slider-lite'),
                'select_label' => __('Select Image', 'motopress-slider-lite')
            ),
            'image_url' => array(
                'type' => 'hidden',
	            'layer_type' => array('image'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                ),
            ),
	        'image_link' => array(
                'type' => 'text',
		        'layer_type' => array('image'),
                'label' => __('Link:', 'motopress-slider-lite'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),
            'image_target' => array(
                'type' => 'checkbox',
	            'layer_type' => array('image'),
                'label2' => __('Open in new window', 'motopress-slider-lite'),
                'default' => 'false',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),
            'image_autolink' => array(
                'type' => 'action_group',
	            'layer_type' => array('image'),
                'label' => __('To Post', 'motopress-slider-lite'),
                'default' => '',
                'list' => array(
                    'permalink' => __('#link to post', 'motopress-slider-lite')
                ),
                'actions' => array(
                    'permalink' => array(
                        'image_link' => '%permalink%',
                    ),
                ),
                'classes' => 'button-link',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),
            'video_type' => array(
                'type' => 'button_group',
	            'layer_type' => array('video'),
                'default' => 'youtube',
                'list' => array(
                    'youtube' => __('Youtube', 'motopress-slider-lite'),
                    'vimeo' => __('Vimeo', 'motopress-slider-lite'),
                    'html' => __('Media Library', 'motopress-slider-lite')
                ),
                'button_size' => 'large',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
//            'video_id' => array(
//                'type' => 'library_video',
//                'default' => '',
//                'dependency' => array(
//                    'parameter' => 'video_type',
//                    'value' => 'html'
//                ),
//                'button_label' => __('Select Video', 'motopress-slider-lite')
//            ),
            'video_src_mp4' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Source MP4: ', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'video_src_webm' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Source WEBM: ', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'video_src_ogg' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Source OGG: ', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'youtube_src' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
	            'label' => __('Link to YouTube video:', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'youtube'
                )
            ),
            'vimeo_src' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default'=> '',
	            'label' => __('Link to Vimeo video:', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'vimeo'
                )
            ),
            'video_preview_image' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Preview Image URL:', 'motopress-slider-lite'),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
            'video_autoplay' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Autoplay', 'motopress-slider-lite'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
//            'video_loop' => array(
//                'type' => 'select',
//                'label' => __('Loop', 'motopress-slider-lite'),
//                'default' => 'disabled',
//                'list' => array(
//                    'disabled' => __('disabled', 'motopress-slider-lite'),
//                    'loop' => __('Loop', 'motopress-slider-lite')
//                ),
//                'dependency' => array(
//                    'parameter' => 'type',
//                    'value' => 'video'
//                )
//            ),
            'video_loop' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Loop', 'motopress-slider-lite'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
            'video_html_hide_controls' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Hide Controls', 'motopress-slider-lite'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'video_youtube_hide_controls' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Hide Controls', 'motopress-slider-lite'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'youtube'
                )
            ),
            'video_mute' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Mute', 'motopress-slider-lite'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
            'video_disable_mobile' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Disable/Hide on Mobile', 'motopress-slider-lite'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
        )
	),

	// --------------------- Position & Size ---------------------
	'position_size' => array(
        'title' => __('Position & Size', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
            'align' => array(
                'type' => 'align_table',
	            'layer_type' => 'all',
                'default' => array(
                    'vert' => 'middle',
                    'hor' => 'center'
                ),
	            'layout_dependent' => true,

                'options' => array(
                    'vert_align' => array(
                        'type' => 'hidden',
	                    'layer_type' => 'all',
                        'default' => 'middle',
		                'layout_dependent' => true
                    ),
                    'hor_align' => array(
                        'type' => 'hidden',
	                    'layer_type' => 'all',
                        'default' => 'center',
		                'layout_dependent' => true
                    ),
                    'offset_x' => array(
                        'type' => 'number',
	                    'layer_type' => 'all',
                        'default' => 0,
                        'label2' => __('X:', 'motopress-slider-lite'),
		                'layout_dependent' => true
                    ),
                    'offset_y' => array(
                        'type' => 'number',
	                    'layer_type' => 'all',
                        'default' => 0,
                        'label2' => __('Y:', 'motopress-slider-lite'),
		                'layout_dependent' => true
                    )
                )
            ),
	        'resizable' => array(
                'type' => 'checkbox',
	            'layer_type' => 'all',
                'label2' => __('Resize layer automatically when resizing browser', 'motopress-slider-lite'),
                'default' => true,
            ),
            'dont_change_position' => array(
                'type' => 'checkbox',
	            'layer_type' => 'all',
                'label2' => __('Don\'t change layer position when resizing browser', 'motopress-slider-lite'),
                'default' => false,
            ),
            'hide_width' => array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label' => __('Hide layer after this width (px)', 'motopress-slider-lite'),
//                'label2' => '',
                'default' => '',
                'min' => 0,
            ),
	        'width' => array(
                'type' => 'number',
	            'layer_type' => array('image'),
                'label2' => __('W:', 'motopress-slider-lite'),
//                'default' => 300,
                'default' => '',
                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                ),
                'layout_dependent' => true
            ),
            'html_width' => array(
                'type' => 'number',
	            'layer_type' => array('html'),
                'label2' => __('W:', 'motopress-slider-lite'),
                'default' => '',
                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                ),
                'layout_dependent' => true
            ),
	        'video_width' => array(
                'type' => 'number',
	            'layer_type' => array('video'),
                'label2' => 'W:',
                'default' => 427,
//                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                ),
                'layout_dependent' => true
            ),
            'video_height' => array(
                'type' => 'number',
	            'layer_type' => array('video'),
                'label2' => 'H:',
                'default' => 240,
//                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                ),
                'layout_dependent' => true
            ),
        )
	),

	// --------------------- Animation ---------------------
	'animation' => array(
        'title' => __('Animation', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
        	'start_animation' => $this->getOptionsByType('start', 'animation', false),
            'start_timing_function' => $this->getOptionsByType('start', 'easings', false),
            'start_duration' => $this->getOptionsByType('start', 'duration', false),
            'end_animation' => $this->getOptionsByType('end', 'animation', false),
            'end_timing_function' => $this->getOptionsByType('end', 'easings', false),
            'end_duration' => $this->getOptionsByType('end', 'duration', false),

            'start_animation_group' => array(
                'type' => 'animation_control',
	            'layer_type' => 'all',
                'id' => 'start_animation_btn',
                'animation_type' => 'start',
                'text' => __('Edit', 'motopress-slider-lite'),
                'skip' => true,
                'skipChild' => true,
                'options' => array(
                    'start_duration_clone' => $this->getOptionsByType('start', 'duration', true),
                    'start_timing_function_clone' => $this->getOptionsByType('start', 'easings',true),
                    'start_animation_clone' => $this->getOptionsByType('start', 'animation', true),
                ),
            ),
            'end_animation_group' => array(
                'type' => 'animation_control',
	            'layer_type' => 'all',
                'id' => 'end_animation_btn',
                'animation_type' => 'end',
                'text' => __('Edit', 'motopress-slider-lite'),
                'skip' => true,
                'skipChild' => true,
                'options' => array(
                    'end_duration_clone' => $this->getOptionsByType('end','duration', true),
                    'end_timing_function_clone' => $this->getOptionsByType('end', 'easings', true),
                    'end_animation_clone' => $this->getOptionsByType('end', 'animation', true),
                ),
            ),
            'start' => array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label2' => __('Display at (ms): ', 'motopress-slider-lite'),
                'default' => 1000,
                'min' => 0,
//                'max' => 9000,
            ),
            'end' => array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label2' => __('Hide at (ms): ', 'motopress-slider-lite'),
                'default' => 0,
                'min' => 0
            ),
        )
	),

	// --------------------- Style ---------------------
	'style' => array(
        'title' => __('Style', 'motopress-slider-lite'),
        'icon' => null,
        'description' => '',
        'options' => array(
			'preset' => array(
                'type' => 'style_editor',
	            'layer_type' => 'all',
                'label2' => __('Style: ', 'motopress-slider-lite'),
                'edit_label' => __('Edit', 'motopress-slider-lite'),
                'remove_label' => __('Clear', 'motopress-slider-lite'),
	            'helpers' => array('private_styles'),
	            'default' => '',
            ),
            'private_preset_class' => array(
                'type' => 'hidden',
	            'layer_type' => 'all',
                'default' => ''
            ),
            'private_styles' => array(
                'type' => 'multiple',
	            'layer_type' => 'all',
                'default' => array() // JSON
            ),
//            'hover_styles' => array(
//                'type' => 'multiple',
//                'layer_type' => array('html', 'button'),
//                'default' => array(),
//	            /*'skip' => true,
//	            'hidden' => true,
//	            'dependency' => array(
//		            'parameter' => 'type',
//		            'value' => array('html', 'button'),
//	            ),*/
//            ),
	        'classes' => array(
                'type' => 'text',
		        'layer_type' => 'all',
                'label2' => __('CSS Classes: ', 'motopress-slider-lite'),
                'default' => ''
            ),
	        'image_link_classes' => array(
                'type' => 'text',
		        'layer_type' => array('image'),
                'label2' => __('Link Custom Classes: ', 'motopress-slider-lite'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),

	        // Deprecated
	        'html_style' => array(
                'type' => 'select',
		        'layer_type' => array('html'),
                'label' => __('Theme Styles (deprecated)', 'motopress-slider-lite'),
                'default' => '',
                'list' => array(
                    '' => __('none', 'motopress-slider-lite'),
                    'mpsl-header-dark' => __('Header Dark', 'motopress-slider-lite'),
                    'mpsl-header-white' => __('Header White', 'motopress-slider-lite'),
                    'mpsl-sub-header-dark' => __('Sub-Header Dark', 'motopress-slider-lite'),
                    'mpsl-sub-header-white' => __('Sub-Header White', 'motopress-slider-lite'),
                    'mpsl-text-dark' => __('Text Dark', 'motopress-slider-lite'),
                    'mpsl-text-white' => __('Text White', 'motopress-slider-lite'),
                ),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                )
            ),
            'button_style' => array(
                'type' => 'select',
	            'layer_type' => array('button'),
                'label' => __('Theme Styles (deprecated)', 'motopress-slider-lite'),
                'default' => '',
                'list' => array(
                    '' => __('none', 'motopress-slider-lite'),
                    'mpsl-button-blue' => __('Button Blue', 'motopress-slider-lite'),
                    'mpsl-button-green' => __('Button Green', 'motopress-slider-lite'),
                    'mpsl-button-red' => __('Button Red', 'motopress-slider-lite')
                ),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),

	        // It's important to name font layer settings as their equivalent in CSS
            'font-size' => array(
                'type' => 'number',
	            'layer_type' => array('html', 'button'),
                'label' => __('Font size', 'motopress-slider-lite') . '*',
                'default' => '',
                'min' => 0,
                'unit' => 'px',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => array('html', 'button'),
                ),
                'layout_dependent' => true
            ),
            'line-height' => array(
                'type' => 'number',
	            'layer_type' => array('html', 'button'),
                'label' => __('Line height', 'motopress-slider-lite') . '*',
                'default' => '',
                'min' => 0,
                'unit' => 'px',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => array('html', 'button'),
                ),
                'layout_dependent' => true
            ),
	        'text-align' => array(
                'type' => 'select',
		        'layer_type' => array('html'),
                'label' => __('Text align', 'motopress-slider-lite') . '*',
                'default' => '',
		        'list' => array(
			        '' => __('Default', 'motopress-slider-lite'),
			        'left' => __('Left', 'motopress-slider-lite'),
			        'center' => __('Center', 'motopress-slider-lite'),
			        'right' => __('Right', 'motopress-slider-lite'),
			        'justify' => __('Justify', 'motopress-slider-lite')
		        ),
		        'dependency' => array(
                    'parameter' => 'type',
                    'value' => array('html')
                ),
                'layout_dependent' => true
            ),
	        'white-space' => array(
                'type' => 'select',
	            'layer_type' => array('html'),
                'label' => __('Whitespace', 'motopress-slider-lite') . '*',
                'default' => 'normal',
                'list' => array(
	                'normal' => __('Normal', 'motopress-slider-lite'),
	                'nowrap' => __('No-wrap', 'motopress-slider-lite')
                ),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                ),
	            'layout_dependent' => true
            ),
        )
	)

);

if ($sliderType === 'custom') {
    unset($result['content']['options']['button_autolink']);
    unset($result['content']['options']['image_autolink']);

} else { // post | woocommerce
	$result['content']['options']['text']['default'] = '%title%';
}

return $result;
