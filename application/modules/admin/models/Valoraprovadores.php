<?php

class Admin_Model_Valoraprovadores extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Valoraprovadores();
        $this->_table   = "CI_VALOR_APROVADORES";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_VALOR_APROVADORES";
    }
   
    public function fetchAllPesquisa($id=null, $valor_inicial=null, $valor_final=null) {
        
        $db = Zend_Registry::get('db2');
        $where  = '';
        $and    = '';
        if(!is_null($valor_inicial) && !is_null($valor_final)){
           $where = " WHERE VALOR_INICIAL BETWEEN to_number('$valor_inicial') AND to_number('$valor_final') ";
        }elseif(!is_null($valor_inicial)){
           $where = " WHERE to_number('$valor_inicial') BETWEEN VALOR_INICIAL AND VALOR_FINAL ";
        }elseif(!is_null($valor_final)){
           $where = " WHERE to_number('$valor_final') BETWEEN VALOR_INICIAL AND VALOR_FINAL ";
        }
        if(!is_null($id)){
           $and = " AND ID <> $id ";
        }
        $res = $db->query("SELECT ID, 
                                  trim(to_char(VALOR_INICIAL, '9999999999999.99')) VALOR_INICIAL, 
                                  trim(to_char(VALOR_FINAL, '9999999999999.99')) VALOR_FINAL, 
                                  APROVADORES, 
                                  EMAILCOPIA 
                          FROM $this->_table $where $and ORDER BY VALOR_INICIAL ASC");
        $result = $res->fetchAll();
        
            return $result;
    }
    
    public function _find($id=null) {
        
        $db = Zend_Registry::get('db2');
        $where  = '';
        if(!is_null($id)){
           $where = " WHERE ID = $id ";
        }
        $res = $db->query("SELECT ID, to_char(VALOR_INICIAL, '9999999999999.99') VALOR_INICIAL, to_char(VALOR_FINAL, '9999999999999.99') VALOR_FINAL, APROVADORES, EMAILCOPIA  FROM $this->_table $where ORDER BY VALOR_INICIAL ASC");
        $result = $res->fetchAll();
        
        return $result[0];
    }
    
    
}
