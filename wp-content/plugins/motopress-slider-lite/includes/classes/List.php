<?php
abstract class MPSLList{
    const SLIDERS_TABLE = 'mpsl_sliders';
    const SLIDES_TABLE = 'mpsl_slides';

    protected $mpsl_settings;
    protected $pluginDir;

    function __construct(){
        global $mpsl_settings;
        $this->mpsl_settings = &$mpsl_settings;
        $this->pluginDir = $mpsl_settings['plugin_dir_path'];
    }
    abstract public function render();
    abstract public function getOptions();
}