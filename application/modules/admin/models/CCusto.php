<?php

class Admin_Model_CCusto extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_CCusto();
        $this->_table = "V_CCUSTO_ORG";
		$this->_table2 = "V_CCUSTO_ORG_ATIVO";
	$this->_primary = 'CCUSTO_ID';
    }
    
    
    public function fetchAllUpdate($id) {        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT * FROM $this->_table WHERE to_number(replace(CD_CCUSTO,'.','')) = $id");
        $resultado = $res->fetchAll();
        return $resultado[0];
        
    }
    
    public function fetchAllPesquisa($codigo=null, $page=null, $lista=true) {
        $db = Zend_Registry::get('db2');
        
        $and = '';
        if(!is_null($codigo)){
           $and = " WHERE COD_FUNCAO = '$codigo'";
        }
        
        $res = $db->query("SELECT CD_CCUSTO, NOME_CCUSTO FROM $this->_table $and");
        $result = $res->fetchAll();
        
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function fetchAllCustosCargos($codigo=null) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT A.CCUSTO_ID, A.CD_CCUSTO, A.NOME_CCUSTO, B.COD_FUNCAO AS CARGO
                           FROM $this->_table A
                           LEFT JOIN CARGOS_CCUSTO B
                           ON B.COD_FUNCAO = '$codigo' AND to_char(B.CD_CCUSTO) = to_char(A.CD_CCUSTO)
                           ORDER BY A.CD_CCUSTO ASC
                           ");
        $result = $res->fetchAll();
        
        return $result;
    }
    
    public function fetchCustoCargo($id_cargo = null){
        $db = Zend_Registry::get('db2');
        $where = "";
        if(!is_null($id_cargo)){
           $where = "JOIN CARGOS_CCUSTO B
                     ON to_char(B.CD_CCUSTO) = to_char(A.CD_CCUSTO)
                     WHERE B.COD_FUNCAO = '$id_cargo' ";
        }
        $query = "SELECT   to_number(replace(A.CD_CCUSTO,'.','')) as CUSTO, A.NOME_CCUSTO
                           FROM $this->_table A
                           $where
                           ORDER BY to_number(replace(A.CD_CCUSTO,'.','')) ASC";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
    
    public function fetchAllCusto(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT   to_number(replace(A.CD_CCUSTO,'.','')) as CUSTO,
                           A.CD_CCUSTO,
                           A.NOME_CCUSTO
                           FROM $this->_table A 
                  ORDER BY A.NOME_CCUSTO,to_number(replace(A.CD_CCUSTO,'.','')) ASC";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
    
    public function fetchAllAnaliticos(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT   to_number(replace(A.CD_CCUSTO,'.','')) as CUSTO,
                           A.CD_CCUSTO,
                           A.NOME_CCUSTO
                           FROM $this->_table A 
                  WHERE A.TIPO = 'ANALITICO'
                  ORDER BY A.NOME_CCUSTO,to_number(replace(A.CD_CCUSTO,'.','')) ASC";
        $res = $db->query($query);
        $result = $res->fetchAll();        
        return $result;
    }   

    public function fetchAllAnaliticos2(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT   to_number(replace(A.CD_CCUSTO,'.','')) as CUSTO,
                           A.CD_CCUSTO,
                           A.NOME_CCUSTO
                           FROM $this->_table2 A 
                  WHERE A.TIPO = 'ANALITICO'
                  ORDER BY A.NOME_CCUSTO,to_number(replace(A.CD_CCUSTO,'.','')) ASC";
        $res = $db->query($query);
        $result = $res->fetchAll();        
        return $result;
    } 	
    
}
