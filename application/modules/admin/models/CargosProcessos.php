<?php

class Admin_Model_CargosProcessos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_CargosProcessos();
        $this->_table = "CARGOS_PROCESSOS";
    }
    
    public function fetchAllDelete($id_processo){
        /*$select = $this->_dbTable->getAdapter()
                        ->select()
                        ->from($this->_table, array('PROCESSOS_ID', 'COD_FUNCAO'))
                        ->where('PROCESSOS_ID = ?', (int) $id_processo);
        return $this->_dbTable->getAdapter()->fetchAll($select);*/
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT PROCESSOS_ID, COD_FUNCAO FROM $this->_table WHERE PROCESSOS_ID = '$id_processo'");
        $resultado = $res->fetchAll();
        return $resultado;
    }
    
    public function fetchAllUpdate($id) {
        /*$select = $this->_dbTable->getAdapter()->select()->from($this->_table, array('PROCESSOS_ID', 'COD_FUNCAO'));//->where('ID = ?', (int) $id);
        return $this->_dbTable->getAdapter()->fetchAll($select);
        //print_r($this->_dbTable->getAdapter()->fetchRow($select));*/
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT PROCESSOS_ID, COD_FUNCAO FROM $this->_table WHERE ID = '$id'");
        $resultado = $res->fetchAll();
        return $resultado;
        
    }
    
    public function fetchAllPesquisa($desc = null, $page = null, $lista = true, $id_processo = null) {
        /*$select = $this->_dbTable   ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.PROCESSOS_ID','A.COD_FUNCAO'))
                                    ->join(array('B' => 'PROCESSOS'), "B.ID = A.PROCESSOS_ID",array('B.DESCRICAO_SUMARIA'))
                                    ->join(array('C' => 'V_CARGOS_ORG'), "C.COD_FUNCAO = A.COD_FUNCAO",array('C.DESCR_FUNCAO'))
                                    ->order('C.DESCR_FUNCAO ASC');
        
        if(!is_null($desc)){
           $select->Where("C.DESCR_FUNCAO LIKE '%$desc%'");
        }
        
        if(!is_null($id_processo)){
           $select->Where("A.PROCESSOS_ID = '$id_processo'");
        }
        //echo $select->__toString();exit;
        $result = $this->_dbTable->getAdapter()->fetchAll($select);*/
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($desc)){
           $and = " WHERE C.DESCR_FUNCAO LIKE '%$desc%' ";
        }
        
        $res = $db->query("SELECT   A.PROCESSOS_ID, 
                                    A.COD_FUNCAO,
                                    B.DESCRICAO_SUMARIA,
                                    C.DESCR_FUNCAO
                           FROM $this->_table A 
                           INNER JOIN PROCESSOS B
                           ON B.ID = A.PROCESSOS_ID
                           INNER JOIN V_CARGOS_ORG C
                           ON C.COD_FUNCAO = A.COD_FUNCAO
                           $and
                           ORDER BY C.DESCR_FUNCAO ASC");
        $result = $res->fetchAll();
        
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function deleterelacionamentocargo($id, $esconder=false, $data=array()) {
        $db = Zend_Registry::get('db2');
            
        //var_dump($id);exit;
        if(!$esconder){
            $return = $db->query("DELETE 
                               FROM  $this->_table
                               WHERE    COD_FUNCAO = '$id'
                               ");
            
            return array(null,$return);
            /*$where = $this->_dbTable->getAdapter()->quoteInto('COD_FUNCAO = ?', $id);
            return $this->_dbTable->delete($where);*/
        }else{
            return $this->_dbTable->update($data, array("ID=?"=>$data['ID']));
        }
    }
    
    public function fetchAllArquivos($codigo = null, $page = null, $lista = true, $id_processo = null){
                                    
        /*$select = $this->_dbTable   ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.PROCESSOS_ID','A.COD_FUNCAO'))
                                    ->join(array('B' => 'PROCESSOS'), "B.ID = A.PROCESSOS_ID",array('B.DESCRICAO_SUMARIA'))
                                    ->join(array('C' => 'V_CARGOS_ORG'), "C.COD_FUNCAO = A.COD_FUNCAO",array('C.DESCR_FUNCAO'))
                                    ->order('C.DESCR_FUNCAO ASC');
        
        if(!is_null($codigo)){
           $select->Where("A.COD_FUNCAO = '$codigo'");
        }
        
        if(!is_null($id_processo)){
           $select->Where("A.PROCESSOS_ID = '$id_processo'");
        }
        //echo $select->__toString();exit;
        $result = $this->_dbTable->getAdapter()->fetchAll($select);*/
        $db = Zend_Registry::get('db2');
        $and = ' WHERE 1 = 1';
        if(!is_null($codigo)){
           $and = (" AND A.COD_FUNCAO = '$codigo' ");
        }
        
        if(!is_null($id_processo)){
           $and = (" AND A.PROCESSOS_ID = '$id_processo' ");
        }
        $query = "SELECT A.PROCESSOS_ID,
                         A.COD_FUNCAO,
                         B.DESCRICAO_SUMARIA,
                         C.DESCR_FUNCAO
                  FROM   $this->_table A
                  INNER JOIN PROCESSOS B
                  ON B.ID = A.PROCESSOS_ID
                  INNER JOIN V_CARGOS_ORG C
                  ON C.COD_FUNCAO = A.COD_FUNCAO";
        $res = $db->query($query);
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
        
    }
    
    
}
