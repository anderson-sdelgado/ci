<?php


class Admin_Model_Fornecedor extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Fornecedor();
        $this->_table   = "V_FORN_CW";
	$this->_primary = 'CODIGO_FORNEC';
    }
    
}

