<?php
/**
 * Parent class for MPCEGroup and MPCEObject
 *
 * @abstract
 */
abstract class MPCEElement extends MPCEBaseElement {
    /**
     * @var int
     */
    public $position = 0;
    /**
     * @var bool
     */
    public $show = true;

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @global stdClass $motopressCELang
     * @param int $position
     */
    public function setPosition($position) {
        global $motopressCELang;

        if (is_int($position)) {
            $min = 0;
            $max = 200;
            $position = filter_var($position, FILTER_VALIDATE_INT, array('options' => array('min_range' => $min, 'max_range' => $max)));
            if ($position !== false) {
                $this->position = $position;
            } else {
                $this->addError('position', strtr($motopressCELang->CEPositionValidation, array('%min%' => $min, '%max%' => $max)));
            }
        } else {
            $this->addError('position', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($position))));
        }
    }

    /**
     * @return boolean
     */
    public function getShow() {
        return $this->show;
    }

    /**
     * @global stdClass $motopressCELang
     * @param boolean $show
     */
    public function setShow($show) {
        global $motopressCELang;

        if (is_bool($show)) {
            $this->show = $show;
        } else {
            $this->addError('show', strtr($motopressCELang->CEInvalidArgumentType, array('%name%' => gettype($show))));
        }
    }
}
