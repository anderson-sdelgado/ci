<?php

class Admin_Model_CiValorTipoDespesa extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_CiValorTipoDespesa();
        $this->_table   = "CI_VALOR_TIPO_DESPESA";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_VALOR_TIPO_DESPESA";
    }
   
    public function fetchAllPesquisa($finalidade, $ci = null, $ativo = null) {
        
        if(!is_null($ativo)){
            $where_ativo1 = " AND ( A.ATIVO = '$ativo' OR B.ID IS NOT NULL )";
            $where_ativo2 = " AND A.ATIVO = '$ativo' ";
        }else{
            $where_ativo1 = "";
            $where_ativo2 = "";
        }
        $db = Zend_Registry::get('db2');
        if(!is_null($ci)){
            $select = "SELECT A.ID AS FINALIDADE_TIPO_DESPESA, "
                . "A.CI_FINALIDADE, "
                . "A.TIPO, "
                . "A.DESCRICAO, "
                . "A.ATIVO, "
                . "B.ID, "
                . "B.CI, "
                . "TRIM(TO_CHAR(B.VALOR, '9999999999999.99')) VALOR "
                . "FROM CI_FINALIDADE_TIPO_DESPESA A LEFT JOIN $this->_table B "
                . "ON A.ID = B.FINALIDADE_TIPO_DESPESA  AND B.CI = $ci "
                . "WHERE A.CI_FINALIDADE = $finalidade "
                . "$where_ativo1"
                . "ORDER BY A.ORDEM, A.DESCRICAO ASC";
        }else{
            $select = "SELECT A.ID AS FINALIDADE_TIPO_DESPESA, "
                . "A.CI_FINALIDADE, "
                . "A.TIPO, "
                . "A.DESCRICAO, "
                . "A.ATIVO, "
                . "NULL ID, "
                . "NULL CI, "
                . "NULL VALOR "
                . "FROM CI_FINALIDADE_TIPO_DESPESA A "
                . "WHERE A.CI_FINALIDADE = $finalidade "
                . "$where_ativo2"
                . "ORDER BY A.ORDEM, A.DESCRICAO ASC";
        }
        
        
        $res = $db->query($select);
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
