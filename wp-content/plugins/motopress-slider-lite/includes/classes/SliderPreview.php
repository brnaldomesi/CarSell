<?php

class MPSLSliderPreview {
    private $type = null;
    protected $id = null;
    protected $sliderId = null;
    protected $options = null;
    protected $slideOrder = null;

    function __construct($type = null, $sliderId = null, $slideId = null) {
        $this->type = $type;
        $this->sliderId = $sliderId;
        $this->slideId = $slideId;
    }

    public function render() {
		MPSLSharing::disableShortcodeRendering();

        $this->hideAdminArea();

//        $preview = in_array($this->type, array('slider', 'slide'));
        $sliderPreview = $this->type === 'slider';
        $slidePreview = $this->type === 'slide';

        $slider = new MPSLSliderOptions((int) $this->sliderId, $sliderPreview, $slidePreview);

        if ($slidePreview) $slider->setPreviewSlideId($this->slideId);
        $sliderData = $slider->getFullSliderData();
        foreach ($sliderData['slides'] as $key => $value ) {
            if ($value['active']) {
                $this->slideOrder = $key + 1;
                break;
            }
        }

        $sliderData['options']['visible_from'] = '';
        $sliderData['options']['visible_till'] = '';
        if ($slidePreview) $sliderData['options']['enable_timer'] = false;
        $sliderData['options']['start_slide'] = $this->slideOrder;
        echo get_mpsl_slider_by_options((int) $this->sliderId, $sliderData);

        //TODO::script resize iframe

	   MPSLSharing::enableShortcodeRendering();
    }

    private function hideAdminArea() {
        echo '<style type="text/css">html{ background-color: #eeeeee;} #adminmenuwrap, #wpadminbar, #adminmenuback, #screen-meta, #screen-meta-links, #wpadminbar, #querylist, #wpfooter, .notice, .error, .updated, .update-nag{display:none;} body{display: table; width: 100%;} #wpbody-content {padding-bottom: 0;} #wpcontent { margin-left:0; height: inherit; padding: 0 10px;} html.wp-toolbar {padding-top:0;} #msp-main-wrapper {margin:0;display:block;}  #wpwrap{ display: table-cell; min-height: 0; vertical-align: middle; } .mpsl-wrapper{ margin: 0; }  #wpbody { padding: 0 !important;} .auto-fold #wpcontent, .auto-fold #wpfooter { margin-left: 0px;} body{ min-width: inherit;} #setting-error-tgmpa{display:none;!important;}</style>';

	    // tmp
	    ?><script type="text/javascript">
			window._mpslResizePreview = function() {
				jQuery(window).trigger('resize');
			};
	    </script><?php
    }

}