<?php

class Admin_Model_LogAcesso extends Admin_Model_Abstract {

 public function __construct() {
        $this->_dbTable = new Default_Model_DbTable_LogAcesso();
        $this->_table = "LOG_ACESSO";
        $this->_primary = "ID";
        $this->_sequence = "ID_LOG";
    }
    
    public function __save($data){
        
        if (@$data["ID"]) {
            return $this->_update($data);
        } else {
            return $this->_insert($data);
        }
        
    }
    
    public function fetchAllPesquisa($data=null,$login=null, $page=null, $lista=true) {
        
        $db = Zend_Registry::get('db2');
        $where_login = "";        
        if(!is_null($login)){
           $where_login = " AND UPPER(B.LOGIN) like '".strtoupper($login)."'";
        }
        if(!is_null($data)){
           $where_login .= " AND TO_CHAR(A.DATA_LOGIN, 'dd/mm/yyyy') = '$data'";
        }
        $query = "SELECT B.ID AS CODIGO, 
                         B.LOGIN,
                         TO_CHAR(A.DATA_LOGIN, 'DD/MM/YYYY HH24:MI') DATA_LOGIN,
                         TO_CHAR(A.DATA_LOGOUT, 'DD/MM/YYYY HH24:MI') DATA_LOGOUT
                  FROM  $this->_table A, USUARIOS B
                      WHERE A.ID_USUARIO = B.ID
                      AND A.DATA_LOGIN IS NOT NULL
                      $where_login
                  ORDER BY A.DATA_LOGIN DESC";
        
        $res = $db->query($query);
        $result = $res->fetchAll();
        
//        
//        
//        $select = $this->_dbTable->getAdapter()
//                                 ->select()
//                                 ->from(array('A' => $this->_table))
//                                 ->order('DATA_LOGIN DESC');
        
//        if(!is_null($login)){
//           $select->Where("A.ID=?", $login);
//        }        

        //echo $select->__toString();exit;
//        $result = $this->_dbTable->getAdapter()->fetchAll($select);
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }

//    public function delete() {
//        return @$this->_dbTable->delete();
//    }
}

