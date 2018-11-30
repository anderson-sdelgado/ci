<?php

class Default_Model_LogAcesso extends Default_Model_Abstract {

 public function __construct() {
        $this->_dbTable = new Default_Model_DbTable_LogAcesso();
        $this->_table = "registro_log_acesso";
    }
}

