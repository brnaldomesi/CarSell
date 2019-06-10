<?php

include_once dirname(__FILE__) . '/MainOptions.php';

class MPSLSliderOptions extends MPSLMainOptions {
    private $title = null;
    private $alias = null;
    private $preview = false;
    private $slidePreview = false;
    private $previewSlideId = null;
    private $edit = false;
    private $templateId = null;
    private $sliderType = 'custom';
//    private $previewOptions = null;

    /**
     * @param mixed (int|array) $id
     * @param mixed (bool|int) $preview Slide ID for preview or FALSE for default functionality
     */
    function __construct($id = null, $preview = false, $slidePreview = false, $edit = false) {
        parent::__construct();

        $this->preview = $preview;
        $this->edit = $edit;
        $this->slidePreview = $slidePreview;

	    $options = $this->load($id);

        $this->options = include($this->getSettingsPath());
        $this->prepareOptions($this->options);

	    $this->prepare($options);
    }

	/**
     * @param mixed int|array $options
	 * @return mixed array|bool
     */
    protected function load($options) {
        $sliderType = isset($_REQUEST['slider_type']) ? $_REQUEST['slider_type'] : self::DEFAULT_SLIDER_TYPE;
	    $_options = false;

	    if (!is_null($options)) {
		    if (is_int($options) || is_string($options)) {
			    global $wpdb;
			    $getBy = is_int($options) ? 'id' : 'alias';

			    $sliderRow = $wpdb->get_row(sprintf(
				    "SELECT * FROM %s WHERE {$getBy} = " . ($getBy === 'id' ? '%d' : '\'%s\''),
				    $wpdb->prefix . ($this->preview && !$this->edit ? parent::SLIDERS_PREVIEW_TABLE : parent::SLIDERS_TABLE),
				    $getBy === 'id' ? (int)$options : (string)$options
			    ), ARRAY_A);

			    if (!is_null($sliderRow)) {
				    $this->setId((int) $sliderRow['id']);
				    $_options = json_decode($sliderRow['options'], true);
			    }

		    } elseif (is_array($options)) {
				$_options = $options['grouped'] ? $this->ungroupOptions($options['options']) : $options['options'];
		    }

		    $sliderType = isset($_options['slider_type']) ? $_options['slider_type'] : self::DEFAULT_SLIDER_TYPE;
	    }

	    $this->setSliderType($sliderType);

	    return $_options;
    }

	protected function prepare($options) {
		$this->overrideOptions($options, false);

		if (!$options) return false;

        if ($this->sliderType !== self::DEFAULT_SLIDER_TYPE) {
            $db = MPSliderDB::getInstance();
            $slides = $db->getSlidesBySlider($this->id);

            if (count($slides)) {
                $this->setTemplateId($slides[0]['id']);
            }
        }
	}

	/*
    public function loadByAlias($alias) {
        global $wpdb;

        $result = $wpdb->get_row(sprintf(
            'SELECT * FROM %s WHERE alias LIKE \'%s\'',
            $wpdb->prefix . ($this->preview ? parent::SLIDERS_PREVIEW_TABLE : parent::SLIDERS_TABLE),
            $alias
        ), ARRAY_A);

        if (is_null($result)) return false;

        $this->id = (int) $result['id'];
        $this->title = $result['title'];
        $this->alias = $result['alias'];

//        $options = is_null($this->previewOptions) ? json_decode($result['options'], true) : json_decode($this->previewOptions, true);
        $options = json_decode($result['options'], true);
        $this->overrideOptions($options, false);

        return true;
    }
	*/

    public function setOptions($options) {}

	public function setPreviewSlideId($id) {
        $this->previewSlideId = $id;
    }

    public function update() {
        global $wpdb;

        // Define query data
        $qTable = $wpdb->prefix . ($this->preview ? parent::SLIDERS_PREVIEW_TABLE : parent::SLIDERS_TABLE);
        $qData = array(
            'title' => $this->getTitle(),
            'alias' => $this->getAlias(),
            'options' => json_encode_slashed($this->getOptionValues())
        );
        $qFormats = array('%s', '%s', '%s');

        // Exec query
//        return $wpdb->update($qTable, $qData, array('id' => $this->getId()), $qFormats);

        if ($this->preview) {
            $sliderInsertResult = false;
            $truncateResult = $wpdb->query(sprintf('TRUNCATE TABLE %s', $qTable));
            if ($truncateResult !== false) {
                $qData['id'] = $this->getId();
                $sliderInsertResult = $wpdb->insert($qTable, $qData);
            }
            return $sliderInsertResult;

        } else {
            return $wpdb->update($qTable, $qData, array('id' => $this->getId()), $qFormats);
        }
    }

    private function createTemplate() {
        $slide = new MPSLSlideOptions();
        $slide->create($this->id);
        $this->setTemplateId($slide->getId());
        return $slide->getId();
    }

    public function getTitle() {
//        return $this->options['main']['options']['title']['value'];
        return $this->title;
    }
    public function setTitle($title) {
        $this->title = $title;
        $this->updateOption('main', 'title', $title);
    }

    public function getAlias() {
//        return $this->options['main']['options']['alias']['value'];
        return $this->alias;
    }
    public function setAlias($alias) {
        $this->alias = $alias;
        $this->updateOption('main', 'alias', $alias);
    }
    public function getAttributes() {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'alias' => $this->alias,
        );
    }
    public function getFullSliderData($slideId = null, $isEditor = false) {
        $slidesData = array();
        if (is_null($slideId)){
            $slides = $this->getSlides();
        } else {
            $slide = $this->getSlide($slideId);
            $slides = array($slide);
        }
        $counter = 1;
        foreach( (array) $slides as $key => $slide) {
	        if ($isEditor) {
		        $slideObj = MPSLSlideOptions::getInstance((int) $slide['id']);
	        } else {
		        $slideObj = new MPSLSlideOptions((int) $slide['id'], ($this->slidePreview && $this->previewSlideId == $slide['id']));
	        }
            $slideData['options'] = $slideObj->getOptionValues();

	        $slideVisible = $isEditor || $slideObj->isSliderVisible();
	        if (!$slideVisible && !($this->slidePreview && $this->previewSlideId == $slide['id'])) continue;

            $slideData['layers'] = $slideObj->getLayers();

            $slideData['active'] = ($this->slidePreview && $this->previewSlideId == $slide['id']) || (!$this->slidePreview && $this->options['appearance']['options']['start_slide']['value'] == $counter);

            $slidesData[] = $slideData;
            $counter++;
        }
        $fullSliderData = array(
            'options' => $this->getOptionValues(),
            'slides' => $slidesData
        );
        return $fullSliderData;
    }

    public function getExportSliderData(&$internalResources){
        $slidesData = array();        
        $slides = $this->getSlides();
	    $presetClasses = array();
        foreach( (array) $slides as $slide) {
            $slideObj = new MPSLSlideOptions((int) $slide['id']);
            $slideData['options'] = $slideObj->getOptionValuesForExport($internalResources);
            $slideData['layers'] = $slideObj->getLayersForExport($internalResources);
            $slidesData[] = $slideData;

	        $presetClasses = array_merge($presetClasses, $slideObj->getUsedPresetClasses());
        }
        $exportSliderData = array(
            'options' => $this->getOptionValues(),
            'slides' => $slidesData
        );        
        return array(
	        'slider' => $exportSliderData,
	        'preset_classes' => array_unique($presetClasses)
        );
    }

    public function overrideOptions($options = false, $isGrouped = true) {
        parent::overrideOptions($options, $isGrouped);

        if ($isGrouped) {
            if (isset($options['main']['title'])) {
                $this->setTitle($options['main']['title']);
            }
            if (isset($options['main']['alias'])) {
                $this->setAlias($options['main']['alias']);
            }

        } else {
            if (isset($options['title'])) {
                $this->setTitle($options['title']);
            }
            if (isset($options['alias'])) {
                $this->setAlias($options['alias']);
            }
        }
    }


    public function create($createTemplate = true) {
        global $wpdb;

        // TODO: Flash messages
//        if (!isset($this->options['title'])) return false;
//        if (!isset($this->options['alias'])) return false;

        // Update options with new data
//        $this->overrideOptions($options, true);

        // Define query data
        $qTable = $wpdb->prefix . self::SLIDERS_TABLE;
        $qData = array(
            'title' => $this->getTitle(),
            'alias' => $this->getAlias(),
            'options' => json_encode_slashed($this->getOptionValues())
        );
        $qFormats = array('%s', '%s', '%s');
        $result = $wpdb->insert($qTable, $qData, $qFormats);

        if (false !== $result) {
            $id = $wpdb->insert_id;
	        $this->setId($id);
	        if ($createTemplate && $this->sliderType !== self::DEFAULT_SLIDER_TYPE) {
		        $this->createTemplate();
	        }
            return $id;

        } else {
            return false;
        }
    }

    public function isNotValidOptions(){
        $errors = array();
        if (is_array($this->options)) {
            foreach($this->options as $groupName => $group) {
                if (is_array($group['options'])) {
                    foreach($group['options'] as $optionName => $option) {
                        $error = $this->isNotValidOption($option);
                        if ($error) {
                            $errors[] = $error;
                        }
                    }
                }
            }
        }
        if (!empty($errors)) {
            return $errors;
        } else {
            return false;
        }
    }

    public function getSlides($sliderId = null, $decodeFields = array()) {
        $db = MPSliderDB::getInstance();
        $slides = $db->getSlidesBySlider(is_null($sliderId) ? $this->getId() : $sliderId, $decodeFields);
//        foreach ($slides as &$slide) {
//            $options = json_decode($slide['options'], true);
//            if ($options) {
//                $slide['title'] = (isset($options['title'])) ? $options['title'] : false;
//            }
//        }
        return $slides;
    }

    public function getSlide($id) {
        global $wpdb, $mpsl_settings;
        $query = sprintf(
            'SELECT * FROM %s WHERE id=%d ORDER BY slide_order ASC',
            $mpsl_settings['slides_table'],
            $id
        );
        $slide = $wpdb->get_row($query, ARRAY_A);
        return $slide;
    }

    public function isNotValidOption($option){
        if (empty($option)) {
            return __('Empty option ', 'motopress-slider-lite') . $option['label'];
        }
        return false;
    }

    public function getSliderEditUrl(){
        global $mpsl_settings;
        $menuUrl = menu_page_url($mpsl_settings['plugin_name'], false);
        $sliderEditUrl = add_query_arg(array('view' => 'slider','id' => $this->getId()), $menuUrl);
        return $sliderEditUrl;
    }

    public function delete() {
        global $wpdb;
        $resultSlides = $wpdb->delete($wpdb->prefix . self::SLIDES_TABLE, array('slider_id' => $this->getId()));
        $resultSlider = $wpdb->delete($wpdb->prefix . self::SLIDERS_TABLE, array('id' => $this->getId()));

	    $layerPresetsObj = MPSLLayerPresetOptions::getInstance();
	    $layerPresetsObj->updatePrivateStyles();

        // Note that since both 0 and FALSE may be returned $wpdb->query
        // http://php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
        return ($resultSlides !== false) && ($resultSlider !== false);
    }

    public function duplicate() {
	    $result = array('slide' => false, 'slideId' => null, 'slides' => false);

        $oldAlias = $this->getAlias();
        $oldTitle = $this->getTitle();
        $newTitle = 'Duplicate of ' . $oldTitle;
        $uniqueAlias = $this->generateUniqueAlias();
        $this->setAlias($uniqueAlias);
        $this->setTitle($newTitle);
        $oldId = $this->getId();
        $newId = $this->create(false);

        if (false !== $newId) {
			/*global $wpdb;
            $selectQuery = sprintf("SELECT %d, slide_order, options, layers FROM %s WHERE slider_id = %d", $newId, $wpdb->prefix . self::SLIDES_TABLE, $oldId);
            $query = sprintf('INSERT INTO %s (slider_id, slide_order, options, layers) (' . $selectQuery . ')', $wpdb->prefix . self::SLIDES_TABLE);
            $wpdb->hide_errors();
            $res = $wpdb->query($query);*/

	        $result['slide'] = true;
	        $result['slide_id'] = $newId;

	        global $wpdb;
	        $wpdb->hide_errors();
	        $slides = $this->getSlides($oldId); /** @todo: Create and use another function (select only IDs) */
	        $slidesRes = true;

	        foreach ($slides as $slide) {
		        $slideObj = new MPSLSlideOptions($slide['id']);
		        $slideDuplicateRes = $slideObj->duplicateSlide($slide['id'], $newId);
		        $slidesRes = $slidesRes && ($slideDuplicateRes !== false);
	        }
	        $result['slides'] = $slidesRes;
        }

	    return $result;
    }

    public function generateUniqueAlias($prefix = 'slider'){
        $uniqueAlias = uniqid($prefix);
        if ($this->isAliasExists($uniqueAlias)) {
            return $this->generateUniqueAlias($prefix);
        } else {
            return $uniqueAlias;
        }
    }
    
    public function makeAliasUnique(){                
        $alias = $this->alias;
        if ( !$this->isAliasValid($alias) ) {
            $alias = 'slider';
        }
        if ($this->isAliasExists($alias)) {
            $alias = $this->generateUniqueAlias($alias);
        }
        $this->setAlias($alias);                
    }

    public function setUniqueAliasIfEmpty(){
        if (is_null($this->getAlias())){
            $this->setAlias($this->generateUniqueAlias());
        }
    }

    public function render(){
        global $mpsl_settings;
        if (!is_plugin_active('woocommerce/woocommerce.php') && $this->sliderType === 'woocommerce') {
            include($this->pluginDir . 'views/woocommerce-not-found.php');
        } else {
            $this->setUniqueAliasIfEmpty();
            include($this->getViewPath());
        }
    }

    public function isAliasValid(){
        $aliasPattern = "/^[-_a-zA-Z0-9]+$/";
        return preg_match($aliasPattern, $this->alias);
    }

    public function isTitleValid(){
        return !empty($this->title);
    }

    public static function isAliasExists($alias){
        global $wpdb;
        return !is_null($wpdb->get_row(sprintf('SELECT * FROM %s WHERE alias LIKE \'%s\'', $wpdb->prefix . parent::SLIDERS_TABLE, $alias)));
    }

    public static function getAliasById($id){
        global $wpdb;
        $result = $wpdb->get_row(sprintf('SELECT alias FROM %s WHERE id = %d', $wpdb->prefix . parent::SLIDERS_TABLE, $id), ARRAY_A);
        if (!is_null($result)) {
            return $result['alias'];
        } else{
            return null;
        }
    }

    public function isValidOptions(){
        if (!$this->isAliasValid())
            return false;
        if (!$this->isTitleValid())
            return false;
        foreach($this->options as $groupKey => $group){
            foreach($group['options'] as $optionName => $option) {
                if (isset($option['required']) && $option['required']) {
                    if (empty($option['value'])) {
                        return false;
                    }
                }
                if (isset($option['pattern']) && !preg_match($option['pattern'],$option['value'])) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function isIdExists($id){
        global $wpdb;
        return !is_null($wpdb->get_row(sprintf('SELECT * FROM %s WHERE id = %d ', $wpdb->prefix . parent::SLIDERS_TABLE, $id)));
    }

	protected function getSettingsFileName() {
		return 'slider';
	}

	protected function getViewFileName() {
		return 'slider';
	}


    public function getTemplateId() {
        return $this->templateId;
    }

    public function setTemplateId($id) {
        $this->templateId = $id;
    }


    public function setSliderType($type) {
        $this->sliderType = $type;
    }

    public function getSliderType() {
        return $this->sliderType;
    }

    public function getTaxonomyName($postTypeName) {

        $taxs = get_object_taxonomies($postTypeName, 'objects');

        if(post_type_supports($postTypeName, 'post-formats') && $postTypeName !== 'post'){
            $postFormat = get_object_taxonomies('post', 'objects');
            $taxs['post_format'] = $postFormat['post_format'];
        }

        $result = array();
        foreach ($taxs as $taxName => $tax) {
            if (($tax->hierarchical) || (!$tax->hierarchical))
                $result[$taxName] = $tax->label;
        }
        return $result;
    }


    public function getTaxTerms($taxs, $postTypeName, $type) {
        $result = array();

        foreach ($taxs as $tax_name => $tax_label) {
            $args = array();
            if ($type === 'format') $args['hide_empty'] = false;

            $tax_terms = get_terms($tax_name, $args);
            foreach ($tax_terms as $tax_term) {
                if (is_object($tax_term)) {
                    $tax_term = get_object_vars($tax_term);
                }

	            $arr = array();
	            if ($type === 'categories') {
		            $arr = array('category', 'product_cat');
	            } elseif ($type === 'tags') {
		            $arr = array('post_tag', 'product_tag');
	            } elseif ($type === 'format') {
		            $arr = array('post_format');
	            }

                if (in_array($tax_term['taxonomy'], $arr)) {
                    $result[] = array(
                        'value' => $tax_term['term_id'],
                        'label' => $tax_term['name']
                    );
                }
            }
        }

//        if (in_array($postTypeName, array('post', 'product'))) {
            array_unshift($result, array('value' => 0, 'label' => 'All ' . $type));
//        }
        return $result;
    }

	/**
	 * Get layouts structure
	 * @return array
	 */
	public function getLayouts() {
		$layouts = array(
			// Add main (default) layout
			'desktop' => array(
				'label' => __('Desktop', 'motopress-slider-lite'),
				'depend_on' => 'desktop',
				'enabled' => true, // Desktop. Desktop never changes...
				'width' => (int) $this->getOptionAttr('main', 'width', 'value'), // But width...
				'height' => (int) $this->getOptionAttr('main', 'height', 'value') // ... and height do
			)
			// 'notebook' => array(...),
			// 'tablet' => array(...),
			// 'mobile' => array(...)
		);

		$secondaryLayouts = array(
			'notebook' => array(
				'label' => __('Laptop', 'motopress-slider-lite'),
				'depend_on' => 'desktop'
			),
			'tablet' => array(
				'label' => __('Tablet', 'motopress-slider-lite'),
				'depend_on' => 'notebook'
			),
			'mobile' => array(
				'label' => __('Mobile', 'motopress-slider-lite'),
				'depend_on' => 'tablet'
			)
		);

		foreach ($secondaryLayouts as $layoutName => $params) {
			$dependOn = $params['depend_on'];

			// Add new layout
			$layouts[$layoutName] = array(
				'label' => $params['label'],
				'depend_on' => $dependOn,
				'enabled' => (bool) $this->getOptionAttr('size', 'enable_' . $layoutName, 'value'),
				'width' => (int) $this->getOptionAttr('size', $layoutName . '_width', 'value'),
				'height' => (int) $this->getOptionAttr('size', $layoutName . '_height', 'value')
			);

			// Depend on enabled layouts only
			if (!$layouts[$dependOn]['enabled']) {
				$layouts[$layoutName]['depend_on'] = $layouts[$dependOn]['depend_on'];
			}
		}

		return $layouts;
	}

	/**
	 * @param array $layouts Layouts structure
	 * @return array
	 */
	public static function filterLayoutsByEnabled($layouts = array()) {
		return array_filter($layouts, function($layout) {
            return $layout['enabled'];
        });

	}

}
