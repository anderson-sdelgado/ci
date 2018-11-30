<?php


class Admin_Model_Entrega extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Entrega();
        $this->_table   = "V_PC_ENTREGA_CW";
	$this->_primary = 'PEDIDO';
    }
    
}

