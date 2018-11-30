<?php


class Admin_Model_Prazopagamento extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Prazopagamento();
        $this->_table   = "V_PRAZO_PAGTO_CW";
//	$this->_primary = 'PEDIDO';
    }
    
    public function Prazo_pedido($pedido){
        $db = Zend_Registry::get('db2');
        $query = "SELECT * FROM  $this->_table 
                  WHERE PEDIDO = $pedido";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
}

