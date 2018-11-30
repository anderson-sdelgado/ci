<?php


class Admin_Model_Empresacobranca extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Empresacobranca();
        $this->_table   = "V_EMPRESA_COBRANCA_CW";
	$this->_primary = 'CODIGO_EMPRESA';
    }
    
}

