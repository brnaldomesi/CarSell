<?php
/**
 * Description of MPCERequirements
 *
 */
class MPCERequirements {
    private $loadedExtensions;
    private $curl;
    private $gd;
    private $fileinfo;
    private $exif;
    private $imagick;
    private $gmagick;

/*
    public static $jQueryUIComponents = array(
        'jquery-ui-core',
        'jquery-ui-widget',
        'jquery-ui-mouse',
        'jquery-ui-position',
        'jquery-ui-draggable',
        'jquery-ui-droppable',
        'jquery-ui-resizable',
        'jquery-ui-button',
        'jquery-ui-dialog'
    );
*/

    const MIN_JQUERY_VER = '1.8.3';
    const MIN_JQUERYUI_VER = '1.9.2';

    public function __construct() {
        @ini_set('magic_quotes_gpc', 0);
        @ini_set('magic_quotes_runtime', 0);
        @ini_set('magic_quotes_sybase', 0);
        @ini_set('allow_url_fopen', 1);

        $this->loadedExtensions = get_loaded_extensions();
        $this->curl = $this->isCurlInstalled();
        $this->gd = $this->isGdInstalled();
        $this->fileinfo = $this->isFileinfoInstalled();
        $this->exif = $this->isExifInstalled();
        $this->imagick = $this->isImagickInstalled();
        $this->gmagick = $this->isGmagickInstalled();
    }

    private function isCurlInstalled() {
        return (in_array('curl', $this->loadedExtensions) && function_exists('curl_init')) ? true : false;
    }
    public function getCurl() {
        return $this->curl;
    }

    private function isGdInstalled() {
        return (in_array('gd', $this->loadedExtensions) && function_exists('gd_info')) ? true : false;
    }
    public function getGd() {
        return $this->gd;
    }

    private function isFileinfoInstalled() {
        return (in_array('fileinfo', $this->loadedExtensions) && class_exists('finfo')) ? true : false;
    }
    public function getFileinfo() {
        return $this->fileinfo;
    }

    private function isExifInstalled() {
        return (in_array('exif', $this->loadedExtensions) && function_exists('exif_imagetype')) ? true : false;
    }
    public function getExif() {
        return $this->exif;
    }

    private function isImagickInstalled() {
        return (in_array('imagick', $this->loadedExtensions) && class_exists('Imagick')) ? true : false;
    }
    public function getImagick() {
        return $this->imagick;
    }

    private function isGmagickInstalled() {
        return (in_array('gmagick', $this->loadedExtensions) && class_exists('Gmagick')) ? true : false;
    }
    public function getGmagick() {
        return $this->gmagick;
    }
}