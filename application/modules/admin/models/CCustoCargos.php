<?php

class Admin_Model_CCustoCargos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_CCustoCargos();
        $this->_table = "CARGOS_CCUSTO";
        $this->_sequence = 'ID_CARGOS_CCUSTO';

    }
        
    public function deleterelacionamentocargo($id, $data=array()) {
        $db = Zend_Registry::get('db2');
        $return = $db->query("DELETE 
                               FROM  CARGOS_CCUSTO
                               WHERE    COD_FUNCAO = '$id'
                               ");
            
        return array(null,$return);
        
        /*$where = $this->_dbTable->getAdapter()->quoteInto('COD_FUNCAO = ?', $id);
        return $this->_dbTable->delete($where);*/        
        //return $this->_dbTable->update($data, array("COD_FUNCAO=?"=>$id));
    }
}
