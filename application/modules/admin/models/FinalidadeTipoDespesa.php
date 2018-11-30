<?php

class Admin_Model_FinalidadeTipoDespesa extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_FinalidadeTipoDespesa();
        $this->_table   = "CI_FINALIDADE_TIPO_DESPESA";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_FINALIDADE_TIPO_DESPESA";
    }
   
    public function fetchAllPesquisa($finalidade = null, $ativo = null) {
        
        $db = Zend_Registry::get('db2');
        $where  = null;
        
        if(!is_null($finalidade)){
           $where = " WHERE CI_FINALIDADE = $finalidade ";
        }
        if(!is_null($ativo)){
            if(!is_null($finalidade)){
                $where.= " AND ATIVO LIKE '$ativo' ";
            }else{
                $where = " WHERE ATIVO LIKE '$ativo' ";
            }           
        }
        $res = $db->query("SELECT ID, CI_FINALIDADE, TIPO, DESCRICAO, ATIVO FROM $this->_table $where ORDER BY ORDEM, DESCRICAO ASC");
        $result = $res->fetchAll();
        
            return $result;
    }
    
    public function _find($id=null) {
        
        $db = Zend_Registry::get('db2');
        $where  = '';
        if(!is_null($id)){
           $where = " WHERE ID = $id ";
        }
        $res = $db->query("SELECT ID, CI_FINALIDADE, TIPO, DESCRICAO, ATIVO FROM $this->_table $where ORDER BY DESCRICAO ASC");
        $result = $res->fetchAll();
        
        return $result[0];
    }
    
    
}
