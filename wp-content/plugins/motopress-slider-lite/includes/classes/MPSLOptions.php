<?php

include_once dirname(__FILE__) . '/OptionsFactory.php';

abstract class MPSLOptions {
    protected $pluginDir;
    protected $options = null;
	private $dependencyTypes = array('dependency', 'disabled_dependency');

    public function __construct() {
        global $mpsl_settings;
        $this->pluginDir = $mpsl_settings['plugin_dir_path'];
    }

    abstract public function render();

    /**
     * Prepare settings before using
     */
	protected function prepareOptions(&$options = array()) {
        foreach ($options as $grpName => $grp) {
            foreach ($grp['options'] as $optName => $opt) {
                $options[$grpName]['options'][$optName]['isChild'] = false;
                $options[$grpName]['options'][$optName]['group'] = $grpName;
                $options[$grpName]['options'][$optName]['name'] = $optName;
	            $options[$grpName]['options'][$optName]['unit'] = array_key_exists('unit', $opt) ? $opt['unit'] : '';
                $options[$grpName]['options'][$optName]['hidden'] = isset($opt['hidden']) ? $opt['hidden'] : false;
                $options[$grpName]['options'][$optName]['layout_dependent'] = isset($opt['layout_dependent']) ? $opt['layout_dependent'] : false;

	            // Prepare option
	            $options[$grpName]['options'][$optName] = $this->prepareOption($optName, $options[$grpName]['options'][$optName]);

	            $options[$grpName]['options'][$optName]['value'] = array_key_exists('default', $opt) ? $options[$grpName]['options'][$optName]['default'] : '';

	            foreach ($this->dependencyTypes as $depType) {
		            if (array_key_exists($depType, $opt)) {
			            // remove dep if empty
			            if (!count($opt[$depType])) {
				            unset($options[$grpName]['options'][$optName][$depType]);
				            continue;
			            }

			            // fix operator
			            if (array_key_exists('operator', $opt[$depType])) {
				            $opt[$depType]['operator'] = strtoupper($opt[$depType]['operator']);
			            } else {
				            $opt[$depType]['operator'] = 'IN';
			            }

			            // value to array + sort
			            if (is_array($opt[$depType]['value'])) {
				            sort($opt[$depType]['value']);
			            } else {
				            $opt[$depType]['value'] = array($opt[$depType]['value']);
			            }

			            // bool to int
			            foreach ($opt[$depType]['value'] as &$val) {
				            if (is_bool($val)) $val = (int)$val;
			            }

			            // update dependency
			            $options[$grpName]['options'][$optName][$depType] = $opt[$depType];
		            }
	            }

                if (array_key_exists('options', $opt)) {
					$skipChild = isset($opt['skipChild']) && $opt['skipChild'];
                    foreach ($options[$grpName]['options'][$optName]['options'] as $childOptName => $childOpt) {
                        $options[$grpName]['options'][$optName]['options'][$childOptName]['isChild'] = true;
                        $options[$grpName]['options'][$optName]['options'][$childOptName]['group'] = $grpName;
                        $options[$grpName]['options'][$optName]['options'][$childOptName]['name'] = $childOptName;
	                    $options[$grpName]['options'][$optName]['options'][$childOptName]['unit'] = array_key_exists('unit', $childOpt) ? $childOpt['unit'] : '';
						$options[$grpName]['options'][$optName]['options'][$childOptName]['skip'] = $skipChild;
						$options[$grpName]['options'][$optName]['options'][$childOptName]['layout_dependent'] = isset($childOpt['layout_dependent']) ? $childOpt['layout_dependent'] : false;

						// Prepare child option
	                    $options[$grpName]['options'][$optName]['options'][$childOptName] = $this->prepareOption($childOptName, $options[$grpName]['options'][$optName]['options'][$childOptName]);

	                    $options[$grpName]['options'][$optName]['options'][$childOptName]['value'] = $options[$grpName]['options'][$optName]['options'][$childOptName]['default'];

	                    foreach ($this->dependencyTypes as $depType) {
		                    if (!array_key_exists($depType, $childOpt) && array_key_exists($depType, $opt) && count($opt[$depType])) {
			                    $options[$grpName]['options'][$optName]['options'][$childOptName][$depType] = $opt[$depType];
		                    }
	                    }
                    }
                }
            }
        }
    }

	protected function prepareOption($name, $option) {
		return $option;
	}

	public function getOptions($grouped = false) {
	    if ($grouped) {
			return $this->options;
		} else {
			$options = array();
			foreach ($this->options as $grp) {
				$options = array_merge($options, $grp['options']);

				foreach ($grp['options'] as $name => $opt) {
					if (array_key_exists('options', $opt)) {
						$options = array_merge($options, $opt['options']);
					}
				}
			}
			return $options;
		}

    }

	protected function getDefaults(&$options = array()){
        $defaults = array();
        foreach($options as $grp){
            foreach($grp['options'] as $optName => $opt) {
	            $defaults[$optName] = array_key_exists('default', $opt) ? $opt['default'] : '';
            }
        }
        return $defaults;
    }

	public function getOptionsDefaults($settingsFileName = false) {
        $options = include($this->getSettingsPath($settingsFileName));
        $defaults = array();

        foreach ($options as $grp) {
            if (isset($grp['options'])) {
                foreach ($grp['options'] as $optName => $opt){
	                $defaults[$optName] = array_key_exists('default', $opt) ? $opt['default'] : '';

                    if (array_key_exists('options', $opt)) {
                        foreach ($opt['options'] as $childOptName => $childOpt) {
                            $defaults[$childOptName] = $childOpt['default'];
                        }
                    }
                }
            }
        }

        return $defaults;
    }

	abstract protected function getSettingsFileName();
	abstract protected function getViewFileName();

	protected function getSettingsPath($settingsFileName = false) {
		return $this->pluginDir . 'settings/' . ($settingsFileName ? $settingsFileName : $this->getSettingsFileName()) . '.php';
	}
	protected function getViewPath($viewFileName = false) {
		return $this->pluginDir . 'views/' . ($viewFileName ? $viewFileName : $this->getViewFileName()) . '.php';
	}

}