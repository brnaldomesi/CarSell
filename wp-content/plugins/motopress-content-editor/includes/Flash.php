<?php
/**
 * Description of MPCEFlash
 *
 */

class MPCEFlash {
    private static $cssClass = 'alert';

    public static function setFlash($messages, $type = 'warning') {
        switch($type) {
            case 'info':
                self::$cssClass .= ' alert-info';
                break;
            case 'success':
                self::$cssClass .= ' alert-success';
                break;
            case 'error':
                self::$cssClass .= ' alert-error';
                break;
        }
        echo '<div class="' . self::$cssClass . '">';
        if (is_array($messages)) {
            foreach ($messages as $message) {
                echo '<span>' . $message . '</span><br>';
            }
        } else {
            echo '<span>' . $messages . '</span>';
        }
        echo '</div>';
    }
}