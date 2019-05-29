<?php

class MPSLShortcode {
//	private static $instance = null;
    private $sliderOptions = null;
    private $editmode = false;
    private $sliderType = null;
    private $sliderFonts = array();
    private $templateSlide = array();
//    private $excerptLength = 80;
    private $excerptLength = 0;
	/** @var WP_Query */
    private $postsArray = null;
    private $linkSlideToPost = false;
    private $emPrivatePresets = array();
    private $layerPresets = null;
    private $isMultipleLayout = false;
	/**
	 * @var array {[layout_name] => enabled_flag, ...}
	 */
    private $sliderLayouts = array();

    public function __construct($sliderOptions, $editmode) {
        $this->setSliderOptions($sliderOptions);
        $this->setEditmode($editmode);
        $this->setSliderType($sliderOptions['options']['slider_type']);

	    if ($this->editmode) {
		    $this->layerPresets = MPSLLayerPresetOptions::getInstance();
	    }
    }

	/*public static function getInstance($sliderOptions, $editmode) {
		if (null === self::$instance) {
			self::$instance = new self($sliderOptions, $editmode);
		}
		return self::$instance;
	}*/

    private function setSliderOptions($sliderOptions) {
        $this->sliderOptions = $sliderOptions;

	    // Multiple layout flag
	    $this->sliderLayouts[MPSLLayout::DEFAULT_LAYOUT] = true;
        foreach (array_diff(MPSLLayout::$LAYOUTS, array(MPSLLayout::DEFAULT_LAYOUT)) as $layout) {
	        $this->sliderLayouts[$layout] = $sliderOptions['options']["enable_{$layout}"];
	        if ($this->sliderLayouts[$layout]) {
		        $this->isMultipleLayout = true;
	        }
        }
    }


    private function setEditmode($editmode) {
        $this->editmode = $editmode;
    }

    private function setSliderType($sliderType) {
        $this->sliderType = $sliderType;
    }

    private function getSliderType() {
        return $this->sliderType;
    }

    public function getSliderSettings() {
        if ($this->editmode) {
            $slides = $this->getSlides();

        } else {
            switch ($this->sliderType) {
                case 'post':
                    $slides = $this->getPSlides();
                    break;

                case 'woocommerce':
                    $slides = $this->getPSlides();
                    break;

                default:
                    $slides = $this->getSlides();
                    break;
            }
        }

        return array(
            'settings' => $this->getSettings(),
            'slides' => $slides
        );

    }


    private function getSettings() {
	    $isMPCE = MPSLSharing::isMPCE();

        $timer = ($this->sliderOptions['options']['enable_timer'] && !$isMPCE);
        $delayInit = ($this->sliderOptions['options']['delay_init'] && !$isMPCE) ? $this->sliderOptions['options']['delay_init'] : 0;
	    $scrollInit = ($this->sliderOptions['options']['scroll_init'] && !$isMPCE) ? 'true' : 'false';

        if ($this->sliderType === 'custom') {
            $timer = $timer && count($this->sliderOptions['slides']) > 1;
        } else {
	        if (is_null($this->postsArray)) { // For editor
		        $timer = false;
	        } else {
		        $timer = $timer && $this->postsArray->post_count > 1;
	        }
        }
        $timer = ($timer) ? 'true' : 'false';

        $result = array(
            'data-full-window-width' => ($this->sliderOptions['options']['full_width'] and !$this->editmode) ? 'true' : 'false',
            'data-full-height' => ($this->sliderOptions['options']['full_height'] and !$this->editmode) ? 'true' : 'false',
            'data-full-height-offset' => $this->sliderOptions['options']['full_height_offset'] . $this->sliderOptions['options']['full_height_units'],
            'data-full-height-offset-container' => $this->sliderOptions['options']['full_height_container'],
            'data-full-size-grid' => ($this->sliderOptions['options']['full_size_grid'] and ! $this->editmode) ? 'true' : 'false',
            'data-timer' => $timer,
            'data-timer-delay' => $this->sliderOptions['options']['slider_delay'],
            'data-hover-timer' => ($this->sliderOptions['options']['hover_timer']) ? 'true' : 'false',
            'data-counter' => ($this->sliderOptions['options']['counter'] and !$this->editmode) ? 'true' : 'false',
            'data-slider-layout' => 'auto',
//            'data-grid-width' => $this->sliderOptions['options']['width'],
//            'data-grid-height' => $this->sliderOptions['options']['height'],
            'data-timer-reverse' => ($this->sliderOptions['options']['timer_reverse']) ? 'true' : 'false',
            'data-arrows-show' => ($this->sliderOptions['options']['arrows_show']) ? 'true' : 'false',
            'data-thumbnails-show' => ($this->sliderOptions['options']['thumbnails_show']) ? 'true' : 'false',
            'data-slideshow-timer-show' => ($this->sliderOptions['options']['slideshow_timer_show']) ? 'true' : 'false',
            'data-slideshow-ppb-show' => ($this->sliderOptions['options']['slideshow_ppb_show']) ? 'true' : 'false',
            'data-controls-hide-on-leave' => ($this->sliderOptions['options']['controls_hide_on_leave']) ? 'true' : 'false',
            'data-swipe' => ($this->sliderOptions['options']['swipe']) ? 'true' : 'false',
            'data-delay-init' => $delayInit,
            'data-scroll-init' => $scrollInit,
//            'data-start-slide' => ($this->sliderOptions['options']['start_slide']) ? $this->sliderOptions['options']['start_slide'] : 1,
        );

        if ($this->sliderOptions['options']['start_slide']){
            $result['data-start-slide'] = $this->sliderOptions['options']['start_slide'] <= 0 ? 1 : $this->sliderOptions['options']['start_slide'];
        } else {
            $result['data-start-slide'] = 1;
        }


        if (!MPSLSharing::isMPCE()) {
            $result['data-visible-from'] = $this->sliderOptions['options']['visible_from'];
            $result['data-visible-till'] = $this->sliderOptions['options']['visible_till'];
        }

        if ($this->sliderOptions['options']['width'] && $this->sliderOptions['options']['height']) {
            $result['data-layout-desktop-width'] = $this->sliderOptions['options']['width'];
            $result['data-layout-desktop-height'] = $this->sliderOptions['options']['height'];
        }

	    // Add layout data
        $enabledLayouts = array(MPSLLayout::DEFAULT_LAYOUT => 'true'); // Desktop always TRUE
        foreach (array_diff(MPSLLayout::$LAYOUTS, array(MPSLLayout::DEFAULT_LAYOUT)) as $layout) {
            $result["data-layout-{$layout}-width"] = $this->sliderOptions['options']["{$layout}_width"];
            $result["data-layout-{$layout}-height"] = $this->sliderOptions['options']["{$layout}_height"];
	        $enabledLayouts[$layout] = $this->sliderOptions['options']["enable_{$layout}"] ? 'true' : 'false';
        }
//        $result['data-layout'] = $this->toLayoutString($enabledLayouts);
	    $result['data-layout'] = implode(';', $enabledLayouts);
	    // End add layout data

        $result['data-custom-class'] = trim($this->sliderOptions['options']['custom_class']);

        //$result['data-ppb-location'] = apply_filters('mpsl_settings_slider_ppb_location', 'arrows');// TODO: Wiki | arrows/pagination
        $result['data-edit-mode'] = $this->editmode ? 'true' : '';

        return $result;
    }


	function mpslRemoveWpautop($content) {
		$content = do_shortcode(shortcode_unautop($content));
		$content = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $content);
		return $content;
	}

    private function getSlides() {
        $result = array();
        $count = 0;

        foreach ($this->sliderOptions['slides'] as $slide) {
	        if (!is_array($slide['layers'])) $slide['layers'] = array();

            $this->sliderFonts = array_merge_recursive($this->sliderFonts, $slide['options']['fonts']);

            $result[$count] = $this->getSettingsAndBackgrounds($slide);
            $countL = 0;

            foreach ($slide['layers'] as $layer) {
                $result[$count]['layers'][$countL] = $this->getLayer($layer);

                if ($layer['end'] !== '0') {
                    $result[$count]['layers'][$countL]['attrs']['data-leave-delay'] = !MPSLSharing::isMPCE() ? $layer['end'] : '0';
                }

                //TODO: add styles to layers
                //TODO: active attr??

	            if ($layer['type'] === 'html') {
		            if (!$this->editmode && !empty($result[$count]['layers'][$countL]['content'])) {
			            // Maybe wp_unslash
			            $result[$count]['layers'][$countL]['content'] = do_shortcode($result[$count]['layers'][$countL]['content']);
		            }

		            /** @todo Test */
		            if (
			            isset($layer['white-space'])
			            /*&& ($whiteSpace = $this->toLayoutString($layer['white-space']))*/
		            ) {
			            $result[$count]['layers'][$countL]['attrs']['data-white-space'] = $this->toLayoutString($layer['white-space']);
		            }
	            }

                $style = isset($result[$count]['layers'][$countL]['style']) ? $result[$count]['layers'][$countL]['style'] : '';
                $result[$count]['layers'][$countL]['attrs']['data-class'] = trim($style . ' ' . $layer['classes'] . ' ' . $this->getLayerPreset($layer));

                $countL++;
            }

            $count++;
        }

        return $result;
    }


    private function getSettingsAndBackgrounds($slide) {
        $result = array();
        $result['layers'] = array();
        $result['settings'] = array(
            'data-class' => isset($slide['options']['slide_classes']) && $slide['options']['slide_classes'] !== '' ? $slide['options']['slide_classes'] : '',
            'data-id' => isset($slide['options']['slide_id']) && $slide['options']['slide_id'] !== '' ? $slide['options']['slide_id'] : '',
            'data-animation' => isset($this->sliderOptions['options']['slider_animation']) && ($this->sliderOptions['options']['slider_animation'] !== '') ? $this->sliderOptions['options']['slider_animation'] : '',
            'data-fade-animation' => isset($this->sliderOptions['options']['slider_animation']) && ($this->sliderOptions['options']['slider_animation'] !== '') ? $this->sliderOptions['options']['slider_animation'] : '',
            'data-duration' => isset($this->sliderOptions['options']['slider_duration']) && ($this->sliderOptions['options']['slider_duration'] !== '') ? $this->sliderOptions['options']['slider_duration'] : '',
            'data-easing' => isset($this->sliderOptions['options']['slider_easing']) && ($this->sliderOptions['options']['slider_easing'] !== '') ? $this->sliderOptions['options']['slider_easing'] : '',
        );


        if (!$this->editmode && isset($slide['options']['link']) && $slide['options']['link'] !== '') {

            $result['settings']['data-link'] = $slide['options']['link'];
            $target = isset($slide['options']['link_target']) && $slide['options']['link_target'] === true ? '_blank' : '_self';
            $result['settings']['data-link-target'] = $target;

            if (isset($slide['options']['link_id']) && $slide['options']['link_id'] !== '') {
                $result['settings']['data-link-id'] = $slide['options']['link_id'];
            }
            if (isset($slide['options']['link_class']) && $slide['options']['link_class'] !== '') {
                $result['settings']['data-link-class'] = $slide['options']['link_class'];
            }
            if (isset($slide['options']['link_rel']) && $slide['options']['link_rel'] !== '') {
                $result['settings']['data-link-rel'] = $slide['options']['link_rel'];
            }
            if (isset($slide['options']['link_title']) && $slide['options']['link_title'] !== '') {
                $result['settings']['data-link-title'] = $slide['options']['link_title'];
            }
        }


        if (isset($slide['options']['bg_color_type'])) {

            if ($slide['options']['bg_color_type'] === 'color' && $slide['options']['bg_color'] !== '') {

                $result['backgrounds']['color'] = array(
                    'data-type' => $slide['options']['bg_color_type'],
                    'data-color' => $slide['options']['bg_color'],

                );

            }


            if ($slide['options']['bg_color_type'] === 'gradient' && ($slide['options']['bg_grad_color_1'] !== '' || $slide['options']['bg_grad_color_2'] !== '')) {
                $result['backgrounds']['gradient'] = array(
                    'data-type' => $slide['options']['bg_color_type'],
                    'data-color-initial' => ($slide['options']['bg_grad_color_1'] ? $slide['options']['bg_grad_color_1'] : 'transparent'),
                    'data-color-final' => ($slide['options']['bg_grad_color_2'] ? $slide['options']['bg_grad_color_2'] : 'transparent'),
                    'data-position' => ($slide['options']['bg_grad_angle'] ? $slide['options']['bg_grad_angle'] : '0') . 'deg',
                );

            }


        }


        if (isset($slide['options']['bg_image_type'])) {
            $result['backgrounds']['image'] = array();

            if ($slide['options']['bg_image_type'] === 'library' && $slide['options']['bg_image_id'] !== '') {

                $image_attributes = wp_get_attachment_image_src($slide['options']['bg_image_id'], 'full');
                if ($image_attributes) {
                    $result['backgrounds']['image']['data-src'] = $image_attributes[0];
                }

            }

            if ($slide['options']['bg_image_type'] === 'external' && $slide['options']['bg_image_url'] !== '') {
                $result['backgrounds']['image']['data-src'] = $slide['options']['bg_image_url'];
            }

            if ((isset($result['backgrounds']['image']) && count($result['backgrounds']['image']))
                || (in_array($slide['options']['bg_image_type'], array('auto', 'featured', 'first'))
                    && !$this->editmode)
            ) {
                $result['backgrounds']['image']['data-type'] = 'image';
                $result['backgrounds']['image']['data-fit'] = $slide['options']['bg_fit'];

                if ($slide['options']['bg_fit'] === 'percentage') {
                    $result['backgrounds']['image']['data-fit-x'] = $slide['options']['bg_fit_x'];
                    $result['backgrounds']['image']['data-fit-y'] = $slide['options']['bg_fit_y'];

                }

                $result['backgrounds']['image']['data-position'] = $slide['options']['bg_position'];

                if ($slide['options']['bg_position'] === 'percentage') {
                    $result['backgrounds']['image']['data-position-x'] = $slide['options']['bg_position_x'];
                    $result['backgrounds']['image']['data-position-y'] = $slide['options']['bg_position_y'];

                }

                $result['backgrounds']['image']['data-repeat'] = $slide['options']['bg_repeat'];

            }


        }
        if (!$this->editmode && ((isset($slide['options']['bg_video_src_mp4']) && $slide['options']['bg_video_src_mp4'] !== '')
                || (isset($slide['options']['bg_video_src_webm']) && $slide['options']['bg_video_src_webm'] !== '')
                || (isset($slide['options']['bg_video_src_ogg']) && $slide['options']['bg_video_src_ogg'] !== '')
            )
        ) {


            $result['backgrounds']['video'] = array(
                'data-type' => 'video',
                'data-src-mp4' => trim($slide['options']['bg_video_src_mp4']),
                'data-src-webm' => trim($slide['options']['bg_video_src_webm']),
                'data-src-ogg' => trim($slide['options']['bg_video_src_ogg']),
                'data-loop' => $slide['options']['bg_video_loop'] ? 'true' : 'false',
                'data-mute' => $slide['options']['bg_video_mute'] ? 'true' : 'false',
                'data-fillmode' => $slide['options']['bg_video_fillmode'],
                'data-cover' => $slide['options']['bg_video_cover'] ? 'true' : 'false',
                'data-cover-type' => $slide['options']['bg_video_cover_type'],
                'data-autoplay' => !MPSLSharing::isMPCE() ? 'true' : 'false',
            );
        }
        return $result;
    }


    private function getLayer($layer) {
	    $result = array();

        switch ($layer['type']) {
            case 'html' :
                $result = $this->getHtmlLayer($layer);
                break;
            case 'image' :
                $result = $this->getImageLayer($layer);
                break;
            case 'button' :
                $result = $this->getButtonLayer($layer);
                break;
            case 'video' :
                $result = $this->getVideoLayer($layer);
                break;
            default: break;
        }

	    return $result;
    }

    private function getLayerPreset($layer) {
        $layerPreset = '';
        if ($layer['preset']) {
            if ($layer['preset'] === 'private') {
                $layerPreset = $layer['private_preset_class'];
                if ($this->editmode && $layerPreset && isset($layer['private_styles'])) {
                    $this->emPrivatePresets[$layerPreset] = $layer['private_styles'];
                }
            } else {
                $layerPreset = $layer['preset'];
            }
        }

        return MPSLLayerPresetOptions::LAYER_CLASS . ' ' . $layerPreset;
    }

    private function getHtmlLayer($layer) {
        $result = array(
            'type' => 'html',
            'content' => $layer['text'],
            'style' => $layer['html_style'],
        );

        $result['attrs'] = $this->getAttrs($layer);

	    /** @todo Fix 'html_width' ## !empty($layer['html_width']) ##   (and maybe other layout options) */
	    if (isset($layer['html_width']) && !empty($layer['html_width'])) {
		    $result['attrs']['data-width'] = $this->toLayoutString($layer['html_width']);
	    }

        return $result;
    }

    private function getImageLayer($layer) {
//        $layerPreset = $this->getLayerPreset($layer);

        $result = array(
            'type' => 'image',
            'attrs' => array(
                'data-width' => $this->toLayoutString($layer['width']),
                'data-link' => $layer['image_link'],
                'data-target' => (isset($layer['image_target']) && $layer['image_target'] === true) ? '_blank' : '_self'
            ),
        );

        $imageLinkClasses = trim($layer['image_link_classes']);
//        if (trim($layer['image_link'])) {
//            $imageLinkClasses .= ' ' . $layerPreset;
//            $layerPreset = '';
//        }
        $result['attrs']['data-link-class'] = $imageLinkClasses;

//        $image_attributes = wp_get_attachment_image_src($layer['image_id'], 'full');
//        if ($image_attributes) {
//            $result['attrs']['src'] = $image_attributes[0];
//        }
        $result['attrs'] = array_merge($result['attrs'], $this->getAttrs($layer));

//        $imageAlt = get_post_meta($layer['image_id'], '_wp_attachment_image_alt', true);
//        $imageTitle = get_the_title($layer['image_id']);
//
//        if ($imageAlt) {
//            $result['attrs']['alt'] = $imageAlt;
//        }
//
//        if ($imageTitle) {
//            $result['attrs']['title'] = $imageTitle;
//        }
        $result['img'] = wp_get_attachment_image( $layer['image_id'], 'full', false, array(
            'title' =>  esc_attr( get_the_title($layer['image_id'])),
        ));

        return $result;
    }

    private function getButtonLayer($layer) {
        $result = array(
            'type' => 'button',
            'content' => $layer['button_text'],
            'style' => $layer['button_style'],
            'attrs' => array(
                'data-link' => $layer['button_link'],
                'data-target' => (isset($layer['button_target']) && $layer['button_target'] === true) ? '_blank' : '_self',
            ),
        );
        $result['attrs'] = array_merge($result['attrs'], $this->getAttrs($layer));

        return $result;

    }


    private function getVideoLayer($layer) {
        switch ($layer['video_type']) {
            case 'html':
                return $this->getHtml5Layer($layer);
                break;
            case 'youtube':
                return $this->getYoutubeLayer($layer);
                break;
            case 'vimeo' :
                return $this->getVimeoLayer($layer);
                break;
            default:
                return true;
                break;
        }
    }


    private function getHtml5Layer($layer) {
        $result = array(
            'type' => 'video',
            'attrs' => array(
                'data-video-type' => $layer['video_type'],
                'data-src-mp4' => trim($layer['video_src_mp4']),
                'data-src-webm' => trim($layer['video_src_webm']),
                'data-src-ogv' => trim($layer['video_src_ogg']),
                'data-controls' => $layer['video_html_hide_controls'] ? 'false' : 'true',
            ),
        );

        if (!empty($layer['video_preview_image'])) {
            $result['attrs']['data-poster'] = $layer['video_preview_image'];
        }
        $result['attrs'] = array_merge($result['attrs'], $this->getVideoAttrs($layer));
        $result['attrs'] = array_merge($result['attrs'], $this->getAttrs($layer));
        $result['attrs']['data-type'] = 'video';

        return $result;


    }

    private function getYoutubeLayer($layer) {
        $result = array(
            'type' => 'youtube',
            'attrs' => array(
                'data-video-type' => $layer['video_type'],
                'data-src' => $layer['youtube_src'],
                'data-controls' => $layer['video_youtube_hide_controls'] ? 'false' : 'true',
            ),
        );

        if (empty($layer['video_preview_image'])) {
            $youtubeDataApi = MPSLYoutubeDataApi::getInstance();
            $youtubeThumbnail = $youtubeDataApi->getThumbnail($layer['youtube_src']);
            if (false !== $youtubeThumbnail) {
                $result['attrs']['data-poster'] = $youtubeThumbnail;
            }
        } else {
            $result['attrs']['data-poster'] = $layer['video_preview_image'];
        }

        $result['attrs'] = array_merge($result['attrs'], $this->getVideoAttrs($layer));
        $result['attrs'] = array_merge($result['attrs'], $this->getAttrs($layer));
        $result['attrs']['data-type'] = 'youtube';

        return $result;

    }

    private function getVimeoLayer($layer) {
        $result = array(
            'type' => 'vimeo',
            'attrs' => array(
                'data-video-type' => $layer['video_type'],
                'data-src' => $layer['vimeo_src'],

            ),
        );

        if (empty($layer['video_preview_image'])) {
            $vimeoOEmbedApi = MPSLVimeoOEmbedApi::getInstance();
            $vimeoThumbnail = $vimeoOEmbedApi->getThumbnail($layer['vimeo_src']);

            if (false !== $vimeoThumbnail) {
                $result['attrs']['data-poster'] = $vimeoThumbnail;
            }
        } else {
            $result['attrs']['data-poster'] = $layer['video_preview_image'];
        }

        $result['attrs'] = array_merge($result['attrs'], $this->getVideoAttrs($layer));
        $result['attrs'] = array_merge($result['attrs'], $this->getAttrs($layer));

        $result['attrs']['data-type'] = 'vimeo';
        return $result;


    }

    private function getLayoutStyleAttrs($layer) {
	    $res = array();
	    foreach (MPSLLayout::$STYLE_OPTIONS as $name) {
		    $res["data-{$name}"] = $this->toLayoutString($layer[$name]);
	    }

	    return $res;
    }

    private function getAttrs($layer) {
	    $result = array(
            'data-type' => $layer['type'],
            'data-align-horizontal' => $this->toLayoutString($layer['hor_align']),
            'data-align-vertical' => $this->toLayoutString($layer['vert_align']),
            'data-offset-x' => $this->toLayoutString($layer['offset_x']),
            'data-offset-y' => $this->toLayoutString($layer['offset_y']),
            'data-animation' => $layer['start_animation'],
            'data-timing-function' => $layer['start_timing_function'],
            'data-duration' => $layer['start_duration'],
            'data-leave-animation' => $layer['end_animation'],
            'data-leave-timing-function' => $layer['end_timing_function'],
            'data-leave-duration' => $layer['end_duration'],
            'data-delay' => !MPSLSharing::isMPCE() ? $layer['start'] : '0',
            'data-resizable' => $layer['resizable'],
            'data-dont-change-position' => $layer['dont_change_position'],
            'data-hide-width' => $layer['hide_width'],

		    // Hover styles
//		    'data-hover-styles' => htmlspecialchars(json_encode_slashed((object) $layer['hover_styles']), ENT_QUOTES, 'UTF-8'),

//            // TODO: Test
//            'data-white-space' => "normal",
        );

	    $result = array_merge($result, $this->getLayoutStyleAttrs($layer));

	    return $result;
    }


    private function getVideoAttrs($layer) {
        $result = array();

        if ($layer['video_width'] !== '') {
            $result['data-width'] = $this->toLayoutString($layer['video_width']);
        }

        if ($layer['video_height'] !== '') {
            $result['data-height'] = $this->toLayoutString($layer['video_height']);
        }
//        $result['data-poster'] = $layer['video_preview_image'];
        $result['data-autoplay'] = ($layer['video_autoplay'] && !MPSLSharing::isMPCE()) ? 'true' : 'false';
        $result['data-loop'] = $layer['video_loop'] ? 'true' : 'false';
        $result['data-mute'] = $layer['video_mute'] ? 'true' : 'false';
        $result['data-disable-mobile'] = $layer['video_disable_mobile'] ? 'true' : 'false';


        return $result;
    }


    public function stringifyAttributes($arr) {
        $row = '';
        if (count($arr)) {
            foreach ($arr as $key => $value) {
                if ($key === 'data-edit-mode' && empty($value)) {
                    continue;
                }
                $row .= ' ' . $key . '="' . $value . '"';
            }
        }
        return $row;
    }


	// Get all presets fonts (except private)
    public function getFontsUrl() {
	    $allPresetFonts = $this->editmode ? $this->layerPresets->getAllPresetFonts() : array();
//	    $sliderFonts = array_merge_recursive($sliderFonts, $privateFonts); // old comment
	    $this->sliderFonts = array_merge_recursive($this->sliderFonts, $allPresetFonts);
        $this->sliderFonts = MPSLLayerPresetOptions::fontsUnique($this->sliderFonts);
        $fontsUrl = array();

        foreach ($this->sliderFonts as $fontName => &$fontData) {
	        if ($this->editmode) {
		        $fontAttrs = MPSLLayerPresetOptions::getFontByName($fontName);
		        if (!is_null($fontAttrs)) {
					$fontData['variants'] = $fontAttrs['variants'];
		        }
	        }

	        // Unique variants
	        $fontData['variants'] = array_unique($fontData['variants']);

	        $fontsUrlPart = $fontName;
            if (count($fontData['variants'])) {
                $fontsUrlPart .= ':' . implode(',', $fontData['variants']);
            }
            $fontsUrl[] = urlencode($fontsUrlPart);
        }

        if (count($fontsUrl)) {
            $fontsUrl = sprintf('https://fonts.googleapis.com/css?family=%s', implode('|', $fontsUrl));
        } else {
            $fontsUrl = '';
        }
        return $fontsUrl;
    }


    private function getPSlides() {
        $this->templateSlide = $this->sliderOptions['slides'][0];

        if (isset($this->sliderOptions['options']['post_excerpt_length']) && !empty($this->sliderOptions['options']['post_excerpt_length'])) {
            $this->excerptLength = (int) $this->sliderOptions['options']['post_excerpt_length'];
        }

        $db = MPSliderDB::getInstance();
        $this->postsArray = $db->getPostsByOptions($this->sliderOptions['options'], $this->sliderType);

        if($this->sliderOptions['options']['post_link_slide']){
            $this->linkSlideToPost = true;
        }

        $result = array();
        $count = 0;

        $templateFonts = $this->templateSlide['options']['fonts'];
        $templateImageType = $this->templateSlide['options']['bg_image_type'];

        if ($this->postsArray->have_posts()) {
	        while ($this->postsArray->have_posts()) {
		        $this->postsArray->the_post();
		        $post = $this->postsArray->post;

		        $this->sliderFonts = array_merge_recursive($this->sliderFonts, $templateFonts);
		        $result[$count] = $this->getSettingsAndBackgrounds($this->templateSlide);
		        if ($this->linkSlideToPost) {
			        $result[$count]['settings']['data-link'] = get_permalink($post);
			        $target = $this->sliderOptions['options']['post_link_target'] === true ? '_blank' : '_self';
			        $result[$count]['settings']['data-link-target'] = $target;
		        }

		        if (!in_array($templateImageType, array('external', 'library'))) {
			        $imgSrc = $db->getImagebyPost($post, true, $templateImageType);

			        if ($imgSrc) {
				        $result[$count]['backgrounds']['image']['data-src'] = $imgSrc;
			        } else {
				        unset($result[$count]['backgrounds']['image']);
			        }
		        }

		        $result[$count]['layers'] = $this->getContentByTemplate($post);
		        $count++;
	        }
        }
	    wp_reset_postdata();

        return $result;
    }

    private function getContentByTemplate(&$post) {
	    if ($this->sliderType === 'woocommerce') {
		    $product = new WC_Product($post);
	    }

        $arr = array();
        $count = 0;
        $db = MPSliderDB::getInstance();

        foreach ($this->templateSlide['layers'] as $layer) {
            $arr[$count] = $this->getLayer($layer);

            $content = $arr[$count]['type'] === 'html' ? $arr[$count]['content'] : $arr[$count]['attrs']['data-link'];
            preg_match_all('/%.*?%/', $content, $matches);

            $inputArr = $outputArr = array();
            $matches = array_unique($matches[0]);

            foreach ($matches as $match) {
                $inputArr[] = $match;
                $tagName = trim(trim($match, '%'));

	            // WooCommerce
                if ($this->sliderType === 'woocommerce') {
                    switch ($tagName) {
                        case 'wc_price':
                            $outputArr[] = wc_format_decimal($product->get_price(), 2);
                            break;

                        case 'wc_add_to_cart':
                            $outputArr[] = do_shortcode( '[add_to_cart id=' . $product->id . ' show_price="false" style="border:none; padding: 0px;" ]');
                            break;

                        case 'wc_currency':
                            $outputArr[] = get_woocommerce_currency_symbol();
                            break;

                        case 'wc_currency_price':
                            $outputArr[] = $product->get_price_html();
                            break;

                        case 'wc_regular_price':
                            $outputArr[] = wc_format_decimal($product->get_regular_price(), 2);
                            break;

                        case 'wc_sale_price':
                            $outputArr[] = $product->get_sale_price() ? wc_format_decimal($product->get_sale_price(), 2) : '';
                            break;

                        case 'wc_stock_status':
                            $outputArr[] = $product->is_in_stock() ? __('In Stock', 'motopress-slider-lite') : __('Out of Stock', 'motopress-slider-lite');
                            break;

                        case 'wc_stock_quantity':
                            $outputArr[] = $product->get_stock_quantity();
                            break;

                        case 'wc_weight':
                            $outputArr[] = $product->get_weight() ? wc_format_decimal($product->get_weight(), 2) : '';
                            break;

                        case 'wc_product_cats':
                            $outputArr[] = $this->getWooCommerceTaxonomies($product, 'product_cat');
                            break;

                        case 'wc_product_tags':
                            $outputArr[] = $this->getWooCommerceTaxonomies($product, 'product_tag');
                            break;

                        case 'wc_total_sales':
                            $outputArr[] = metadata_exists('post', $product->id, 'total_sales') ? (int)get_post_meta($product->id, 'total_sales', true) : 0;
                            break;

                        case 'wc_average_rating':
                            $outputArr[] = wc_format_decimal($product->get_average_rating(), 2);
                            break;

                        case 'wc_rating_count':
                            $outputArr[] = $product->get_rating_count();
                            break;
                    }
                }

	            // Post
	            switch ($tagName) {
                    case 'title':
//                        $outputArr[] = $post->post_title;
                        $outputArr[] = get_the_title($post);
                        break;

                    case 'content':
//                        $outputArr[] = $post->post_content;
						if (post_password_required($post)) {
						    $outputArr[] = get_the_password_form($post);
						} else {
							$outputArr[] = apply_filters('the_content', get_post_field('post_content', $post));
						}
                        break;

                    case 'excerpt':
                        $outputArr[] = $db->getExcerpt($post, $this->excerptLength);
                        break;

                    case 'categories':
                        $outputArr[] = $this->getCategories($post);
                        break;

                    case 'tags':
                        $outputArr[] = $this->getTags($post);
                        break;

                    case 'permalink':
                        $outputArr[] = get_permalink($post);
                        break;

                    case 'author':
                        $outputArr[] = $this->getAuthor($post);
                        break;

                    case 'post_id':
                        $outputArr[] = $post->ID;
                        break;

                    case 'image':
                        $outputArr[] = $db->getImagebyPost($post);
                        break;

                    case 'image-url':
                        $outputArr[] = $db->getImagebyPost($post, true);
                        break;

                    case 'year':
                        $outputArr[] = $db->getFormatDate('Y', $post);
                        break;

                    case 'monthnum':
                        $outputArr[] = $db->getFormatDate('m', $post);
                        break;

                    case 'month':
                        $outputArr[] = $db->getFormatDate('F', $post);
                        break;

                    case 'daynum':
                        $outputArr[] = $db->getFormatDate('j', $post);
                        break;

                    case 'day':
                        $outputArr[] = $db->getFormatDate('l', $post);
                        break;

                    case 'time':
                        $outputArr[] = $db->getFormatDate('H:i', $post);
                        break;

                    case 'date-published' :
                        $outputArr[] = $post->post_date;
                        break;

                    case 'date-modified' :
                        $outputArr[] = $post->post_modified;
                        break;

                    case 'commentnum' :
                        $outputArr[] = $post->comment_count;
                        break;

                    default:
			            $outputArr[] = get_post_meta($post->ID, $tagName, true);
						break;
                }
            }

            if ($arr[$count]['type'] === 'html') {
	            if (/*!$this->editmode && */!empty($arr[$count]['content'])) {
		            // Maybe wp_unslash
		            $arr[$count]['content'] = do_shortcode(str_replace($inputArr, $outputArr, $arr[$count]['content']));
	            }

	            /** @todo Test */
	            if (
		            isset($layer['white-space'])
//		            && ($whiteSpace = $this->toLayoutString($layer['white-space']))
	            ) {
		            $arr[$count]['attrs']['data-white-space'] = $this->toLayoutString($layer['white-space']);
	            }

            } else if ($arr[$count]['type'] === 'button' || $arr[$count]['type'] === 'image') {
                $arr[$count]['attrs']['data-link'] = str_replace($inputArr, $outputArr, $arr[$count]['attrs']['data-link']);
            }

            if ($layer['end'] !== '0') {
                $arr[$count]['attrs']['data-leave-delay'] = !MPSLSharing::isMPCE() ? $layer['end'] : '0';
            }

            $style = isset($arr[$count]['style']) ? $arr[$count]['style'] : '';
            $arr[$count]['attrs']['data-class'] = trim($style . ' ' . $layer['classes'] . ' ' . $this->getLayerPreset($layer));

            $count++;
        }

        return $arr;
    }

    private function getCategories($post) {
        $taxonomy_objects = get_object_taxonomies($post, 'objects');
        $value = '';
        foreach ($taxonomy_objects as $tax_name => $tax_info) {
            if (1 == $tax_info->hierarchical) {
                $term_list = wp_get_post_terms($post->ID, $tax_name, array("fields" => "names"));
                $value .= implode(',', $term_list);
            }
        }

        return rtrim($value, ',');
    }

    private function getTags($post) {
        $post_tags = wp_get_post_tags($post->ID);
        $tags = array();

        foreach ($post_tags as $t) {
            $tag = get_tag($t);
            $tags[] = $tag->name;
        }

        return implode(',', $tags);
    }


    private function getAuthor($post) {
        return get_the_author_meta('display_name', (int) $post->post_author);
    }

	private function getWooCommerceTaxonomies($product, $type) {
		$cats = wp_get_post_terms($product->id, $type, array('fields' => 'names'));
		return !empty($cats) ? implode(',', $cats) : '';
	}

	public function getPrivatePresets() {
		return $this->emPrivatePresets;
	}

	/**
	 * Convert to layout format string
	 * @param array $value Layout option value
	 * @return string
	 */
	private function toLayoutString($value) {
		$res = $value;

		if (is_array($value)) {
			if ($this->isMultipleLayout || true) {
				$extendedValue = array();
				$prevValue = $value[MPSLLayout::DEFAULT_LAYOUT];

				/** @todo Use only enabled layouts */
				foreach (MPSLLayout::$LAYOUTS as $layout)
				{
					$layoutEnabled = $this->sliderLayouts[$layout];

					if (
						$layoutEnabled
						&& array_key_exists($layout, $value)
						&& !is_null($value[$layout])
//						&& !empty($value[$layout])
					) {
						$extendedValue[$layout] = $value[$layout];
					} else {
						$extendedValue[$layout] = $prevValue;
					}

					$prevValue = $extendedValue[$layout];   // Save previous value
				}

				$res = implode(';', $extendedValue);

			} else {
				$res = $value[MPSLLayout::DEFAULT_LAYOUT];
			}
		}

		return $res;
	}

}