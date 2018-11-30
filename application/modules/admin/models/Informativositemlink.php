<?php

class Admin_Model_Informativositemlink extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Informativositemlink();
        $this->_table = "INFORMATIVOS_ITEM_LINK";
        $this->_sequence = "ID_INFORMATIVOS_ITEM_LINK";
    }
        
    public function fetchAll($id=null, $page=null, $lista=false) {
        
        $db = Zend_Registry::get('db2');
        $where = '';
        if(!is_null($id)){
           $where = " WHERE ID_INFORMATIVO_ITEM = $id ";
        }
        $res = $db->query("SELECT * FROM $this->_table $where ORDER BY LINK ASC");
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
//    public function deleteLink($id) {
//            $db = Zend_Registry::get('db2');            
//            try{
//                $return = $db->query("DELETE 
//                               FROM  PROCESSOS_LINK
//                               WHERE    ID = '$id'
//                               ");
//            $retorno = array(null,$return);
//            }catch (Exception $e){
//
//                $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
//            }
//            return $retorno;
//    }
    
}
