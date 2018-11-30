<?php

class Admin_Model_Ccustoaprovadores extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Ccustoaprovadores();
        $this->_table   = "CI_CCUSTO_APROVADORES";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_CCUSTO_APROVADORES";
    }
    public function fetchAllAprovadores(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT A.ID,
                        A.CD_CCUSTO,
                        B.LOGIN,
                        B.ID_USUARIO_ORACLE,
                        B.APROVADOR,
                        B.VALOR_INICIAL,
                        B.VALOR_FINAL,
                        NVL(C.NOME, B.LOGIN) NOME
                 FROM CI_CCUSTO_APROVADORES A
                 JOIN USUARIOS B
                 ON A.APROVADOR =B.ID
                 LEFT JOIN V_USU_EMAIL_CW C
                 ON B.LOGIN = C.CD_USU_BD
                 where B.APROVADOR = 't'";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
    
    
}
