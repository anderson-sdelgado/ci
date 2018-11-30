<?php


class Admin_Model_HoraExtra extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_HoraExtra();
        $this->_table   = "HORA_EXTRA";
	$this->_primary = 'ID';
        $this->_sequence = 'ID_HORA_EXTRA';
    }
    
    public function fetchAllUpdate($id) {
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT * FROM $this->_table WHERE ID = '$id'");
        $resultado = $res->fetchAll();
        return $resultado;
    }
    
    public function fetchAllPesquisa($id_cargo = null, $cd_ccusto= null, $page = null, $lista = true) {
        
        $db = Zend_Registry::get('db2');
        $and = 'WHERE 1 = 1 ';
        if(!is_null($id_cargo)){
           $and .= " AND A.COD_FUNCAO = '$id_cargo' ";
        }
        if(!is_null($cd_ccusto)){
           $and .= " AND (V.NOME_CCUSTO LIKE '%$cd_ccusto%' OR A.CD_CCUSTO LIKE '%$cd_ccusto%') ";
        }
        $query = "SELECT    A.ID,
                            A.ID_HORA_EXTRA,
                            A.COD_FUNCAO,
                            A.CD_CCUSTO,
                            A.MES,
                            A.DESCRICAO,
                            A.QUANTIDADE,
                            V.NOME_CCUSTO
                   FROM $this->_table A
                   INNER JOIN V_CCUSTO_ORG V
                   ON   A.CD_CCUSTO = V.CD_CCUSTO
                   $and
                   ORDER BY CD_CCUSTO ASC";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function fetchHoraCCusto($id_cargo = null,$dataRef=null){
        $db = Zend_Registry::get('db2');
        $query = "SELECT    A.CD_CCUSTO,
                            A.COD_FUNCAO,
                            A.MES,
                            SUM(A.QUANTIDADE) AS QTDE
                  FROM $this->_table A
                  WHERE to_char(CD_CCUSTO) IN (SELECT CD_CCUSTO FROM CARGOS_CCUSTO C WHERE C.COD_FUNCAO = '$id_cargo' )
                  AND      A.MES = '$dataRef'
                  GROUP BY A.CD_CCUSTO,
                            A.COD_FUNCAO,
                            A.MES";
        $res = $db->query($query);
        return $result = $res->fetchAll();
    }
}
