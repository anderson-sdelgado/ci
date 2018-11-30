<?php

class Admin_Model_Arquivos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Arquivos();
        $this->_table = "CARGOS_ARQUIVOS";
	   $this->_sequence = 'ID_ARQUIVO';
		
    }
        
    public function deleterelacionamentocargo($id, $data=array()) {
        return $this->_dbTable->update($data, array("COD_FUNCAO=?"=>$id));
    }
    
    public function fetchAllArquivos($id_cargo){
                                    
        /*$select = $this->_dbTable ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.COD_FUNCAO','A.ARQUIVO'));
        
        if(!is_null($id_cargo)){
           $select->Where("A.COD_FUNCAO = '$id_cargo'");
        }
        
        //echo $select->__toString();exit;
        $result = $this->_dbTable->getAdapter()->fetchAll($select);*/
        $and = '';
        if(!is_null($id_cargo)){
           $and = " WHERE A.COD_FUNCAO = '$id_cargo'";
        }
        $db = Zend_Registry::get('db2');
        $query = "SELECT A.COD_FUNCAO, A.ARQUIVO, A.TIPO FROM $this->_table A $and";
        $res = $db->query($query);        
        $result = $res->fetchAll();
        
        return $result;
    }
    
    public function _delete($id_cargo,$arquivo){
        /*$where = array();
        $where[] = $this->_dbTable->getAdapter()->quoteInto('COD_FUNCAO = ?', $id_cargo);
        $where[] = $this->_dbTable->getAdapter()->quoteInto('ARQUIVO = ?', $arquivo);
        //var_dump($where);exit;
        return $this->_dbTable->delete($where);*/
        $db = Zend_Registry::get('db2');
        $return = $db->query("DELETE 
                               FROM  $this->_table
                               WHERE    COD_FUNCAO = '$id_cargo'
                               AND      ARQUIVO = '$arquivo'
                               ");
            
            return array(null,$return);
    }
}
