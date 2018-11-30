<?php

class Admin_Model_LogErro extends Admin_Model_Abstract {

 public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_LogErro();
        $this->_table = "registro_log_erro";
    }
    
    public function fetchAllPesquisa($nome=null, $page=null, $lista=true) {
        
        $select = $this->_dbTable->getAdapter()
                                 ->select()
                                 ->from(array('A' => $this->_table))
                                 ->order('DATA DESC');
        
        if(!is_null($nome)){
           $select->Where("A.NOME LIKE '%$nome%'");
        }

        //echo $select->__toString();exit;
        $result = $this->_dbTable->getAdapter()->fetchAll($select);
        if($lista){
            return $this->setPaginator($result, $page, $this->getBuscaRegPagina());
        }else{
            return $result;
        }
    }

//    public function delete() {
//        return @$this->_dbTable->delete();
//    }
}

