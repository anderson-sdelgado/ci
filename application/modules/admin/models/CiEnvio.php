<?php


class Admin_Model_CiEnvio extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable  = new Admin_Model_DbTable_CiEnvio();
        $this->_table    = "CI_ENVIO";
	$this->_primary  = 'ID';
	$this->_sequence = 'ID_CI_ENVIO';
    }
    
    public function fetchAll($registros = null, $enviado = null, $assunto = null, $page = 1, $lista = false) {
        $where      = "";
        $rownumber  = "";
        if($enviado){
            $where = "WHERE ENVIADO = '$enviado' ";
        }
        if($assunto){
            if($where==""){
                $where = "WHERE ASSUNTO LIKE '%$assunto%' ";                
            }else{
                $where .= " AND ASSUNTO LIKE '%$assunto%' ";
            }
        }
        if($registros){
            $rownumber= "WHERE REGISTRO <= $registros ";
        }
        $db = Zend_Registry::get('db2');        
        $res = $db->query("SELECT B.ID,
                                  B.DE_NOME, 
                                  B.PARA_NOME,
                                  B.PARA_EMAIL,
                                  B.ASSUNTO,
                                  B.ENVIADO,
                                  B.REGISTRO
                           FROM( SELECT ID,DE_NOME, PARA_NOME,PARA_EMAIL,ASSUNTO,ENVIADO,row_number() over (order by ID DESC) REGISTRO FROM $this->_table A $where ORDER BY A.ID DESC) B $rownumber");
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 15);
        }else{
            return $result;
        }
    }
    
    public function fetchAllEnviar($id = null) {
        
        $db = Zend_Registry::get('db2');
        if(is_null($id)){
            $where = " WHERE ENVIADO = 'N' ";
        }else{
            $where = " WHERE ID = $id ";
        }
        $sql = "SELECT ID,
                       ASSUNTO,
                       DE_EMAIL,
                       DE_NOME,
                       PARA_EMAIL,
                       PARA_NOME,
                       DE_TEXTO
                FROM $this->_table 
                $where
                ORDER BY ID";        
        $res = $db->query($sql);
        $result = $res->fetchAll();
        return $result;
    }
    
}

