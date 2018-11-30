<?php

class Admin_Model_FinalidadeCriadores extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_FinalidadeCriadores();
        $this->_table   = "CI_FINALIDADE_CRIADORES";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_FINALIDADE_CRIADORES";
    }
    public function fetchAllUsuarios(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT A.ID,
                        A.FINALIDADE,
                        B.LOGIN,
                        B.ID_USUARIO_ORACLE,
                        B.APROVADOR,
                        B.VALOR_INICIAL,
                        B.VALOR_FINAL,
                        NVL(C.NOME, B.LOGIN) NOME
                 FROM $this->_table A
                 JOIN USUARIOS B
                 ON A.USUARIO =B.ID
                 LEFT JOIN V_USU_EMAIL_CW C
                 ON B.LOGIN = C.CD_USU_BD
                 ORDER BY NVL(C.NOME, B.LOGIN)";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
    
    
}
