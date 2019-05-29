<?php

class MPSLSharing {
	public static $isScriptsStylesEnqueued = false;
	public static $isPreviewPage = false;
	private static $shortcodeIsRendering = false;

	public static function isMPCE() {
		return class_exists('MPCEShortcode') && MPCEShortcode::isContentEditor();
	}

	public static function disableShortcodeRendering() {
		self::$shortcodeIsRendering = true;
	}

	public static function enableShortcodeRendering() {
		self::$shortcodeIsRendering = false;
	}

	public static function isShortcodeRendering() {
		return self::$shortcodeIsRendering;
	}

}