<?php

class Admin_Model_Colabview extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Colabview();
        $this->_table = "V_COLAB_USU";
    }
    
    public function fetchAllPesquisa($id = null) {
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT   A.CD_COLAB, 
                                    B.CD_CARGO,
                                    C.COD_FUNCAO_SUP
                           FROM     $this->_table A
                           INNER JOIN V_COLAB_ORG B
                           ON B.CD_COLAB = A.CD_COLAB
                           INNER JOIN V_CARGOS_ORG C
                           ON C.COD_FUNCAO = B.CD_CARGO
                           WHERE A.CD_USU_BD = '$id'
                ");
        $result = $res->fetchAll();
        
        /*$select = $this->_dbTable   ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.CD_COLAB'))
                                    ->join(array('B' => 'V_COLAB_ORG'),'B.CD_COLAB = A.CD_COLAB',array('B.CD_CARGO'))
                                    ->join(array('C' => 'V_CARGOS_ORG'),'C.COD_FUNCAO = B.CD_CARGO',array('C.COD_FUNCAO_SUP'))
                                    ->Where("A.CD_USU_BD = '$id'");
        
        //echo $select->__toString();exit;
        $result = $this->_dbTable->getAdapter()->fetchAll($select);*/
        return $result;
    }
    
    
    
}
