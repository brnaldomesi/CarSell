<?php

/** @todo: Maybe make preview styles like in MPCE (transient OR parent.window variable) */

require_once dirname(__FILE__) . '/ChildOptions.php';

class MPSLLayerPresetOptions extends MPSLChildOptions {
	private static $instance = null;
	private $defaults = null;
	private $defaultPresets = null;
	private $presets = null;
	private $lastPresetId = 0;
	private $lastPrivatePresetId = 0;
	private $preview = false;
	private static $originalFontList = null;
	private static $fontList = null;
	private static $fontAssoc = array();
	private static $defaultFontWeightList = null;

	const PRESETS_OPT = 'mpsl_preset';
	const CSS_OPT = 'mpsl_css';
	const DEFAULT_CSS_OPT = 'mpsl_default_css';
	const PREVIEW_CSS_OPT = 'mpsl_preview_css';
	const PREVIEW_DEFAULT_CSS_OPT = 'mpsl_preview_default_css';
	const PRIVATE_CSS_OPT = 'mpsl_private_css';
	const PRIVATE_PREVIEW_CSS_OPT = 'mpsl_private_preview_css';
	const LAST_PRESET_ID_OPT = 'mpsl_last_preset_id';
	const LAST_PRIVATE_PRESET_ID_OPT = 'mpsl_last_private_preset_id';
	const PRESET_PREFIX = 'mpsl-preset-';
	const PRIVATE_PRESET_PREFIX = 'mpsl-private-preset-';
	const LAST_PRESET_ID_DEFAULT = 0;
	const LAYER_CLASS = 'mpsl-layer';
	const LAYER_HOVER_CLASS = 'mpsl-layer-hover';

	function __construct($preview = false) {
		parent::__construct();

		$this->preview = $preview;

		$this->lastPresetId = get_option(self::LAST_PRESET_ID_OPT, self::LAST_PRESET_ID_DEFAULT);
		$this->lastPrivatePresetId = get_option(self::LAST_PRIVATE_PRESET_ID_OPT, self::LAST_PRESET_ID_DEFAULT);

		$this->defaultPresets = include($this->pluginDir . 'defaults/style-presets/presets.php');

		$this->options = include($this->getSettingsPath());
		$this->prepareOptions($this->options);

		$this->defaults = $this->getDefaults($this->options);

		$loaded = $this->load();

		if (!$loaded) {
			// TODO: Throw error
//            _e('Record not found', 'motopress-slider-lite');
		}
	}

	public static function getInstance($preview = false) {
		if (null === self::$instance) {
			self::$instance = new self($preview);
		}
		return self::$instance;
	}

	protected function load() {
		$this->defaultPresets = $this->override($this->defaultPresets, false, true);
		$this->override(get_option(self::PRESETS_OPT, array()));
		return true;
	}

	public function override($presets = null, $single = false, $silent = false) {
		if (!empty($presets)) {
			if ($single) {
				if (!is_array($presets)) $presets = array();
				$presets = array_replace_recursive($this->defaults, $presets);
				$presets['hover']['allow_style'] = $presets['settings']['hover'];
			} else {
				foreach ($presets as $presetKey => $preset) {
					if (!is_array($presets[$presetKey])) $presets[$presetKey] = array();
					$presets[$presetKey] = array_replace_recursive($this->defaults, $presets[$presetKey]);
					$presets[$presetKey]['hover']['allow_style'] = $presets[$presetKey]['settings']['hover'];
				}
			}
		}

		if (!$single && !$silent) $this->presets = $presets;

		return $presets;
	}

	public function update() {
		if (!$this->preview) {
			update_option(self::LAST_PRESET_ID_OPT, $this->getLastPresetId());
			update_option(self::LAST_PRIVATE_PRESET_ID_OPT, $this->getLastPrivatePresetId());

			$cleanPresets = $this->clearPresets($this->presets);
			update_option(self::PRESETS_OPT, $cleanPresets);
		}

		$defaultCss = $this->compile($this->defaultPresets);
		$css = $this->compile($this->presets);

		if ($defaultCss !== false && is_string($defaultCss)) update_option($this->preview ? self::PREVIEW_DEFAULT_CSS_OPT : self::DEFAULT_CSS_OPT, $defaultCss);
		if ($css !== false && is_string($css)) update_option($this->preview ? self::PREVIEW_CSS_OPT : self::CSS_OPT, $css);

		return true;
	}

	public function render() {
		global $mpsl_settings;
		include($this->getViewPath());
	}

	public function getDefaultPresets() {
		return $this->defaultPresets;
	}

	public function getPresets() {
		return $this->presets;
	}

	public function getAllPresets() {
		return array_merge($this->defaultPresets, $this->presets);
	}

	public function setPresets($presets) {
		return $this->presets = $presets;
	}

	public function getDefaults(&$options = array()) {
		$defaults = parent::getDefaults($options);
		return array(
			'style' => $defaults,
			'hover' => $defaults,
			'settings' => array(
				'label' => '',
				'hover' => true
			),
		);
	}

	public function getOptionsDefaults($settingsFileName = false) {
		$defaults = parent::getOptionsDefaults($settingsFileName);
		return array(
			'style' => $defaults,
			'hover' => $defaults,
			'settings' => array(
				'label' => '',
				'hover' => true
			),
		);
	}

	protected function getSettingsFileName() {
		return 'preset';
	}

	protected function getViewFileName() {
		return 'preset';
	}

	/*public function setUniqueName(&$preset) {
		// TODO: Generate Name
	}*/

	public function getLastPresetId() {
		return $this->lastPresetId;
	}

	public function setLastPresetId($id) {
		if (is_numeric($id)) $this->lastPresetId = $id;
	}

	public function incLastPresetId() {
		$this->lastPresetId ++;
	}

	public function getLastPresetClass() {
		return self::PRESET_PREFIX . $this->getLastPresetId();
	}

	public function getLastPrivatePresetId() {
		return $this->lastPrivatePresetId;
	}

	public function setLastPrivatePresetId($id) {
		if (is_numeric($id)) $this->lastPrivatePresetId = $id;
	}

	public function incLastPrivatePresetId() {
		$this->lastPrivatePresetId ++;
	}

	public function getLastPrivatePresetClass() {
		return self::PRIVATE_PRESET_PREFIX . $this->getLastPrivatePresetId();
	}

	/**
	 * @param $presets
	 * @param bool|false $prepare
	 * @param bool|false $separated Editor option
	 * @return array|string
	 */
	public function compile($presets, $prepare = false, $separated = false) {
		if ($prepare) $presets = $this->override($presets, false, true);
		$options = $this->getOptions(false); // TODO: Get options on init
		$css = '';
		$cssArr = array();

		foreach ($presets as $class => $preset) {
			if (!$this->isValidPreset($preset)) continue;

			$types = array('style');
			if (!isset($preset['settings']['hover']) || $preset['settings']['hover']) {
				$types[] = 'hover';
			}
			if ($separated) $css = '';

			foreach ($types as $type) {
				// Add cross-browser options
				foreach ($preset[$type] as $optName => $optVal) {
					if (!array_key_exists($optName, $options)) continue;
					switch ($optName) {
						case 'border-radius':
							$options['-moz-' . $optName] = $options['-webkit-' . $optName] = $options[$optName];
							unset($preset[$type][$optName]);
							$preset[$type][$optName] = $preset[$type]['-moz-' . $optName] = $preset[$type]['-webkit-' . $optName] = $optVal;
							break;
					}
				}

				$css .= '.' . self::LAYER_CLASS . ".$class";
				if ($type === 'hover') {
					$css .= $separated ? ('.' . self::LAYER_HOVER_CLASS) : ":hover";
				}
				$css .= "{";
				foreach ($preset[$type] as $optName => $optVal) {
					if (!array_key_exists($optName, $options)) continue;
					if ($options[$optName]['isChild']) continue;

//					switch ($optName) {
//						case 'font-style':
//							$optVal = $optVal ? 'italic' : '';
//							break;
						/*case 'line-height':
							$optVal = $optVal === '' ? 'normal' : $optVal;
							break;*/
//					}

					// Skip empty & helper options
					if (!is_string($optVal) || $optVal === '' || in_array($optName, array('allow_style', 'custom_styles'))) continue;

					// Add unit
					$css .= $optName . ':' . trim($optVal);
					if (is_numeric($optVal) && $unit = $options[$optName]['unit']) {
						$css .= $unit;
					}

//					if ($separated) {
//					if ($type === 'hover' && in_array($optName, MPSLLayout::$PRESET_HOVER_OPTIONS)) {
					if ($type === 'hover' && in_array($optName, MPSLLayout::$STYLE_OPTIONS)) {
						$css .= ' !important';
					}
//					}

					$css .= ';';
				}
				// Remove line breaks
				if (array_key_exists('custom_styles', $preset[$type])) {
					$css .= preg_replace('/\s+/S', " ", $preset[$type]['custom_styles']);
				}
				$css .= "}";
			}

			$cssArr[$class] = $css;
		}

		return $separated ? $cssArr : $css;
	}

	public function updatePrivateStyles($previewSlideId = null) {
		$db = MPSliderDB::getInstance();
		$slides = $db->getSlideList(array('id', 'layers'));
		$privateStyleList = array();

	    foreach ($slides as $slide) {
			if (!$slide || empty($slide)) continue;

		    // Get preview slide
		    if ($this->preview && $slide['id'] == $previewSlideId) {
			    $previewSlide = $db->getPreviewSlide($slide['id'], array('id', 'layers'));
			    if (!$previewSlide || empty($previewSlide)) continue;
			    $slide['layers'] = json_decode($previewSlide['layers'], true);
			    if (!is_array($slide['layers'])) $slide['layers'] = array();
		    }

		    foreach ($slide['layers'] as &$layer) {
			    if (isset($layer['preset']) && $layer['preset'] === 'private') {
				    if (isset($layer['private_preset_class']) && $layer['private_preset_class']) {
					    $privateStyleList[$layer['private_preset_class']] = $layer['private_styles'];
				    }
			    }
		    }
	    }

	    $css = $this->compile($privateStyleList, true);
		if ($css !== false && is_string($css)) {
			update_option($this->preview ? self::PRIVATE_PREVIEW_CSS_OPT : self::PRIVATE_CSS_OPT, $css);
		}
	}

	public function loadNewPresets($newPresets) {
		$newClasses = array();
		foreach ($newPresets as $pClass => $preset) {
			if (preg_match('/^' . self::PRESET_PREFIX . '[0-9]+$/', $pClass)) {
				$this->incLastPresetId();
				$pClassNew = $this->getLastPresetClass();
				$newClasses[$pClass] = $pClassNew;
				$this->presets[$pClassNew] = $this->override($preset, true, true);
			}
		}
		return $newClasses;
	}

	public function clearPresets($presets = array()) {
		if (!is_array($presets)) return array();

		foreach ($presets as &$preset) {
			$preset = $this->clearPreset($preset);
		}

		return $presets;
	}

	public function clearPreset($preset) {
		if ($preset && $this->isValidPreset($preset)) {
			foreach (array('style', 'hover') as $mode) {
				if (!isset($preset[$mode])) continue;
				foreach ($preset[$mode] as $optKey => $optVal) {
					if ($optVal === '') {
						unset($preset[$mode][$optKey]);
					}
				}
			}
			if (isset($preset['settings']['label']) && !$preset['settings']['label']) {
				unset($preset['settings']['label']);
			}
			if (isset($preset['settings']['hover']) && $preset['settings']['hover']) {
				unset($preset['settings']['hover']);
			}
		}

		return $preset;
	}

	/**
	 * Remove layout options (used for private preset)
	 * @param $preset array
	 * @return array
	 */
	public function clearLayoutOptions($preset) {
		if ($preset && $this->isValidPreset($preset)) {
			foreach (array('style', 'hover') as $mode) {
				if (!isset($preset[$mode])) continue;
				foreach (MPSLLayout::$PRESET_OPTIONS_TO_SKIP[$mode] as $optName) {
					if (array_key_exists($optName, $preset[$mode])) {
						unset($preset[$mode][$optName]);
					}
				}
			}
		}

		return $preset;
	}

	public function getHoverStylesByPreset($preset) {
        $result = array();

		if ($preset && $this->isValidPreset($preset)) {
//			foreach (MPSLLayout::$PRESET_HOVER_OPTIONS as $optName) {
			foreach (MPSLLayout::$STYLE_OPTIONS as $optName) {
				if (isset($preset['settings']['hover']) && $preset['settings']['hover']) {
					if (!empty($preset['hover'][$optName])) {
						$result[$optName] = $preset['hover'][$optName];
					}
				}
			}
		}

        return $result;
    }

	/**
	 * @param $preset
	 * @param array $normalVariantsMixin
	 * @return array [ <font-family> => [ variants => [ ... ] ], ... ]
	 */
	// TODO: Maybe fix Italic for hover style (if normal font-style set to Italic & hover - set to Inherit). Maybe add `Normal` option to font-style.
	public function getFontsByPreset($preset, $normalVariantsMixin = array()) {
		$fonts = array();
		if (!$this->isValidPreset($preset)) return $fonts;

		$inheritStyles = array('font-family', 'font-style');
		$types = array('style');
		if ($preset['settings']['hover']) $types[] = 'hover';

		foreach ($types as $type) {

			// Inherit preset font-(family/style) for hover style (needed for gfont link)
			if ($type === 'hover') {
				foreach ($inheritStyles as $inheritStyle) {
					if (!isset($preset[$type][$inheritStyle]) || !$preset[$type][$inheritStyle]) {
						$preset[$type][$inheritStyle] = $preset['style'][$inheritStyle];
					}
				}
			}

			if (isset($preset[$type]['font-family']) && ($fontName = $preset[$type]['font-family'])) {
				if (!array_key_exists($fontName, $fonts)) {
					$fonts[$fontName] = array('variants' => array());
				}

				$fontWeights = array();
				// Get weight from preset
				if (($fontWeight = $preset[$type]['font-weight']) && !in_array($fontWeight, $fonts[$fontName]['variants'])) {
					$fontWeights[] = $fontWeight;
				}
				// Mixin normal variants
				if ($type === 'style' && count($normalVariantsMixin)) {
					$fontWeights = array_merge($fontWeights, $normalVariantsMixin);
				}

				// Process weights
				foreach ($fontWeights as $fontWeight) {
					// Normal
					$fontWeight = $fontWeight === 'normal' ? 'regular' : $fontWeight;
					$fonts[$fontName]['variants'][] = $fontWeight;

					// Italic
					if (isset($preset[$type]['font-style']) && $preset[$type]['font-style'] === 'italic') {
						$fontWeight = $fontWeight === 'regular' ? 'italic' : $fontWeight . 'italic';
						if (!in_array($fontWeight, $fonts[$fontName]['variants'])) {
							$fonts[$fontName]['variants'][] = $fontWeight;
						}
					}
				}
			}
		}

		return $fonts;
	}

	public function getDefaultPresetFonts() {
		$defaultFonts = array();
		if (count($this->defaultPresets)) {
			foreach ($this->defaultPresets as $defaultPreset) {
				$defaultFonts = array_merge_recursive($defaultFonts, $this->getFontsByPreset($defaultPreset));
			}
		}
		return self::fontsUnique($defaultFonts);
	}

	public function getPresetFonts() {
		$fonts = array();
		if (count($this->presets)) {
			foreach ($this->presets as $preset) {
				$fonts = array_merge_recursive($fonts, $this->getFontsByPreset($preset));
			}
		}
		return self::fontsUnique($fonts);
	}

	public function getAllPresetFonts() {
		$fonts = array_merge_recursive($this->getDefaultPresetFonts(), $this->getPresetFonts());
		return self::fontsUnique($fonts);
	}

	public function isValidPreset($preset) {
		return (
			is_array($preset) && !empty($preset) &&
			isset($preset['settings']) && is_array($preset['settings']) &&
			isset($preset['style']) && is_array($preset['style']) &&
			isset($preset['hover']) && is_array($preset['hover'])
		);
	}

	public static function fontsUnique($fonts) {
		foreach ($fonts as $fKey => $fVal) {
			$fonts[$fKey]['variants'] = array_values(array_unique($fVal['variants']));
		}
		return $fonts;
	}

	public static function getDefaultCss() {
		return wp_strip_all_tags(get_option(MPSLSharing::$isPreviewPage ? self::PREVIEW_DEFAULT_CSS_OPT : self::DEFAULT_CSS_OPT, ''));
	}

	public static function getCustomCss() {
		return wp_strip_all_tags(get_option(MPSLSharing::$isPreviewPage ? self::PREVIEW_CSS_OPT : self::CSS_OPT, ''));
	}

	public static function getPrivateCss() {
		return wp_strip_all_tags(get_option(MPSLSharing::$isPreviewPage ? self::PRIVATE_PREVIEW_CSS_OPT : self::PRIVATE_CSS_OPT, ''));
	}

	public static function getAllCss() {
		return self::getDefaultCss() . self::getCustomCss() . self::getPrivateCss();
	}

	public static function getOriginalFontList() {
		if (is_null(self::$originalFontList)) {
			global $mpsl_settings;
			$googleFonts = file_get_contents($mpsl_settings['plugin_dir_path'] . 'vendor/googlefonts/webfonts.json');
			$googleFonts = $googleFonts ? json_decode($googleFonts, true) : array();
			self::$originalFontList = $googleFonts;

			if (isset($googleFonts['items'])) {
				foreach ($googleFonts['items'] as $key => $font) {
					self::$fontAssoc[$font['family']] = $key;
				}
			}
		}

		return self::$originalFontList;
	}

	public static function getFontByName($name) {
		$result = null;
		$fonts = self::getOriginalFontList();

		if (isset(self::$fontAssoc[$name])) {
			$key = self::$fontAssoc[$name];
			if (isset($fonts['items'][$key])) {
				$result = $fonts['items'][$key];
			}
		}

		return $result;
	}

	public static function getFontList($withDefault = false) {
		$fonts = array();

		if (is_null(self::$fontList)) {
			$googleFonts = self::getOriginalFontList();

			if (!is_null($googleFonts) && isset($googleFonts['items'])) {
				foreach ($googleFonts['items'] as $gFont) {
					foreach ($gFont['variants'] as $key => $variant) {
						if (strpos($variant, 'italic') !== false) {
							unset($gFont['variants'][$key]);
							continue;
						}

						$variant = str_replace('regular', 'normal', $variant);
						$gFont['variants'][$key] = array('value' => $variant, 'label' => ucfirst($variant));
					}

					array_unshift($gFont['variants'], array('value' => '', 'label' => __('Inherit', 'motopress-slider-lite')));

					$fonts[$gFont['family']] = array(
						'family' => $gFont['family'],
						'variants' => $gFont['variants']
					);
				}
			}

			self::$fontList = $fonts;

		} else {
			$fonts = self::$fontList;
		}

		if ($withDefault) {
			$fonts = array_merge(array(
				'' => array(
					'family' => '',
					'variants' => self::getDefaultFontWeightList()
				)
			), $fonts);
		}

		return $fonts;
	}

	public static function getDefaultFontWeightList() {
		if (is_null(self::$defaultFontWeightList)) {
			self::$defaultFontWeightList = array(
				array('value' => '',    'label' => __('Inherit', 'motopress-slider-lite')),
				array('value' => '100', 'label' => __('100 (Thin)', 'motopress-slider-lite')),
				array('value' => '200', 'label' => __('200 (Extra Light)', 'motopress-slider-lite')),
				array('value' => '300', 'label' => __('300 (Light)', 'motopress-slider-lite')),
				array('value' => '400', 'label' => __('400 (Normal)', 'motopress-slider-lite')),
				array('value' => '500', 'label' => __('500 (Medium)', 'motopress-slider-lite')),
				array('value' => '600', 'label' => __('600 (Semi Bold)', 'motopress-slider-lite')),
				array('value' => '700', 'label' => __('700 (Bold)', 'motopress-slider-lite')),
				array('value' => '800', 'label' => __('800 (Extra Bold)', 'motopress-slider-lite')),
				array('value' => '900', 'label' => __('900 (Heavy)', 'motopress-slider-lite')),
			);
		}
		return self::$defaultFontWeightList;
	}

}