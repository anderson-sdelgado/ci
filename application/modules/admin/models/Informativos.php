<?php

class Admin_Model_Informativos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable     = new Admin_Model_DbTable_Informativos();
        $this->_table       = "INFORMATIVOS";
        $this->_sequence    = "ID_INFORMATIVOS";
        $this->_primary     = "ID";
    }
    
    
    public function fetchAllUpdate($id) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT ID, DESCRICAO FROM $this->_table WHERE ID = '$id' ORDER BY DESCRICAO ASC");
        $resultado = $res->fetchAll();
        return $resultado;
        
        
    }
    
    public function getAll() {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT ID, DESCRICAO FROM $this->_table ORDER BY DESCRICAO ASC");
        $resultado = $res->fetchAll();
        return $resultado;
        
        
    }
    
    public function fetchAllPesquisa($descricao=null, $page=null, $lista=true) {
        
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($descricao)){
           $and = " WHERE DESCRICAO LIKE '%$descricao%'";
        }
        $res = $db->query("SELECT ID, DESCRICAO FROM $this->_table $and ORDER BY DESCRICAO ASC");
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function fetchAllInformativosCargos($codigo=null) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT   A.ID,
                                    A.DESCRICAO,
                                    B.DESCRICAO_SUMARIA,
                                    B.ID INFORMATIVOS_ITEM,
                                    C.COD_FUNCAO,
                                    C.ID_INFORMATIVOS_ITEM
                           FROM     $this->_table A
                           JOIN INFORMATIVOS_ITEM B
                           ON A.ID = B.ID_INFORMATIVO
                           
                           LEFT JOIN CARGOS_INFORMATIVOS_ITEM C
                           ON B.ID = C.ID_INFORMATIVOS_ITEM AND C.COD_FUNCAO = '$codigo' 
                           ORDER BY A.DESCRICAO,A.ID");
        $result = $res->fetchAll();
        return $result;
    }
    
    
    public function fetchAllInformativosCargo($codigo=null) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT   A.ID,
                                    A.DESCRICAO,
                                    B.DESCRICAO_SUMARIA,
                                    B.ID INFORMATIVOS_ITEM,
                                    C.COD_FUNCAO,
                                    C.ID_INFORMATIVOS_ITEM
                           FROM     $this->_table A
                           JOIN INFORMATIVOS_ITEM B
                           ON A.ID = B.ID_INFORMATIVO
                           
                           JOIN CARGOS_INFORMATIVOS_ITEM C
                           ON B.ID = C.ID_INFORMATIVOS_ITEM AND C.COD_FUNCAO = '$codigo' 
                           ORDER BY A.DESCRICAO,A.ID");
        $result = $res->fetchAll();
        return $result;
    }
    
    
    
    
}
