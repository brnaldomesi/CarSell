<?php
/**
 * Description of Shortcodes
 *
 */
class MPCEShortcode {
    const PREFIX = 'mp_';

    private $shortcodeFunctions = array(
        'row' => 'motopressRow',
        'row_inner' => 'motopressRowInner',
        'span' => 'motopressSpan',
        'span_inner' => 'motopressSpanInner',
        'text' => 'motopressText',
        'heading' => 'motopressTextHeading',
        'image' => 'motopressImage',
        'image_slider' => 'motopressImageSlider',
        'grid_gallery' => 'motopressGridGallery',
        'video' => 'motopressVideo',
        'code' => 'motopressCode',
        'space' => 'motopressSpace',
        'button' => 'motopressButton',
        'wp_archives' => 'motopressWPWidgetArchives',
        'wp_calendar' => 'motopressWPWidgetCalendar',
        'wp_categories' => 'motopressWPWidgetCategories',
        'wp_navmenu' => 'motopressWPNavMenu_Widget',
        'wp_meta' => 'motopressWPWidgetMeta',
        'wp_pages' => 'motopressWPWidgetPages',
        'wp_posts' => 'motopressWPWidgetRecentPosts',
        'wp_comments' => 'motopressWPWidgetRecentComments',
        'wp_rss' => 'motopressWPWidgetRSS',
        'wp_search' => 'motopressWPWidgetSearch',
        'wp_tagcloud' => 'motopressWPWidgetTagCloud',
        'wp_widgets_area' => 'motopressWPWidgetArea',
        'gmap' => 'motopressGoogleMap',
        'embed' => 'motopressEmbedCode',
        'quote' => 'motopressQuotes',
        'members_content' => 'motopressMembersContent',
        'social_buttons' => 'motopressSocialShare',
        'social_profile' => 'motopressSocialProfile',
        'google_chart' => 'motopressGoogleCharts',
        'wp_audio' => 'motopressWPAudio',
        'tabs' => 'motopressTabs',
        'tab' => 'motopressTab',
        'accordion' => 'motopressAccordion',
        'accordion_item' => 'motopressAccordionItem',
        'table' => 'motopressTable'
    );

    public static $attributes = array(
        'closeType' => 'data-motopress-close-type',
        'shortcode' => 'data-motopress-shortcode',
        'group' => 'data-motopress-group',
        'parameters' => 'data-motopress-parameters',
        'styles' => 'data-motopress-styles',
        'content' => 'data-motopress-content',
        'unwrap' => 'data-motopress-unwrap'
    );

    public static $styles = array(
        'mp_style_classes' => '',
        'margin' => ''
    );

    private static $curPostSaveInVer;
    private static $isNeedFix = false;
    public static function setCurPostData($wp, $id = null) {
//        var_dump(get_the_ID());
//        global $post;
//        var_dump($post->ID);
//        global $wp_query;
//        var_dump($wp_query->post->ID);

        $postId = (isset($id) && !empty($id)) ? (int) $id : get_the_ID();
        self::$curPostSaveInVer = get_post_meta($postId, 'motopress-ce-save-in-version', true);
        self::$isNeedFix = version_compare(self::$curPostSaveInVer, '1.5', '<');
    }

    public function register() {
        add_filter( 'the_content', array($this, 'runShortcodesBeforeAutop'), 8 );
        $shortcode = $this->shortcodeFunctions;
        foreach ($shortcode as $sortcode_name => $function_name) {
            add_shortcode(self::PREFIX . $sortcode_name, array($this, $function_name));
        }
        // shortcodes which use 'the_content' must register here
        add_shortcode(self::PREFIX . 'posts_grid', array($this, 'motopressPostsGrid'));
    }

    /**
     * @param string $content
     * @return string
     */
    public function runShortcodesBeforeAutop($content) {
        global $shortcode_tags;
        // Back up current registered shortcodes and clear them all out
        $orig_shortcode_tags = $shortcode_tags;
        remove_all_shortcodes();

        $shortcode = $this->shortcodeFunctions;
        foreach ($shortcode as $sortcode_name => $function_name) {
            add_shortcode(self::PREFIX . $sortcode_name, array($this, $function_name));
        }

        // Do the shortcode (only the [motopress shortcodes] are registered)
        $content = do_shortcode( $content );
        // Put the original shortcodes back
        $shortcode_tags = $orig_shortcode_tags;

        return $content;
    }

    /**
     * @param string $content
     * @return string
     */
    public function cleanupShortcode($content) {
        return strtr($content, array(
            '<p>[' => '[',
            '</p>[' => '[',
            ']<p>' => ']',
            ']</p>' => ']',
            ']<br />' => ']'
        ));
    }

    /**
     * @param string $closeType
     * @param string $shortcode
     * @param stdClass $parameters
     * @param stdClass $styles
     * @param string $content
     * @return string
     */
    public function toShortcode($closeType, $shortcode, $parameters, $styles, $content) {
        $str = '[' . $shortcode;
        if (!is_null($parameters)) {
            foreach ($parameters as $attr => $values) {
                if (isset($values->value)) {
                    $str .= ' ' . $attr . '="' . $values->value . '"';
                }
            }
        }
        if (!is_null($styles)) {
            foreach ($styles as $attr => $values) {
                if (isset($values->value)) {
                    $str .= ' ' . $attr . '="' . $values->value . '"';
                }
            }
        }
        $str .= ']';
        if ($closeType === MPCEObject::ENCLOSED) {
            if (!is_null($content)) {
                $str .= $content;
            }
            $str .= '[/' . $shortcode . ']';
        }
        return $str;
    }

    /**
     * @param array $atts
     * @return array
     */
    public static function addStyleAtts($atts = array()) {
        $styles = self::$styles;
        $styles['classes'] = ''; //for support versions less than 1.4.6 where margin save in classes
        $styles['custom_class'] = ''; //for support versions less than 1.5 where mp_style_classes has not yet been

        $intersect = array_intersect_key($atts, $styles);
        if (!empty($intersect)) {
            echo '<p>Shortcode attributes intersect with style attributes</p>';
            var_dump($intersect);
        }
        return array_merge($atts, $styles);
    }

    /**
     * @param string $margin
     * @param bool $space
     * @return string
     */
    public static function getMarginClasses($margin, $space = true) {
        $result = '';
        if (is_string($margin)) {
            $margin = trim($margin);
            if (!empty($margin)) {
                $margin = explode(',', $margin, 4);
                $margin = array_map('trim', $margin);

                $marginClasses = array();
                if (count($margin) === 4 && count(array_unique($margin)) === 1 && $margin[0] !== 'none') {
                    $marginClasses[] = 'motopress-margin-' . $margin[0];
                } else {
                    $sides = array('top', 'bottom', 'left', 'right');
                    foreach ($margin as $key => $value) {
                        if ($value !== 'none') {
                            $marginClasses[] = 'motopress-margin-' . $sides[$key] . '-' . $value;
                        }
                    }
                }
                if (!empty($marginClasses)) $result = implode(' ', $marginClasses);
                if (!empty($result) && $space) $result = ' ' . $result;
            }
        }
        return $result;
    }

    /**
     * @param string $shortcodeName
     * @param bool $space
     * @return string
     */
    public static function getBasicClasses($shortcodeName, $space = false) {
        global $motopressCELibrary;
        $result = '';
        if (isset($motopressCELibrary) && !empty($shortcodeName)) {
            $object = &$motopressCELibrary->getObject($shortcodeName);
            if ($object) {
                $styleClasses = &$object->getStyle('mp_style_classes');
                if (array_key_exists('basic', $styleClasses) && !empty($styleClasses['basic'])) {
                    $classes = array();
                    if (!array_key_exists('class', $styleClasses['basic'])) {
                        foreach ($styleClasses['basic'] as $value) {
                            $classes[] = $value['class'];
                        }
                    } else {
                        $classes[] = $styleClasses['basic']['class'];
                    }
                    if (!empty($classes)) $result = implode(' ', $classes);
                    if (!empty($result) && $space) $result = ' ' . $result;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $shortcodeName
     * @param string $classes
     * @return string
     */
    public static function enqueueCustomStyle($shortcodeName, $classes){
        global $motopressCELibrary;
        global $motopressCESettings;
        if (!empty($classes)) {
            $object = &$motopressCELibrary->getObject($shortcodeName);
            if ($object) {
                $styleClasses = &$object->getStyle('mp_style_classes');
                if (array_key_exists('predefined', $styleClasses)
                        && array_key_exists('google-font-classes', $styleClasses['predefined'])
                        && array_key_exists('values', $styleClasses['predefined']['google-font-classes'])) {
                    $fontClasses = $styleClasses['predefined']['google-font-classes']['values'];
                    $classes = explode(' ', $classes);
                    $enqueueArr = array_intersect(array_keys($fontClasses), $classes);
                    foreach($enqueueArr as $key) {
                        $handle = 'motopress-custom-class-' . $key;
                        wp_enqueue_style($handle, $fontClasses[$key]['external']);
                    }
                }
            }
        }
    }

    /**
     * @param string $styleClasses
     * @param bool $space
     * @return string
     */
/*
    public static function splitStyleClasses($styleClasses, $space = true) {
        $result = array(
            'responsiveUtility' => '',
            'mpStyle' => ($space) ? ' ' . $styleClasses : $styleClasses
        );
        if (!empty($styleClasses)) {
            $pattern = '/mp-(hidden|visible)-(phone|tablet|desktop)/i';
            preg_match_all($pattern, $styleClasses, $matches);
            if (!empty($matches[0])) {
                $result['responsiveUtility'] = implode(' ', $matches[0]);
                $result['mpStyle'] = implode(' ', preg_grep($pattern, explode(' ', $styleClasses), PREG_GREP_INVERT));
                if ($space) {
                    foreach ($result as &$val) {
                        $val = ' ' . $val;
                    }
                    unset($val);
                }
            }
        }
        return $result;
    }
*/

    const DEFAULT_YOUTUBE_BG = 'https://www.youtube.com/watch?v=hPLoY1rQ2z4';
    const DEFAULT_VIDEO_BG_MP4 = 'http://static.getmotopress.com/motopress-video-background-demo.mp4';
    const DEFAULT_VIDEO_BG_WEBM = 'http://static.getmotopress.com/motopress-video-background-demo.webm';
    const DEFAULT_VIDEO_BG_OGG = 'http://static.getmotopress.com/motopress-video-background-demo.ogv';
    const DEFAULT_VIDEO_BG_COVER = 'http://static.getmotopress.com/motopress-background-video-overlay.png';
    // paste default image link also in controls.js parallax render
    const DEFAULT_PARALLAX_IMAGE = 'http://static.getmotopress.com/motopress-parallax-background-demo.jpg';

    public static function motopressRowUniversal ($atts, $content, $shortcode){
        extract(shortcode_atts(self::addStyleAtts(array(
            'bg_media_type' => 'disabled',
            
//            'parallax_speed' => 5
        )), $atts));
        $bg_video_mp4 = self::DEFAULT_VIDEO_BG_MP4;
        $bg_video_webm = self::DEFAULT_VIDEO_BG_WEBM;
        $bg_video_ogg = self::DEFAULT_VIDEO_BG_OGG;
        $bg_video_cover = self::DEFAULT_VIDEO_BG_COVER;
        $bg_video_repeat = 'true';
        $bg_video_mute = 'true';
        $bg_video_youtube = self::DEFAULT_YOUTUBE_BG;
        $bg_video_youtube_cover = self::DEFAULT_VIDEO_BG_COVER;
        $bg_video_youtube_repeat = 'true';
        $bg_video_youtube_mute = 'true';
        $parallax_image = self::DEFAULT_PARALLAX_IMAGE;
        $style = '';
        if (!empty($mp_style_classes)) {
            if (!self::isContentEditor() && preg_match('/\bmp-row-fullwidth\b/', $mp_style_classes)) {
                wp_enqueue_script('mp-row-fullwidth');
            }
            $mp_style_classes = ' ' . $mp_style_classes;
        }
        if ( $bg_media_type === 'video' || $bg_media_type === 'youtube') {
            wp_enqueue_script('mp-video-background');
        }
        if ( $bg_media_type === 'parallax') {
            wp_enqueue_script('stellar');
            wp_enqueue_script('mp-row-parallax');
        }
        if ( $bg_media_type === 'youtube') {
            wp_enqueue_script('mp-youtube-api');
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $videoHTML = '';
        $bgClass = '';
        $dataParallax = '';
        switch ($bg_media_type) {
            case 'video' :
                $videoHTML = self::generateHTML5BackgroundVideoHTML($bg_video_webm, $bg_video_mp4, $bg_video_ogg, $bg_video_cover, $bg_video_mute, $bg_video_repeat);
                $bgClass = ' mp-row-video';
                break;
            case 'youtube' :
                $videoHTML = self::generateYoutubeBackgroundVideoHtml($bg_video_youtube, $bg_video_youtube_cover, $bg_video_youtube_repeat, $bg_video_youtube_mute);
                $bgClass = ' mp-row-video';
                break;
            case 'parallax' :
                $parallax_speed = 0.5;
                $dataParallax = ' data-stellar-background-ratio="' . $parallax_speed . '" ';
                if (!empty($parallax_image)) {
                    $imgSrc[0] = $parallax_image;
                    $style = ' style=\'background-image:url("' . $imgSrc[0] . '"); \'';
                }
                $bgClass = ' motopress-row-parallax';
        }
        return '<div ' . $dataParallax . $style . ' class="mp-row-fluid motopress-row' . $bgClass . self::getMarginClasses($margin) . self::getBasicClasses($shortcode, true) . $mp_style_classes . '">' . do_shortcode($content). $videoHTML . '</div>';
    }

    public function motopressRow($atts, $content = null) {
        if (!self::isContentEditor()) {
            wp_enqueue_style('mpce-bootstrap-grid');
            //@todo for support custom grid must enqueue on all pages
//            wp_enqueue_style('mpce-theme');
        }
        return self::motopressRowUniversal($atts, $content, self::PREFIX . 'row');
    }

    public function motopressRowInner($atts, $content = null) {
        return self::motopressRowUniversal($atts, $content, self::PREFIX . 'row_inner');
    }

    public static function renderYoutubeBackgroundVideo(){
        $bg_video_youtube = self::DEFAULT_YOUTUBE_BG;
        $bg_video_youtube_cover = self::DEFAULT_VIDEO_BG_COVER;
        $bg_video_youtube_repeat = 'true';
        $bg_video_youtube_mute = 'true';
        exit(self::generateYoutubeBackgroundVideoHtml($bg_video_youtube, $bg_video_youtube_cover, $bg_video_youtube_repeat, $bg_video_youtube_mute));
    }

    public static function generateYoutubeBackgroundVideoHtml($bg_video_youtube, $bg_video_youtube_cover, $bg_video_youtube_repeat, $bg_video_youtube_mute){
        if (!empty($bg_video_youtube)) {
            if (preg_match('/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/', $bg_video_youtube, $idVideo)) {
                $videoHTML = '<section class="mp-video-container"><div class="mp-youtube-container">';
                if (self::isContentEditor()){
                    $videoHTML .= '<img src="http://img.youtube.com/vi/' . $idVideo[1] . '/0.jpg">';
                } else {
                    if ($bg_video_youtube_repeat == 'true') {
                        $loop ='&loop=1';
                        $playlist = '&playlist=' . $idVideo[1];
                    } else {
                        $loop = '';
                        $playlist = '';
                    }
                    $dataMute = ($bg_video_youtube_mute == 'true') ? ' data-mute="1"' : ' data-mute="0"';
                    $videoHTML .= '<iframe class="mp-youtube-video"' . $dataMute . ' src="https://www.youtube.com/embed/' . $idVideo[1] . '?controls=0&rel=0&showinfo=0&autoplay=1' . $loop . '&disablekb=1&showsearch=0&iv_load_policy=3&enablejsapi=1&vq=hd720' . $playlist . '"></iframe>';
                }
                $videoCover = '<div class="mp-youtube-cover"></div>';
                if (!empty($bg_video_youtube_cover)){
                    $imgSrc[0] = $bg_video_youtube_cover;
                    if ($imgSrc) {
                        $videoCover = '<div class="mp-youtube-cover" style="background-image:url(\'' . $imgSrc[0] . '\')"></div>';
                    }
                }
                $videoHTML .= '</div>' . $videoCover . '</section>';
            }
        } else {
            $videoHTML = '';
        }
        return $videoHTML;
    }

    public static function renderHTML5BackgroundVideo(){
        $bg_video_webm = self::DEFAULT_VIDEO_BG_WEBM;
        $bg_video_mp4 = self::DEFAULT_VIDEO_BG_MP4;
        $bg_video_ogg = self::DEFAULT_VIDEO_BG_OGG;
        $bg_video_cover = self::DEFAULT_VIDEO_BG_COVER;
        $bg_video_mute = 'true';
        $bg_video_repeat = 'true';
        exit(self::generateHTML5BackgroundVideoHTML($bg_video_webm, $bg_video_mp4, $bg_video_ogg, $bg_video_cover, $bg_video_mute, $bg_video_repeat));
    }

    public static function generateHTML5BackgroundVideoHTML($bg_video_webm, $bg_video_mp4, $bg_video_ogg, $bg_video_cover, $bg_video_mute, $bg_video_repeat){
        $loop = ($bg_video_repeat == 'true') ? ' loop="loop"' : '';
        $mute = ($bg_video_mute == 'true') ? ' muted="muted"' : '';
        $autoplay = self::isContentEditor() ? '' : ' autoplay="autoplay"';
        $videoCover = '';
        if (!empty($bg_video_cover)){
            $imgSrc[0] = $bg_video_cover;
            if ($imgSrc) {
                $videoCover = '<div class="mp-video-cover" style="background-image:url(\'' . $imgSrc[0] . '\')"></div>';
            }
        }


        $videoHTML = '<section class="mp-video-container"><video' . $autoplay . $loop . $mute . '>';
        if (!empty($bg_video_mp4)) {
            $videoHTML .= '<source id="mp4" src="' . $bg_video_mp4 . '" type="video/mp4">';
        }
        if (!empty($bg_video_ogg)) {
            $videoHTML .= '<source id="ogg" src="' . $bg_video_ogg . '" type="video/ogg">';
        }
        if (!empty($bg_video_webm)) {
            $videoHTML .= '<source id="webm" src="' . $bg_video_webm . '" type="video/webm">';
        }
        $videoHTML .= '</video>' . $videoCover . '</section>';
        return $videoHTML;
    }

    public function motopressSpan($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'col' => 12,
            'style' => ''
        )), $atts));

        if (!empty($classes)) $classes = ' ' . $classes;
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        if (!empty($style)) $style = ' style="' . $style . '"';

        return '<div class="mp-span' . $col . ' motopress-clmn' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'span', true) . $mp_style_classes . '"' . $style . '>' . do_shortcode($content) . '</div>';
    }

    public function motopressSpanInner($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'col' => 12,
            'style' => ''
        )), $atts));

        if (!empty($classes)) $classes = ' ' . $classes;
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        if (!empty($style)) $style = ' style="' . $style . '"';

        return '<div class="mp-span' . $col . ' motopress-clmn' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'span_inner', true) . $mp_style_classes . '"' . $style . '>' . do_shortcode($content) . '</div>';
    }

    public function motopressText($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(), $atts));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        self::enqueueCustomStyle(self::PREFIX . 'text', $mp_style_classes);
        return '<div class="motopress-text-obj' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'text', true) . $mp_style_classes . '">' . $content . '</div>';
    }

    public function motopressTextHeading($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(), $atts));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = empty($content) ? '<h2>' . $content . '</h2>' : $content;
        self::enqueueCustomStyle(self::PREFIX . 'heading', $mp_style_classes);
        return '<div class="motopress-text-obj' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'heading', true) . $mp_style_classes . '">' . $result . '</div>';
    }

    public function motopressImage($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'id' => '',
            'link_type' => 'custom_url',
            
            'rel' => '',
            'align' => 'left',
            'size' => 'full',
            'custom_size' => ''
        )), $atts));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (isset($id) && !empty($id)) {
            $id = (int) $id;
            $attachment = get_post($id);
            if (!empty($attachment) && $attachment->post_type === 'attachment') {
                if (wp_attachment_is_image($id)) {
                    $title = esc_attr($attachment->post_title);

                    $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true)));
                    if (empty($alt)) {
                        $alt = trim(strip_tags($attachment->post_excerpt));
                    }
                    if (empty($alt)) {
                        $alt = trim(strip_tags($attachment->post_title));
                    }

                    if ($size === 'custom') {
                        $size = array_pad(explode('x', $custom_size), 2, 0);
                    }
                    $imgSrc = wp_get_attachment_image_src( $id, $size );
                    $imgSrc = ($imgSrc && isset($imgSrc[0])) ? $imgSrc[0] : false;

                } else {
                    $error = $motopressCELang->CEAttachmentNotImage;
                }
            } else {
                $error = $motopressCELang->CEAttachmentEmpty;
            }
        } else {
//            $error = $motopressCELang->CEImageIdEmpty;
            $imgSrc = $motopressCESettings['plugin_root_url'] . '/' . $motopressCESettings['plugin_name'] . '/images/ce/no-image.png?ver=' . $motopressCESettings['plugin_version'];
        }

        if (empty($error)) {
            $img = '<img';
            if ($imgSrc) {
                $img .= ' src="' . $imgSrc  . '"';
            }
            if (!empty($title)) {
                $img .= ' title="' . $title . '"';
            }
            if (!empty($alt)) {
                $img .= ' alt="' . $alt . '"';
            }
            if (self::$isNeedFix && empty($mp_style_classes)) {
                if (!empty($custom_class)) $mp_style_classes = $custom_class;
            }
            if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
            $img .= ' class="'. self::getBasicClasses(self::PREFIX . 'image') . $mp_style_classes .'"';
            $img .= ' />';
            
        }

        if (!empty($classes)) $classes = ' ' . $classes;
        $imgHtml = '<div class="motopress-image-obj motopress-text-align-' . $align . $classes . self::getMarginClasses($margin) . '">';
        if (empty($error)) {
            $imgHtml .= $img;
        } else {
            $imgHtml .= $error;
        }
        $imgHtml .= '</div>';

        return $imgHtml;
    }

    public function motopressImageSlider($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'ids' => '',
            'size' => 'full',
            'custom_size' => '',
            
        )), $atts));
        $animation = 'fade';
        $control_nav = 'true';
        $slideshow = 'true';
        $slideshow_speed = 7;
        $animation_speed = 600;
        $smooth_height = 'false';

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (isset($ids) && !empty($ids)) {
            $ids = trim($ids);
            $ids = explode(',', $ids);
            $ids = array_filter($ids);

            if (!empty($ids)) {
                wp_enqueue_style('mpce-flexslider');
                wp_enqueue_script('mpce-flexslider');

                $images = array();
                $imageErrors = array();
                foreach ($ids as $id) {
                    $id = (int) trim($id);

                    $attachment = get_post($id);
                    if (!empty($attachment) && $attachment->post_type === 'attachment') {
                        if (wp_attachment_is_image($id)) {
                            $title = esc_attr($attachment->post_title);

                            $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true)));
                            if (empty($alt)) {
                                $alt = trim(strip_tags($attachment->post_excerpt));
                            }
                            if (empty($alt)) {
                                $alt = trim(strip_tags($attachment->post_title));
                            }
                            if ($size === 'custom') {
                                $size = array_pad(explode('x', $custom_size), 2, 0);
                            }
                            $img = '<img';
                            $imgSrc = wp_get_attachment_image_src( $id, $size );
                            if ($imgSrc && isset($imgSrc[0])) {
                                $img .= ' src="' . $imgSrc[0]  . '"';
                            }
                            if (!empty($title)) {
                                $img .= ' title="' . $title . '"';
                            }
                            if (!empty($alt)) {
                                $img .= ' alt="' . $alt . '"';
                            }
                            $img .= ' />';

                            $images[] = $img;
                            unset($img);
                        } else {
                            $imageErrors[] = $motopressCELang->CEAttachmentNotImage;
                        }
                    } else {
                        $imageErrors[] = $motopressCELang->CEAttachmentEmpty;
                    }
                }
            } else {
                $error = $motopressCELang->CEImagesNotSet;
            }
        } else {
            $error = $motopressCELang->CEImagesNotSet;
        }

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $uniqid = uniqid();
        $sliderHtml = '<div class="motopress-image-slider-obj flexslider' . $classes . self::getMarginClasses($margin) . '" id="' . $uniqid . '">';
        if (empty($error)) {
            if (!empty($images)) {
                $sliderHtml .= '<ul class="slides' . self::getBasicClasses(self::PREFIX . 'image_slider', true) . $mp_style_classes . '">';
                foreach ($images as $image) {
                    $sliderHtml .= '<li>' . $image . '</li>';
                }
                $sliderHtml .= '</ul>';
            } elseif (!empty($imageErrors)) {
                $sliderHtml .= '<ul class="'. self::getBasicClasses(self::PREFIX . 'image_slider') . $mp_style_classes .'">';
                foreach ($imageErrors as $imageError) {
                    $sliderHtml .= '<li>' . $imageError . '</li>';
                }
                $sliderHtml .= '</ul>';
            }
        } else {
            $sliderHtml .= $error;
        }
        $sliderHtml .= '</div>';

        $slideshow = (self::isContentEditor()) ? 'false' : $slideshow;
        $keyboard = (self::isContentEditor()) ? 'false' : 'true';
        $slideshow_speed = (int) $slideshow_speed * 1000;

        if ($animation !== 'slide')
            $smooth_height = 'false';

        $sliderHtml .= '<p class="motopress-hide-script"><script type="text/javascript">
            jQuery(document).ready(function($) {
                var mpImageSlider = $(".motopress-image-slider-obj#' . $uniqid . '");
                if (mpImageSlider.data("flexslider")) {
                    mpImageSlider.flexslider("destroy");
                }
                if (!' . $control_nav . ') mpImageSlider.css("margin-bottom", 0);
                mpImageSlider.flexslider({
                    slideshow: ' . $slideshow .  ',
                    animation: "' . 'fade' . '",
                    controlNav: ' . true . ',
                    slideshowSpeed: ' . '7000' . ',
                    animationSpeed: ' . '600' . ',
                    smoothHeight: ' . 'false' . ',
                    keyboard: ' . $keyboard . '
                });
            });
            </script></p>';
        return $sliderHtml;
    }

    public function motopressGridGallery($atts, $content = null){
        extract(shortcode_atts(self::addStyleAtts(array(
            'ids' => '',
            'columns' => '2',
            'size' => 'thumbnail',
            'custom_size' => '',
            'link_type' => 'none',
            'rel' => '',
            'target' => 'false',
            'caption' => 'false'
        )), $atts));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (!self::isContentEditor()) {
            wp_enqueue_style('mpce-bootstrap-grid');
        }

        if (isset($ids) && !empty($ids)) {
            $ids = trim($ids);
            $ids = explode(',', $ids);
            $ids = array_filter($ids);

            if (!empty($ids)) {

                $images = array();
                $imageErrors = array();
                foreach ($ids as $id) {
                    $id = (int) trim($id);

                    $attachment = get_post($id);
                    if (!empty($attachment) && $attachment->post_type === 'attachment') {
                        if (wp_attachment_is_image($id)) {
                            $title = esc_attr($attachment->post_title);

                            $alt = trim(strip_tags(get_post_meta($id, '_wp_attachment_image_alt', true)));
                            if (empty($alt)) {
                                $alt = trim(strip_tags($attachment->post_excerpt));
                            }
                            if (empty($alt)) {
                                $alt = trim(strip_tags($attachment->post_title));
                            }

                            if ($size === 'custom') {
                                $size = array_pad(explode('x', $custom_size), 2, 0);
                            }
                            $imgSrc = wp_get_attachment_image_src( $id, $size );

                            $galleryItem = '<img';

                            if ($imgSrc && isset($imgSrc[0])) {
                                $galleryItem .= ' src="' . $imgSrc[0]  . '"';
                            }
                            if (!empty($title)) {
                                $galleryItem .= ' title="' . $title . '"';
                            }
                            if (!empty($alt)) {
                                $galleryItem .= ' alt="' . $alt . '"';
                            }
                            $galleryItem .= ' />';

                            if ($link_type !== 'none') {
                                if ($link_type === 'lightbox') {
                                    $rel = 'motopressGalleryLightbox';
                                    if (!self::isContentEditor()) {
                                        wp_enqueue_style('magnific-popup');
                                        wp_enqueue_script('magnific-popup');
                                        wp_enqueue_script('mp-lightbox');
                                    }
                                }

                                $relAttr = '';
                                if ($link_type === 'attachment') {
                                    $link = get_attachment_link($id);
                                } else if ($link_type === 'media_file' || $link_type === 'lightbox') {
                                    $relAttr = ' rel="' . $rel . '"';
                                    $imgSrcFull = wp_get_attachment_image_src( $id, 'full' );
                                    $link = $imgSrcFull && isset($imgSrcFull[0]) ? $imgSrcFull[0] : '';
                                }

                                $target = ($target == 'true') ? '_blank' : '_self';
                                $galleryItem = '<a href="' . $link . '"' . $relAttr . '" target="' . $target . '" title="' . $attachment->post_title . '">' . $galleryItem . '</a>';
                            }
                            $captionHtml = ($caption == 'true') ? '<p class="motopress-image-caption">' . $attachment->post_excerpt . '</p>' : '';
                            $galleryItem = $galleryItem . $captionHtml;
                            $galleryItems[] = $galleryItem;
                            unset($galleryItem);
                        } else {
                            $galleryErrors[] = $motopressCELang->CEAttachmentNotImage;
                        }
                    } else {
                        $galleryErrors[] = $motopressCELang->CEAttachmentEmpty;
                    }
                }
            } else {
                $error = $motopressCELang->CEImagesNotSet;
            }
        } else {
            $error = $motopressCELang->CEImagesNotSet;
        }

        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $uniqid = uniqid();
        $js = '';
        $needRecalcClass = '';
        $oneColumnClass = '';
        if (($columns !== '1') && (count($galleryItems) > $columns)) {
            $needRecalcClass = ' motopress-grid-gallery-need-recalc';
            $js = "<p class=\"motopress-hide-script\"><script>jQuery(function(){
                mpRecalcGridGalleryMargins(jQuery('#$uniqid'));
            });</script></p>";
        } elseif ($columns == '1') {
            $oneColumnClass = ' motopress-grid-gallery-one-column';
        }

        $galleryHtml = '<div class="motopress-grid-gallery-obj' . self::getBasicClasses(self::PREFIX . 'grid_gallery', true) . $mp_style_classes . self::getMarginClasses($margin) . $needRecalcClass . $oneColumnClass . '" id="' . $uniqid . '">';
        if (empty($error)) {
            if (!empty($galleryItems)) {
                wp_enqueue_script('mp-grid-gallery');
                $galleryHtml .= '<div class="mp-row-fluid">';
                $i = 0;
                $spanClass = 12 / $columns;
                foreach ($galleryItems as $galleryItem) {
                    $galleryHtml .= '<div class="mp-span' . $spanClass . '">' . $galleryItem . '</div>';
                    if ( ($i % $columns == $columns - 1) && ($i != count($galleryItems) -1) ) {
                        $galleryHtml .= '</div>';
                        $galleryHtml .= '<div class="mp-row-fluid">';
                    }
                    $i++;
                }
                $galleryHtml .= '</div>';
            } elseif (!empty($galleryErrors)) {
                foreach ($galleryErrors as $galleryError) {
                    $galleryHtml .= $galleryError;
                }
            }
        } else {
            $galleryHtml .= $error;
        }
        $galleryHtml .= $js;
        $galleryHtml .= '</div>';

        return $galleryHtml;
    }

    const DEFAULT_VIDEO = 'www.youtube.com/watch?v=t0jFJmTDqno';
    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';

    public function motopressVideo($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'src' => ''
        )), $atts));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();
        $error = null;

        if (!empty($src)) {
            $src = filter_var($src, FILTER_SANITIZE_URL);
            $src = str_replace('&amp;', '&', $src);
            $url = parse_url($src);
            if ($url) {
                if (!isset($url['scheme']) || empty($url['scheme'])) {
                    $src = 'http://' . $src; //protocol use only for correct parsing url
                    $url = parse_url($src);
                }
            }

            if ($url) {
                if (isset($url['host']) && !empty($url['host']) && isset($url['path']) && !empty($url['path'])) {
                    $videoSite = self::getVideoSite($url);
                    if ($videoSite) {
                        $videoId = self::getVideoId($videoSite, $url);
                        if ($videoId) {
                            $query = (isset($url['query'])) ? $url['query'] : null;
                            $src = self::getVideoSrc($videoSite, $videoId, $query);
                        } else {
                            $error = $motopressCELang->CEVideoIdError;
                        }
                    } else {
                        $error = $motopressCELang->CEIncorrectVideoURL;
                    }
                } else {
                    $error = $motopressCELang->CEIncorrectVideoURL;
                }
            } else {
                $error = $motopressCELang->CEParseVideoURLError;
            }
        } else {
            $error = $motopressCELang->CEIncorrectVideoURL;
        }

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        $videoHtml = '<div class="motopress-video-obj' . $classes . self::getMarginClasses($margin) . '">';
        if (empty($error)) {
            $videoHtml .= '<iframe src="' . $src . '" class="'. self::getBasicClasses(self::PREFIX . 'video') . $mp_style_classes .'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        } else {
            $videoHtml .= $error;
        }
        $videoHtml .= '</div>';

        return $videoHtml;
    }

    private static function getVideoSite($url) {
        $videoSite = false;

        $youtubeRegExp = '/youtube\.com|youtu\.be/is';
        $vimeoRegExp = '/vimeo\.com/is';
        if (preg_match($youtubeRegExp, $url['host'])) {
            $videoSite = self::YOUTUBE;
        } else if (preg_match($vimeoRegExp, $url['host'])) {
            $videoSite = self::VIMEO;
        }

        return $videoSite;
    }

    private static function getVideoId($videoSite, $url) {
        $videoId = false;

        switch ($videoSite) {
            case self::YOUTUBE:
                if (preg_match('/youtube\.com/is', $url['host'])) {
                    if (preg_match('/watch/is', $url['path']) && isset($url['query']) && !empty($url['query'])) {
                        parse_str($url['query'], $parameters);
                        if (isset($parameters['v']) && !empty($parameters['v'])) {
                            $videoId = $parameters['v'];
                        }
                    } else if (preg_match('/embed/is', $url['path'])) {
                        $path = explode('/', $url['path']);
                        if (isset($path[2]) && !empty($path[2])) {
                            $videoId = $path[2];
                        }
                    }
                } else if (preg_match('/youtu\.be/is', $url['host'])) {
                    $path = explode('/', $url['path']);
                    if (isset($path[1]) && !empty($path[1])) {
                        $videoId = $path[1];
                    }
                }
                break;
            case self::VIMEO:
                if (preg_match('/player\.vimeo\.com/is', $url['host']) && preg_match('/video/is', $url['path'])) {
                    $path = explode('/', $url['path']);
                    if (isset($path[2]) && !empty($path[2])) {
                        $videoId = $path[2];
                    }
                } else if (preg_match('/vimeo\.com/is', $url['host'])) {
                    $path = explode('/', $url['path']);
                    if (isset($path[1]) && !empty($path[1])) {
                        $videoId = $path[1];
                    }
                }
                break;
        }

        return $videoId;
    }

    private static function getVideoSrc($videoSite, $videoId, $query) {
        $youtubeSrc = '//www.youtube.com/embed/';
        $vimeoSrc = '//player.vimeo.com/video/';
        $videoQuery = '';
        $wmode = 'wmode=opaque';

        if (!empty($query)) {
            parse_str($query, $parameters);
            if (self::isContentEditor()) {
                if (isset($parameters['autoplay']) && !empty($parameters['autoplay'])) {
                    unset($parameters['autoplay']);
                }
            }
        }

        switch ($videoSite) {
            case self::YOUTUBE:
                $videoSrc = $youtubeSrc;
                if (isset($parameters['v']) && !empty($parameters['v'])) {
                    unset($parameters['v']);
                }
                break;
            case self::VIMEO:
                $videoSrc = $vimeoSrc;
                break;
        }

        $videoSrc .= $videoId;

        if (!empty($parameters)) {
            $videoQuery = http_build_query($parameters);
        }

        if (!empty($videoQuery)) {
            $videoSrc .= '?' . $videoQuery . '&' . $wmode;
        } else {
            $videoSrc .= '?' . $wmode;
        }

        return $videoSrc;
    }

    public static function isContentEditor() {
        if (
            (isset($_GET['motopress-ce']) && $_GET['motopress-ce'] === '1') ||
            (isset($_POST['action']) && (in_array($_POST['action'], array('motopress_ce_render_shortcode', 'motopress_ce_render_video_bg', 'motopress_ce_render_youtube_bg') )))
        ) {
            return true;
        }
        return false;
    }

    public function motopressCode($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(), $atts));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        return '<div class="motopress-code-obj' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'code', true) . $mp_style_classes . '">' . do_shortcode($content) . '</div>';
    }

    public function motopressSpace($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(), $atts));
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        return '<div class="motopress-space-obj' . self::getBasicClasses(self::PREFIX . 'space', true) . $mp_style_classes  . self::getMarginClasses($margin) . '"><div></div></div>';
    }

    public function motopressButton($atts, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'text' => '',
            'link' => '#',
            
            'color' => 'silver',
            'size' => 'middle',
            'align' => 'left'
        )), $atts));

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($color)) {
                if ($color === 'default') $color = 'silver';
                $mp_style_classes = 'motopress-btn-color-' . $color;
            }
            if (!empty($size)) {
                if ($size === 'default') $size = 'middle';
                $mp_style_classes .= ' motopress-btn-size-' . $size;
            }
            $mp_style_classes .= ' motopress-btn-rounded';
            if (!empty($custom_class)) $mp_style_classes .= ' ' . $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
//        $splitStyle = self::splitStyleClasses($mp_style_classes);

        

        $buttonHtml = '<div class="motopress-button-obj motopress-text-align-' . $align . $classes . self::getMarginClasses($margin) . '"><a href="' . $link . '" class="' . self::getBasicClasses(self::PREFIX . 'button') . $mp_style_classes . '"';
        
        $buttonHtml .= '>' . $text . '</a></div>';
        return $buttonHtml;
//        return '<div class="motopress-button-obj motopress-text-align-' . $align . $classes . self::getMarginClasses($margin) . '"><a href="' . $link . '" class="' . self::getBasicClasses(self::PREFIX . 'button') . $mp_style_classes . '" target="' . $target . '">' . $text . '</a></div>';
    }

    public function motopressWPWidgetArchives($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'dropdown' => '',
            'count' => ''
        )), $attrs));

        ($dropdown == 'true' || $dropdown == 1)  ? $attrs['dropdown'] = 1 : $attrs['dropdown'] = 0;
        ($count == 'true' || $count == 1) ? $attrs['count'] = 1 : $attrs['count'] = 0;
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_archives' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_archives', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Archives';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetCalendar($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => ''
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_calendar' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_calendar', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Calendar';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetCategories($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'dropdown' => '',
            'count' => '',
            'hierarchical' => ''
        )), $attrs));

        ($dropdown == 'true' || $dropdown == 1) ? $attrs['dropdown'] = 1 : $attrs['dropdown'] = 0;
        ($count == 'true' || $count == 1) ? $attrs['count'] = 1 : $attrs['count'] = 0;
        ($hierarchical == 'true' || $hierarchical == 1) ? $attrs['hierarchical'] = 1 : $attrs['hierarchical'] = 0;
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_categories' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_categories', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Categories';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPNavMenu_Widget($attrs, $content = null) {
        $result = '';
        $title = '';
        $nav_menu = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'nav_menu' => ''
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_custommenu' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_navmenu', true) . $mp_style_classes . '">';
        $type = 'WP_Nav_Menu_Widget';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetMeta($attrs, $content = null) {
        $result = '';
        $title = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => ''
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_meta' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_meta', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Meta';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetPages($attrs, $content = null) {
        $result = '';
        $title = '';
        $sortby = '';
        $exclude = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'sortby' => 'menu_order',
            'exclude' => null
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_pages' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_pages', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Pages';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetRecentPosts($attrs, $content = null) {
        $result = '';
        $title = '';
        $number = '';
        $show_date = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'number' => 5,
            'show_date' => false
        )), $attrs));
        ($show_date == 'true' || $show_date == 1) ? $attrs['show_date'] = 1 : $attrs['show_date'] = 0;
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_posts' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_posts', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Recent_Posts';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetRecentComments($attrs, $content = null) {
        $result = '';
        $title = '';
        $number = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'number' => 5
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_recentcomments' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_comments', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Recent_Comments';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetRSS($attrs, $content = null) {
        $result = '';
        $title = '';
        $url = '';
        $items = '';
        $options = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'url' => '',
            'items' => 10,
            'show_summary' => '',
            'show_author' => '',
            'show_date' => ''
        )), $attrs));
        if ($url == '')
            return;
        $attrs['title'] = $title;
        $attrs['items'] = ($items + 1);

        ($show_summary == 'true' || $show_summary == 1) ? $attrs['show_summary'] = 1 : $attrs['show_summary'] = 0;
        ($show_author == 'true' || $show_author == 1) ? $attrs['show_author'] = 1 : $attrs['show_author'] = 0;
        ($show_date == 'true' || $show_date == 1) ? $attrs['show_date'] = 1 : $attrs['show_date'] = 0;
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_rss' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_rss', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_RSS';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetSearch($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'align' => 'left'
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_search_widget' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_search', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Search';
        $args = array();

        ob_start();
        the_widget($type, $attrs, $args);
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function motopressWPWidgetTagCloud($attrs, $content = null) {
        $result = '';
        $title = '';
        $taxonomy = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => __('Tags'),
            'taxonomy' => 'post_tag'
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_tagcloud' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_tagcloud', true) . $mp_style_classes . '">';
        $type = 'WP_Widget_Tag_Cloud';
        $args = array();

        ob_start();
        add_filter( 'widget_tag_cloud_args', array($this, 'tagCloudFilter'));
        the_widget($type, $attrs, $args);
        remove_filter('widget_tag_cloud_args', array($this, 'tagCloudFilter'));
        $result .= ob_get_clean();

        $result .= '</div>';

        return $result;
    }

    public function tagCloudFilter($args){
        $args['separator'] = ' ';
        return $args;
    }

    public function motopressWPWidgetArea($attrs, $content = null) {
        $result = '';
        $title = '';
        $sidebar = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'sidebar' => ''
        )), $attrs));
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result = '<div class="motopress-wp_widgets_area ' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'wp_widgets_area', true) . $mp_style_classes . '">';

        if ($title)
            $result .= '<h2 class="widgettitle">' . $title . '</h2>';

        if (function_exists('dynamic_sidebar') && $sidebar && $sidebar != 'no') {
            ob_start();
            dynamic_sidebar($sidebar);
            $result .= ob_get_clean();

            $result .= '</div>';

            return $result;
        } else {
            return false;
        }
    }

    public function motopressGoogleMap($attrs, $content = null) {
        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/Requirements.php';

        $motopressCELang = motopressCEGetLanguageDict();

        $result = $motopressCELang->CEGoogleMapNothingFound;
        $address = '';
        $zoom = '';
        extract( shortcode_atts(self::addStyleAtts(array(
            'address' => 'Sidney, New South Wales, Australia',
            'zoom' => '13'
        )), $attrs ));

        if ( $address == '' ) { return $result; }

        $address = str_replace(" ", "+", $address);

        $formattedAddresses = get_transient('motopress-gmap-addresses');
        $formattedAddresses = (false === $formattedAddresses) ? array() : $formattedAddresses;

        if (!array_key_exists($address, $formattedAddresses)) {
            $formattedAddress = false;
            $url = 'http://maps.googleapis.com/maps/api/geocode/json?address='. $address .'&sensor=false';

            $requirements = new MPCERequirements();
            if ($requirements->getCurl()) {
                $ch = curl_init();
                $options = array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true
                );
                curl_setopt_array($ch, $options);
                $jsonData = curl_exec($ch);
                curl_close($ch);
            } else {
                $jsonData = file_get_contents($url);
            }

            $data = json_decode($jsonData);

            if ($data && isset($data->status)) {
                if ($data->status === 'OK') {
                    if ($data && isset($data->results)) {
                        $results = $data->{'results'};
                        if ($results && $results[0]) {
                            $formattedAddress = $results[0]->{'formatted_address'};
                            $expiration = 60 * 60 * 24; // one day
                            $formattedAddresses[$address] = $formattedAddress;
                            set_transient('motopress-gmap-addresses', $formattedAddresses, $expiration);
                        }
                    }
                } else {
                    switch ($data->status) {
                        case 'ZERO_RESULTS' : $result = $motopressCELang->CEGoogleMapNothingFound; break;
                        case 'OVER_QUERY_LIMIT' : $result = "Usage limits exceeded."; break;
                        case 'REQUEST_DENIED' : $result = "Request was denied for some reason."; break;
                        case 'INVALID_REQUEST' : $result = "Query (address) is missing."; break;
                    }
                }
            } else {
                $result = "Bad response from Google Map API.";
            }
        } else {
            $formattedAddress = $formattedAddresses[$address];
        }

        if ($formattedAddress) {
            if (!empty($classes)) $classes = ' ' . $classes;
            if (self::$isNeedFix && empty($mp_style_classes)) {
                if (!empty($custom_class)) $mp_style_classes = $custom_class;
            }
            if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
            $result = '<div class="motopress-google-map-obj' . $classes . self::getMarginClasses($margin) . '">';
            $result .= '<iframe class="' . self::getBasicClasses(self::PREFIX . 'gmap') . $mp_style_classes . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q='.$address.'&amp;t=m&amp;z='.$zoom.'&amp;output=embed&amp;iwloc=near"></iframe>';
            $result .= '</div>';
        }
        return $result;
    }

    public function motopressEmbedCode($attrs, $content = null) {
        $embed = $data = $result = $fill_space = '';

        extract(shortcode_atts(self::addStyleAtts(array(
            'data' => '',
            'fill_space' => 'true'
        )), $attrs) );
        $embed = base64_decode(strip_tags($data));
        $embed = preg_replace('~[\r\n]~', '', $embed);

        if (self::isContentEditor()) {
            $embed = '<div class="motopress-embed-obj-select"></div>' . $embed;
        }
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $result .= '<div class="motopress-embed-obj' . (($fill_space == 'true' || $fill_space == '1') ?
            " fill-space" : "") . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'embed', true) . $mp_style_classes . '">' . $embed . '</div>';
        return $result;
    }

    public function motopressQuotes($attrs, $content = null) {
        $result = '';
        $class = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'cite' => '',
            'cite_url' => '',
            'quote_content' => ''
        )), $attrs));

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        if ($cite && $cite_url) {
            $result = '<div class="motopress-quotes' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'quote', true) . $mp_style_classes . '"><blockquote><p>'. $quote_content .'</p></blockquote><p style="text-align:right;"><a href="'.$cite_url.'">'.$cite.'</a></p></div>';
        } elseif ($cite) {
            $result = '<div class="motopress-quotes' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'quote', true) . $mp_style_classes . '"><blockquote><p>'. $quote_content .'</p></blockquote><p style="text-align:right;">'.$cite.'</p></div>';
        } else {
            $result = '<div class="motopress-quotes' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'quote', true) . $mp_style_classes . '"><blockquote><p>'. $quote_content .'</p></blockquote></div>';
        }

        return $result;
    }

    public function motopressMembersContent($attrs, $content = null) {
        $result = '';
        $text = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'message'    =>  '',
            'login_text' =>  '',
            'members_content' => ''
        )), $attrs));

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        if (!is_user_logged_in()) {
            if (!$message) $message = 'This content is for registered users only. Please %login%.';
            if (!$login_text) $login_text = 'login';
            $text = '<a href="' . esc_attr(wp_login_url()) . '">' . $login_text . '</a>';
            $result = '<div class="motopress-members-content' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'members_content', true) . $mp_style_classes . '">' . str_replace( '%login%', $text, $message ) . '</div>';
        } else {
            $result = "<div class='motopress-members-content". $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'members_content', true) . $mp_style_classes . "'>". $members_content . "</div>";
        }

        return $result;
    }

    public function motopressSocialShare($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'size' => 'motopress-buttons-32x32',
            'style' => 'motopress-buttons-square',
            'align' =>  'motopress-text-align-left'
        )), $attrs));

        if (!$align) $align = 'motopress-text-align-left';
        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($size)) $mp_style_classes = $size;
            if (!empty($style)) $mp_style_classes .= ' ' . $style;
            if (!empty($custom_class)) $mp_style_classes .= ' ' . $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        wp_enqueue_script('mp-social-share');

//        $result = '<div class="motopress-share-buttons ' . $align . ' ' . $size . ' ' . $style . $classes . self::getMarginClasses($margin) . $custom_class . self::getBasicClasses(self::PREFIX . 'social_buttons', true) . $mp_style_classes . '">';
        $result = '<div class="motopress-share-buttons ' . $align . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'social_buttons', true) . $mp_style_classes . '">';
        $result.= '<span class="motopress-button-facebook"><a href="#" title="Facebook" target="_blank"></a></span>';
        $result.= '<span class="motopress-button-twitter"><a href="#" title="Twitter" target="_blank"></a></span>';
        $result.= '<span class="motopress-button-google"><a href="#" title="Google +" target="_blank"></a></span>';
        $result.= '<span class="motopress-button-pinterest"><a href="#" title="Pinterest" target="_blank"></a></span>';
        $result.= '</div>';

        return $result;
    }

    public function motopressSocialProfile($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'facebook' => '',
            'google' => '',
            'twitter' => '',
            'pinterest' => '',
            'linkedin' => '',
            'flickr' => '',
            'vk' => '',
            'delicious' => '',
            'youtube' => '',
            'rss' => '',
            'size' => 32,
            'style' => 'square',
            'align' =>  'left'
        )), $attrs));

        $sites = array(
            'facebook' => 'Facebook',
            'google' => 'Google +',
            'twitter' => 'Twitter',
            'pinterest' => 'Pinterest',
            'linkedin' => 'LinkedIn',
            'flickr' => 'Flickr',
            'vk' => 'VK',
            'delicious' => 'Delicious',
            'youtube' => 'YouTube',
            'rss' => 'RSS'
        );
        $target = ' target="_blank"';

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($size)) $mp_style_classes = 'motopress-buttons-' . $size . 'x' . $size;
            if (!empty($style)) $mp_style_classes .= ' motopress-buttons-' . $style;
            if (!empty($custom_class)) $mp_style_classes .= ' ' . $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
//        $socialProfileHtml = '<div class="motopress-social-profile-obj motopress-text-align-' . $align . ' motopress-buttons-' . $size . 'x' . $size . ' motopress-buttons-' . $style . self::getMarginClasses($margin) . $classes .  $custom_class . self::getBasicClasses(self::PREFIX . 'social_profile', true) . $mp_style_classes . '">';
        $socialProfileHtml = '<div class="motopress-social-profile-obj motopress-text-align-' . $align . self::getMarginClasses($margin) . $classes . self::getBasicClasses(self::PREFIX . 'social_profile', true) . $mp_style_classes . '">';
        foreach($sites as $name => $title) {
            $link = trim(filter_var($$name, FILTER_SANITIZE_URL));
            if (!empty($link) && filter_var($link, FILTER_VALIDATE_URL) !== false) {
                $socialProfileHtml.= '<span class="motopress-button-' . $name . '"><a href="' . $link . '" title="' . $title . '"' . $target . '></a></span>';
            }
        }
        $socialProfileHtml .= '</div>';

        return $socialProfileHtml;
    }

    public function motopressGoogleCharts($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            
            'colors' => '',
            'transparency' => 'false',
            'donut' => ''
        )), $attrs) );

        wp_enqueue_script('google-charts-api');
        wp_enqueue_script('mp-google-charts');

        $id = uniqid('motopress-google-chart-');

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        $js = "<p class=\"motopress-hide-script\"><script>jQuery(function(){
            var height = jQuery(document.getElementById('". $id ."')).parent().parent().height();
            if ( height < 100 ) { height = 200; }
            google.motopressDrawChart( '". $id ."',  height );
        });</script></p>";

        $chartTable = array();

        if ($content) {
            $content = trim($content);
            $content = preg_replace('/^<p>|<\/p>$/', '', $content);
            $content = preg_replace('/<br[^>]*>\s*\r*\n*/is', "\n", $content);
            $content = json_encode($content);
            $delimiter = ( strpos( $content, '\r\n') !== false) ? '\r\n' : '\n';
            $content = trim($content, '"');
            $content = str_replace('\"', '"', $content);
            $rows = explode( $delimiter, $content );
            $rowsCount = count($rows);

            if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
                for ($i=0; $i < $rowsCount; $i++) {
                    $rows[$i] = str_getcsv($rows[$i]);
                    if ($i !== 0) {
                        $newArr = array();
                        for ($index=0; $index < count($rows[$i]); $index++) {
                            if ($index == 0) {
                                $newArr[] = $rows[$i][0];
                            } else {
                                $newArr[] = (integer) $rows[$i][$index];
                            }
                        }
                        $rows[$i] = $newArr;
                    }
                    $chartTable[] = $rows[$i];
                }
            } else {
                $tmpFile = new SplTempFileObject();
                $tmpFile->setFlags(SplFileObject::SKIP_EMPTY);
                $tmpFile->setFlags(SplFileObject::DROP_NEW_LINE);
                $resultedArray = $rowsConv = $itemsTypeConv = array();

                for ($i=0; $i < $rowsCount; $i++) {
                    $write = $tmpFile->fwrite( $rows[$i] . "\n" );
                    if (!is_null($write)) {
                        if ( $i == $rowsCount - 1 ) {
                            $tmpFile->rewind();
                            while (!$tmpFile->eof()) {
                                $row = $tmpFile->fgetcsv();
                                $resultedArray[] = $row;
                            }
                        }
                    }
                }

                foreach ($resultedArray as $array => $arrs) {
                    $arrsCounter = count($arrs);
                    for ($i = 0; $i < $arrsCounter; $i++) {
                        if ($array === 0) {
                            $rowsConv[0] = $arrs;
                        }
                        if ($array != 0 ) {
                            if ($i != 0) {
                                $itemsTypeConv[$i] = (int) $arrs[$i];
                            } else {
                                $itemsTypeConv[$i] = $arrs[$i];
                            }
                        }
                        if (!empty($itemsTypeConv) && $i == ($arrsCounter - 1)) {
                            $rowsConv[] = $itemsTypeConv;
                        }
                    }
                }
                $chartTable = $rowsConv;
            }

            $colors = str_replace(' ', '', $colors);
            if (!empty($colors)) {
                $colors = explode(',', $colors);
            } else {
                $colors = null;
            }

            if ($transparency !== 'false') {
                $backgroundColor = array('fill' => 'transparent');
            } else {
                $backgroundColor = null;
            }

            $chartData = array(
                'ID' => $id,
                'type' => 'ColumnChart',
                'title' => $title,
                'donut' => $donut,
                'table' => $chartTable,
                'height' => null,
                'colors' => $colors,
                'backgroundColor' => $backgroundColor
            );

            $content = json_encode($chartData);
            $content = htmlspecialchars($content);

        } else {
            $content = null;
        }

        $result = "<div id=\"". $id ."\" class=\"motopress-google-chart" . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'google_chart', true) . $mp_style_classes .  "\" data-chart=\"". $content ."\"></div>";

        if (is_admin()) $result .= $js;

        return $result;
    }

    public function motopressWPAudio($attrs, $content = null) {
        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/Requirements.php';

        $motopressCELang = motopressCEGetLanguageDict();

        $result = '';
        $admin = '';
        $shortcode = '';
        $script = '';
        $mediaIsSet = '';
        $audioTitle = '';
        $src = '';
        extract(shortcode_atts(self::addStyleAtts(array(
            'source' => '',
            'id' => '',
            'url' => '',
            'autoplay' => '',
            'loop'     => ''
        )), $attrs) );

        $admin = is_admin();

        $blockID = uniqid('motopress-wp-audio-');

        if ( !empty($id) ) {
            $attachment = get_post( $id );
            $audioTitle = ' data-audio-title="'. $attachment->post_title .'"';
        }

        if ( $source == 'library' && !empty($id) ) {
            $audioURL = wp_get_attachment_url( $id );
            $mediaIsSet = true;
        } elseif ( $source == 'external' && !empty($url) ) {
            $audioURL = $url;
            $mediaIsSet = true;
        }

        if ( $mediaIsSet ) {
            $src = 'src="'. $audioURL .'"';
            if ( !isset($_GET['motopress-ce']) && !$admin ) {
                if ($autoplay == 'true' || $autoplay == 1) {
                    $autoplay = ' autoplay="on"';
                }else {
                    $autoplay = null;
                }
                if ($loop == 'true' || $loop == 1) {
                    $loop = ' loop="on"';
                }else {
                    $loop = null;
                }
            }
            $shortcode = "[audio '. $src . $autoplay . $loop .']";
        }else {
            $shortcode = "<p>". $motopressCELang->CCEwpAudioNoMediaSet ."</p>";
        }

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        $result = do_shortcode( '<div class="motopress-audio-object'. $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'google_chart', true) . $mp_style_classes .  '" id="' . $blockID .'"' . $audioTitle .'>'. $shortcode . '</div>');

        $script = "<p class=\"motopress-hide-script\"><script>jQuery(function() { jQuery('#".$blockID."').find('.wp-audio-shortcode').mediaelementplayer(); }); </script></p>";

        if ( $admin && !empty($src) ) $result .= $script;

        return $result;
    }

    public function motopressTabs($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'active' => null,
            'padding' => 20
        )), $attrs));

        wp_enqueue_script('jquery-ui-tabs');

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes = $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        $uniqid = uniqid();
        $tabsHtml = '<div class="motopress-tabs-obj' . $classes . ' motopress-tabs-padding-'. $padding . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'tabs', true) . $mp_style_classes . '" id="' . $uniqid . '">';

        preg_match_all('/mp_tab id="([^\"]+)" title="([^\"]+)" active="(true|false)"/i', $content, $matches);

        if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
            $tabsHtml .= '<ul>';
            $count = count($matches[1]);
            for ($i = 0; $i < $count; $i++) {
                $tabsHtml .= '<li><a href="#'. $matches[1][$i] . '">' . $matches[2][$i] . '</a></li>';
            }
            $tabsHtml .= '</ul>';

            $tabsHtml .= do_shortcode($content);

            if (!self::isContentEditor() || is_null($active)) {
                $active = array_search('true', $matches[3]);
            }

            $tabsHtml .= '<p class="motopress-hide-script"><script type="text/javascript">
                jQuery(document).ready(function($) {
                    var mpTabs = $(".motopress-tabs-obj#' . $uniqid . '");
                    if (mpTabs.data("uiTabs")) {
                        mpTabs.tabs("destroy");
                    }
                    mpTabs.tabs({
                        active: ' . (int) $active . '
                    });
                });
                </script></p>';
        }
        $tabsHtml .= '</div>';

        return $tabsHtml;
    }

    public function motopressTab($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'id' => '',
            'title' => '',
            'active' => ''
        )), $attrs));
        return '<div class="motopress-tab' . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'tab', true) . '" id="' . $id . '">' . do_shortcode($content) . '</div>';
    }

    public function motopressAccordion($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'active' => 'false',
            'style' => 'light'
        )), $attrs));

        wp_enqueue_script('jquery-ui-accordion');

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($style)) $mp_style_classes = 'motopress-accordion-' . $style;
            if (!empty($custom_class)) $mp_style_classes .= ' ' . $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;
        $uniqid = uniqid();
//        $accordionHtml = '<div class="motopress-accordion-obj' . $classes . ' motopress-accordion-'. $style . self::getMarginClasses($margin) . $custom_class . self::getBasicClasses(self::PREFIX . 'accordion', true) . $mp_style_classes . '" id="' . $uniqid . '">';
        $accordionHtml = '<div class="motopress-accordion-obj' . $classes . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'accordion', true) . $mp_style_classes . '" id="' . $uniqid . '">';
        preg_match_all('/mp_accordion_item title="([^\"]+)" active="(true|false)"/i', $content, $matches);

        if (!empty($matches[1]) && !empty($matches[2])) {
            $isContentEditor = self::isContentEditor();

            $accordionHtml .= do_shortcode($content);

            if (!$isContentEditor || $active === 'false') {
                $search = array_search('true', $matches[2]);
                if ($search !== false) $active = $search;
            }

            $header = '> div > h3';
            if ($isContentEditor) $header = '> div ' . $header;

            $accordionHtml .= '<p class="motopress-hide-script"><script type="text/javascript">
                jQuery(document).ready(function($) {
                    var mpAccordion = $(".motopress-accordion-obj#' . $uniqid . '");
                    if (mpAccordion.data("uiAccordion")) {
                        mpAccordion.accordion("destroy");
                    }
                    mpAccordion.accordion({
                        active: ' . $active . ',
                        collapsible: true,
                        header: "' . $header . '",
                        heightStyle: "content"
                    });
                });
                </script></p>';
        }
        $accordionHtml .= '</div>';

        return $accordionHtml;
    }

    public function motopressAccordionItem($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            'title' => '',
            'active' => ''
        )), $attrs));
        $accordionItemHtml = '<div class="motopress-accordion-item' . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'accordion_item', true) . '">';
        $accordionItemHtml .= '<h3>' . $title . '</h3>';
        $accordionItemHtml .= '<div>' . do_shortcode($content) . '</div>';
        $accordionItemHtml .= '</div>';

        return  $accordionItemHtml;
    }

    public function motopressTable($attrs, $content = null) {
        extract(shortcode_atts(self::addStyleAtts(array(
            
        )), $attrs));

        global $motopressCESettings;
        require_once $motopressCESettings['plugin_root'] . '/' . $motopressCESettings['plugin_name'] . '/includes/getLanguageDict.php';
        $motopressCELang = motopressCEGetLanguageDict();

        if (!empty($classes)) $classes = ' ' . $classes;
        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($style) && $style != 'none') $mp_style_classes = 'motopress-table-style-' . $style;
            if (!empty($custom_class)) $mp_style_classes .= ' ' . $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;

        $result = '<div class="motopress-table-obj' . self::getMarginClasses($margin) . $classes . '">';

        $content = trim($content);
        $content = preg_replace('/^<p>|<\/p>$/', '', $content);
        $content = preg_replace('/<br[^>]*>\s*\r*\n*/is', "\n", $content);

        if (!empty($content)) {
//            $result .= '<table class="' . self::getBasicClasses(self::PREFIX . 'table', true) . $mp_style_classes   . '">';
            $result .= '<table class="' . self::getBasicClasses(self::PREFIX . 'table') . $mp_style_classes . '">';
            $i = 0;
            if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
                $rows = explode("\n", $content);
                $rowsCount = count($rows);
                foreach ($rows as $row) {
                    $row = str_getcsv($row);
                    $isLast = ($i === $rowsCount - 1) ? true : false;
                    self::addRow($row, $i, $isLast, $result);
                    $i++;
                }
            } else {
                $tmpFile = new SplTempFileObject();
                $tmpFile->setFlags(SplFileObject::SKIP_EMPTY);
                $tmpFile->setFlags(SplFileObject::DROP_NEW_LINE);
                $write = $tmpFile->fwrite($content);
                if (!is_null($write)) {
                    $tmpFile->rewind();
                    while (!$tmpFile->eof()) {
                        $row = $tmpFile->fgetcsv();
                        $isLast = $tmpFile->eof();
                        self::addRow($row, $i, $isLast, $result);
                        $i++;
                    }
                }
            }
            $result .= '</table>';
        } else {
            $result .= $motopressCELang->CETableObjNoData;
        }
        $result .= '</div>';
        return $result;
    }

    /**
     * @param array $row
     * @param int $i
     * @param boolean $isLast
     * @param string $result
     */
    private static function addRow($row, $i, $isLast, &$result) {
        if ($i === 0) {
            $result .= '<thead>';
            $result .= '<tr>';
            foreach ($row as $col) {
                $result .= '<th>' . trim($col) . '</th>';
            }
            $result .= '</tr>';
            $result .= '</thead>';
        } else {
            if ($i === 1) {
                $result .= '<tbody>';
            }
            if (($i - 1) % 2 !== 0) {
                $result .= '<tr class="odd-row">';
            } else {
                $result .= '<tr>';
            }
            foreach ($row as $col) {
                $result .= '<td>'. trim($col) .'</td>';
            }
            $result .= '</tr>';
            if ($isLast) {
                $result .= '</tbody>';
            }
        }
    }

     public function motopressPostsGrid($attrs, $content = null){
        extract(shortcode_atts(self::addStyleAtts(array(
            'post_type' =>  'post',
            'columns' => 3,
            'category' => '',
            'tag' => '',
            
            
            'template' => '/plugins/motopress-content-editor/includes/ce/shortcodes/post_grid/templates/template1.php',
            'posts_gap' => 30,
            'show_featured_image' => 'true',
            'image_size' => 'large',
            'image_custom_size' => '',
            'title_tag' => 'h2',
            'show_date_comments' => 'true',
            'show_content' => 'short',
            'short_content_length' => 200,
            'read_more_text' => '',
            'pagination' => 'false'
        )), $attrs));

        $result = '';

        $exclude_posts = array();
        $posts_per_page = 3;
        $posts_order = 'DESC';

        if (self::isContentEditor()) {
            if ( isset($_POST['postID']) && !empty($_POST['postID'])) {
                $id = $_POST['postID'];
                $exclude_posts[] = (int) $_POST['postID'];
            } else {
                $id = get_the_ID();
            }

            $editedPost = get_post_meta($id, 'motopress-ce-edited-post', true);
            if (!empty($editedPost)) {
                $exclude_posts[] = (int) $editedPost;
            }

            if (isset($_GET['p'])){
                $exclude_posts[] = (int) $_GET['p'];
            }
        } else {
            wp_enqueue_style('mpce-bootstrap-grid');
            $id = get_the_ID();
            $exclude_posts = array($id);
        }

        $paged = isset($_GET['mp_posts_grid_paged']) ? $_GET['mp_posts_grid_paged'] : 1;

        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'post__not_in' => $exclude_posts,
            'order' => $posts_order,
            'paged' => $paged,
            'category_name' => $category,
            'tag' => $tag
        );

        $custom_query = new WP_Query($args);
        $url = get_permalink();
        $url .= is_null(parse_url($url, PHP_URL_QUERY)) ? '?mp_posts_grid_paged=' : '&mp_posts_grid_paged=';
        $nextpage = $paged + 1;
        $prevpage = $paged - 1;

        if (self::$isNeedFix && empty($mp_style_classes)) {
            if (!empty($custom_class)) $mp_style_classes .= ' ' . $custom_class;
        }
        if (!empty($mp_style_classes)) $mp_style_classes = ' ' . $mp_style_classes;


        if ($image_size === 'custom') {
            $featured_image_size = array_pad(explode('x', $image_custom_size), 2, 0);
        } else {
            $featured_image_size = $image_size;
        }

        $result .= '<div class="motopress-posts-grid-obj motopress-posts-grid-gap-'. $posts_gap . self::getMarginClasses($margin) . self::getBasicClasses(self::PREFIX . 'posts_grid', true) . $mp_style_classes . '">';

        if( $custom_query->have_posts() ) {
            $i = 0;
            $result .= '<div class="mp-row-fluid">';
            while ( $custom_query->have_posts() ) {

                $custom_query->the_post();
                self::setCurPostData(null, get_the_ID());
                $result .= '<div class="mp-span' . 12 / $columns . '">';

                ob_start();
                require(WP_CONTENT_DIR . '/' . $template);
                $result .= ob_get_contents();
                ob_end_clean();

                $result .= '</div>';

                if ( ($i % $columns == $columns - 1) && ($i != $custom_query->post_count - 1) ) {
                    $result .= '</div>';
                    $result .= '<div class="mp-row-fluid">';
                }
                $i++;
            }
            $result .= '</div>';

            if ($pagination == 'true') {
                if (isset($posts_order) && $posts_order === 'ASC') {
                    $nextPageLabel = 'Newer posts';
                    $prevPageLabel = 'Older posts';
                } else {
                    $nextPageLabel = 'Older posts';
                    $prevPageLabel = 'Newer posts';
                }
                $result .= '<div class="mp-row-fluid motopress-posts-grid-pagination">';
                if ($paged > 1) {
                    $result .= '<div class="nav-prev"><a href="' . $url . $prevpage . '"><span class="meta-nav">&#8592;</span>' . $prevPageLabel . '</a></div>';
                }
                if ($paged < $custom_query->max_num_pages) {
                    $result .= '<div class="nav-next"><a href="' . $url . $nextpage . '">' . $nextPageLabel . '<span class="meta-nav">&#8594;</span></a></div>';
                }
                $result .= '</div>';
            }


        } else {
            $result .= '<p>No posts of this post-type found.</p>';
        }
        $result .= '</div>';
        return $result;
    }


    public static function getPostTypes(){
        $args = array(
            'public' => TRUE,
        );
        $postTypes = get_post_types($args, 'objects');

        if (isset($postTypes['page'])) unset($postTypes['page']);
        if (isset($postTypes['attachment'])) unset($postTypes['attachment']);

        foreach($postTypes as $postTypeName => $postType){
            $result[$postTypeName] = $postType->labels->singular_name;
        }

        return $result;
    }

    public static function getPostsGridTemplatesList(){
        global $motopressCELang;

        $templates  = array();
        $path = dirname(__FILE__) . '/shortcodes/post_grid/templates/';

        $files = array_diff(scandir($path), array('.', '..'));

        $phpFilePattern = '/\.php$/is';
        $templateFiles = preg_grep($phpFilePattern, $files);

        if (!empty($templateFiles)) {
            foreach ($templateFiles as $templateFile) {
                $fileContent = file_get_contents($path . '/' . $templateFile);
                $namePattern = '/\*\s*Name:\s*([^\*]+)\s*\*/is';

                preg_match($namePattern, $fileContent, $matches);

                if (!empty($matches[1])) {
                    $name = $motopressCELang->{trim($matches[1])};
                } else {
                    $name = basename($templateFile, '.php');
                }
                $relativePath =  'plugins/' . dirname( plugin_basename(__FILE__) ) . '/shortcodes/post_grid/templates/' . $templateFile;
                $templates[$relativePath] = $name;
            }
        }

        return $templates;
    }
}
