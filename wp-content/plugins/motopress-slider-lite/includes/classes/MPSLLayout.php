<?php

class MPSLLayout {

	const DEFAULT_LAYOUT = 'desktop';
	static $LAYOUTS = array('desktop', 'notebook', 'tablet', 'mobile'); // DESC order
	static $OPTIONS = array('align', 'vert_align', 'hor_align', 'offset_x', 'offset_y', 'width', 'html_width', 'video_width', 'video_height', 'white-space', 'font-size', 'line-height', 'text-align');
	static $STYLE_OPTIONS = array('font-size', 'line-height', 'text-align');
	static $PRESET_OPTIONS_TO_SKIP = array(
		'style' => array(),
		'hover' => array()
	);
//	static $PRESET_HOVER_OPTIONS = array('color');
//	static $PRESET_HOVER_OPTIONS = array('font-size', 'line-height');


	/**
	 * Check for option format (single or multiple)
	 * @param * $option Option value (from layer data)
	 * @return bool
	 */
	public static function isLayoutedOption($option) {
		return is_array($option) && array_key_exists(self::DEFAULT_LAYOUT, $option);
	}

	/**
	 * Check for fully layouted value
	 * @param * $option Option value (from layer data)
	 * @return bool
	 */
	public static function isFullyLayoutedOption($option) {
//		return self::isLayoutedOption($option) && count(array_intersect_key(array_flip(self::$LAYOUTS), $option)) === count(self::$LAYOUTS);

		if ($result = self::isLayoutedOption($option)) {
			foreach (self::$LAYOUTS as $layout) {
				if (!array_key_exists($layout, $option)) {
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Convert single option to layouted
	 * @param string|array $option Option value (from layer data)
	 * @param bool|true $nonexistent2Null Nonexistent layout values set to NULL
	 * @return array
	 */
	public static function makeLayouted($option = '', $nonexistent2Null = true) {
		if ($nonexistent2Null) {
			$defaultLayoutValue = $option;
			$option = array_fill_keys(self::$LAYOUTS, null);
			$option[self::DEFAULT_LAYOUT] = $defaultLayoutValue;

		} else {
			$option = array_fill_keys(self::$LAYOUTS, $option);
		}

		return $option;
	}

	/**
	 * Convert not fully layouted option to fully layouted (fill the missing layouts)
	 * @param string|array $option Layouted Option value (from layer data)
	 * @param bool|true $nonexistent2Null Nonexistent layout values set to NULL
	 * @return array
	 */
	public static function makeFullyLayouted($option = '', $nonexistent2Null = true) {
		if ($nonexistent2Null) {
			foreach (self::$LAYOUTS as $layout) {
				if (!array_key_exists($layout, $option)) {
					$option[$layout] = null;
				}
			}

		} else {
			$prevValue = $option[self::DEFAULT_LAYOUT];
			foreach (self::$LAYOUTS as $layout) {
				if (!array_key_exists($layout, $option)) {
					$option[$layout] = $prevValue;
				}
				$prevValue = $option[$layout];
			}
		}

		return $option;
	}

	/**
	 * Check for depends from layout by option settings
	 * @param array $option Option settings (from settings file)
	 * @return bool
	 */
	public static function isLayoutDependent($option = array()) {
		return array_key_exists('layout_dependent', $option) && $option['layout_dependent'];
	}

	/**
	 * Check for depends from layout by option name
	 * @param string $optionName Option name
	 * @return bool
	 */
	public static function isLayoutDependentByName($optionName) {
		return in_array($optionName, self::$OPTIONS);
	}

	/**
	 * @param array $option Layouted Option value (from layer data)
	 * @return array|mixed
	 */
	public static function clearEmptyLayoutOptions($option) {
		// Remove not specified values
		$option = array_filter($option, function($layoutOpt) {
            return !is_null($layoutOpt);
        });

		// Array to single value if only one layout specified
		if (count($option) === 1) {
			$option = reset($option);
		}

		return $option;
	}

}