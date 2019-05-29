<?php

class MPSL_Fix_Factory {

	/**
	 * @param String $version Version of the fixer (written through the dot)
	 * @return MPSL_Fix
	 */
	public static function getFixer($version) {
		global $mpsl_settings;

		$version = str_replace('.', '_', $version);
		$className = "MPSL_Fix_v$version";

		require_once $mpsl_settings['plugin_dir_path'] . "includes/classes/update_fixes/{$className}.php";

        return new $className();
	}

}