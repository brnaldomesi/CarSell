<?php
require_once 'BaseElement.php';
require_once 'Element.php';
require_once 'Group.php';
require_once 'Object.php';
require_once 'Template.php';

/**
 * Description of MPCELibrary
 *
 */
class MPCELibrary {
    private $library = array();
    public $globalPredefinedClasses = array();
    public $tinyMCEStyleFormats = array();
    private $templates = array();
    private $gridObjects = array();
    public static $isAjaxRequest;
    private static $defaultGroup;
    public $deprecatedParameters = array(
        'mp_button' => array(
            'color' => array(
                'prefix' => 'motopress-btn-color-'
            ),
            'size' => array(
                'prefix' => 'motopress-btn-size-'
            )
        ),
        'mp_accordion' => array(
            'style' => array(
                'prefix' => 'motopress-accordion-'
            )
        ),
        'mp_social_buttons' => array(
            'size' => array(
                'prefix' => ''
            ),
            'style' => array(
                'prefix' => ''
            )
        ),
        'mp_table' => array(
            'style' => array(
                'prefix' => 'motopress-table-style-'
            )
        )
    );

    /**
     * @global stdClass $motopressCELang
     */
    public function __construct() {
        global $motopressCELang;
        self::$isAjaxRequest = $this->isAjaxRequest();

/*
        $this->globalPredefinedClasses = array(
            'hidden' => array(
                'label' => $motopressCELang->CEHidden,
                'values' => array(
                    'phone' => array(
                        'class' => 'mp-hidden-phone',
                        'label' => $motopressCELang->CEHiddenPhone
                    ),
                    'tablet' => array(
                        'class' => 'mp-hidden-tablet',
                        'label' => $motopressCELang->CEHiddenTablet
                    ),
                    'desktop' => array(
                        'class' => 'mp-hidden-desktop',
                        'label' => $motopressCELang->CEHiddenDesktop
                    )
                )
            ),
            'align' => array(
                'label' => 'Align',
                'values' => array(
                    'left' => array(
                        'class' => 'motopress-text-align-left',
                        'label' => 'Left'
                    ),
                    'center' => array(
                        'class' => 'motopress-text-align-center',
                        'label' => 'Center'
                    ),
                    'right' => array(
                        'class' => 'motopress-text-align-right',
                        'label' => 'Right'
                    )
                )
            )
        );
*/

        $padding = array(
            'label' => 'Padding',
            'values' => array(
                'padding-5' => array(
                    'class' => 'motopress-padding-5',
                    'label' => 'Padding 5'
                ),
                'padding-10' => array(
                    'class' => 'motopress-padding-10',
                    'label' => 'Padding 10',
                ),
                'padding-15' => array(
                    'class' => 'motopress-padding-15',
                    'label' => 'Padding 15'
                ),
                'padding-25' => array(
                    'class' => 'motopress-padding-25',
                    'label' => 'Padding 25',
                    'disabled' => true
                ),
                'padding-50' => array(
                    'class' => 'motopress-padding-50',
                    'label' => 'Padding 50',
                    'disabled' => true
                ),
                'padding-100' => array(
                    'class' => 'motopress-padding-100',
                    'label' => 'Padding 100',
                    'disabled' => true
                ),
                'vertical-padding-100' => array(
                    'class' => 'motopress-vetical-padding-100',
                    'label' => 'Vertical Padding 100',
                    'disabled' => true
                ),
                'vertical-padding-150' => array(
                    'class' => 'motopress-vetical-padding-150',
                    'label' => 'Vertical Padding 150',
                    'disabled' => true
                ),
                'vertical-padding-200' => array(
                    'class' => 'motopress-vetical-padding-200',
                    'label' => 'Vertical Padding 200',
                    'disabled' => true
                )
            )
        );

        $backgroundColor = array(
            'label' => 'Background Color',
            'values' => array(
                'blue' => array(
                    'class' => 'motopress-bg-color-blue',
                    'label' => 'Blue',
                    'disabled' => true
                ),
                'dark' => array(
                    'class' => 'motopress-bg-color-dark',
                    'label' => 'Dark',
                    'disabled' => true
                ),
                'gray' => array(
                    'class' => 'motopress-bg-color-gray',
                    'label' => 'Gray',
                    'disabled' => true
                ),
                'green' => array(
                    'class' => 'motopress-bg-color-green',
                    'label' => 'Green',
                    'disabled' => true
                ),
                'red' => array(
                    'class' => 'motopress-bg-color-red',
                    'label' => 'Red',
                    'disabled' => true
                ),
                'silver' => array(
                    'class' => 'motopress-bg-color-silver',
                    'label' => 'Silver'
                ),
                'white' => array(
                    'class' => 'motopress-bg-color-white',
                    'label' => 'White'
                ),
                'yellow' => array(
                    'class' => 'motopress-bg-color-yellow',
                    'label' => 'Yellow',
                    'disabled' => true
                )
            )
        );

        $style = array(
            'label' => 'Style',
            'allowMultiple' => true,
            'values' => array(
                'bg-alpha-75' => array(
                    'class' => 'motopress-bg-alpha-75',
                    'label' => 'Transparent'
                ),
                'border' => array(
                    'class' => 'motopress-border',
                    'label' => 'Border'
                ),
                'border-radius' => array(
                    'class' => 'motopress-border-radius',
                    'label' => 'Rounded'
                ),
                'shadow' => array(
                    'class' => 'motopress-shadow',
                    'label' => 'Shadow'
                ),
                'shadow-bottom' => array(
                    'class' => 'motopress-shadow-bottom',
                    'label' => 'Bottom Shadow',
                    'disabled' => true
                ),
                'text-shadow' => array(
                    'class' => 'motopress-text-shadow',
                    'label' => 'Text Shadow'
                )
            )
        );

        $border = array(
            'label' => 'Border Side',
            'allowMultiple' => true,
            'values' => array(
                'border-top' => array(
                    'class' => 'motopress-border-top',
                    'label' => 'Border Top',
                    'disabled' => true
                ),
                'border-right' => array(
                    'class' => 'motopress-border-right',
                    'label' => 'Border Right'
                ),
                'border-bottom' => array(
                    'class' => 'motopress-border-bottom',
                    'label' => 'Border Bottom'
                ),
                'border-left' => array(
                    'class' => 'motopress-border-left',
                    'label' => 'Border Left',
                    'disabled' => true
                )
            )
        );

        $textColor = array(
            'label' => 'Text Color',
            'values' => array(
                'color-light' => array(
                    'class' => 'motopress-color-light',
                    'label' => 'Light Text'
                ),
                'color-dark' => array(
                    'class' => 'motopress-color-dark',
                    'label' => 'Dark Text'
                )
            )
        );

        $rowPredefinedStyles = array(
            'fullwidth' => array(
                'class' => 'mp-row-fullwidth',
                'label' => 'Full Width'
            ),
            'padding' => $padding,
            'background-color' => $backgroundColor,
            'style' => $style,
            'border' => $border,
            'color' => $textColor
        );

        $spanPredefinedStyles = array(
            'padding' => $padding,
            'background-color' => $backgroundColor,
            'style' => $style,
            'border' => $border,
            'color' => $textColor
        );

        $spacePredefinedStyles = array(
            'type' => array(
                'label' => 'Type',
                'values' => array(
                    'light' => array(
                        'class' => 'motopress-space-light',
                        'label' => 'Light'
                    ),
                    'normal' => array(
                        'class' => 'motopress-space-normal',
                        'label' => 'Normal'
                    ),
                    'dotted' => array(
                        'class' => 'motopress-space-dotted',
                        'label' => 'Dotted'
                    ),
                    'dashed' => array(
                        'class' => 'motopress-space-dashed',
                        'label' => 'Dashed'
                    ),
                    'double' => array(
                        'class' => 'motopress-space-double',
                        'label' => 'Double'
                    ),
                    'groove' => array(
                        'class' => 'motopress-space-groove',
                        'label' => 'Grouve'
                    ),
                    'ridge' => array(
                        'class' => 'motopress-space-ridge',
                        'label' => 'Ridge'
                    ),
                    'heavy' => array(
                        'class' => 'motopress-space-heavy',
                        'label' => 'Heavy'
                    )
                )
            )
        );
        /* Objects */
        //grid
        $rowParameters = array(
            'bg_media_type' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CERowObjTypeBGLabel,
                'description' => $motopressCELang->CERowObjTypeBGDesc,
//                'default' => 'disabled',
                'list' => array(
                    'disabled' => $motopressCELang->CERowObjTypeBGDisabled,
                    'video' => $motopressCELang->CERowObjTypeBGMP4,
                    'youtube' => $motopressCELang->CERowObjTypeBGYoutube,
                    'parallax' => $motopressCELang->CERowObjTypeBGParallax
                )
            ),
            'bg_video_youtube' => array(
                'type' => 'video',
                'label' => $motopressCELang->CERowObjBGYoutubeLabel,
                
                'description' => $motopressCELang->CERowObjBGYoutubeDesc,
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                ),
                'disabled' => 'true'
            ),
            'bg_video_youtube_cover' => array(
                'type' => 'image',
                'label' => $motopressCELang->CERowObjBGVideoCoverImageLabel,
                'description' => $motopressCELang->CERowObjBGVideoCoverImageDesc,
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                ),
                'disabled' => 'true'
            ),
            'bg_video_youtube_repeat' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CERowObjBGVideoRepeatLabel,
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                ),
                'disabled' => 'true'
            ),
            'bg_video_youtube_mute' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CERowObjBGVideoMuteLabel,
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                ),
                'disabled' => 'true'
            ),
            'bg_video_webm' => array(
                'type' => 'media-video',
                'legend' => $motopressCELang->CERowObjBGVideoWEBMLegend,
                'label' => strtr($motopressCELang->CERowObjBGVideoFormatLabel, array('%name%' => 'WEBM')),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                ),
                'disabled' => 'true'
            ),
            'bg_video_mp4' => array(
                'type' => 'media-video',
                'label' => strtr($motopressCELang->CERowObjBGVideoFormatLabel, array('%name%' => 'MP4')),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                ),
                'disabled' => 'true'
            ),
            'bg_video_ogg' => array(
                'type' => 'media-video',
                'label' => strtr($motopressCELang->CERowObjBGVideoFormatLabel, array('%name%' => 'OGV')),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                ),
                'disabled' => 'true'
            ),
            'bg_video_cover' => array(
                'type' => 'image',
                'label' => $motopressCELang->CERowObjBGVideoCoverImageLabel,
                'description' => $motopressCELang->CERowObjBGVideoCoverImageDesc,
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                ),
                'disabled' => 'true'
            ),
            'bg_video_repeat' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CERowObjBGVideoRepeatLabel,
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                ),
                'disabled' => 'true'
            ),
            'bg_video_mute' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CERowObjBGVideoMuteLabel,
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                ),
                'disabled' => 'true'
            ),
            'parallax_image' => array(
                'type' => 'image',
                'label' => $motopressCELang->CERowObjParallaxImageLabel,
                'description' => $motopressCELang->CERowObjParallaxImageDesc,
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'parallax'
                ),
                'disabled' => 'true'
            ),
//            'parallax_speed' => array(
//                'type' => 'spinner',
//                'label' => '',
//                'description' => '',
//                'default' => 0.5,
//                'min' => -5,
//                'max' => 5,
//                'step' => 0.1,
//                'dependency' => array(
//                    'parameter' => 'bg_media_type',
//                    'value' => 'parallax'
//                )
//            )
        );
        $rowObj = new MPCEObject(MPCEShortcode::PREFIX . 'row', $motopressCELang->CERowObjName, null, $rowParameters, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
        $rowObj->addStyle(
            array(
                'mp_style_classes' => array(
                    'predefined' => $rowPredefinedStyles,
                    'additional_description' => $motopressCELang->CERowStyleClassesLabelAddtlDesc
                )
            )
        );

        $rowInnerObj = new MPCEObject(MPCEShortcode::PREFIX . 'row_inner', $motopressCELang->CERowInnerObjName, null, $rowParameters, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
        $rowInnerObj->addStyle(
            array(
                'mp_style_classes' => array(
                    'predefined' => $rowPredefinedStyles,
                    'additional_description' => $motopressCELang->CERowStyleClassesLabelAddtlDesc
                )
            )
        );

        $spanObj = new MPCEObject(MPCEShortcode::PREFIX . 'span', $motopressCELang->CESpanObjName, null, null, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
        $spanObj->addStyle(
            array(
                'mp_style_classes' => array(
                    'predefined' => $spanPredefinedStyles
                )
            )
        );
        $this->setGrid(array(
            'row' => array(
                'shortcode' => 'mp_row',
                'inner' => 'mp_row_inner',
                'class' => 'mp-row-fluid',
                'col' => '12'
            ),
            'span' => array(
                'type' => 'single',
                'shortcode' => 'mp_span',
                'inner' => 'mp_span_inner',
                'class' => 'mp-span',
                'attr' => 'col'
            )
        ));

        $spanInnerObj = new MPCEObject(MPCEShortcode::PREFIX . 'span_inner', $motopressCELang->CESpanInnerObjName, null, null, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
        $spanInnerObj->addStyle(
            array(
                'mp_style_classes' => array(
                    'predefined' => $spanPredefinedStyles
                )
            )
        );

        //text
        $textObj = new MPCEObject(MPCEShortcode::PREFIX . 'text', $motopressCELang->CETextObjName, 'text.png', array(
            'button' => array(
                'type' => 'editor-button',
                'label' => '',
                'default' => '',
                'description' => $motopressCELang->CETextObjButtonDesc . ' ' . $motopressCELang->CETextObjName,
                'text' => $motopressCELang->edit . ' ' . $motopressCELang->CETextObjName
            )
        ), 20, MPCEObject::ENCLOSED);
        $textPredefinedStyles = array();
        $this->extendPredefinedWithGoogleFonts($textPredefinedStyles);
        $textObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $textPredefinedStyles,
                'additional_description' => $motopressCELang->CEGoogleFontsStyleClassesLabelAddtlDesc
            )
        ));

        $headingObj = new MPCEObject(MPCEShortcode::PREFIX . 'heading', $motopressCELang->CEHeadingObjName, 'heading.png', array(
            'button' => array(
                'type' => 'editor-button',
                'label' => '',
                'default' => '',
                'description' => $motopressCELang->CETextObjButtonDesc . ' ' . $motopressCELang->CEHeadingObjName,
                'text' => $motopressCELang->edit . ' ' . $motopressCELang->CEHeadingObjName
            )
        ), 10, MPCEObject::ENCLOSED);
        $headingPredefinedStyles = array();
        $this->extendPredefinedWithGoogleFonts($headingPredefinedStyles);
        $headingObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $headingPredefinedStyles,
                'additional_description' => $motopressCELang->CEGoogleFontsStyleClassesLabelAddtlDesc
            )
        ));

        $codeObj = new MPCEObject(MPCEShortcode::PREFIX . 'code', $motopressCELang->CECodeObjName, 'wordpress.png', array(
            'button' => array(
                'type' => 'editor-button',
                'label' => '',
                'default' => '',
                'description' => $motopressCELang->CETextObjButtonDesc . ' ' . $motopressCELang->CECodeObjName,
                'text' => $motopressCELang->edit . ' ' . $motopressCELang->CECodeObjName
            )
        ), 30, MPCEObject::ENCLOSED);
        $codePredefinedStyles = array();
        $this->extendPredefinedWithGoogleFonts($codePredefinedStyles);
        $codeObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $codePredefinedStyles,
                'additional_description' => $motopressCELang->CEGoogleFontsStyleClassesLabelAddtlDesc
            )
        ));

        //image
        $imageObj = new MPCEObject(MPCEShortcode::PREFIX . 'image', $motopressCELang->CEImageObjName, 'image.png', array(
            'id' => array(
                'type' => 'image',
                'label' => $motopressCELang->CEImageObjSrcLabel,
                'default' => '',
                'description' => $motopressCELang->CEImageObjSrcDesc,
                'autoOpen' => 'true'
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjImageSizeLabel,
                'default' => 'full',
                'disabled' => 'true',
                'list' => array(
                    'full' => $motopressCELang->CEFull,
                    'large' => $motopressCELang->CELarge,
                    'medium' => $motopressCELang->CEMedium,
                    'thumbnail' => $motopressCELang->CEThumbnail,
                    'custom' => $motopressCELang->CECustom
                )
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => $motopressCELang->CEImageCustomSizeLabel,
                'dependency' => array(
                    'parameter' => 'size',
                    'value' => 'custom'
                ),
            ),
            'link_type' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEImageLinkLabel,
                'default' => 'custom_url',
                'disabled' => 'true',
                'list' => array(
                    'custom_url' => $motopressCELang->CECustomURL,
                    'media_file' => $motopressCELang->CEMediaFile,
                    'lightbox' => $motopressCELang->CELightbox
                )
            ),
            'link' => array(
                'type' => 'link',
                'label' => $motopressCELang->CEImageLinkLabel,
                'default' => '#',
                'description' => $motopressCELang->CEImageObjLinkDesc,
                'disabled' => 'true',
                'dependency' => array(
                    'parameter' => 'link_type',
                    'value' => 'custom_url'
                )
            ),
            'rel' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEImageRelLabel,
                'default' => '',
                'dependency' => array(
                    'parameter' => 'link_type',
                    'value' => 'media_file'
                )
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEOpenLinkInNewWindow,
                'default' => 'false',
                'disabled' => 'true'
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjAlignLabel,
                'default' => 'left',
                'list' => array(
                    'left' => $motopressCELang->CELeft,
                    'center' => $motopressCELang->CECenter,
                    'right' => $motopressCELang->CERight
                )
            )
        ), 10);
        $imageObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-image-obj-basic',
                    'label' => 'Image'
                ),
                'selector' => '> img'
            )
        ));

        $gridGalleryObj = new MPCEObject(MPCEShortcode::PREFIX . 'grid_gallery', $motopressCELang->CEGridGalleryObjName,  'grid-gallery.png', array(
            'ids' => array(
                'type' => 'multi-images',
                'default' => '',
                'description' => $motopressCELang->CEMediaLibraryImagesIdsDesc,
                'text' => $motopressCELang->CEImageSliderObjIdsText,
                'autoOpen' => 'true'
            ),
            'columns' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEColumnsCount,
                'default' => 3,
                'list' => array(
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    6 => 6
                ),
                'disabled' => 'true'
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjImageSizeLabel,
                'default' => 'thumbnail',
                'list' => array(
                    'full' => $motopressCELang->CEFull,
                    'large' => $motopressCELang->CELarge,
                    'medium' => $motopressCELang->CEMedium,
                    'thumbnail' => $motopressCELang->CEThumbnail,
                    'custom' => $motopressCELang->CECustom
                )
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => $motopressCELang->CEImageCustomSizeLabel,
                'dependency' => array(
                    'parameter' => 'size',
                    'value' => 'custom'
                ),
            ),
            'link_type' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEImageLinkLabel,
                'default' => 'none',
                'list' => array(
                    'none' => $motopressCELang->CENone,
                    'media_file' => $motopressCELang->CEMediaFile,
                    'attachment' => $motopressCELang->CEAttachmentPage,
                    'lightbox' => $motopressCELang->CELightbox,
                ),
                'disabled' => 'true'
            ),
            'rel' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEImageRelLabel,
                'default' => '',
                'dependency' => array(
                    'parameter' => 'link_type',
                    'value' => 'media_file'
                )
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEOpenLinkInNewWindow,
                'default' => 'false',
            ),
            'caption' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEGalleryGridObjCaptionLabel,
                'default' => 'false',
            )
        ), 30);
        $gridGalleryObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-grid-gallery-obj-basic',
                    'label' => 'Grid Gallery'
                )
            )
        ));

        $imageSlider = new MPCEObject(MPCEShortcode::PREFIX . 'image_slider', $motopressCELang->CEImageSliderObjName, 'image-slider.png', array(
            'ids' => array(
                'type' => 'multi-images',
                'label' => $motopressCELang->CEImageSliderObjIdsLabel,
                'default' => '',
                'description' => $motopressCELang->CEMediaLibraryImagesIdsDesc,
                'text' => $motopressCELang->CEImageSliderObjIdsText,
                'autoOpen' => 'true'
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjImageSizeLabel,
                'default' => 'full',
                'list' => array(
                    'full' => $motopressCELang->CEFull,
                    'large' => $motopressCELang->CELarge,
                    'medium' => $motopressCELang->CEMedium,
                    'thumbnail' => $motopressCELang->CEThumbnail,
                    'custom' => $motopressCELang->CECustom
                )
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => $motopressCELang->CEImageCustomSizeLabel,
                'dependency' => array(
                    'parameter' => 'size',
                    'value' => 'custom'
                ),
            ),
            'animation' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEImageSliderObjAnimationLabel,
                'default' => 'fade',
                'description' => $motopressCELang->CEImageSliderObjAnimationDesc,
                'list' => array(
                    'fade' => $motopressCELang->CEImageSliderObjAnimationFade,
                    'slide' => $motopressCELang->CEImageSliderObjAnimationSlide
                ),
                'disabled' => 'true'
            ),
            'smooth_height' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEImageSliderObjSmoothHeightLabel,
                'default' => 'false',
                'description' => $motopressCELang->CEImageSliderObjSmoothHeightDesc,
                'dependency' => array(
                    'parameter' => 'animation',
                    'value' => 'slide'
                ),
                'disabled' => 'true'
            ),
            'slideshow' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEImageSliderObjAutoplayLabel,
                'default' => 'true',
                'description' => $motopressCELang->CEImageSliderObjAutoplayDesc,
                'disabled' => 'true'
            ),
            'slideshow_speed' => array(
                'type' => 'slider',
                'label' => $motopressCELang->CEImageSliderObjSlideshowSpeedLabel,
                'default' => 7,
                'min' => 1,
                'max' => 20,
                'dependency' => array(
                    'parameter' => 'slideshow',
                    'value' => 'true'
                ),
                'disabled' => 'true'
            ),
            'animation_speed' => array(
                'type' => 'slider',
                'label' => $motopressCELang->CEImageSliderObjAnimationSpeedLabel,
                'default' => 600,
                'min' => 200,
                'max' => 10000,
                'step' => 200,
                'disabled' => 'true'
            ),
            'control_nav' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEImageSliderObjControlNavLabel,
                'default' => 'true',
                'disabled' => 'true'
            )
        ), 20);
        $imageSlider->addStyle(array(
            'mp_style_classes' => array(
                'selector' => '> ul:first-of-type'
            )
        ));

        //button
        $buttonObj = new MPCEObject(MPCEShortcode::PREFIX . 'button', $motopressCELang->CEButtonObjName, 'button.png', array(
            'text' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEButtonObjTextLabel,
                'default' => $motopressCELang->CEButtonObjName
            ),
            'link' => array(
                'type' => 'link',
                'label' => $motopressCELang->CEButtonObjLinkLabel,
                'default' => '#',
                'description' => $motopressCELang->CEButtonObjLinkDesc
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEOpenLinkInNewWindow,
                'default' => 'false',
                'disabled' => 'true'
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjAlignLabel,
                'default' => 'left',
                'list' => array(
                    'left' => $motopressCELang->CELeft,
                    'center' => $motopressCELang->CECenter,
                    'right' => $motopressCELang->CERight
                )
            )
        ), 10);
        $buttonObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-btn',
                    'label' => $motopressCELang->CEButtonObjBasicClassLabel
                ),
                'predefined' => array(
                    'color' => array(
                        'label' => $motopressCELang->CEButtonObjColorLabel,
                        'values' => array(
                            'silver' => array(
                                'class' => 'motopress-btn-color-silver',
                                'label' => $motopressCELang->CESilver
                            ),
                            'red' => array(
                                'class' => 'motopress-btn-color-red',
                                'label' => $motopressCELang->CERed
                            ),
                            'pink-dreams' => array(
                                'class' => 'motopress-btn-color-pink-dreams',
                                'label' => $motopressCELang->CEPinkDreams
                            ),
                            'warm' => array(
                                'class' => 'motopress-btn-color-warm',
                                'label' => $motopressCELang->CEWarm
                            ),
                            'hot-summer' => array(
                                'class' => 'motopress-btn-color-hot-summer',
                                'label' => $motopressCELang->CEHotSummer
                            ),
                            'olive-garden' => array(
                                'class' => 'motopress-btn-color-olive-garden',
                                'label' => $motopressCELang->CEOliveGarden
                            ),
                            'green-grass' => array(
                                'class' => 'motopress-btn-color-green-grass',
                                'label' => $motopressCELang->CEGreenGrass
                            ),
                            'skyline' => array(
                                'class' => 'motopress-btn-color-skyline',
                                'label' => $motopressCELang->CESkyline
                            ),
                            'aqua-blue' => array(
                                'class' => 'motopress-btn-color-aqua-blue',
                                'label' => $motopressCELang->CEAquaBlue
                            ),
                            'violet' => array(
                                'class' => 'motopress-btn-color-violet',
                                'label' => $motopressCELang->CEViolet
                            ),
                            'dark-grey' => array(
                                'class' => 'motopress-btn-color-dark-grey',
                                'label' => $motopressCELang->CEDarkGrey
                            ),
                            'black' => array(
                                'class' => 'motopress-btn-color-black',
                                'label' => $motopressCELang->CEBlack
                            )
                        )
                    ),
                    'size' => array(
                        'label' => $motopressCELang->CEObjSizeLabel,
                        'values' => array(
                            'mini' => array(
                                'class' => 'motopress-btn-size-mini',
                                'label' => $motopressCELang->CEMini
                            ),
                            'small' => array(
                                'class' => 'motopress-btn-size-small',
                                'label' => $motopressCELang->CESmall
                            ),
                            'middle' => array(
                                'class' => 'motopress-btn-size-middle',
                                'label' => $motopressCELang->CEMiddle
                            ),
                            'large' => array(
                                'class' => 'motopress-btn-size-large',
                                'label' => $motopressCELang->CELarge
                            )
                        )
                    ),
                    'rounded' => array(
                        'class' => 'motopress-btn-rounded',
                        'label' => $motopressCELang->CERounded
                    )
                ),
                'default' => array('motopress-btn-color-silver', 'motopress-btn-size-middle', 'motopress-btn-rounded'),
                'selector' => '> a'
            )
        ));

        $accordionObj = new MPCEObject(MPCEShortcode::PREFIX . 'accordion', $motopressCELang->CEAccordionObjName, 'accordion.png', array(
            'accordionItems' => array(
                'type' => 'group',
                'items' => array(
                    'label' => array(
                        'default' => $motopressCELang->CEAccordionItemObjTitleLabel,
                        'parameter' => 'title'
                    ),
                    'count' => 2
                ),
                'text' => strtr($motopressCELang->CEAddNewItem, array('%name%' => $motopressCELang->CEAccordionObjName)),
                'disabled' => 'true'
            ),
        ), 11, MPCEObject::ENCLOSED);
        $accordionObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-accordion',
                    'label' => $motopressCELang->CEAccordionObjBasicClassLabel
                ),
                'predefined' => array(
                    'style' => array(
                        'label' => $motopressCELang->CEObjStyleLabel,
                        'values' => array(
                            'light' => array(
                                'class' => 'motopress-accordion-light',
                                'label' => $motopressCELang->CEAccordionObjStyleListLight
                            ),
                            'dark' => array(
                                'class' => 'motopress-accordion-dark',
                                'label' => $motopressCELang->CEAccordionObjStyleListDark,
                                'disabled' => true
                            )
                        )
                    )
                ),
                'default' => array('motopress-accordion-light')
            )
        ));

        $accordionItemObj = new MPCEObject(MPCEShortcode::PREFIX . 'accordion_item', $motopressCELang->CEAccordionItemObjName, null, array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEAccordionItemObjTitleLabel,
                'default' => $motopressCELang->CEAccordionItemObjTitleLabel
            ),
            'content' => array(
                'type' => 'longtext-tinymce',
                'label' => $motopressCELang->CEAccordionItemObjContentLabel,
                'default' => $motopressCELang->CEContentDefault,
                'text' => $motopressCELang->CEOpenInWPEditor,
                'saveInContent' => 'true'
            ),
            'active' => array(
                'type' => 'group-checkbox',
                'label' => $motopressCELang->CEActive,
                'default' => 'false',
                'description' => strtr($motopressCELang->CEActiveDesc, array('%name%' => $motopressCELang->CEAccordionItemObjName))
            )
        ), null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE, false);

        $tabsObj = new MPCEObject(MPCEShortcode::PREFIX . 'tabs', $motopressCELang->CETabsObjName, 'tabs.png', array(
            'tabs' => array(
                'type' => 'group',
                'items' => array(
                    'label' => array(
                        'default' => $motopressCELang->CETabObjTitleLabel,
                        'parameter' => 'title'
                    ),
                    'count' => 2
                ),
                'text' => strtr($motopressCELang->CEAddNewItem, array('%name%' => $motopressCELang->CETabObjName)),
                'disabled' => 'true'
            ),
            'padding' => array(
                'type' => 'slider',
                'label' => $motopressCELang->CETabsObjPaddingLabel,
                'default' => 20,
                'min' => 0,
                'max' => 50,
                'step' => 10
            )
/*
            'color' => array(
                'type' => 'color-picker',
                'label' => $motopressCELang->CEColor,
                'default' => ''
            ),
            'spinner' => array(
                'type' => 'spinner',
                'label' => 'spinner',
                'description' => "desc <a href='http://google.ru' target='_blank'>link</a> <i>foo</i> <b>bar</b>",
                'default' => 50,
                'min' => 0,
                'max' => 100,
                'step' => 10
            ),
            'slider' => array(
                'type' => 'slider',
                'label' => 'Slider',
                'default' => 500,
                'description' => 'Description',
                'min' => -101,
                'max' => 999,
                'step' => 1
            ),
            'buttonsgroup' => array(
                'type' => 'radio-buttons',
                'label' => 'Toggle button group',
                'default' => '#00ff00',
                'list' => array(
                    '#ff0000' => 'Red',
                    '#00ff00' => 'Green',
                    '#0000ff' => 'Blue',
                    '#000000' => 'Black',
                    '#f32222' => 'Red 2',
                    '#22f322' => 'Green 2',
                    '#2222f3' => 'Blue 2',
                    '#cccccc' => 'Gray'
                )
            )

            'layout' => array(
                'type' => 'select',
                'label' => 'layout',
                'default' => 'top-left',
                'list' => array(
                    'top-left' => 'top left'
                )
            ),
            'color' => array(
                'type' => 'select',
                'label' => 'color',
                'default' => 'gray',
                'list' => array(
                    'left' => 'gray'
                )
            )
*/
        ), 20, MPCEObject::ENCLOSED);
        $tabsObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-tabs-basic',
                    'label' => $motopressCELang->CETabsObjBasicClassLabel
                ),
                'selector' => '> div'
            )
        ));

        $tabObj = new MPCEObject(MPCEShortcode::PREFIX . 'tab', $motopressCELang->CETabObjName, null, array(
            'id' => array(
                'type' => 'text-hidden'
            ),
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CETabObjTitleLabel,
                'default' => $motopressCELang->CETabObjTitleLabel
            ),
            'content' => array(
                'type' => 'longtext-tinymce',
                'label' => $motopressCELang->CETabObjContentLabel,
                'default' => $motopressCELang->CEContentDefault,
                'text' => $motopressCELang->CEOpenInWPEditor,
                'saveInContent' => 'true'
            ),
            'active' => array(
                'type' => 'group-checkbox',
                'label' => $motopressCELang->CEActive,
                'default' => 'false',
                'description' => strtr($motopressCELang->CEActiveDesc, array('%name%' => $motopressCELang->CETabObjName))
            )
        ), null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE, false);

        $socialsObj = new MPCEObject(MPCEShortcode::PREFIX . 'social_buttons', $motopressCELang->CESocialsObjName, 'social-buttons.png', array(
            'align' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjAlignLabel,
                'default' => 'motopress-text-align-left',
                'list' => array(
                    'motopress-text-align-left' => $motopressCELang->CELeft,
                    'motopress-text-align-center' => $motopressCELang->CECenter,
                    'motopress-text-align-right' => $motopressCELang->CERight
                )
            )
        ), 20, MPCEObject::ENCLOSED);
        $socialsObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => array(
                    'size' => array(
                        'label' => $motopressCELang->CEObjSizeLabel,
                        'values' => array(
                            'normal' => array(
                                'class' => 'motopress-buttons-32x32',
                                'label' => $motopressCELang->CESocialsObjSizeNormal
                            ),
                            'large' => array(
                                'class' => 'motopress-buttons-64x64',
                                'label' => $motopressCELang->CESocialsObjSizeLarge
                            )
                        )
                    ),
                    'style' => array(
                        'label' => $motopressCELang->CEObjStyleLabel,
                        'values' => array(
                            'plain' => array(
                                'class' => 'motopress-buttons-square',
                                'label' => $motopressCELang->CESocialsObjStyleSquare
                            ),
                            'rounded' => array(
                                'class' => 'motopress-buttons-rounded',
                                'label' => $motopressCELang->CERounded
                            ),
                            'circular' => array(
                                'class' => 'motopress-buttons-circular',
                                'label' => $motopressCELang->CESocialsObjStyleCircular
                            ),
                            'volume' => array(
                                'class' => 'motopress-buttons-volume',
                                'label' => $motopressCELang->CESocialsObjStyleVolume
                            )
                        )
                    )
                ),
                'default' => array('motopress-buttons-32x32', 'motopress-buttons-square')
            )
        ));

        $socialProfileObj = new MPCEObject(MPCEShortcode::PREFIX . 'social_profile', $motopressCELang->CESocialProfileObjName, 'social-profile.png', array(
            'facebook' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'Facebook')),
                'default' => 'https://www.facebook.com/motopressapp'
            ),
            'google' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'Google+')),
                'default' => 'https://plus.google.com/+Getmotopress/posts'
            ),
            'twitter' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'Twitter')),
                'default' => 'https://twitter.com/motopressapp'
            ),
            'pinterest' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'Pinterest')),
                'default' => 'http://www.pinterest.com/motopress/'
            ),
            'linkedin' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'LinkedIn')),
            ),
            'flickr' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'Flickr')),
            ),
            'vk' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'VK')),
            ),
            'delicious' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'Delicious')),
            ),
            'youtube' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'YouTube')),
                'default' => 'https://www.youtube.com/channel/UCtkDYmIQ5Lv_z8KbjJ2lpFQ'
            ),
            'rss' => array(
                'type' => 'text',
                'label' => strtr($motopressCELang->CESocialProfileObjURLLabel, array('%name%' => 'RSS')),
                'default' => 'http://www.getmotopress.com/feed/'
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjAlignLabel,
                'default' => 'left',
                'list' => array(
                    'left' => $motopressCELang->CELeft,
                    'center' => $motopressCELang->CECenter,
                    'right' => $motopressCELang->CERight
                )
            )
        ), 30);
        $socialProfileObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => array(
                    'size' => array(
                        'label' => $motopressCELang->CEObjSizeLabel,
                        'values' => array(
                            'normal' => array(
                                'class' => 'motopress-buttons-32x32',
                                'label' => $motopressCELang->CESocialsObjSizeNormal
                            ),
                            'large' => array(
                                'class' => 'motopress-buttons-64x64',
                                'label' => $motopressCELang->CESocialsObjSizeLarge
                            )
                        )
                    ),
                    'style' => array(
                        'label' => $motopressCELang->CEObjStyleLabel,
                        'values' => array(
                            'plain' => array(
                                'class' => 'motopress-buttons-square',
                                'label' => $motopressCELang->CESocialsObjStyleSquare
                            ),
                            'rounded' => array(
                                'class' => 'motopress-buttons-rounded',
                                'label' => $motopressCELang->CERounded
                            ),
                            'circular' => array(
                                'class' => 'motopress-buttons-circular',
                                'label' => $motopressCELang->CESocialsObjStyleCircular
                            ),
                            'volume' => array(
                                'class' => 'motopress-buttons-volume',
                                'label' => $motopressCELang->CESocialsObjStyleVolume
                            )
                        )
                    )
                ),
                'default' => array('motopress-buttons-32x32', 'motopress-buttons-square')
            )
        ));

        //media
        $videoObj = new MPCEObject(MPCEShortcode::PREFIX . 'video', $motopressCELang->CEVideoObjName, 'video.png', array(
            'src' => array(
                'type' => 'video',
                'label' => $motopressCELang->CEVideoObjSrcLabel,
                'default' => MPCEShortcode::DEFAULT_VIDEO,
                'description' => $motopressCELang->CEVideoObjSrcDesc
            )
        ), 10);
        $videoObj->addStyle(array(
            'mp_style_classes' => array(
                'selector' => '> iframe'
            )
        ));

        // WP Audio
         $wpAudioObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_audio', $motopressCELang->CEwpAudio, 'player.png', array(
            'source' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpAudioSourceTitle,
                'description' => $motopressCELang->CEwpAudioSourceDesc,
                'list' => array(
                    'library' => $motopressCELang->CEwpAudioSourceLibrary,
                    'external' => $motopressCELang->CEwpAudioSourceURL,
                ),
                'default' => 'external'
            ),
            'id' => array(
                'type' => 'audio',
                'label' => $motopressCELang->CEwpAudioIdTitle,
                'description' => $motopressCELang->CEwpAudioIdDescription,
                'default' => '',
                'dependency' => array(
                    'parameter' => 'source',
                    'value' => 'library'
                )
                ),
            'url' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpAudioUrlTitle,
                'description' => $motopressCELang->CEwpAudioUrlDescription,
                'default' => 'http://wpcom.files.wordpress.com/2007/01/mattmullenweg-interview.mp3',
                'dependency' => array(
                    'parameter' => 'source',
                    'value' => 'external'
                )
            ),
            'autoplay' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpAudioAutoplayTitle,
                'description' => $motopressCELang->CEwpAudioAutoplayDesc,
                'default' => '',
            ),
            'loop' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpAudioLoopTitle,
                'description' => $motopressCELang->CEwpAudioLoopDesc,
                'default' => '',
            )
        ), 20, MPCEObject::ENCLOSED);

        //other
        $gMapObj = new MPCEObject(MPCEShortcode::PREFIX.'gmap', $motopressCELang->CEGoogleMapObjName, 'map.png', array(
            'address' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEGoogleMapObjAddressLabel,
                'default' => 'Sidney, New South Wales, Australia',
                'description' => $motopressCELang->CEGoogleMapObjAddressDesc
            ),
            'zoom' => array(
                'type' => 'slider',
                'label' => $motopressCELang->CEGoogleMapObjZoomLabel,
                'default' => 13,
                'min' => 0,
                'max' => 20
            )
        ), 60, null, MPCEObject::RESIZE_ALL);
        $gMapObj->addStyle(array(
            'mp_style_classes' => array(
                'selector' => '> iframe'
            )
        ));

        $spaceObj = new MPCEObject(MPCEShortcode::PREFIX . 'space', $motopressCELang->CESpaceObjName, 'space.png', null, 50, null, MPCEObject::RESIZE_ALL);
        $spaceObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $spacePredefinedStyles
            )
        ));

        $embedObj = new MPCEObject(MPCEShortcode::PREFIX . 'embed', $motopressCELang->CEEmbedObjName, 'code.png', array(
            'data' => array(
                'type' => 'longtext64',
                'label' => $motopressCELang->CEEmbedObjPasteCode,
                'default' => 'PGk+UGFzdGUgeW91ciBjb2RlIGhlcmUuPC9pPg==',
                'description' => $motopressCELang->CEEmbedObjPasteCodeDescription
            ),
            'fill_space' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEEmbedObjFill,
                'default' => 'true',
                'description' => $motopressCELang->CEEmbedObjFillDescription
            )
        ), 40);

        $quotesObj = new MPCEObject(MPCEShortcode::PREFIX . 'quote', $motopressCELang->CEQuotesObjName, 'quotes.png', array(
            'cite' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEQuotesObjCiteLabel,
                'default' => 'John Smith',
                'description' => $motopressCELang->CEQuotesObjCiteDesc,
            ),
            'cite_url' => array(
                'type' => 'link',
                'label' => $motopressCELang->CEQuotesObjUrlLabel,
                'default' => '#',
                'description' => $motopressCELang->CEQuotesObjUrlDesc,
            ),
            'quote_content' => array(
                'type' => 'longtext',
                'label' => $motopressCELang->CEQuotesObjContentLabel,
                'default' => 'Lorem ipsum dolor sit amet.'
            )
        ), 40, MPCEObject::ENCLOSED);

        $membersObj = new MPCEObject(MPCEShortcode::PREFIX . 'members_content', $motopressCELang->CEMembersObjName, 'members.png', array(
            'message' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEMembersObjMessageLabel,
                'default' => $motopressCELang->CEMembersObjMessageDefault,
                'description' => $motopressCELang->CEMembersObjMessageDesc,
            ),
            'login_text' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEMembersObjLoginTextLabel,
                'default' => $motopressCELang->CEMembersObjLoginTextDefault,
                'description' => $motopressCELang->CEMembersObjLoginTextDesc,
            ),
            'members_content' => array(
                'type' => 'longtext',
                'label' => $motopressCELang->CEMembersObjContentLabel,
                'default' => $motopressCELang->CEMembersObjContentValue,
            ),
        ), 50, MPCEObject::ENCLOSED);

        $googleChartsObj = new MPCEObject(MPCEShortcode::PREFIX . 'google_chart', $motopressCELang->CEGoogleChartsObjName, 'chart.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEObjTitleLabel,
                'default' => 'Company Performance'
            ),
            'type' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEGoogleChartsObjTypeLabel,
                'description' => $motopressCELang->CEGoogleChartsObjTypeDesc,
                'default' => 'ColumnChart',
                'list' => array(
                    'ColumnChart' => $motopressCELang->CEGoogleChartsObjTypeListColumn,
                    'BarChart' => $motopressCELang->CEGoogleChartsObjTypeListBar,
                    'AreaChart' => $motopressCELang->CEGoogleChartsObjTypeListArea,
                    'SteppedAreaChart' => $motopressCELang->CEGoogleChartsObjTypeListStepped,
                    'PieChart' => $motopressCELang->CEGoogleChartsObjTypeListPie,
                    'PieChart3D' => $motopressCELang->CEGoogleChartsObjTypeList3D,
                    'LineChart' => $motopressCELang->CEGoogleChartsObjTypeListLine,
                    'Histogram' => $motopressCELang->CEGoogleChartsObjTypeListHistogram
                ),
                'disabled' => 'true'
            ),
            'donut' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEGoogleChartsObjDonutLabel,
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' =>'PieChart'
                )
            ),
            'colors' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEGoogleChartsObjColorsLabel,
                'description' => $motopressCELang->CEGoogleChartsObjColorsDesc,
                'disabled' => 'true',
            ),
            'transparency' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEGoogleChartsObjTransparencyLabel,
                'default' => 'false',
                'disabled' => 'true',
            ),
            'table' => array(
                'type' => 'longtext-table',
                'label' => $motopressCELang->CEObjTableDataLabel,
                'description' => $motopressCELang->CEGoogleChartsObjDataDesc,
                'default' => 'Year,Sales,Expenses<br />2004,1000,400<br />2005,1170,460<br />2006,660,1120<br />2007,1030,540',
                'saveInContent' => 'true'
            )
        ), 30, MPCEObject::ENCLOSED, MPCEObject::RESIZE_ALL);

        $tableObj = new MPCEObject(MPCEShortcode::PREFIX . 'table', $motopressCELang->CETableObjName, 'table.png', array(
            'table' => array(
                'type' => 'longtext-table',
                'label' => $motopressCELang->CEObjTableDataLabel,
                'default' => 'Year,Sales,Expenses<br />2004,1000,400<br />2005,1170,460<br />2006,660,1120<br />2007,1030,540',
                'description' => $motopressCELang->CEObjTableDataDesc,
                'saveInContent' => 'true'
            )
        ), 10, MPCEObject::ENCLOSED);
        $tableObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-table',
                    'label' => $motopressCELang->CETableObjBasicClassLabel
                ),
                'predefined' => array(
                    'style' => array(
                        'label' => $motopressCELang->CEObjStyleLabel,
                        'allowMultiple' => true,
                        'values' => array(
                            'silver' => array(
                                'class' => 'motopress-table-style-silver',
                                'label' => $motopressCELang->CETableObjListLight,
                                'disabled' =>true
                            ),
                            'left' => array(
                                'class' => 'motopress-table-first-col-left',
                                'label' => $motopressCELang->CETableObjFirstColLeft
                            )
                        )
                    )
                ),
                'default' => array('motopress-table-first-col-left'),
                'selector' => '> table'
            )
        ));

        $postsGridObj = new MPCEObject(MPCEShortcode::PREFIX . 'posts_grid', $motopressCELang->CEPostsGridObjName, 'posts-grid.png', array(
            'post_type' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEPostsGridObjPostTypeLabel,
                'description' => $motopressCELang->CEPostsGridObjPostTypeDesc,
                'list' =>MPCEShortcode::getPostTypes()
            ),
            'columns' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEColumnsCount,
                'default' => 1,
                'list' => array(
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    6 => 6
                )
            ),
            'category' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEPostsGridObjCategoryLabel,
                'description' => $motopressCELang->CEPostsGridObjCategoryDesc,
                'disabled' => 'true'
            ),
            'tag' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEPostsGridObjTagLabel,
                'description' => $motopressCELang->CEPostsGridObjTagDesc,
                'disabled' => 'true'
            ),
            'posts_per_page' => array(
                'type' => 'spinner',
                'label' => $motopressCELang->CEPostsGridObjPostsPerPageLabel,
                'default' => 3,
                'min' => 1,
                'max' => 40,
                'step' => 1,
                'disabled' => 'true'
            ),
            'posts_order' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEPostsGridObjSortOrder,
                'default' => 'DESC',
                'list' => array(
                    'ASC' => $motopressCELang->CEPostsGridObjSortOrderAscending,
                    'DESC' => $motopressCELang->CEPostsGridObjSortOrderDescending
                ),
                'disabled' => 'true'
            ),
            'template' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEPostsGridObjTemplateLabel,
                'list' => MPCEShortcode::getPostsGridTemplatesList(),
            ),
            'posts_gap' => array(
                'type' => 'slider',
                'label' => $motopressCELang->CEPostsGridObjPostsGapLabel,
                'default' => 30,
                'min' => 0,
                'max' => 100,
                'step' => 10,
            ),
            'show_featured_image' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEPostsGridObjShowFeaturedImage,
                'default' => 'true',
            ),
            'image_size' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEObjImageSizeLabel,
                'default' => 'large',
                'list' => array(
                    'full' => $motopressCELang->CEFull,
                    'large' => $motopressCELang->CELarge,
                    'medium' => $motopressCELang->CEMedium,
                    'thumbnail' => $motopressCELang->CEThumbnail,
                    'custom' => $motopressCELang->CECustom
                ),
                'dependency' => array(
                    'parameter' => 'show_featured_image',
                    'value' => 'true'
                ),
            ),
            'image_custom_size' => array(
                'type' => 'text',
                'description' => $motopressCELang->CEImageCustomSizeLabel,
                'dependency' => array(
                    'parameter' => 'image_size',
                    'value' => 'custom'
                ),
            ),
            'title_tag' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEPostsGridObjTitleTag,
                'default' => 'h2',
                'list' => array(
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'hide' => $motopressCELang->CEPostsGridObjTitleTagNone,
                )
            ),
            'show_date_comments' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEPostsGridObjShowDateComments,
                'default' => 'true',
            ),
            'show_content' => array(
                'type' => 'radio-buttons',
                'label' => $motopressCELang->CEPostsGridObjShowContent,
                'default' => 'short',
                'list' => array(
                    'short' => $motopressCELang->CEPostsGridObjShowContentShort,
                    'full' => $motopressCELang->CEPostsGridObjShowContentFull,
                    'excerpt' => $motopressCELang->CEPostsGridObjShowContentExcerpt,
                    'hide' => $motopressCELang->CEPostsGridObjShowContentNone,
                )
            ),
            'short_content_length' => array(
                'type' => 'slider',
                'label' => $motopressCELang->CEPostsGridObjShortContentLength,
                'default' => 200,
                'min' => 0,
                'max' => 1000,
                'step' => 20,
                'dependency' => array(
                    'parameter' => 'show_content',
                    'value' => 'short'
                ),
            ),
            'read_more_text' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEPostsGridObjReadMoreTextLabel,
                'default' => $motopressCELang->CEPostsGridObjReadMoreText
            ),
            'pagination' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEPostsGridObjShowPagination,
                'default' => 'false'
            )
        ));
        $postsGridObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-posts-grid-basic',
                    'label' => $motopressCELang->CEPostsGridObjBasicClassLabel
                )
            )
        ));

        //wordpress
        // WP Widgets Area
        global $wp_registered_sidebars;
        $wpWidgetsArea_array = array();
        $wpWidgetsArea_default = '';
        if ( $wp_registered_sidebars ){
            foreach ( $wp_registered_sidebars as $sidebar ) {
                if (empty($wpWidgetsArea_default))
                        $wpWidgetsArea_default = $sidebar['id'];
                $wpWidgetsArea_array[$sidebar['id']] = $sidebar['name'];
            }
        }else {
            $wpWidgetsArea_array['no'] = $motopressCELang->CEwpWidgetsAreaNoSidebars;
        }
        $wpWidgetsAreaObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_widgets_area', $motopressCELang->CEwpWidgetsArea, 'sidebar.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => '',
                'description' => $motopressCELang->CEwpWidgetsAreaDescription
            ),
            'sidebar' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpWidgetsAreaSelect,
                'default' => $wpWidgetsArea_default,
                'description' => '',
                'list' => $wpWidgetsArea_array
            )
        ), 5);

        // archives
        $wpArchiveObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_archives', $motopressCELang->CEwpArchives, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpArchives,
                'description' => $motopressCELang->CEwpArchivesDescription
            ),
            'dropdown' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpDisplayAsDropDown,
                'default' => '',
                'description' => ''
            ),
            'count' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpShowPostCounts,
                'default' => '',
                'description' => ''
            )
        ), 45);

        // calendar
        $wpCalendarObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_calendar', $motopressCELang->CEwpCalendar, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpCalendar,
                'description' => $motopressCELang->CEwpCalendarDescription
            )
        ), 30);

        // wp_categories
        $wpCategoriesObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_categories', $motopressCELang->CEwpCategories, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpCategories,
                'description' => $motopressCELang->CEwpCategoriesDescription
            ),
            'dropdown' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpDisplayAsDropDown,
                'default' => '',
                'description' => ''
            ),
            'count' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpShowPostCounts,
                'default' => '',
                'description' => ''
            ),
            'hierarchy' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpCategoriesShowHierarchy,
                'default' => '',
                'description' => ''
            )
        ), 40);

        // wp_navmenu
        $wpCustomMenu_menus = get_terms('nav_menu');
        $wpCustomMenu_array = array();
        $wpCustomMenu_default = '';
        if ($wpCustomMenu_menus){
            foreach($wpCustomMenu_menus as $menu){
                if (empty($wpCustomMenu_default))
                    $wpCustomMenu_default = $menu->slug;
                $wpCustomMenu_array[$menu->slug] = $menu->name;
            }
        }else{
            $wpCustomMenu_array['no'] = $motopressCELang->CEwpCustomMenuNoMenus;
        }
        $wpCustomMenuObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_navmenu', $motopressCELang->CEwpCustomMenu, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpCustomMenu,
                'description' => $motopressCELang->CEwpCustomMenuDescription
            ),
            'nav_menu' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpCustomMenuSelectMenu,
                'default' => $wpCustomMenu_default,
                'description' => '',
                'list' => $wpCustomMenu_array
            )
        ), 10);

        // wp_meta
        $wpMetaObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_meta', $motopressCELang->CEwpMeta, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpMeta,
                'description' => $motopressCELang->CEwpMetaDescription
            )
        ), 55);

        // wp_pages
        $wpPagesObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_pages', $motopressCELang->CEwpPages, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpPages,
                'description' => $motopressCELang->CEwpPagesDescription
            ),
            'sortby' => array(
                'type' => 'select',
                'label' => $motopressCELang->CESortBy,
                'default' => 'menu_order',
                'description' => '',
                'list' => array(
                    'post_title' => $motopressCELang->CESortByPageTitle,
                    'menu_order' => $motopressCELang->CESortByPageOrder,
                    'ID' => $motopressCELang->CESortByPageID
                ),
            ),
            'exclude' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEExclude,
                'default' => '',
                'description' => $motopressCELang->CEwpPagesExcludePages
            )
        ), 15);

        // wp_posts
        $wpPostsObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_posts', $motopressCELang->CEwpRecentPosts, 'wordpress.png', array(
            'title' => array(
                    'type' => 'text',
                    'label' => $motopressCELang->CEParametersTitle,
                    'default' => $motopressCELang->CEwpRecentPosts,
                    'description' => $motopressCELang->CEwpRecentPostsDescription
            ),
            'number' => array(
                    'type' => 'text',
                    'label' => $motopressCELang->CEwpRecentPostsNumber,
                    'default' => '5',
                    'description' => ''
            ),
            'show_date' => array(
                    'type' => 'checkbox',
                    'label' => $motopressCELang->CEwpRecentPostsDisplayDate,
                    'default' => '',
                    'description' => ''
            )
        ), 20);

        // wp_comments
        $wpRecentCommentsObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_comments', $motopressCELang->CEwpRecentComments, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpRecentComments,
                'description' => $motopressCELang->CEwpRecentCommentsDescription
            ),
            'number' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpRecentCommentsNumber,
                'default' => '5',
                'description' => ''
            )
        ), 25);

        // wp_rss
        $wpRSSObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_rss', $motopressCELang->CEwpRSS, 'wordpress.png', array(
            'url' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpRSSUrl,
                'default' => 'http://www.getmotopress.com/feed/',
                'description' => $motopressCELang->CEwpRSSUrlDescription
            ),
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEwpRSSFeedTitle,
                'default' => '',
                'description' => $motopressCELang->CEwpRSSFeedTitleDescription
            ),
            'items' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpRSSQuantity,
                'default' => 9,
                'description' => $motopressCELang->CEwpRSSQuantityDescription,
                'list' => range(1, 20),
            ),
            'show_summary' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpRSSDisplayContent,
                'default' => '',
                'description' => ''
            ),
            'show_author' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpRSSDisplayAuthor,
                'default' => '',
                'description' => ''
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => $motopressCELang->CEwpRSSDisplayDate,
                'default' => '',
                'description' => ''
            )
        ), 50);

        // search
        $wpSearchObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_search', $motopressCELang->CEwpRSSSearch, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpRSSSearch,
                'description' => $motopressCELang->CEwpRSSSearchDescription
            )
        ), 35);

        // tag cloud
        $wpTagCloudObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_tagcloud', $motopressCELang->CEwpTagCloud, 'wordpress.png', array(
            'title' => array(
                'type' => 'text',
                'label' => $motopressCELang->CEParametersTitle,
                'default' => $motopressCELang->CEwpTags,
                'description' => $motopressCELang->CEwpTagCloudDescription
            ),
            'taxonomy' => array(
                'type' => 'select',
                'label' => $motopressCELang->CEwpTagCloudTaxonomy,
                'default' => 10,
                'description' => '',
                'list' => array(
                    'post_tag' => $motopressCELang->CEwpTags,
                    'category' => $motopressCELang->CEwpTagCloudCategories,
                )
            )
        ), 60);
        /* wp widgets END */

        /* Groups */
        $gridGroup = new MPCEGroup();
        $gridGroup->setId(MPCEShortcode::PREFIX . 'grid');
        $gridGroup->setName($motopressCELang->CEGridGroupName);
        $gridGroup->setShow(false);
        $gridGroup->addObject(array($rowObj, $rowInnerObj, $spanObj, $spanInnerObj));

        $textGroup = new MPCEGroup();
        $textGroup->setId(MPCEShortcode::PREFIX . 'text');
        $textGroup->setName($motopressCELang->CETextGroupName);
        $textGroup->setIcon('text.png');
        $textGroup->setPosition(0);
        $textGroup->addObject(array($textObj, $headingObj, $codeObj, $quotesObj, $membersObj));

        $imageGroup = new MPCEGroup();
        $imageGroup->setId(MPCEShortcode::PREFIX . 'image');
        $imageGroup->setName($motopressCELang->CEImageGroupName);
        $imageGroup->setIcon('image.png');
        $imageGroup->setPosition(10);
        $imageGroup->addObject(array($imageObj, $imageSlider, $gridGalleryObj));

        $buttonGroup = new MPCEGroup();
        $buttonGroup->setId(MPCEShortcode::PREFIX . 'button');
        $buttonGroup->setName($motopressCELang->CEButtonGroupName);
        $buttonGroup->setIcon('button.png');
        $buttonGroup->setPosition(20);
        $buttonGroup->addObject(array($buttonObj, $socialsObj, $socialProfileObj));

        $mediaGroup = new MPCEGroup();
        $mediaGroup->setId(MPCEShortcode::PREFIX . 'media');
        $mediaGroup->setName($motopressCELang->CEMediaGroupName);
        $mediaGroup->setIcon('media.png');
        $mediaGroup->setPosition(30);
        $mediaGroup->addObject(array($videoObj, $wpAudioObj));

        $otherGroup = new MPCEGroup();
        $otherGroup->setId(MPCEShortcode::PREFIX . 'other');
        $otherGroup->setName($motopressCELang->CEOtherGroupName);
        $otherGroup->setIcon('other.png');
        $otherGroup->setPosition(40);
        $otherGroup->addObject(array($gMapObj, $spaceObj, $embedObj, $googleChartsObj, $tabsObj, $tabObj, $accordionObj, $accordionItemObj, $tableObj, $postsGridObj));

        $wordpressGroup = new MPCEGroup();
        $wordpressGroup->setId(MPCEShortcode::PREFIX . 'wordpress');
        $wordpressGroup->setName($motopressCELang->CEWordPressGroupName);
        $wordpressGroup->setIcon('wordpress.png');
        $wordpressGroup->setPosition(50);
        $wordpressGroup->addObject(array($wpArchiveObj, $wpCalendarObj, $wpCategoriesObj, $wpCustomMenuObj, $wpMetaObj, $wpPagesObj, $wpPostsObj, $wpRecentCommentsObj, $wpRSSObj, $wpSearchObj, $wpTagCloudObj, $wpWidgetsAreaObj));

        self::$defaultGroup = $otherGroup->getId();

        $this->addGroup(array($gridGroup, $textGroup, $imageGroup, $buttonGroup, $mediaGroup, $otherGroup, $wordpressGroup));

        /* Templates */
        require_once 'templates/landing.php';
        require_once 'templates/callToAction.php';
        require_once 'templates/feature.php';
        require_once 'templates/description.php';
        require_once 'templates/service.php';
        require_once 'templates/product.php';

        $landingTemplate = new MPCETemplate(MPCEShortcode::PREFIX . 'landing_page', $motopressCELang->CELandingTemplate . ' ' . $motopressCELang->CEPage, $landingContent, 'landing-page.png');

        $callToActionTemplate = new MPCETemplate(MPCEShortcode::PREFIX . 'call_to_action_page', $motopressCELang->CECallToActionTemplate . ' ' . $motopressCELang->CEPage, $callToActionContent, 'call-to-action-page.png');

        $featureTemplate = new MPCETemplate(MPCEShortcode::PREFIX . 'feature_list', $motopressCELang->CEFeatureTemplate . ' ' . $motopressCELang->CEList, $featureContent, 'feature-list.png');

        $descriptionTemplate = new MPCETemplate(MPCEShortcode::PREFIX . 'description_page', $motopressCELang->CEDescriptionTemplate . ' ' . $motopressCELang->CEPage, $descriptionContent, 'description-page.png');

        $serviceTemplate = new MPCETemplate(MPCEShortcode::PREFIX . 'service_list', $motopressCELang->CEServiceTemplate . ' ' . $motopressCELang->CEList, $serviceContent, 'service-list.png');

        $productTemplate = new MPCETemplate(MPCEShortcode::PREFIX . 'product_page', $motopressCELang->CEProductTemplate . ' ' . $motopressCELang->CEPage, $productContent, 'product-page.png');

        $this->addTemplate(array($landingTemplate, $callToActionTemplate, $featureTemplate, $descriptionTemplate, $serviceTemplate, $productTemplate));
    }

    /**
     * @return MPCEGroup[]
     */
    public function getLibrary() {
        return $this->library;
    }

    /**
     * @param string $id
     * @return MPCEGroup|boolean
     */
    public function &getGroup($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->library)) {
                        return $this->library[$id];
                    }
                }
            }
        }
        $group = false;
        return $group;
    }

    /**
     * @param MPCEGroup|MPCEGroup[] $group
     */
    public function addGroup($group) {
        if ($group instanceof MPCEGroup) {
            if ($group->isValid()) {
                if (!array_key_exists($group->getId(), $this->library)) {
                    if (count($group->getObjects()) > 0) {
                        $this->library[$group->getId()] = $group;
                    }
                }
            } else {
                if (!self::$isAjaxRequest) {
                    $group->showErrors();
                }
            }
        } elseif (is_array($group)) {
            if (!empty($group)) {
                foreach ($group as $g) {
                    if ($g instanceof MPCEGroup) {
                        if ($g->isValid()) {
                            if (!array_key_exists($g->getId(), $this->library)) {
                                if (count($g->getObjects()) > 0) {
                                    $this->library[$g->getId()] = $g;
                                }
                            }
                        } else {
                            if (!self::$isAjaxRequest) {
                                $g->showErrors();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function removeGroup($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->library)) {
                        unset($this->library[$id]);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param string $id
     * @return MPCEObject|boolean
     */
    public function &getObject($id) {
        foreach ($this->library as $group) {
            $object = &$group->getObject($id);
            if ($object) return $object;
        }
        $object = false;
        return $object;
    }

    /**
     * @param MPCEObject|MPCEObject[] $object
     * @param string $group [optional]
     */
    public function addObject($object, $group = 'mp_other') {
        $groupObj = &$this->getGroup($group);
        if (!$groupObj) { //for support versions less than 1.5 where group id without MPCEShortcode::PREFIX
            $groupObj = &$this->getGroup(MPCEShortcode::PREFIX . $group);
        }
        if (!$groupObj) {
            $groupObj = &$this->getGroup(self::$defaultGroup);
        }
        if ($groupObj) {
            $groupObj->addObject($object);
        }
    }

    /**
     * @param string $id
     */
    public function removeObject($id) {
        foreach ($this->library as $group) {
            if ($group->removeObject($id)) break;
        }
    }

    /**
     * @return MPCETemplate[]
     */
    public function getTemplates() {
        return $this->templates;
    }

    /**
     * @param string $id
     * @return MPCETemplate|boolean
     */
    public function &getTemplate($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->templates)) {
                        return $this->templates[$id];
                    }
                }
            }
        }
        $template = false;
        return $template;
    }

    /**
     * @param MPCETemplate|MPCETemplate[] $template
     */
    public function addTemplate($template) {
        if ($template instanceof MPCETemplate) {
            if ($template->isValid()) {
                if (!array_key_exists($template->getId(), $this->templates)) {
                    $this->templates[$template->getId()] = $template;
                }
            } else {
                if (!self::$isAjaxRequest) {
                    $template->showErrors();
                }
            }
        } elseif (is_array($template)) {
            if (!empty($template)) {
                foreach ($template as $t) {
                    if ($t instanceof MPCETemplate) {
                        if ($t->isValid()) {
                            if (!array_key_exists($t->getId(), $this->templates)) {
                                $this->templates[$t->getId()] = $t;
                            }
                        } else {
                            if (!self::$isAjaxRequest) {
                                $t->showErrors();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function removeTemplate($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->templates)) {
                        unset($this->templates[$id]);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getData() {
        $library = array(
            'groups' => array(),
            'globalPredefinedClasses' => array(),
            'tinyMCEStyleFormats' => array(),
            'templates' => array(),
            'grid' => array()
        );
        foreach ($this->library as $group) {
            if (count($group->getObjects()) > 0) {
                uasort($group->objects, array(__CLASS__, 'positionCmp'));
                $library['groups'][$group->getId()] = $group;
            }
        }
        uasort($library['groups'], array(__CLASS__, 'positionCmp'));
        $library['globalPredefinedClasses'] = $this->globalPredefinedClasses;
        $library['tinyMCEStyleFormats'] = $this->tinyMCEStyleFormats;
        $library['templates'] = $this->templates;
        $library['grid'] = $this->gridObjects;
        return $library;
    }

    /**
     * @return array
     */
    public function getObjectsList() {
        $list = array();
        foreach ($this->library as $group){
            foreach ($group->getObjects() as $object) {
                $parameters = $object->getParameters();
                if (!empty($parameters)) {
                    foreach ($parameters as $key => $value) {
                        unset($parameters[$key]);
                        $parameters[$key] = array();
                    }
                }

                $list[$object->getId()] = array(
                    'parameters' => $parameters,
                    'group' => $group->getId()
                );
            }
        }
        return $list;
    }

    /**
     * @return array
     */
    public function getObjectsNames() {
        $names = array();
        foreach ($this->library as $group){
            foreach ($group->getObjects() as $object){
                $names[] = $object->getId();
            }
        }
        return $names;
    }

    /**
     * @static
     * @param MPCEObject $a
     * @param MPCEObject $b
     * @return int
     */
    /*
    public static function nameCmp(MPCEObject $a, MPCEObject $b) {
        return strcmp($a->getName(), $b->getName());
    }
    */

    /**
     * @param MPCEElement $a
     * @param MPCEElement $b
     * @return int
     */
    public function positionCmp(MPCEElement $a, MPCEElement $b) {
        $aPosition = $a->getPosition();
        $bPosition = $b->getPosition();
        if ($aPosition == $bPosition) {
            return 0;
        }
        return ($aPosition < $bPosition) ? -1 : 1;
    }

    /**
     * @return boolean
     */
    private function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? true : false;
    }

    private function extendPredefinedWithGoogleFonts(&$predefined){
        global $motopressCESettings, $motopressCELang;
        $fontClasses = get_option('motopress_google_font_classes', array());
        if (!empty($fontClasses)) {
            $items = array();
            foreach ($fontClasses as $fontClassName => $fontClass) {
                $items[$fontClass['fullname']] = array(
                    'class' => $fontClass['fullname'],
                    'label' => $fontClassName,
                    'external' => $motopressCESettings['google_font_classes_dir_url'] . $fontClass['file']
                );
                if (!empty($fontClass['variants'])){
                    foreach($fontClass['variants'] as $variant){
                        $items[$fontClass['fullname'] . '-' . $variant] = array(
                            'class' => $fontClass['fullname'] . '-' . $variant,
                            'label' => $fontClassName . ' ' . $variant,
                            'external' => $motopressCESettings['google_font_classes_dir_url'] . $fontClass['file']
                        );
                    }
                }
            }
            $googleFontClasses = array(
                'label' => $motopressCELang->CEOptGoogleFontsSettings,
                'values' => $items
            );
            $predefined['google-font-classes'] = $googleFontClasses;
        }
    }

    public function getGridObjects(){
        return $this->gridObjects;
    }

    public function setGrid($grid){

        if (is_array($grid)
            && isset($grid['row'])
            && isset($grid['span'])
        ){
            $grid['span']['minclass'] = $grid['span']['class'] . 1;
            $grid['span']['fullclass'] = $grid['span']['class'] . $grid['row']['col'];

            $this->gridObjects = $grid;
        }
    }
    public function setRow($rowArgs){
        $this->gridObjects['row'] = $rowArgs;
    }

    public function setSpan($spanArgs){
        $this->gridObjects['span'] =$spanArgs;
    }
}
