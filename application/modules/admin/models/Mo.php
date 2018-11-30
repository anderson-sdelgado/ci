<?php

class Admin_Model_Mo extends Admin_Model_Abstract{
    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Mo();
        $this->_table   = "CARGOS_MO";
        $this->_sequence = 'ID_MO';
    }
    
    
    public function fetchAllPesquisa($codigo=null) {
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT COD_FUNCAO, MO FROM $this->_table WHERE COD_FUNCAO = '$codigo'");
        $result = $res->fetchAll();
        /*
        $select = $this->_dbTable   ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.COD_FUNCAO','A.MO'))
                                    ->Where("A.COD_FUNCAO = '$codigo'");
        //echo $select->__toString();
        $result = $this->_dbTable->getAdapter()->fetchAll($select);*/
        
        return $result;
    }
    
    public function _delete($id){
        /*$where = $this->_dbTable->getAdapter()->quoteInto('COD_FUNCAO = ?', $id);
        return $this->_dbTable->delete($where);*/
        $db = Zend_Registry::get('db2');
        $return = $db->query("DELETE FROM $this->_table WHERE COD_FUNCAO = '$id'");
        return array(null,$return);
    }
}
