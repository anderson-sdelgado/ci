<?php

class Admin_Model_Informativositem extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable  = new Admin_Model_DbTable_Informativositem();
        $this->_table    = "INFORMATIVOS_ITEM";
        $this->_sequence = "ID_INFORMATIVOS_ITEM";
        $this->_primary  = "ID";
    }
    
    
    public function fetchAllUpdate($id) {
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT ID, ID_INFORMATIVO, DESCRICAO_SUMARIA, DESCRICAO_DETALHADA FROM $this->_table WHERE ID = '$id' ");
        $resultado = $res->fetchAll();
        return $resultado;
        
        
    }
    
    public function fetchAllPesquisa($informativo=null, $descricao=null, $page=null, $lista=true) {
        
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($informativo)){
           $and = " WHERE ID_INFORMATIVO = $informativo";
        }
        if(!is_null($descricao)){
           $and .= " AND DESCRICAO_SUMARIA LIKE '%$descricao%'";
        }
        $res = $db->query("SELECT ID, ID_INFORMATIVO, DESCRICAO_SUMARIA FROM $this->_table $and ORDER BY DESCRICAO_SUMARIA ASC");
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
                           ON B.PROCESSOS_ID = A.ID AND B.COD_FUNCAO = '$codigo'");
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
