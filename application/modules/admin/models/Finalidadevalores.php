<?php

class Admin_Model_Finalidadevalores extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Finalidadevalores();
        $this->_table   = "CI_FINALIDADE_VALOR";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_FINALIDADE_VALOR";
    }
   
    public function fetchAllPesquisa($id=null, $finalidade = null, $valor_inicial=null, $valor_final=null, $ativo=null) {
        
        $db = Zend_Registry::get('db2');
        $where  = '';
        $and    = '';
        $and_ativo    = '';
        if(!is_null($valor_inicial) && !is_null($valor_final)){
           $where = " WHERE A.VALOR_INICIAL BETWEEN to_number('$valor_inicial') AND to_number('$valor_final') ";
        }elseif(!is_null($valor_inicial)){
           $where = " WHERE to_number('$valor_inicial') BETWEEN A.VALOR_INICIAL AND A.VALOR_FINAL ";
        }elseif(!is_null($valor_final)){
           $where = " WHERE to_number('$valor_final') BETWEEN A.VALOR_INICIAL AND A.VALOR_FINAL ";
        }
        if(!is_null($id)){
           $and = " AND A.ID <> $id ";
        }
        if(!is_null($finalidade)){
           $and .= " AND A.FINALIDADE = $finalidade ";
        }        
        if(!is_null($ativo)){
           $and_ativo = " AND B.ATIVO = '$ativo' ";
        }        
        $res = $db->query("SELECT A.ID, 
                                  trim(to_char(A.VALOR_INICIAL, '9999999999999.99')) VALOR_INICIAL, 
                                  trim(to_char(A.VALOR_FINAL, '9999999999999.99')) VALOR_FINAL, 
                                  A.APROVADORES, 
                                  A.FINALIDADE,
                                  B.DESCRICAO DESC_FINALIDADE
                          FROM $this->_table A 
                          JOIN CI_FINALIDADE B
                          ON A.FINALIDADE = B.ID
                          $and_ativo
                $where $and ORDER BY B.DESCRICAO, A.VALOR_INICIAL ASC");
        $result = $res->fetchAll();
        
            return $result;
    }
    
    public function _find($id=null) {
        
        $db = Zend_Registry::get('db2');
        $where  = '';
        if(!is_null($id)){
           $where = " WHERE ID = $id ";
        }
        $res = $db->query("SELECT ID, to_char(VALOR_INICIAL, '9999999999999.99') VALOR_INICIAL, to_char(VALOR_FINAL, '9999999999999.99') VALOR_FINAL, APROVADORES, FINALIDADE  FROM $this->_table $where ORDER BY VALOR_INICIAL ASC");
        $result = $res->fetchAll();
        
        return $result[0];
    }
    
    
}
