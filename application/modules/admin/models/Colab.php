<?php

class Admin_Model_Colab extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Colab();
        $this->_table = "V_COLAB_ORG";
    }
    
    
    public function fetchAllUpdate($id) {
        /*
        $select = $this->_dbTable->getAdapter()->select()->from($this->_table, array('CD_CARGO', 'DESCR_FUNCAO'));//,'COD_FUNCAO_SUP','DESCR_FUNCAO_SUP','DESCR_SUMARIA','DESCR_DETALH','INFO_ADIC','DESAFIOS','MISSAO','AUTORIDADE'));//->where('ID = ?', (int) $id);
        return $this->_dbTable->getAdapter()->fetchAll($select);
        */
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                           CD_CARGO,
                           DESCR_FUNCAO
                  FROM     $this->_table";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
    }
    
    public function fetchAllPesquisa($codigo=null, $page=null, $lista=true) {
        /*$select = $this->_dbTable   ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.CD_CARGO','A.DESCR_FUNCAO'));*/
        $and = '';
        if(!is_null($codigo)){
           $and = " WHERE CD_CARGO = '$codigo'";
        }
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                           CD_CARGO,
                           DESCR_FUNCAO
                  FROM     $this->_table $and";
        
        $res = $db->query($query);
        $result = $res->fetchAll();
        //$result = $this->_dbTable->getAdapter()->fetchAll($select);
        
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    
    
}
