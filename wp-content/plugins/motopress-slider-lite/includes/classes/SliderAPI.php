<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('MPSlider')) {
    class MPSlider {
        private $mpsl_settings;
        private $db;

        public function __construct() {
            global $mpsl_settings;
            $this->mpsl_settings = &$mpsl_settings;
            $this->db = MPSliderDB::getInstance();
        }

        public function enqueueScriptsStyles() {
            mpsl_enqueue_core_scripts_styles();
        }

        public function getSliderList($fields = null, $keyField = null) {
            return $this->db->getSliderList($fields, $keyField);
        }

    }
}

if (!array_key_exists('mpSlider', $GLOBALS)) {
    $GLOBALS['mpSlider'] = new MPSlider();
}