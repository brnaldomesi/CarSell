<?php

require_once dirname(__FILE__) . '/MainOptions.php';
require_once dirname(__FILE__) . '/LayerPresetOptions.php';

class MPSLSlideOptions extends MPSLMainOptions {
	const LAYER_WHITE_SPACE_CLASS_PREFIX = 'mpsl-white-space-';

    private $sliderId = null;
    private $sliderAlias = null;
    private $slideOrder = null;
    private $layers = array();
    private $layerOptions = null;
    private $preview = false;
    private $edit = false;
	/** @var MPSLSliderOptions $slider */
    public $slider = null;
	private $_slideRow = null;
//    private $layerStyleOptions = null;
	/** @var MPSLLayerPresetOptions $layerPresets */
    public $layerPresets = null;
	private static $instance = null;

	/**
	 * @var (int) $id - Slide ID
	 * @var (boolean) $preview - Preview flag
	 * @var (boolean) $edit - Edit or View slide flag (use with $preview = true)
	 */
    function __construct($id = null, $preview = false, $edit = false) {
        parent::__construct();

        $this->preview = $preview;
        $this->edit = $edit;

	    $options = $this->load($id);

        $this->options = include($this->getSettingsPath());
        $this->prepareOptions($this->options);

        $this->layerOptions = include($this->getSettingsPath('layer'));
        $this->prepareOptions($this->layerOptions);

	    $this->layerPresets = MPSLLayerPresetOptions::getInstance($this->preview);

	    $this->prepare($options);
    }

	public static function getInstance($id = null, $preview = false, $edit = false) {
		if (null === self::$instance) {
			self::$instance = new self($id, $preview, $edit);
		}
		return self::$instance;
	}

    protected function load($id) {
		$options = false;

	    if (!is_null($id)) {
		    if (is_null($this->_slideRow)) {

			    global $wpdb;
			    $this->_slideRow = $wpdb->get_row(sprintf(
				    'SELECT * FROM %s WHERE id = %d',
				    $wpdb->prefix . ($this->preview && !$this->edit ? parent::SLIDES_PREVIEW_TABLE : parent::SLIDES_TABLE),
				    (int)$id
			    ), ARRAY_A);
		    }

		    if (!is_null($this->_slideRow)) {
//			    if (is_null($result)) return false;

			    $this->id = (int)$id;
			    $this->sliderId = (int)$this->_slideRow['slider_id'];
			    $this->sliderAlias = MPSLSliderOptions::getAliasById($this->sliderId);
			    $this->slideOrder = (int)$this->_slideRow['slide_order'];

//			    $this->overrideOptions(json_decode($result['options'], true), false);
//			    $this->overrideLayers(json_decode($result['layers'], true));
			    $options = array(
				    'slide' => json_decode($this->_slideRow['options'], true),
				    'layers' => json_decode($this->_slideRow['layers'], true)
			    );

			    $this->loadSlider();
		    }
	    }

        return $options;
    }

	protected function prepare($options) {
		if ($options) {
			$this->overrideOptions($options['slide'], false);
			$this->overrideLayers($options['layers']);

		} else {
			$this->overrideOptions(false, false);
		}
	}

	protected function prepareOption($name, $option) {
		$option = parent::prepareOption($name, $option);

		if (MPSLLayout::isLayoutDependent($option)) {
			$option['default'] = MPSLLayout::makeLayouted($option['default']);
		}

		return $option;
	}

    public function overrideLayers($layers = null) {
        $defaults = $this->getDefaults($this->layerOptions);

	    if (!empty($layers)) {
            foreach($layers as $layerKey => $layer) {

	            // Convert single layout option to multiple (layouted)
	            foreach (MPSLLayout::$OPTIONS as $layoutOpt) {
					if (array_key_exists($layoutOpt, $layers[$layerKey])) {
						if (!MPSLLayout::isLayoutedOption($layers[$layerKey][$layoutOpt])) {
							$layers[$layerKey][$layoutOpt] = MPSLLayout::makeLayouted($layers[$layerKey][$layoutOpt]);

						} elseif (!MPSLLayout::isFullyLayoutedOption($layers[$layerKey][$layoutOpt])) {
							$layers[$layerKey][$layoutOpt] = MPSLLayout::makeFullyLayouted($layers[$layerKey][$layoutOpt]);
						}
					}
			    }

//                $layers[$layerKey] = array_merge($defaults, $layer);
                $layers[$layerKey] = array_replace_recursive($defaults, $layers[$layerKey]);

	            $layers[$layerKey]['private_styles'] = $this->layerPresets->override($layers[$layerKey]['private_styles'], true);
	            if ($layers[$layerKey]['preset'] === 'private' && !$layers[$layerKey]['private_preset_class']) {
		            $this->layerPresets->incLastPrivatePresetId();
		            $layers[$layerKey]['private_preset_class'] = $this->layerPresets->getLastPrivatePresetClass();
	            }

                // update attached image url
                if (isset($layers[$layerKey]['image_id']) && !empty($layers[$layerKey]['image_id'])) {
                    $image_url = wp_get_attachment_url($layers[$layerKey]['image_id']);
                    if (false === $image_url) {
                        $image_url = '?';
                    }
                    $layers[$layerKey]['image_url'] = $image_url;
                }

            }
        }

        $this->layers = $layers;
    }

    public function overrideOptions($options = false, $isGrouped = true) {
        if (isset($options['bg_image_id']) && !empty($options['bg_image_id'])) {
            $image_url = wp_get_attachment_url($options['bg_image_id']);
            if (false === $image_url) {
                $image_url = '?';
            }
            $options['bg_internal_image_url'] = $image_url;
        }
        parent::overrideOptions($options, $isGrouped);
    }

    public function create($sliderId = null, $silent = false) {
        global $wpdb;

        // Update options with new data
        $this->overrideOptions();

        // Define query data
        $qTable = $wpdb->prefix . self::SLIDES_TABLE;

        $order = $this->getNextOrder($sliderId);

        $qData = array(
            'slider_id' => $sliderId,
            'slide_order' => $order,
            'options' => json_encode_slashed($this->getOptionValues()),
            'layers' => json_encode_slashed(array())
        );
        $qFormats = array('%d', '%d', '%s', '%s');

        // Exec query
        $wpdb->hide_errors();
        $result = $wpdb->insert($qTable, $qData, $qFormats);

        if ($result !== false) {
	        $id = ($result) ? $wpdb->insert_id : null;
            $this->id = (int) $id;

	        if (!$silent) {
		        $this->_slideRow = $qData;
		        $this->_slideRow['id'] = $this->id;
		        self::__construct($id);
	        }

	        $this->setGeneratedByIdTitle();
            $this->update();

            return $this->id;

        } else {
            return false;
        }


    }

    public function prepareLayersForImport(&$layers, $presetClasses = array()) {
	    $presetsExists = count($presetClasses);

		foreach ($layers as &$layer) {
			// Update preset class
			if ($presetsExists && isset($layer['preset']) && $layer['preset']) {
				if (array_key_exists($layer['preset'], $presetClasses)) {
					$layer['preset'] = $presetClasses[$layer['preset']];
				}
			}

			// Private preset
			$this->regenerateLayerPrivatePreset($layer);
		}
    }

    public function import($sliderId) {
        global $wpdb;
        $qTable = $wpdb->prefix . self::SLIDES_TABLE;
        $order = $this->getNextOrder($sliderId);
        $qData = array(
            'slider_id' => $sliderId,
            'slide_order' => $order,
            'options' => json_encode_slashed($this->getOptionValues()),
            'layers' => json_encode_slashed($this->layers)
        );
        $qFormats = array('%d', '%d', '%s', '%s');
        $wpdb->hide_errors();
        $this->setId(null);
        $result = $wpdb->insert($qTable, $qData, $qFormats);
        $id = ($result) ? $wpdb->insert_id : null;
        $this->id = (int) $id;
        return $id;
    }

    public function getNextOrder($sliderId){
        global $wpdb;
        $qTable = $wpdb->prefix . self::SLIDES_TABLE;
        $order = $wpdb->get_var(sprintf(
            "SELECT MAX(slide_order) FROM %s WHERE slider_id=%d",
            $qTable, $sliderId
        ));
        return is_null($order) ? 1 : $order + 1;
    }

    public function update() {
        global $wpdb;

//	    update_option(MPSLLayerPresetOptions::LAST_PRIVATE_PRESET_ID_OPT, $this->layerPresets->getLastPrivatePresetId());

	    $options = $this->getOptionValues();
//	    $presets = $this->layerPresets->getPresets();
	    $presets = $this->layerPresets->getAllPresets();
	    $fonts = array();

	    foreach ($this->layers as &$layer) {
//		    $hoverStyles = array();

		    // Get used fonts
		    if ($presetClass = $layer['preset']) {

			    if ($presetClass === 'private') {
					$styles = $layer['private_styles'];

			    } else if (isset($presets[$presetClass])) {
				    $styles = $presets[$presetClass];
			    }

			    if (isset($styles)) {
				    // Save fonts
				    $fonts = array_merge_recursive($fonts, $this->layerPresets->getFontsByPreset($styles));

				    // Save hover styles
//				    $hoverStyles = $this->layerPresets->getHoverStylesByPreset($styles);
			    }

		    }

//		    $layer['hover_styles'] = $hoverStyles;
		    $layer['private_styles'] = $this->layerPresets->clearPreset($layer['private_styles']);
		    $layer['private_styles'] = $this->layerPresets->clearLayoutOptions($layer['private_styles']);
	    }

        $this->clearLayerOptions();
        $this->processLayerLayoutOptions();

	    // Set used fonts
	    $options['fonts'] = MPSLLayerPresetOptions::fontsUnique($fonts);

        // Define query data
        $qTable =  $wpdb->prefix . ($this->preview ? self::SLIDES_PREVIEW_TABLE : self::SLIDES_TABLE);
        $qData = array(
            'options' => json_encode_slashed($options),
            'layers' => json_encode_slashed($this->layers)
        );
        $qFormats = array('%s', '%s');

        // Exec query
//        if (is_null($id)) {
//            $result = $wpdb->insert($qTable, $qData, $qFormats);
//            $id = ($result) ? $wpdb->insert_id : null;
//        } else {
            $wpdb->hide_errors();

            if ($this->preview) {
	            $slideInsertResult = false;
                $truncateResult = $wpdb->query(sprintf('TRUNCATE TABLE %s', $qTable));
				if ($truncateResult !== false) {
					$qData['id'] = $this->id;
					$qData['slider_id'] = $this->sliderId;
					$qData['slide_order'] = $this->slideOrder;
					$slideInsertResult = $wpdb->insert($qTable, $qData);
				}
	            return $slideInsertResult;

            } else {
                return $wpdb->update($qTable, $qData, array('id' => $this->id), $qFormats);
            }

//        }
    }

    /** @todo: Test and fix */
    private function clearLayerOptions() {
        foreach ($this->layerOptions as &$group) {
            foreach ($group['options'] as $optKey => $option) {
                $skip = isset($option['skip']) && $option['skip'];
                $skipChild = isset($option['skipChild']) && $option['skipChild'];
                if ($skip || $skipChild) {
                    $optsToSkip = $skipChild ? array_keys($option['options']) : array();
                    foreach ($this->layers as &$layer) {
                        if ($skip && array_key_exists($optKey, $layer)) {
                            unset($layer[$optKey]);
                        }
                        if ($skipChild) {
                            foreach ($optsToSkip as $optToSkip) {
                                if (array_key_exists($optToSkip, $layer)) {
                                    unset($layer[$optToSkip]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

	private function processLayerLayoutOptions() {
		foreach ($this->layers as &$layer) {
			foreach (MPSLLayout::$OPTIONS as $layoutOpt) {
				if (array_key_exists($layoutOpt, $layer) && MPSLLayout::isLayoutedOption($layer[$layoutOpt])) {
					$layer[$layoutOpt] = MPSLLayout::clearEmptyLayoutOptions($layer[$layoutOpt]);
				}
			}
		}
	}

    public function delete() {
        global $wpdb;
        $wpdb->hide_errors();
        $deleteResult = $wpdb->delete($wpdb->prefix . self::SLIDES_TABLE, array('id' => $this->id));

	    $this->layerPresets->updatePrivateStyles();

	    return $deleteResult;
    }

    public function duplicateSlide($slideId, $sliderId = null) {
        global $wpdb;
        $wpdb->hide_errors();
        $db = MPSliderDB::getInstance();

        $slide = $db->getSlide($slideId, array('slider_id', 'slide_order', 'options', 'layers'));
        if (is_null($slide)) {
            mpslSetError(__('Slide ID is not set.', 'motopress-slider-lite'));
        }
        $order = $wpdb->get_var(sprintf(
            "SELECT MAX(slide_order) FROM %s WHERE slider_id=%d",
            $wpdb->prefix . parent::SLIDES_TABLE, is_null($sliderId) ? $this->sliderId : $sliderId
        ));
        $order = is_null($order) ? 0 : $order + 1;

        if (!is_null($sliderId)) $slide['slider_id'] = $sliderId;
        $slide['slide_order'] = $order;
        $options = json_decode($slide['options'], true);
        $layers = json_decode($slide['layers'], true);

        if ($options !== false && isset($options['title'])) {
            if (is_null($sliderId)) $options['title'] = __('Duplicate of ', 'motopress-slider-lite') . $options['title'];
            $slide['options'] = json_encode_slashed($options);

	        // Prepare layers
	        if ($layers !== false) {
		        foreach ($layers as &$layer) {
			        $this->regenerateLayerPrivatePreset($layer);
		        }
		        $slide['layers'] = json_encode_slashed($layers);
	        }
        }

	    $result = $wpdb->insert($wpdb->prefix . parent::SLIDES_TABLE, $slide);

	    return $result === false ? false : $wpdb->insert_id;
    }

	private function regenerateLayerPrivatePreset(&$layer) {
        if (
	        !isset($layer['private_preset_class'])
	        || !$layer['private_preset_class']
	        || preg_match('/^' . MPSLLayerPresetOptions::PRIVATE_PRESET_PREFIX . '[0-9]+$/', $layer['private_preset_class'])
        ) {
	        $this->layerPresets->incLastPrivatePresetId();
	        $layer['private_preset_class'] = $this->layerPresets->getLastPrivatePresetClass();
        }
	}

    public function getSliderAttrs() {
        $db = MPSliderDB::getInstance();
        $slider = $db->getSlider($this->sliderId);
        $slider['options'] = json_decode($slider['options']);
        return $slider;
    }

    public function getAttributes() {
        return array(
            'id' => $this->id,
            'slider_id' => $this->sliderId,
            'slide_order' => $this->slideOrder,
        );
    }

    public function render() {
        global $mpsl_settings;
        if (!is_plugin_active('woocommerce/woocommerce.php')  && $this->slider->getSliderType() === 'woocommerce') {
            include($this->pluginDir . 'views/woocommerce-not-found.php');
        } else {
            $options = $this->getOptions(true);
            include $this->getViewPath();
        }
    }

    public function renderLayer() {
        global $mpsl_settings;
	    MPSLOptionsFactory::configPrintValue(false);
        include($this->getViewPath('layer'));
	    MPSLOptionsFactory::resetPrintValue();
    }

    public function getSliderId() {
        return $this->sliderId;
    }

    public function getSlideOrder() {
        return $this->slideOrder;
    }

    public function getLayers() {
        return $this->layers;
    }

    public function getLayersForExport(&$internalResources){
        $options = array();
        $layers = $this->layers;
        foreach ($layers as &$layer) {
            foreach ($layer as $optionName => $optionValue) {
                switch ($optionName) {
                    case 'image_id' :
                        if (!empty($optionValue)) {
                            if (!isset($internalResources[$optionValue])) {
                                $internalResources[$optionValue] = array();
                                $internalResources[$optionValue]['value'] = wp_get_attachment_url($optionValue);
                            }
                            $layer[$optionName] = array(
                                'need_update' => true,
                                'old_value' => $optionValue
                            );
                        }
                        break;

	                case 'private_styles':
		                $layer[$optionName] = $this->layerPresets->clearPreset($layer[$optionName]);
		                break;
                }
            }
        }
        return $layers;
    }

    public function setLayers($layers) {
        $this->layers = $layers;
    }

    public function getLayerOptions($grouped = false) {
	    if ($grouped) {
			return $this->layerOptions;

		} else {
			$options = array();
			foreach ($this->layerOptions as $grpName => $grp) {
				$options = array_merge($options, $grp['options']);

				foreach ($grp['options'] as $optName => $opt) {
					if (array_key_exists('options', $opt)) {
						$options = array_merge($options, $opt['options']);
					}
				}
			}

			return $options;
		}
    }

	public function isSliderVisible() {
		$optionValues = $this->getOptionValues();

		$isPublished = isset($optionValues['status']) && $optionValues['status'] === 'published';
        $isNeedLogin = isset($optionValues['need_logged_in']) && $optionValues['need_logged_in'];
        $canCurrentUserView = $isNeedLogin ? is_user_logged_in() : true;

        $isCurDateInVisiblePeriod = true;
        if (isset($optionValues['date_from']) && $optionValues['date_from'] !== '') {
            $dateFrom = strtotime($optionValues['date_from']);
            if (false !== $dateFrom && -1 !== $dateFrom && current_time('timestamp') < $dateFrom) {
                $isCurDateInVisiblePeriod = false;
            }
        }
        if (isset($optionValues['date_until']) && $optionValues['date_until'] !== '') {
            $dateUntil = strtotime($optionValues['date_until']);
            if (false !== $dateUntil && -1 !== $dateUntil && current_time('timestamp') > $dateUntil) {
                $isCurDateInVisiblePeriod = false;
            }
        }

		return ($isPublished && $canCurrentUserView && $isCurDateInVisiblePeriod);
	}

	protected function getSettingsFileName() {
		return 'slide';
	}

	protected function getViewFileName() {
		return 'slide';
	}

    public function setGeneratedByIdTitle(){
        $newTitle = $this->getTitle() . '-' . $this->id;
        $this->setTitle($newTitle);
    }

    public function getTitle(){
        return $this->options['main']['options']['title']['value'];
    }

    public function setTitle($title){
        $this->options['main']['options']['title']['value'] = $title;
    }

    public function getSiblingsSlides() {
        $db = MPSliderDB::getInstance();

        if (!$db->isSliderExists($this->sliderId)) return false;

        $slides = $db->getSiblings($this->sliderId);

        foreach ($slides as $key => $value) {

            if($value['id'] == $this->id){
                $nextEl = current(array_slice($slides, array_search($key, array_keys($slides)) + 1, 1));
                $prevEl = current(array_slice($slides, array_search($key, array_keys($slides)) - 1, 1));

                return array(
                        'next' => $nextEl ? $nextEl['id'] : $slides[0]['id'],
                        'prev' => $prevEl ? $prevEl['id'] : $value['id']
                    );
            }
        }
    }

	public function getUsedPresetClasses() {
		$classes = array();
		foreach ($this->layers as $layer) {
			if (isset($layer['preset']) && $layer['preset'] && $layer['preset'] !== 'private') {
			    $classes[] = $layer['preset'];
		    }
		}
		return array_unique($classes);
	}

    private function loadSlider() {
        if (!$this->slider) {
            $this->slider = new MPSLSliderOptions((int) $this->sliderId);
        }

        return $this->slider;
    }

	public function getSliderType() {
		return $this->slider ? $this->slider->getSliderType() : MPSLMainOptions::DEFAULT_SLIDER_TYPE;
	}

    private function getStartOptionByType($type, $isCloned = false) {
        $result = array();

        if($type === 'duration') {
            $result = array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label2' => __('duration (ms): ', 'motopress-slider-lite'),
                'default' => 1000,
                'min' => 0
            );

            if($isCloned) {
                $result['label2'] = __('Duration: ', 'motopress-slider-lite');
                $result['helpers'] = array('start_duration');
            }
        } else if ($type === 'easings'){
            $result =  array(
                'type' => 'select',
	            'layer_type' => 'all',
                'label2' => __('Easing :', 'motopress-slider-lite'),
                'default' => 'linear',
                'list' => array(
                    'linear' => __('linear', 'motopress-slider-lite'),
                    'ease' => __('ease', 'motopress-slider-lite'),
                    'easeIn' => __('easeIn', 'motopress-slider-lite'),
                    'easeInOut' => __('easeInOut', 'motopress-slider-lite'),
                    'easeInQuad' => __('easeInQuad', 'motopress-slider-lite'),
                    'easeInCubic' => __('easeInCubic', 'motopress-slider-lite'),
                    'easeInQuart' => __('easeInQuart', 'motopress-slider-lite'),
                    'easeInQuint' => __('easeInQuint', 'motopress-slider-lite'),
                    'easeInSine' => __('easeInSine', 'motopress-slider-lite'),
                    'easeInExpo' => __('easeInExpo', 'motopress-slider-lite'),
                    'easeInCirc' => __('easeInCirc', 'motopress-slider-lite'),
                    'easeInBack' => __('easeInBack', 'motopress-slider-lite'),
                    'easeOutQuad' => __('easeOutQuad', 'motopress-slider-lite'),
                    'easeOutCubic' => __('easeOutCubic', 'motopress-slider-lite'),
                    'easeOutQuart' => __('easeOutQuart', 'motopress-slider-lite'),
                    'easeOutQuint' => __('easeOutQuint', 'motopress-slider-lite'),
                    'easeOutSine' => __('easeOutSine', 'motopress-slider-lite'),
                    'easeOutExpo' => __('easeOutExpo', 'motopress-slider-lite'),
                    'easeOutCirc' => __('easeOutCirc', 'motopress-slider-lite'),
                    'easeOutBack' => __('easeOutBack', 'motopress-slider-lite'),
                    'easeInOutQuad' => __('easeInOutQuad', 'motopress-slider-lite'),
                    'easeInOutCubic' => __('easeInOutCubic', 'motopress-slider-lite'),
                    'easeInOutQuart' => __('easeInOutQuart', 'motopress-slider-lite'),
                    'easeInOutQuint' => __('easeInOutQuint', 'motopress-slider-lite'),
                    'easeInOutSine' => __('easeInOutSine', 'motopress-slider-lite'),
                    'easeInOutExpo' => __('easeInOutExpo', 'motopress-slider-lite'),
                    'easeInOutCirc' => __('easeInOutCirc', 'motopress-slider-lite'),
                    'easeInOutBack' => __('easeInOutBack', 'motopress-slider-lite'),

                )
            );
            if($isCloned) {
                $result['label2'] = __('Ease function: ', 'motopress-slider-lite');
                $result['helpers'] = array('start_timing_function');
            }
        } else {
            $result =  array(
                'type' => 'select',
	            'layer_type' => 'all',
                'label2' => __('Start Animation :', 'motopress-slider-lite'),
                'default' => 'fadeIn',
                'list' => array(
                    'bounceIn' => __('bounceIn', 'motopress-slider-lite'),
                    'bounceInDown' => __('bounceInDown', 'motopress-slider-lite'),
                    'bounceInLeft' => __('bounceInLeft', 'motopress-slider-lite'),
                    'bounceInRight' => __('bounceInRight', 'motopress-slider-lite'),
                    'bounceInUp' => __('bounceInUp', 'motopress-slider-lite'),
                    'fadeIn' => __('fadeIn', 'motopress-slider-lite'),
                    'fadeInDown' => __('fadeInDown', 'motopress-slider-lite'),
                    'fadeInDownBig' => __('fadeInDownBig', 'motopress-slider-lite'),
                    'fadeInLeft' => __('fadeInLeft', 'motopress-slider-lite'),
                    'fadeInLeftBig' => __('fadeInLeftBig', 'motopress-slider-lite'),
                    'fadeInRight' => __('fadeInRight', 'motopress-slider-lite'),
                    'fadeInRightBig' => __('fadeInRightBig', 'motopress-slider-lite'),
                    'fadeInUp' => __('fadeInUp', 'motopress-slider-lite'),
                    'fadeInUpBig' => __('fadeInUpBig', 'motopress-slider-lite'),
                    'flip' => __('flip', 'motopress-slider-lite'),
                    'flipInX' => __('flipInX', 'motopress-slider-lite'),
                    'flipInY' => __('flipInY', 'motopress-slider-lite'),
                    'lightSpeedIn' => __('lightSpeedIn', 'motopress-slider-lite'),
                    'rotateIn' => __('rotateIn', 'motopress-slider-lite'),
                    'rotateInDownLeft' => __('rotateInDownLeft', 'motopress-slider-lite'),
                    'rotateInDownRight' => __('rotateInDownRight', 'motopress-slider-lite'),
                    'rotateInUpLeft' => __('rotateInUpLeft', 'motopress-slider-lite'),
                    'rotateInUpRight' => __('rotateInUpRight', 'motopress-slider-lite'),
                    'rollIn' => __('rollIn', 'motopress-slider-lite'),
                    'zoomIn' => __('zoomIn', 'motopress-slider-lite'),
                    'zoomInDown' => __('zoomInDown', 'motopress-slider-lite'),
                    'zoomInLeft' => __('zoomInLeft', 'motopress-slider-lite'),
                    'zoomInRight' => __('zoomInRight', 'motopress-slider-lite'),
                    'zoomInUp' => __('zoomInUp', 'motopress-slider-lite')
                )
            );
            if($isCloned) {
                unset($result['label2']);
                $result['type'] = 'pretty_select';
                $result['helpers'] = array('start_animation');
            }
        }

        return $result;
    }

    private function getEndOptionByType($type, $isCloned = false) {

        $result = array();

        if($type === 'duration') {
            $result = array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label2' => __('duration (ms): ', 'motopress-slider-lite'),
                'default' => 1000,
                'min' => 0
            );

            if ($isCloned) {
                $result['label2'] = __('Duration: ', 'motopress-slider-lite');
                $result['helpers'] = array('end_duration');
            }
        } else if ($type === 'easings'){
            $result = array(
                'type' => 'select',
	            'layer_type' => 'all',
                'label2' => __('Easing :', 'motopress-slider-lite'),
                'default' => 'linear',
                'list' => array(
                    'linear' => __('linear', 'motopress-slider-lite'),
                    'ease' => __('ease', 'motopress-slider-lite'),
                    'easeIn' => __('easeIn', 'motopress-slider-lite'),
                    'easeInOut' => __('easeInOut', 'motopress-slider-lite'),
                    'easeInQuad' => __('easeInQuad', 'motopress-slider-lite'),
                    'easeInCubic' => __('easeInCubic', 'motopress-slider-lite'),
                    'easeInQuart' => __('easeInQuart', 'motopress-slider-lite'),
                    'easeInQuint' => __('easeInQuint', 'motopress-slider-lite'),
                    'easeInSine' => __('easeInSine', 'motopress-slider-lite'),
                    'easeInExpo' => __('easeInExpo', 'motopress-slider-lite'),
                    'easeInCirc' => __('easeInCirc', 'motopress-slider-lite'),
                    'easeInBack' => __('easeInBack', 'motopress-slider-lite'),
                    'easeOutQuad' => __('easeOutQuad', 'motopress-slider-lite'),
                    'easeOutCubic' => __('easeOutCubic', 'motopress-slider-lite'),
                    'easeOutQuart' => __('easeOutQuart', 'motopress-slider-lite'),
                    'easeOutQuint' => __('easeOutQuint', 'motopress-slider-lite'),
                    'easeOutSine' => __('easeOutSine', 'motopress-slider-lite'),
                    'easeOutExpo' => __('easeOutExpo', 'motopress-slider-lite'),
                    'easeOutCirc' => __('easeOutCirc', 'motopress-slider-lite'),
                    'easeOutBack' => __('easeOutBack', 'motopress-slider-lite'),
                    'easeInOutQuad' => __('easeInOutQuad', 'motopress-slider-lite'),
                    'easeInOutCubic' => __('easeInOutCubic', 'motopress-slider-lite'),
                    'easeInOutQuart' => __('easeInOutQuart', 'motopress-slider-lite'),
                    'easeInOutQuint' => __('easeInOutQuint', 'motopress-slider-lite'),
                    'easeInOutSine' => __('easeInOutSine', 'motopress-slider-lite'),
                    'easeInOutExpo' => __('easeInOutExpo', 'motopress-slider-lite'),
                    'easeInOutCirc' => __('easeInOutCirc', 'motopress-slider-lite'),
                    'easeInOutBack' => __('easeInOutBack', 'motopress-slider-lite'),
                )
            );

            if ($isCloned) {
                $result['label2'] = __('Ease function: ', 'motopress-slider-lite');
                $result['helpers'] = array('end_timing_function');
            }
        } else {
            $result =  array(
                'type' => 'select',
	            'layer_type' => 'all',
                'label2' => __('End Animation :', 'motopress-slider-lite'),
                'default' => 'auto',
                'list' => array(
                    'auto' => __('auto', 'motopress-slider-lite'),
                    'bounceOut' => __('bounceOut', 'motopress-slider-lite'),
                    'bounceOutDown' => __('bounceOutDown', 'motopress-slider-lite'),
                    'bounceOutLeft' => __('bounceOutLeft', 'motopress-slider-lite'),
                    'bounceOutRight' => __('bounceOutRight', 'motopress-slider-lite'),
                    'bounceOutUp' => __('bounceOutUp', 'motopress-slider-lite'),
                    'fadeOut' => __('fadeOut', 'motopress-slider-lite'),
                    'fadeOutDown' => __('fadeOutDown', 'motopress-slider-lite'),
                    'fadeOutDownBig' => __('fadeOutDownBig', 'motopress-slider-lite'),
                    'fadeOutLeft' => __('fadeOutLeft', 'motopress-slider-lite'),
                    'fadeOutLeftBig' => __('fadeOutLeftBig', 'motopress-slider-lite'),
                    'fadeOutRight' => __('fadeOutRight', 'motopress-slider-lite'),
                    'fadeOutUp' => __('fadeOutUp', 'motopress-slider-lite'),
                    'fadeOutUpBig' => __('fadeOutUpBig', 'motopress-slider-lite'),
                    'flip' => __('flip', 'motopress-slider-lite'),
                    'flipOutX' => __('flipOutX', 'motopress-slider-lite'),
                    'flipOutY' => __('flipOutY', 'motopress-slider-lite'),
                    'lightSpeedOut' => __('lightSpeedOut', 'motopress-slider-lite'),
                    'rotateOut' => __('rotateOut', 'motopress-slider-lite'),
                    'rotateOutDownLeft' => __('rotateOutDownLeft', 'motopress-slider-lite'),
                    'rotateOutDownRight' => __('rotateOutDownRight', 'motopress-slider-lite'),
                    'rotateOutUpLeft' => __('rotateOutUpLeft', 'motopress-slider-lite'),
                    'rotateOutUpRight' => __('rotateOutUpRight', 'motopress-slider-lite'),
                    'rollOut' => __('rollOut', 'motopress-slider-lite'),
                    'zoomOut' => __('zoomOut', 'motopress-slider-lite'),
                    'zoomOutDown' => __('zoomOutDown', 'motopress-slider-lite'),
                    'zoomOutLeft' => __('zoomOutLeft', 'motopress-slider-lite'),
                    'zoomOutRight' => __('zoomOutRight', 'motopress-slider-lite'),
                    'zoomOutUp' => __('zoomOutUp', 'motopress-slider-lite')
                ),
                'helpers' => array('start_animation')
            );

            if ($isCloned) {
                unset($result['label2']);
                $result['type'] = 'pretty_select';
                $result['helpers'] = array('end_animation');
            }
        }
        return $result;
    }

    public function getOptionsByType($statusType, $type, $isCloned) {
        return $statusType === 'start'  ? $this->getStartOptionByType($type, $isCloned) : $this->getEndOptionByType($type, $isCloned);
    }

	public function getLayoutedOptionsDefaults($settingsFileName = false) {
		$defaults = $this->getOptionsDefaults($settingsFileName);

		foreach ($defaults as $name => &$value) {
			if (MPSLLayout::isLayoutDependentByName($name)) {
				$value = MPSLLayout::makeLayouted($value);
			}
		}

		return $defaults;
	}

}
