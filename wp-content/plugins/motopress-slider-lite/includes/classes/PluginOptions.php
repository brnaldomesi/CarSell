<?php

class MPSLPluginOptions {

    public function __construct(){
        $this->addActions();
    }

    function renderPage() {
        global $mpsl_settings;

        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            add_settings_error(
                'motopressSliderOptions',
                esc_attr('settings_updated'),
                __('Slider Settings Updated', 'motopress-slider-lite'),
                'updated'
            );
        }

        echo '<div class="wrap">';
        echo '<h2>'.__('Slider Settings', 'motopress-slider-lite').'</h2>';
        settings_errors('motopressSliderOptions', false);
        echo '<form actoin="options.php" method="POST">';
        do_settings_sections('motopress-slider-options');
        wp_nonce_field( 'motopress-slider-options');
        echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="'.__('Save', 'motopress-slider-lite').'" /></p>';
        echo '</form>';
        echo '</div>';
    }

    public function save() {
        global $mpsl_settings;
        if (!empty($_POST)) {
            if (check_admin_referer( 'motopress-slider-options' )) {
                $disabledRoles = array();
                if ( isset($_POST['disabled_roles']) and count($_POST['disabled_roles']) > 0 ) {
                    $disabledRoles = $_POST['disabled_roles'];
                }
                update_option('mpsl-disabled-roles', $disabledRoles);
                wp_redirect( get_admin_url() . 'admin.php?page=' . $_GET['page'] . '&settings-updated=true' );
            }
        }
    }

    private function addActions() {
    }

    public function registerSettings(){
        $currentUser = wp_get_current_user();
        if (in_array('administrator', $currentUser->roles)) {
            $optionName = 'MPSLRolesSettingsFields';
            register_setting($optionName, $optionName);
            add_settings_section($optionName, '', array($this, 'MPSLRolesSettingsSecTxt'), 'motopress-slider-options');
            add_settings_field('motopressSliderRoles', __('Disable Slider for user groups:', 'motopress-slider-lite'), array($this, 'MPSLRolesSettingsFields'), 'motopress-slider-options', 'MPSLRolesSettingsFields');
        }
    }

    public function addMenu(){
        global $mpsl_settings;
        $currentUser = wp_get_current_user();
        $optionsHook = add_submenu_page($mpsl_settings['plugin_name'], __('Settings', 'motopress-slider-lite'), __('Settings', 'motopress-slider-lite'), 'manage_options', 'motopress-slider-options', array($this, 'renderPage'));
        add_action('load-' . $optionsHook, array($this, 'save'));
        $this->registerSettings();
    }

    public function MPSLRolesSettingsSecTxt(){}
    public function MPSLRolesSettingsFields(){
        global $wp_roles;
        if ( ! isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $disabledRoles = get_option('mpsl-disabled-roles', array());

        $roles = $wp_roles->get_names();
        unset($roles['administrator']);

        foreach ($roles as $role => $roleName ){
            $checked = '';
            if (in_array($role, $disabledRoles)){
                $checked = 'checked="checked"';
            }
            echo '<label><input type="checkbox" name="disabled_roles[]" value="'.$role.'" '.$checked. ' /> '.$roleName.'</label><br/>';
        }

        echo '<p class="description">' . __('Hide Slider menu for selected groups', 'motopress-slider-lite') . '</p>';
    }

}

