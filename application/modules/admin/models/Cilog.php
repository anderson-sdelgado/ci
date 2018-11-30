<?php


class Admin_Model_Cilog extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Cilog();
        $this->_table   = "CI_LOG";
	$this->_primary = 'ID_LOG';
	$this->_sequence = 'ID_CI_LOG';
    }
    
    public function fetchAllPesquisa($de_ci=null, $ate_ci=null, $data=null, $ccusto_de=null, $ccusto_para=null, $status=null, $finalidade=null, $page=null, $lista=true) {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->ID;
        $login_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($de_ci) && !is_null($ate_ci)){
           $and .= " AND A.ID BETWEEN $de_ci AND $ate_ci ";
        }elseif(!is_null($de_ci)){
           $and .= " AND A.ID > $de_ci ";
        }elseif(!is_null($ate_ci)){
           $and .= " AND A.ID < $ate_ci ";
        }
        if(!is_null($data)){
           $and .= " AND TO_CHAR(F.DATA, 'dd/mm/yyyy') = '$data'";
        }
        if(!is_null($ccusto_de)){
           $and .= " AND A.CCUSTO_DE = '$ccusto_de'";
        }
        if(!is_null($ccusto_para)){
           $and .= " AND A.CCUSTO_PARA = '$ccusto_para'";
        }
        if(!is_null($status)){
           $and .= " AND F.STATUS = '$status'";
        }
        if(!is_null($finalidade)){
           $and .= " AND A.FINALIDADE = '$finalidade'";
        }
        $res = $db->query("SELECT  
                                  F.ID,
                                  A.ID CI,
                                  TO_CHAR(A.DATA, 'DD/MM/YYYY') DATA,
                                  A.EMPRESA,
                                  A.DESC_CCUSTO_DE CCUSTO_DE,
                                  A.DESC_CCUSTO_PARA CCUSTO_PARA,
                                  A.DESC_FINALIDADE FINALIDADE,
                               -- A.MOTIVO_CI,
                                  NVL(D.NOME, A.USUARIO)  USUARIO,
                                  A.DESC_STATUS CI_STATUS,
                                  G.DESCRICAO STATUS,
                                  TO_CHAR(F.DATA, 'DD/MM/YYYY HH24:MI') DATA_LOG,
                                  NVL(I.NOME, H.LOGIN) APROVADOR, 
                                  to_char(A.VALOR, '99999999.99')VALOR
                           FROM CI A
                           
                           LEFT JOIN V_USU_EMAIL_CW D
                           ON A.USUARIO = D.CD_USU_BD
                           
                           JOIN $this->_table F
                           ON A.ID = F.CI
                           
                           JOIN CI_STATUS G
                           ON F.STATUS = G.ID
                           
                           JOIN USUARIOS H 
                           ON F.USUARIO = H.ID
                           
                           LEFT JOIN V_USU_EMAIL_CW I
                           ON H.LOGIN = I.CD_USU_BD                           
                           
                           WHERE ( F.USUARIO = $id_usuario
                                 OR A.USUARIO = '$login_usuario' 
                                 OR 't' = (SELECT VISUALIZA_CI FROM USUARIOS WHERE ID = $id_usuario)
                                 OR A.FINALIDADE IN( SELECT FV.FINALIDADE FROM CI_FINALIDADE_VISUALIZADORES FV WHERE FV.USUARIO = $id_usuario )
                                     )

                            $and ORDER BY F.DATA DESC");
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 15);
        }else{
            return $result;
        }
    }
    
    
    
    public function getLogCI($id=null) {
        
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($id)){
           $and .= " WHERE A.ID = $id";
        }
        $res = $db->query("SELECT   TO_CHAR(F.DATA, 'DD/MM/YYYY HH24:MI') DATA,
                                    C.LOGIN,
                                    NVL(D.NOME, C.LOGIN) APROVADOR, 
                                    G.DESCRICAO STATUS, 
                                    F.STATUS ID_STATUS,
                                    F.MOTIVO_CANCELAMENTO 
                          FROM CI A 
                          JOIN $this->_table F ON A.ID = F.CI 
                          JOIN USUARIOS C ON F.USUARIO = C.ID 
                          LEFT JOIN V_USU_EMAIL_CW D ON C.LOGIN = D.CD_USU_BD 
                          JOIN CI_STATUS G ON F.STATUS = G.ID 
                          $and
                          ORDER BY F.DATA DESC");
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        return $result;
    }
    
    
    public function fetchAllAprovados( $ci=null ) {
        
        $db = Zend_Registry::get('db2');
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->ID;
        
        $res = $db->query("SELECT *
            
                           FROM $this->_table A
                               
                           WHERE A.CI = ".$ci." AND A.STATUS = 3
                               
                           ORDER BY A.STATUS DESC");
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        return $result;
    }
    
    
    public function usuarioAprovou( $ci, $usuario = null) {
        
        $db = Zend_Registry::get('db2');
        if(is_null($usuario)){
            $usuario = Zend_Auth::getInstance()->getIdentity()->ID;
        }
        
        $res = $db->query("SELECT *
            
                           FROM $this->_table A
                               
                           WHERE A.CI = ".$ci." AND (A.STATUS = 3 OR A.STATUS = 7) AND A.USUARIO = $usuario
                               
                           ORDER BY A.STATUS DESC");
        
        $result = $res->fetchAll();
        
        return count($result) > 0 ? true : false;
    }
    
    public function _save( $dados = array()) {
        
        $db = Zend_Registry::get('db2');
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->ID;
        
        $res = $db->query("SELECT *
            
                           FROM $this->_table A
                               
                           WHERE A.CI = ".$dados["CI"]."
                               AND A.USUARIO = $id_usuario
                           ORDER BY A.STATUS DESC");
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        if(count($result)==0){
            return $this->save($dados);
        }else{
            return array(0 => null);
        }
    }    
}

