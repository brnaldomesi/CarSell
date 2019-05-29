<?php
require_once dirname(__FILE__) . '/List.php';

class MPSLSlidesList extends MPSLList {
    private $sliderId;

    public function __construct($id) {
        parent::__construct();
        $this->sliderId = $id;
    }

    public function getSliderId(){
        return $this->sliderId;
    }

    public function render() {
        global $mpsl_settings;
        $slides = self::getList($this->sliderId);

        if ($slides !== false) {
            include $this->pluginDir . 'views/slides.php';

        } else {
            // TODO: Throw error
            _e('Record not found', 'motopress-slider-lite');
        }
    }

    public function getOptions() {}
    public function getAttributes() {
        return array(
            'slider_id' => $this->getSliderId(),
        );
    }

    public static function getList($sliderId) {

        $db = MPSliderDB::getInstance();

        // TODO: Message
        if (!$db->isSliderExists($sliderId)) return false;

        $slider = $db->isSliderExists($sliderId);
        if (is_null($slider)) return false;

        $slides = $db->getSlidesBySlider($sliderId, array('options'));

        foreach ($slides as &$slide) {
            if ($slide['options']) {
                $slide['title'] = (isset($slide['options']['title'])) ? $slide['options']['title'] : false;
            }
        }

        return $slides;
    }

}