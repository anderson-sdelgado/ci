<?php


class Admin_Model_Usuario extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Usuario();
        $this->_table   = "USUARIOS";
        $this->_primary = "ID";
    }
    
    public function fetchAllUpdate($id) {
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                           ID,
                           LOGIN,
                           ATIVO
                  FROM     $this->_table
                  WHERE    ID = '$id'";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }    
    
    public function fetchAllAprovadores($ccusto = null, $id_valor = null, $fin_valor = null) {
        $db = Zend_Registry::get('db2');
        $where  = "";
        $and    = "";
        if(!is_null($ccusto)){
            $where  = "LEFT JOIN CI_CCUSTO_APROVADORES C
                      ON A.ID = C.APROVADOR
                      AND C.CD_CCUSTO = $ccusto";
            
            $and    = "AND C.APROVADOR IS NULL";
        }elseif(!is_null($id_valor)){
            $where  = "LEFT JOIN CI_VALOR_APROVADORES_GERAL C
                      ON A.ID = C.USUARIO
                      AND C.VALOR_APROVADORES = $id_valor";
            
            $and    = "AND C.USUARIO IS NULL";
        }elseif(!is_null($fin_valor)){
            $where  = "LEFT JOIN CI_FINALIDADE_VALOR_APROVADOR C
                      ON A.ID = C.APROVADOR
                      AND C.FINALIDADE_VALOR = $fin_valor";
            
            $and    = "AND C.APROVADOR IS NULL";
        }
        
        $query = "SELECT DISTINCT
                           A.ID,
                           A.LOGIN,
                           trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  $where
                  WHERE A.APROVADOR = 't'
                  $and ORDER BY NVL( B.NOME, A.LOGIN ) ASC";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    
    public function fetchAllVisualizadores($finalidade = null) {
        $db = Zend_Registry::get('db2');
        $where  = "";
        $and    = "";
        if(!is_null($finalidade)){
            $where  = "LEFT JOIN CI_FINALIDADE_VISUALIZADORES C
                      ON A.ID = C.USUARIO
                      AND C.FINALIDADE = $finalidade";
            
            $and    = "WHERE C.USUARIO IS NULL";
        }
        
        $query = "SELECT 
                           A.ID,
                           A.LOGIN,
                           trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  $where
                  $and 
                  ORDER BY NVL( B.NOME, A.LOGIN ) ASC";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    
    public function fetchAllCriadores($finalidade = null) {
        $db = Zend_Registry::get('db2');
        $where  = "";
        $and    = "";
        if(!is_null($finalidade)){
            $where  = "LEFT JOIN CI_FINALIDADE_CRIADORES C
                      ON A.ID = C.USUARIO
                      AND C.FINALIDADE = $finalidade";
            
            $and    = "WHERE C.USUARIO IS NULL";
        }
        
        $query = "SELECT 
                           A.ID,
                           A.LOGIN,
                           trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  $where
                  $and 
                  ORDER BY NVL( B.NOME, A.LOGIN ) ASC";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    public function fetchAllAprovadores2($ccusto = null, $valor = null, $login_usuario = null) {
        $db = Zend_Registry::get('db2');
        $where1 = "";
        $where2 = "";
        $and1   = "";
        $and2   = "";
        $query1 = null;
        $query2 = null;
        if(is_null($login_usuario)){
            $login_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        }
        
        if(!is_null($valor)){
            $where2  = "JOIN CI_VALOR_APROVADORES_GERAL D
                      ON A.ID = D.USUARIO
                      AND D.VALOR_APROVADORES = (SELECT ID FROM CI_VALOR_APROVADORES E WHERE to_number('$valor') BETWEEN E.VALOR_INICIAL AND E.VALOR_FINAL)";
            
            $and1     = " AND to_number('$valor') BETWEEN A.VALOR_INICIAL AND A.VALOR_FINAL";
            $and2     = " AND to_number('$valor') BETWEEN A.VALOR_INICIAL AND A.VALOR_FINAL";
             $query2 = "SELECT 
                           A.ID,
                           A.LOGIN,
                           trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A 
                  $where2
                      
                  LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  
                  WHERE A.APROVADOR = 't'
                  AND     A.LOGIN <> '$login_usuario'
                  $and2";            
        }
        
        if(!is_null($ccusto)){
            $where1  = "JOIN CI_CCUSTO_APROVADORES C
                      ON A.ID = C.APROVADOR
                      AND C.CD_CCUSTO = $ccusto ";
            
            $and1    .= " AND C.APROVADOR IS NOT NULL ";
           
            
            $query1 = "SELECT DISTINCT
                           A.ID,
                           A.LOGIN,
                           trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  
                  $where1
                  WHERE A.APROVADOR = 't'
                  AND     A.LOGIN <> '$login_usuario' 
                  $and1";
           
        }
//        echo nl2br("$query1 UNION $query2");
        if($query1 && $query2){
            $res = $db->query("$query1 UNION $query2");
        }elseif($query1){
            $res = $db->query($query1);
        }elseif($query2){
            $res = $db->query($query2);
        }        
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    public function fetchAllAprovadoresFinalidade($finalidade = null, $valor = null, $login_usuario = null) {
        $db = Zend_Registry::get('db2');
        if(is_null($login_usuario)){
            $login_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        }
        $where2  = "JOIN CI_FINALIDADE_VALOR_APROVADOR D
                  ON A.ID = D.APROVADOR
                  AND D.FINALIDADE_VALOR = (SELECT ID FROM CI_FINALIDADE_VALOR E WHERE E.FINALIDADE = $finalidade AND to_number('$valor') BETWEEN E.VALOR_INICIAL AND E.VALOR_FINAL)";

//        $and2     = " AND to_number('$valor') BETWEEN A.VALOR_INICIAL AND A.VALOR_FINAL";
         $query2 = "SELECT DISTINCT
                       A.ID,
                       A.LOGIN,
                       trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                       trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                       NVL( B.NOME, A.LOGIN ) NOME,
                       B.EMAIL
              FROM     $this->_table A 
              $where2

              LEFT JOIN V_USU_EMAIL_CW B
              ON      B.CD_USU_BD = A.LOGIN

              WHERE A.APROVADOR = 't'
              AND     A.LOGIN <> '$login_usuario' ";
        $res = $db->query($query2);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    public function fetchAllEmailCopia($ccusto = null, $valor = null, $login_usuario = null) {
        $db = Zend_Registry::get('db2');
        $join   = "";
        $and1   = "";
        $query1 = null;
        $query2 = null;
        
        
        if(!is_null($valor)){            
            $and1    .= " AND to_number('$valor') BETWEEN A.VALOR_INICIAL AND A.VALOR_FINAL";
             $query2 = "SELECT 
                                D.ID,
                                NULL LOGIN,
                                trim(to_char(D.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                                trim(to_char(D.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                                D.EMAILCOPIA NOME,
                                D.EMAILCOPIA EMAIL
                        FROM    CI_VALOR_APROVADORES D 
                        WHERE to_number('$valor') BETWEEN D.VALOR_INICIAL AND D.VALOR_FINAL";            
        }
        
        if(!is_null($ccusto)){
		
            $join  = "LEFT JOIN CI_CCUSTO_APROVADORES C
                      ON A.ID = C.APROVADOR
                      AND C.CD_CCUSTO = $ccusto ";
            
            $and1    .= " AND C.APROVADOR IS NOT NULL ";
           
			if(!is_null($login_usuario)){
				$where ="WHERE (A.APROVADOR = 't' $and1 ) OR A.LOGIN = '$login_usuario' ";
			}else{
				$where ="WHERE A.APROVADOR = 't' $and1 ";
			}
		
            
            $query1 = "SELECT 
                           A.ID,
                           A.LOGIN,
                           trim(to_char(A.VALOR_INICIAL, '9999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999.99')) VALOR_FINAL,
                           B.NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  
                  $join
                  $where ";
           
        }
//        echo nl2br($query1)."<br/><br/>UNION<br/><br/>".nl2br($query2);
        if($query1 && $query2){
            $res = $db->query("$query1 UNION $query2");
        }elseif($query1){
            $res = $db->query($query1);
        }elseif($query2){
            $res = $db->query($query2);
        }        
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    public function fetchIdOracle($id){
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                           A.ID,
                           A.LOGIN,
                           A.TIPO,
                           A.APROVADOR,
                           A.VISUALIZA_CI,
                           A.PRE_APROVADOR,
                           trim(to_char(A.VALOR_INICIAL, '9999999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                WHERE    ID_USUARIO_ORACLE = '$id'";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        reset($retorno);
        return $retorno;
        
    }
    
    public function fetchId($id){
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                           A.ID,
                           A.LOGIN,
                           A.TIPO,
                           A.APROVADOR,
                           A.VISUALIZA_CI,
                           A.PRE_APROVADOR,
                           trim(to_char(A.VALOR_INICIAL, '9999999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                WHERE    A.ID = '$id'";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        reset($retorno);
        return $retorno;
        
    }
    
    
    public function fetchLogin($login){
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                           A.ID,
                           A.LOGIN,
                           A.TIPO,
                           A.APROVADOR,
                           A.VISUALIZA_CI,
                           A.PRE_APROVADOR,
                           trim(to_char(A.VALOR_INICIAL, '9999999999999.99')) VALOR_INICIAL,
                           trim(to_char(A.VALOR_FINAL, '9999999999999.99')) VALOR_FINAL,
                           NVL( B.NOME, A.LOGIN ) NOME,
                           B.EMAIL
                  FROM     $this->_table A LEFT JOIN V_USU_EMAIL_CW B
                  ON      B.CD_USU_BD = A.LOGIN
                  WHERE   A.LOGIN = '$login'";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        reset($retorno);
        return $retorno;
        
    }
    
    public function fetchAllUsuEmail(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT * FROM V_USU_EMAIL_CW";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    public function __save($data){
        if($this->find($data['ID'])){
            $data['ID'] = $this->findProximoId();
        }
        return $this->_insert($data);
    }
}