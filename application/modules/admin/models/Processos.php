<?php

class Admin_Model_Processos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Processos();
        $this->_table = "PROCESSOS";
        $this->_sequence = "ID_PROCESSOS";
    }
    
    
    public function fetchAllUpdate($id) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT ID, CODIGO, DESCRICAO_SUMARIA, DESCRICAO_DETALHADA FROM $this->_table WHERE ID = '$id' ORDER BY CODIGO ASC");
        $resultado = $res->fetchAll();
        return $resultado;
        
        
    }
    
    public function fetchAllPesquisa($codigo=null, $page=null, $lista=true) {
        
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($codigo)){
           $and = " WHERE CODIGO LIKE '%$codigo%'";
        }
//        $res = $db->query("SELECT ID, CODIGO, DESCRICAO_SUMARIA FROM $this->_table $and ORDER BY to_number(replace(CODIGO,'.','')) ASC");
        $res = $db->query("SELECT ID, CODIGO, DESCRICAO_SUMARIA FROM $this->_table $and ORDER BY CODIGO ASC");
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function fetchAllProcessosCargos($codigo=null) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT   ID, 
                                    CODIGO, 
                                    DESCRICAO_SUMARIA,
                                    B.COD_FUNCAO,
                                    B.PROCESSOS_ID
                           FROM     $this->_table A 
                           LEFT JOIN CARGOS_PROCESSOS B
                           ON B.PROCESSOS_ID = A.ID AND B.COD_FUNCAO = '$codigo'
ORDER BY to_number(replace(CODIGO,'.','')) ASC");
        $result = $res->fetchAll();
        return $result;
    }
    
    public function fetchAllProcessosCargosRel($codigo=null) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT   ID, 
                                    CODIGO, 
                                    DESCRICAO_SUMARIA,
                                    B.COD_FUNCAO,
                                    B.PROCESSOS_ID
                           FROM     $this->_table A 
                           INNER JOIN CARGOS_PROCESSOS B
                           ON B.PROCESSOS_ID = A.ID AND B.COD_FUNCAO = '$codigo'
                           ORDER BY CODIGO");
        $result = $res->fetchAll();
        return $result;
    }
    
    public function fetchAllLinks($id=null, $page=null, $lista=false) {
        
        $db = Zend_Registry::get('db2');
        $where = '';
        if(!is_null($id)){
           $where = " WHERE ID_PROCESSO = $id ";
        }
        $res = $db->query("SELECT * FROM PROCESSOS_LINK $where ORDER BY LINK ASC");
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function deleteLink($id) {
            $db = Zend_Registry::get('db2');            
            try{
                $return = $db->query("DELETE 
                               FROM  PROCESSOS_LINK
                               WHERE    ID = '$id'
                               ");
            $retorno = array(null,$return);
            }catch (Exception $e){

                $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
            }
            return $retorno;
    }
    
}
