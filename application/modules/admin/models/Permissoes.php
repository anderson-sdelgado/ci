<?php

class Admin_Model_Permissoes extends Admin_Model_Abstract{
    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Permissoes();
        $this->_table   = "MENU_ACESSO";
    }
}
