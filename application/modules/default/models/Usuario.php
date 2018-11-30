<?php

class Default_Model_Usuario extends Default_Model_Abstract {

 public function __construct() {
        $this->_dbTable = new Default_Model_DbTable_Usuario();
        $this->_table = "USUARIO";
    }

    public function getAll($params) {

        //Zend_Debug::dump(parent::fetchAll());
    }
}

