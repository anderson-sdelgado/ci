<?php


class Admin_Model_CiAnexo extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable     = new Admin_Model_DbTable_CiAnexo();
        $this->_table       = "CI_ANEXO";
	$this->_primary     = 'ID';
	$this->_sequence    = 'ID_CI_ANEXO';
    }
    
    public function fetchAll($ci=null) {
        
        $db = Zend_Registry::get('db2');
        $where = '';
        if(!is_null($ci)){
           $where = " WHERE CI = $ci";
        }
        $res = $db->query("SELECT * FROM $this->_table $where");
        $result = $res->fetchAll();
        return $result;
    }
    
}

