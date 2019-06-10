<?php
/**
 * Class MPCETemplate
 */
class MPCETemplate extends MPCEBaseElement {
    public $content;
    protected $errors = array(
        'id' => array(),
        'name' => array(),
        'icon' => array(),
        'content' => array()
    );

    const ICON_DIR = 'template';

    /**
     * @param $id
     * @param $name
     * @param $content
     * @param string $icon [optional]
     */
    function __construct($id, $name, $content, $icon = 'no-template.png') {
        $this->setId($id);
        $this->setName($name);
        if (empty($icon)) {
            $icon = 'no-template.png';
        }
        $this->setIcon($icon); //size 85x142 px
        $this->setContent($content);
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon) {
        parent::icon($icon, self::ICON_DIR);
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        global $motopressCELang;

        if (is_string($content)) {
            $content = trim($content);
            if (!empty($content)) {
                $content = filter_var($content, FILTER_UNSAFE_RAW);
                $this->content = $content;
            } else {
                $this->addError('content', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('content', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($content))));
        }
    }

    /**
     * @return boolean
     */
    public function isValid() {
        return (
            empty($this->errors['id']) &&
            empty($this->errors['name']) &&
            empty($this->errors['icon']) &&
            empty($this->errors['content'])
        ) ? true : false;
    }

    /**
     * @return string
     */
    public function __toString() {
        $str = 'id: ' . $this->getId() . ', ';
        $str .= 'name: ' . $this->getName() . ', ';
        $str .= 'icon: ' . $this->getIcon();
        return $str;
    }
}