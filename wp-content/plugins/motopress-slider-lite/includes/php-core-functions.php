<?php

if (!function_exists('array_replace_recursive')) {
	function array_replace_recursive($array, $array1) {
		if (!function_exists('_mpsl_recurse')) {
			function _mpsl_recurse($array, $array1) {
				foreach ($array1 as $key => $value) {
					// create new key in $array, if it is empty or not an array
					if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
						$array[$key] = array();
					}

					// overwrite the value in the base array
					if (is_array($value)) {
						$value = _mpsl_recurse($array[$key], $value);
					}
					$array[$key] = $value;
				}
				return $array;
			}
		}

		// handle the arguments, merge one by one
		$args = func_get_args();
		$array = $args[0];
		if (!is_array($array)) {
			return $array;
		}
		for ($i = 1; $i < count($args); $i++) {
			if (is_array($args[$i])) {
				$array = _mpsl_recurse($array, $args[$i]);
			}
		}
		return $array;
	}
}

if (!function_exists('json_encode_slashed')) {
	if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
		function json_encode_slashed($value, $options = 0, $depth = 512) {
			return json_encode($value, $options | JSON_UNESCAPED_SLASHES, $depth);
		}
	} else if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
		function json_encode_slashed($value, $options = 0) {
			return json_encode($value, $options | JSON_UNESCAPED_SLASHES);
		}
	} else {
		// For PHP 5.3
		function json_encode_slashed($value, $options = 0) {
			return json_encode($value, $options);
		}
	}
}
