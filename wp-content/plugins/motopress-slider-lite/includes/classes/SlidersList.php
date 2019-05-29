<?php
require_once dirname(__FILE__) . '/List.php';

class MPSLSlidersList extends MPSLList{
    public function __construct(){
        parent::__construct();
    }
    public function render(){
        global $mpsl_settings;
        $sliders = $this->getSlidersList();
        include $this->pluginDir . 'views/sliders.php';
    }
    public function getOptions() {}

    public function getSliderAliases(){
        global $wpdb, $mpsl_settings;

        $result = $wpdb->get_results(sprintf(
            'SELECT id, alias, title FROM %s ORDER BY id ASC',
            $this->mpsl_settings['sliders_table']
        ), ARRAY_A);

        foreach ($result as &$value) {
            $value['shortcode'] = '[' . $mpsl_settings['shortcode_name'] . ' ' . $value['alias'] . ']';
        }

        return $result;
    }
    public function getSlidersList(){
        global $wpdb;

        $result = $wpdb->get_results(sprintf(
            'SELECT * FROM %s ORDER BY id ASC',
            $this->mpsl_settings['sliders_table']
        ), ARRAY_A);

        foreach ($result as &$slider) {
            $slider['options'] = json_decode($slider['options'], true);
            $slider['options']['slider_type'] = isset($slider['options']['slider_type']) ? $slider['options']['slider_type'] : 'custom';
        }
        return $result;
    }
    public function getRowHtml($id){
        $slider = $this->getSliderData($id);
        ob_start();
        include $this->pluginDir . 'views/slider_row.php';
        return ob_get_clean();
    }
    public function getSliderData($id) {
        global $wpdb;

        $result = $wpdb->get_row(sprintf(
            'SELECT * FROM %s WHERE id = %d',
            $this->mpsl_settings['sliders_table'],
            $id
        ), ARRAY_A);

        $result['options'] = json_decode($result['options'], true);
        $result['options']['slider_type'] = isset($result['options']['slider_type']) ? $result['options']['slider_type'] : 'custom';

        return $result;
    }
    public function getSliderCreateUrl(){
        global $mpsl_settings;
        $menu_url = menu_page_url($mpsl_settings['plugin_name'], false);
        return add_query_arg(array('view' => 'slider'), $menu_url);
    }
    public function getSlidersExportUrl(){
        global $mpsl_settings;
        $menu_url = menu_page_url($mpsl_settings['plugin_name'], false);
        return add_query_arg(array('view' => 'export'), $menu_url);
    }

    public function getTemplateId($id) {
        $db = MPSliderDB::getInstance();
        $slides = $db->getSlidesBySlider($id);
        return count($slides) ? $slides[0]['id'] : false;
    }
}