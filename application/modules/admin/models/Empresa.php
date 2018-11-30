<?php


class Admin_Model_Empresa extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Empresa();
        $this->_table   = "V_EMPRESA_CW";
	$this->_primary = 'CODIGO_EMPRESA';
    }
    
}

