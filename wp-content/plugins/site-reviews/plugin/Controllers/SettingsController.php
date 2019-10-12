<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;

class SettingsController extends Controller
{
    /**
     * @param mixed $input
     * @return array
     * @callback register_setting
     */
    public function callbackRegisterSettings($input)
    {
        $settings = Arr::consolidateArray($input);
        if (1 === count($settings) && array_key_exists('settings', $settings)) {
            $options = array_replace_recursive(glsr(OptionManager::class)->all(), $input);
            $options = $this->sanitizeGeneral($input, $options);
            $options = $this->sanitizeSubmissions($input, $options);
            $options = $this->sanitizeTranslations($input, $options);
            $options = apply_filters('site-reviews/settings/callback', $options, $settings);
            if (filter_input(INPUT_POST, 'option_page') == Application::ID.'-settings') {
                glsr(Notice::class)->addSuccess(__('Settings updated.', 'site-reviews'));
            }
            return $options;
        }
        return $input;
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerSettings()
    {
        register_setting(Application::ID.'-settings', OptionManager::databaseKey(), [
            'sanitize_callback' => [$this, 'callbackRegisterSettings'],
        ]);
    }

    /**
     * @return array
     */
    protected function sanitizeGeneral(array $input, array $options)
    {
        $inputForm = $input['settings']['general'];
        if (!$this->hasMultilingualIntegration($inputForm['support']['multilingual'])) {
            $options['settings']['general']['support']['multilingual'] = '';
        }
        if ('' == trim($inputForm['notification_message'])) {
            $options['settings']['general']['notification_message'] = glsr()->defaults['settings']['general']['notification_message'];
        }
        $options['settings']['general']['notifications'] = Arr::get($inputForm, 'notifications', []);
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeSubmissions(array $input, array $options)
    {
        $inputForm = $input['settings']['submissions'];
        $options['settings']['submissions']['required'] = isset($inputForm['required'])
            ? $inputForm['required']
            : [];
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeTranslations(array $input, array $options)
    {
        if (isset($input['settings']['strings'])) {
            $options['settings']['strings'] = array_values(array_filter($input['settings']['strings']));
            $allowedTags = [
                'a' => ['class' => [], 'href' => [], 'target' => []],
                'span' => ['class' => []],
            ];
            array_walk($options['settings']['strings'], function (&$string) use ($allowedTags) {
                if (isset($string['s2'])) {
                    $string['s2'] = wp_kses($string['s2'], $allowedTags);
                }
                if (isset($string['p2'])) {
                    $string['p2'] = wp_kses($string['p2'], $allowedTags);
                }
            });
        }
        return $options;
    }

    /**
     * @return bool
     */
    protected function hasMultilingualIntegration($integration)
    {
        if (!in_array($integration, ['polylang', 'wpml'])) {
            return false;
        }
        $integrationClass = 'GeminiLabs\SiteReviews\Modules\\'.ucfirst($integration);
        if (!glsr($integrationClass)->isActive()) {
            glsr(Notice::class)->addError(sprintf(
                __('Please install/activate the %s plugin to enable integration.', 'site-reviews'),
                constant($integrationClass.'::PLUGIN_NAME')
            ));
            return false;
        } elseif (!glsr($integrationClass)->isSupported()) {
            glsr(Notice::class)->addError(sprintf(
                __('Please update the %s plugin to v%s or greater to enable integration.', 'site-reviews'),
                constant($integrationClass.'::PLUGIN_NAME'),
                constant($integrationClass.'::SUPPORTED_VERSION')
            ));
            return false;
        }
        return true;
    }
}
