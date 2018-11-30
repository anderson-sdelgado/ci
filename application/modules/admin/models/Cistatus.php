<?php


class Admin_Model_Cistatus extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Cistatus();
        $this->_table   = "CI_STATUS";
	$this->_primary = 'ID';
    }
    
    public function fetchAll() {
        
        $db = Zend_Registry::get('db2');        
        $res = $db->query("SELECT * FROM $this->_table ");
        $result = $res->fetchAll();
        return $result;
    }
    
}

