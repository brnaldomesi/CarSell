<?php
class MPCEThemeFix {
    private $curUser;
    private $curTheme;
    private $deactivated;

    const ACTIVATE = 'activate';
    const DEACTIVATE = 'deactivate';

    const DEACTIVATED_KEY = 'motopress-ce-deactivated';

    const PAGE_LINES_KEY = 'pl_editor_state';
    private $mysitemywayKey;
    private $mysitemywayArrKey = 'disable_cufon';

    function __construct($action) {
        $this->curUser = wp_get_current_user();
        $this->curTheme = wp_get_theme();
        $this->deactivated = get_option(self::DEACTIVATED_KEY, array());

        if (strcasecmp($this->curTheme->get('Author'), 'pagelines') === 0) {
            if ($action === self::ACTIVATE) {
                $this->activatePageLinesEditor();
            } elseif ($action === self::DEACTIVATE) {
                $this->deactivatePageLinesEditor();
            }
        }

        if (strcasecmp($this->curTheme->get('Author'), 'mysitemyway') === 0) {
            $this->mysitemywayKey = 'mysite_' . $this->curTheme->get_stylesheet() . '_options';

            if ($action === self::ACTIVATE) {
                $this->activateMysitemywayCufon();
            } elseif ($action === self::DEACTIVATE) {
                $this->deactivateMysitemywayCufon();
            }
        }
    }

    private function activatePageLinesEditor() {
        $state = get_user_meta($this->curUser->ID, self::PAGE_LINES_KEY, true);
        if (in_array('pagelines', $this->deactivated) && !empty($state) && strcasecmp($state, 'off') === 0) {
            $updated = update_user_meta($this->curUser->ID, self::PAGE_LINES_KEY, 'on');
            if ($updated) {
                unset($this->deactivated[array_search('pagelines', $this->deactivated)]);
                update_option(self::DEACTIVATED_KEY, $this->deactivated);
            }
        }
    }

    private function deactivatePageLinesEditor() {
        $state = get_user_meta($this->curUser->ID, self::PAGE_LINES_KEY, true);
        if (!empty($state) && strcasecmp($state, 'on') === 0) {
            $updated = update_user_meta($this->curUser->ID, self::PAGE_LINES_KEY, 'off');
            if ($updated) {
                if (!in_array('pagelines', $this->deactivated)) {
                    $this->deactivated[] = 'pagelines';
                }
                update_option(self::DEACTIVATED_KEY, $this->deactivated);
            }
        }
    }

    private function activateMysitemywayCufon() {
        $themeOptions = get_option($this->mysitemywayKey);
        if (in_array('mysitemyway', $this->deactivated) && !empty($themeOptions) && array_key_exists($this->mysitemywayArrKey, $themeOptions)) {
            unset($themeOptions[$this->mysitemywayArrKey]);
            $updated = update_option($this->mysitemywayKey, $themeOptions);
            if ($updated) {
                unset($this->deactivated[array_search('mysitemyway', $this->deactivated)]);
                update_option(self::DEACTIVATED_KEY, $this->deactivated);
            }
        }
    }

    private function deactivateMysitemywayCufon() {
        $themeOptions = get_option($this->mysitemywayKey);
        if (!empty($themeOptions) && !array_key_exists($this->mysitemywayArrKey, $themeOptions)) {
            $themeOptions[$this->mysitemywayArrKey] = array(true);
            $updated = update_option($this->mysitemywayKey, $themeOptions);
            if ($updated) {
                if (!in_array('mysitemyway', $this->deactivated)) {
                    $this->deactivated[] = 'mysitemyway';
                }
                update_option(self::DEACTIVATED_KEY, $this->deactivated);
            }
        }
    }

    public function isHeadwayTheme() {
        return strcasecmp($this->curTheme->get('Author'), 'Headway Themes') === 0;
    }
}