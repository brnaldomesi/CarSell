<?php
/**
 * Description of MPCEGroup
 *
 */
class MPCEGroup extends MPCEElement {
    public $objects = array();

    protected $errors = array(
        'id' => array(),
        'name' => array(),
        'icon' => array(),
        //'title' => array(),
        'position' => array(),
        'show' => array(),
        'objects' => array()
    );

    const ICON_DIR = 'group';

    public function __construct() {
        $this->setIcon('no-group.png');
    }

    public function setIcon($icon) {
        parent::icon($icon, self::ICON_DIR);
    }

    /**
     * @return MPCEObject[]
     */
    public function getObjects() {
        return $this->objects;
    }

    /**
     * @param string $id
     * @return MPCEObject|boolean
     */
    public function &getObject($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->objects)) {
                        return $this->objects[$id];
                    }
                }
            }
        }
        $object = false;
        return $object;
    }

    /**
     * @param MPCEObject|MPCEObject[] $object
     */
    public function addObject($object) {
        global $motopressCELang;

        if ($object instanceof MPCEObject) {
            if ($object->isValid()) {
                if (!array_key_exists($object->getId(), $this->objects)) {
                    $this->objects[$object->getId()] = $object;
                }
            } else {
                if (!MPCELibrary::$isAjaxRequest) {
                    $object->showErrors();
                }
            }
        } elseif (is_array($object)) {
            if (!empty($object)) {
                foreach ($object as $obj) {
                    if ($obj instanceof MPCEObject) {
                        if ($obj->isValid()) {
                            if (!array_key_exists($obj->getId(), $this->objects)) {
                                $this->objects[$obj->getId()] = $obj;
                            }
                        } else {
                            if (!MPCELibrary::$isAjaxRequest) {
                                $obj->showErrors();
                            }
                        }
                    }
                }
            } else {
                $this->addError('objects', $motopressCELang->CEEmpty);
            }
        } else {
            $this->addError('objects', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($object))));
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function removeObject($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    $unremoved = array(MPCEShortcode::PREFIX . 'text', MPCEShortcode::PREFIX . 'code');
                    if (array_key_exists($id, $this->objects) && !in_array($id, $unremoved)) {
                        unset($this->objects[$id]);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function isValid() {
        return (
            empty($this->errors['id']) &&
            empty($this->errors['name']) &&
            empty($this->errors['icon']) &&
            //empty($this->errors['title']) &&
            empty($this->errors['position']) &&
            empty($this->errors['show']) &&
            empty($this->errors['objects'])
        ) ? true : false;
    }

    /**
     * @return string
     */
    public function __toString() {
        $str = 'id: ' . $this->getId() . ', ';
        $str .= 'name: ' . $this->getName() . ', ';
        $str .= 'icon: ' . $this->getIcon() . ', ';
        //$str .= 'title: ' . $this->getTitle() . ', ';
        $str .= 'position: ' . $this->getPosition() . ', ';
        $str .= 'show: ' . $this->getShow();
        return $str;
    }
}