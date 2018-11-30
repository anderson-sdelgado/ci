<?php

class Admin_Model_Finalidade extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Finalidade();
        $this->_table   = "CI_FINALIDADE";
        $this->_primary = "ID";
        $this->_sequence = "ID_CI_FINALIDADE";
    }
   
    public function fetchAllPesquisa($descricao = null, $ativo = null) {
        
        $db = Zend_Registry::get('db2');
        $where  = null;
        
        if(!is_null($descricao)){
           $where = " WHERE DESCRICAO LIKE '%$descricao%' ";
        }
        if(!is_null($ativo)){
            if(!is_null($descricao)){
                $where.= " AND ATIVO LIKE '$ativo' ";
            }else{
                $where = " WHERE ATIVO LIKE '$ativo' ";
            }           
        }
        $res = $db->query("SELECT ID, DESCRICAO, EMAIL_COPIA, ATIVO, DETALHES, PRE_APROVACAO, VALOR_OBRIGATORIO FROM $this->_table $where ORDER BY DESCRICAO ASC");
        $result = $res->fetchAll();
        
            return $result;
    }
    
   
    public function fetchAllPesquisaPermissao($descricao = null, $ativo = null) {
        $db = Zend_Registry::get('db2');
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->ID;
        
        $where  = null;
        
        if(!is_null($descricao)){
           $where = " WHERE C.DESCRICAO LIKE '%$descricao%' ";
        }
        if(!is_null($ativo)){
            if(!is_null($descricao)){
                $where.= " AND C.ATIVO LIKE '$ativo' ";
            }else{
                $where = " WHERE C.ATIVO LIKE '$ativo' ";
            }           
        }
        
        $sql = "SELECT C.ID, C.DESCRICAO, C.EMAIL_COPIA, C.ATIVO, C.DETALHES, C.PRE_APROVACAO, C.VALOR_OBRIGATORIO, count(D.USUARIO) AS QTDE_USUARIOS FROM $this->_table C
                LEFT JOIN CI_FINALIDADE_CRIADORES D
                ON C.ID = D.FINALIDADE 
                LEFT JOIN CI_FINALIDADE_CRIADORES E
                ON C.ID = E.FINALIDADE AND E.USUARIO = $id_usuario 
                $where
                GROUP BY C.ID, C.DESCRICAO, C.EMAIL_COPIA, C.ATIVO, C.DETALHES, C.PRE_APROVACAO, C.VALOR_OBRIGATORIO, E.USUARIO
                HAVING count(D.USUARIO) = 0 OR E.USUARIO IS NOT NULL
                ORDER BY C.DESCRICAO ASC";
//        echo nl2br($sql);
        $res = $db->query($sql);
        $result = $res->fetchAll();
        
            return $result;
    }
    
    public function _find($id=null) {
        
        $db = Zend_Registry::get('db2');
        $where  = '';
        if(!is_null($id)){
           $where = " WHERE ID = $id ";
        }
        $res = $db->query("SELECT ID, DESCRICAO, DETALHES, EMAIL_COPIA, ATIVO, PRE_APROVACAO, VALOR_OBRIGATORIO FROM $this->_table $where ORDER BY DESCRICAO ASC");
        $result = $res->fetchAll();
        
        return $result[0];
    }
    
    
}
