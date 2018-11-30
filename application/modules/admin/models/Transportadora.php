<?php


class Admin_Model_Transportadora extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Transportadora();
        $this->_table   = "V_TRANSP_CW";
	$this->_primary = 'CODIGO_FORNEC';
    }
    
}

