<?php


class Admin_Model_Pedidocontrole extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Pedidocontrole();
        $this->_table   = "PEDIDO";
	$this->_primary = 'ID_PEDIDO';
    }
    
    public function __save($data){
        
        if (count( $this->find($data["ID_PEDIDO"])) != 0) {
            return $this->_dbTable->update($data, array("ID_PEDIDO=?"=>$data["ID_PEDIDO"]));
        } else {
            return $this->_insert($data);
        }
        
    }
    
    
}

