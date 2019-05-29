<?php
/**
 * Not used
*/

include_once dirname(__FILE__) . '/MPSL_Fix.php';

class MPSL_Fix_v2_0_0 implements MPSL_Fix {

	public function fixLayers($layers = array()) {

		foreach ($layers as &$layer) {
		    // Make layouted
		    /*
		    foreach (MPSLLayout::$OPTIONS as $layoutOpt) {
				if (array_key_exists($layoutOpt, $layer) && !MPSLLayout::isLayoutedOption($layer[$layoutOpt])) {
					$layer[$layoutOpt] = MPSLLayout::makeLayouted($layer[$layoutOpt]);
				}
		    }
		    */

		    // Move layout styles from private preset to layer options
		    $privateActive = isset($layer['preset']) && $layer['preset'] === 'private'; // Private style is active
		    $hasPrivateStyles = isset($layer['private_styles']) && is_array($layer['private_styles']); // Has private styles
		    $hasNormalStyles = $hasPrivateStyles && (isset($layer['private_styles']['style']) && is_array($layer['private_styles']['style'])); // Has normal styles
		    if ($hasPrivateStyles && $hasNormalStyles) {
				$style = &$layer['private_styles']['style'];
				foreach (MPSLLayout::$STYLE_OPTIONS as $name) {
					if (isset($style[$name])) {
						if ($privateActive) {
							$layer[$name] = $style[$name];
						}
						unset($style[$name]);
					}
				}
		    }

	    }

		return $layers;
	}

}
