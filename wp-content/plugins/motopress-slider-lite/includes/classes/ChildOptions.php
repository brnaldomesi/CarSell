<?php

include_once dirname(__FILE__) . '/MPSLOptions.php';

abstract class MPSLChildOptions extends MPSLOptions {

    public function __construct() {
        parent::__construct();
    }

    abstract protected function load();
}