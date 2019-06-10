<?php
/**
 * Description of MPCEAccess
 *
 * @author dima
 */
class MPCEAccess {
    private $capabilities = array(
        'read' => false,
        //'unfiltered_html' => false,
        'upload_files' => false,
        'post' => array(
            'edit_posts' => false
            /*
            'delete_posts' => false,
            'read_private_posts' => false,
            'edit_private_posts' => false,
            'delete_private_posts' => false
            */
        ),
        'page' => array(
            'edit_pages' => false
            /*
            'delete_pages' => false,
            'read_private_pages' => false,
            'edit_private_pages' => false,
            'delete_private_pages' => false
            */
        )
    );

    public function __construct() {
        global $motopressCESettings;

        /*if (isset($motopressCESettings['demo']) && $motopressCESettings['demo']) {
            if (isset($this->capabilities['unfiltered_html'])) {
                unset($this->capabilities['unfiltered_html']);
            }
        }*/
    }

    /**
     * @return boolean
     */
    public function hasAccess($postId = false) {
        require_once ABSPATH . WPINC . '/pluggable.php';

        if (!$postId) $postId = get_the_ID();

        $postType = get_post_type($postId);
        if ($postType !== 'page') $postType = 'post';

        $this->checkCapabilities($postId);

        return (is_user_logged_in() && !in_array(false, $this->capabilities, true) && !in_array(false, $this->capabilities[$postType], true) && !$this->isCEDisabledForCurRole()) ? true : false;
    }

    /*
     * @return boolean
     */
    public function isCEDisabledForCurRole(){
        $disabledRoles = get_option('motopress-ce-disabled-roles', array());
        $currentUser = wp_get_current_user();
        $currentUserRoles = $currentUser->roles;

        if (is_super_admin()) return false;
        
        foreach ($currentUserRoles as $key => $role) {
            if ( !in_array($role, $disabledRoles)){
                return false;
            }
        }
        // in case if all user rules are disabled
        return true;
    }

    /**
     * @param int $postId
     */
    private function checkCapabilities($postId) {
        foreach ($this->capabilities as $key => $value) {
            if (is_bool($value)) {
                $this->capabilities[$key] = current_user_can($key, $postId);
            } elseif (is_array($value)) {
                foreach ($value as $k => $v) {
                    $this->capabilities[$key][$k] = current_user_can($k, $postId);
                }
            }
        }
    }
}