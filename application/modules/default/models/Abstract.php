<?php

/**
 * Description of Abstract
 *
 * @author wesleywillians
 */
abstract class Default_Model_Abstract {

    protected $_dbTable;
    protected $_table;

    public function save(array $data) {

        if (@$data['id'] != 0) {
            return $this->_update($data);
        } else {
            return $this->_insert($data);
        }
    }
    
    public function find($id) {
        return $this->_dbTable->find((int) $id)->current();
    }

    public function fetchPairs() {
        $select = $this->_dbTable->getAdapter()->select()->from($this->_table, array('id', 'name'));
        return $this->_dbTable->getAdapter()->fetchPairs($select);
    }

    protected function _insert(array $data) {
        try {
            $retorno = $this->_dbTable->insert($data);
            $retorno = array(null,$retorno);
        }catch (Exception $e){

            $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
	}
	return $retorno;
    }
    
    protected function _update(array $data) {
        return $this->_dbTable->update($data, array("id=?"=>$data['id']));
    }
    
    public function fetchAll() {
        return $this->_dbTable->fetchAll();
    }

}