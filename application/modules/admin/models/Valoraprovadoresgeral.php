<?php

class Admin_Model_Valoraprovadoresgeral extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Valoraprovadoresgeral();
        $this->_table   = "CI_VALOR_APROVADORES_GERAL";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_VALOR_APROVADORES_GERAL";
    }
   
    public function fetchAllPesquisa($id_valor=null) {
        
        $db = Zend_Registry::get('db2');
        $and  = '';
        
        if(!is_null($id_valor)){
           $and = " AND VALOR_APROVADORES = $id_valor ";
        }
        $query = "SELECT A.ID,
                        A.VALOR_APROVADORES,
                        B.LOGIN,
                        B.ID_USUARIO_ORACLE,
                        B.APROVADOR,
                        B.VALOR_INICIAL,
                        B.VALOR_FINAL,
                        NVL( C.NOME, B.LOGIN ) NOME
                 FROM $this->_table A
                 JOIN USUARIOS B
                 ON A.USUARIO =B.ID
                 $and
                 LEFT JOIN V_USU_EMAIL_CW C
                 ON B.LOGIN = C.CD_USU_BD
                 where B.APROVADOR = 't'
                 ORDER BY C.NOME ASC";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
            return $result;
    }
}
