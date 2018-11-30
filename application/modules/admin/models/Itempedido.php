<?php


class Admin_Model_Itempedido extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Itempedido();
        $this->_table   = "V_ITENS_PC_CW";
	   //$this->_sequence = 'ID_MO';
    }
    
    
    public function itens($pedido){
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                    PEDIDO,
                    QTDE,
                    UNIDADE,
                    COD_PRODUTO,
                    DESCR_PRODUTO,
                    to_char(PRECO_UNIT, '999999.99') PRECO_UNIT,
                    to_char(VALOR_TOTAL_ITEM, '999999999.99') VALOR_TOTAL_ITEM,
                    to_char(PERC_IPI, '99.99') PERC_IPI,
                    to_char(PERC_DESC, '99.99') PERC_DESC,
                    TO_CHAR(PRAZO_ENTREGA, 'dd/mm/yyyy') PRAZO_ENTREGA,
                    OBS_ITEM,
                    MARCA,
                    CLASSIFICAO,
                    EMBALAGEM
                FROM  $this->_table 
                  WHERE PEDIDO = $pedido";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    public function __save($data){
        return $this->_insert($data);
    }
}

