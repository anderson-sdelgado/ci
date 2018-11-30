<?php

class Admin_Model_Horaview extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Horaview();
        $this->_table = "v_pte_horas_intrajornadas";
    }
    
    public function fetchAllPesquisa($id = null, $dataRef = null) {
        $db = Zend_Registry::get('db2');
        $query = "SELECT    A.COD_CCUSTO, 
                            A.NOME_CCUSTO,
                            A.CD_COLAB,
                            A.NOME_COLAB,
                            A.DT_ADMIS,
                            A.COD_EVENTO,
                            A.EVENTO,
                            SUM(A.QTDE) AS REALIZADO,
                            A.DT_REFERENCIA,
                            A.FUNCAO_ID,
                            A.DESCR_FUNCAO
                   FROM        v_pte_horas_intrajornadas A
                   WHERE       A.COD_CCUSTO IN (SELECT CD_CCUSTO FROM CARGOS_CCUSTO C WHERE C.COD_FUNCAO = '$id')
                   AND      TO_CHAR(A.DT_REFERENCIA,'mm/yyyy') = '$dataRef'
                   GROUP BY  A.COD_CCUSTO, 
                            A.NOME_CCUSTO,
                            A.CD_COLAB,
                            A.NOME_COLAB,
                            A.DT_ADMIS,
                            A.COD_EVENTO,
                            A.EVENTO,
                            A.DT_REFERENCIA,
                            A.FUNCAO_ID,
                            A.DESCR_FUNCAO";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
    
    public function fetchAllCargo($id = null, $dataRef = null) {
        $db = Zend_Registry::get('db2');
        $query = "SELECT    A.COD_CCUSTO, 
                            A.NOME_CCUSTO,
                            A.CD_COLAB,
                            A.NOME_COLAB,
                            A.DT_ADMIS,
                            A.COD_EVENTO,
                            A.EVENTO,
                            SUM(A.QTDE) AS REALIZADO,
                            A.DT_REFERENCIA,
                            A.FUNCAO_ID,
                            A.DESCR_FUNCAO
                   FROM        v_pte_horas_intrajornadas A
                   WHERE       A.COD_CCUSTO IN (SELECT CD_CCUSTO FROM CARGOS_CCUSTO C WHERE C.COD_FUNCAO = '$id')
                   AND      TO_CHAR(A.DT_REFERENCIA,'mm/yyyy') = '$dataRef'
                   GROUP BY  A.COD_CCUSTO, 
                            A.NOME_CCUSTO,
                            A.CD_COLAB,
                            A.NOME_COLAB,
                            A.DT_ADMIS,
                            A.COD_EVENTO,
                            A.EVENTO,
                            A.DT_REFERENCIA,
                            A.FUNCAO_ID,
                            A.DESCR_FUNCAO";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
}
