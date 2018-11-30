<?php

class Admin_Model_Processoslink extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Processoslink();
        $this->_table = "PROCESSOS_LINK";
        $this->_sequence = "ID_PROCESSOS_LINK";
    }
        
    public function fetchAll($id=null, $page=null, $lista=false) {
        
        $db = Zend_Registry::get('db2');
        $where = '';
        if(!is_null($id)){
           $where = " WHERE ID_PROCESSO = $id ";
        }
        $res = $db->query("SELECT * FROM PROCESSOS_LINK $where ORDER BY DESCRICAO ASC");
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
}
