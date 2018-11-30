<?php


class Admin_Model_Ci extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Ci();
        $this->_table   = "CI";
	$this->_primary = 'ID';
	$this->_sequence = 'ID_CI';
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
           $and .= " AND TO_CHAR(A.DATA, 'dd/mm/yyyy') = '$data'";
        }
        if(!is_null($ccusto_de)){
           $and .= " AND A.CCUSTO_DE = '$ccusto_de'";
        }
        if(!is_null($ccusto_para)){
           $and .= " AND A.CCUSTO_PARA = '$ccusto_para'";
        }
        if(!is_null($status)){
           $and .= " AND A.STATUS = '$status'";
        }
        if(!is_null($finalidade)){
           $and .= " AND A.FINALIDADE = '$finalidade'";
        }
        $sql = "SELECT DISTINCT A.ID,
                                  TO_CHAR(A.DATA, 'DD/MM/YYYY') DATA,
                                  A.EMPRESA,
                                  A.DESC_CCUSTO_DE CCUSTO_DE,
                                  A.DESC_CCUSTO_PARA CCUSTO_PARA,
                                  A.DESC_FINALIDADE FINALIDADE,
                               -- A.MOTIVO_CI,
                               -- COALESCE(J.NOME,I.LOGIN,D.NOME,A.USUARIO) USUARIO,
                                  CASE WHEN A.STATUS IN(3,4,5) THEN NVL(D.NOME,A.USUARIO) ELSE COALESCE(J.NOME,I.LOGIN,D.NOME,A.USUARIO) END USUARIO,
                               -- NVL(D.NOME,A.USUARIO) USUARIO,
                                  A.USUARIO USUARIO_LOGIN,
                                  A.DESC_STATUS STATUS,
                                  A.STATUS ID_STATUS,
                                  trim(to_char(A.VALOR, '9999999999999.99')) VALOR,
                                  CASE WHEN G.ID IS NOT NULL AND A.STATUS <> 6 THEN 1 ELSE NULL END AS ID_CI_LOG
                           FROM $this->_table A                                                                

                           LEFT JOIN CI_CCUSTO_APROVADORES F
                           ON A.CCUSTO_PARA = F.CD_CCUSTO                           
                           
                           LEFT JOIN CI_LOG G
                           ON A.ID = G.CI
                           AND G.USUARIO = $id_usuario
                           
                           LEFT JOIN V_USU_EMAIL_CW D
                           ON A.USUARIO = D.CD_USU_BD                           
                           
                           LEFT JOIN CI_LOG H
                           ON A.ID = H.CI
                           AND H.STATUS = 7

                           LEFT JOIN USUARIOS I
                           ON I.ID = H.USUARIO

                           LEFT JOIN V_USU_EMAIL_CW J
                           ON J.CD_USU_BD = I.LOGIN

                           WHERE (
                                   A.USUARIO = '$login_usuario'
                                   OR G.USUARIO = $id_usuario
                                   OR 't' = (SELECT VISUALIZA_CI FROM USUARIOS WHERE ID = $id_usuario)
                                   OR A.FINALIDADE IN( SELECT FV.FINALIDADE FROM CI_FINALIDADE_VISUALIZADORES FV WHERE FV.USUARIO = $id_usuario )
                                   OR
                                (
                                
                                    ( 
                                        ".$id_usuario." IN (SELECT L.APROVADOR FROM CI_FINALIDADE_VALOR_APROVADOR L
                                        WHERE L.FINALIDADE_VALOR = (SELECT K.ID FROM CI_FINALIDADE_VALOR K WHERE K.FINALIDADE = A.FINALIDADE AND A.VALOR BETWEEN K.VALOR_INICIAL AND K.VALOR_FINAL))
                                    )
                                    OR(
                                        0 = (SELECT COUNT(*) FROM CI_FINALIDADE_VALOR_APROVADOR L
                                                WHERE L.FINALIDADE_VALOR = (SELECT K.ID FROM CI_FINALIDADE_VALOR K WHERE K.FINALIDADE = A.FINALIDADE AND A.VALOR BETWEEN K.VALOR_INICIAL AND K.VALOR_FINAL))

                                        AND (F.APROVADOR = ".$id_usuario." OR ".$id_usuario." IN (SELECT I.USUARIO FROM CI_VALOR_APROVADORES_GERAL I
                                                                                            WHERE I.VALOR_APROVADORES = (SELECT J.ID FROM CI_VALOR_APROVADORES J WHERE A.VALOR BETWEEN J.VALOR_INICIAL AND J.VALOR_FINAL))
                                        )
                                        AND A.VALOR BETWEEN (SELECT USUARIOS.VALOR_INICIAL FROM USUARIOS WHERE USUARIOS.ID = ".$id_usuario.") AND (SELECT USUARIOS.VALOR_FINAL FROM USUARIOS WHERE USUARIOS.ID = ".$id_usuario.")
                                    )


                                AND 't' = (SELECT APROVADOR FROM USUARIOS WHERE ID = $id_usuario)    )
                                )
                                   
                            $and 
                            UNION
                            SELECT
                                E.ID,
                                TO_CHAR(E.DATA, 'DD/MM/YYYY') DATA,
                                E.EMPRESA,
                                E.DESC_CCUSTO_DE CCUSTO_DE,
                                E.DESC_CCUSTO_PARA CCUSTO_PARA,
                                E.DESC_FINALIDADE FINALIDADE,
                                -- E.MOTIVO_CI,
                                E.USUARIO,
                                -- NVL(D.NOME,E.USUARIO) USUARIO,
                                E.USUARIO USUARIO_LOGIN,
                                E.DESC_STATUS STATUS,
                                E.STATUS ID_STATUS,
                                trim(to_char(E.VALOR, '9999999999999.99')) VALOR,
                                CASE WHEN G.ID IS NOT NULL AND E.STATUS <> 6  THEN 1 ELSE NULL END AS ID_CI_LOG
                            FROM CI E
                            JOIN CI_FINALIDADE F ON E.FINALIDADE = F.ID AND F.PRE_APROVACAO = 's'
                            JOIN USUARIOS G ON E.USUARIO = G.LOGIN AND G.PRE_APROVADOR = $id_usuario
                            -- LEFT JOIN V_USU_EMAIL_CW D ON E.USUARIO = D.CD_USU_BD  
                            WHERE E.STATUS = 6 ".  str_replace("A.", "E.", $and)."
                            ORDER BY ID DESC";
//        echo nl2br($sql);
//        exit;
        $res = $db->query($sql);
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 15);
        }else{
            return $result;
        }
    }
    
    public function fetchAllPesquisaTudo($de_ci=null, $ate_ci=null, $data=null, $ccusto_de=null, $ccusto_para=null, $status=null, $page=null, $lista=true) {
        
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
           $and .= " AND TO_CHAR(A.DATA, 'dd/mm/yyyy') = '$data'";
        }
        if(!is_null($ccusto_de)){
           $and .= " AND A.CCUSTO_DE = '$ccusto_de'";
        }
        if(!is_null($ccusto_para)){
           $and .= " AND A.CCUSTO_PARA = '$ccusto_para'";
        }
        if(!is_null($status)){
           $and .= " AND A.STATUS = '$status'";
        }
        $res = $db->query("SELECT A.ID,
                                  TO_CHAR(A.DATA, 'DD/MM/YYYY') DATA,
                                  A.EMPRESA,
                                  A.DESC_CCUSTO_DE CCUSTO_DE,
                                  A.DESC_CCUSTO_PARA CCUSTO_PARA,
                                  A.DESC_FINALIDADE FINALIDADE,
                                  A.MOTIVO_CI,
                                  NVL(D.NOME,A.USUARIO) USUARIO,
                                  A.USUARIO USUARIO_LOGIN,
                                  A.DESC_STATUS STATUS,
                                  A.STATUS ID_STATUS,
                                  trim(to_char(A.VALOR, '9999999999999.99')) VALOR
                           FROM $this->_table A
                               
                           LEFT JOIN V_USU_EMAIL_CW D
                           ON A.USUARIO = D.CD_USU_BD                           
                           
                           WHERE 1 = 1

                            $and ORDER BY A.ID DESC");
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 15);
        }else{
            return $result;
        }
    }
    
    
    public function getCI($id=null) {
        
        $db = Zend_Registry::get('db2');
        $and = '';
        if(!is_null($id)){
           $and .= " AND A.ID = '$id'";
        }
        $res = $db->query("SELECT A.ID,
                                  TO_CHAR(A.DATA, 'DD/MM/YYYY HH24:MI') DATA,
                                  A.EMPRESA,
                                  A.CCUSTO_DE CD_CCUSTO_DE,
                                  A.CCUSTO_PARA CD_CCUSTO_PARA,
                                  B.NOME_CCUSTO CCUSTO_DE,
                                  C.NOME_CCUSTO CCUSTO_PARA,
                                  A.FINALIDADE ID_FINALIDADE,
                                  F.DESCRICAO FINALIDADE,
                                  A.MOTIVO_CI,
                                  D.NOME USUARIO,
                                  A.USUARIO USUARIO_LOGIN,
                                  A.STATUS ID_STATUS,
                                  E.DESCRICAO STATUS,
                                  trim(to_char(A.VALOR, '9999999999999.99')) VALOR,
                                  A.VALOR VALOR_ORIGINAL
                           FROM $this->_table A
                           JOIN V_CCUSTO_ORG B
                           ON A.CCUSTO_DE = TO_NUMBER(REPLACE(B.CD_CCUSTO,'.',''))
                           
                           JOIN V_CCUSTO_ORG C
                           ON A.CCUSTO_PARA = TO_NUMBER(REPLACE(C.CD_CCUSTO,'.',''))
                           
                           JOIN CI_STATUS E
                           ON A.STATUS = E.ID
                           
                           LEFT JOIN CI_FINALIDADE F
                           ON A.FINALIDADE = F.ID
                           
                           LEFT JOIN V_USU_EMAIL_CW D
                           ON A.USUARIO = D.CD_USU_BD
                           
                           WHERE 1 = 1

                            $and ORDER BY A.DATA DESC");
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        
        return $result[0];
    }
    
    public function findCI($id=null) {
        $db = Zend_Registry::get('db2');
        $where = '';
        if(!is_null($id)){
           $where = " WHERE A.ID = '$id'";
        }
        $res = $db->query("SELECT   A.ID,
                                    A.EMPRESA,
                                    TO_CHAR(A.DATA, 'DD/MM/YYYY HH24:MI') DATA,
                                    A.CCUSTO_DE,
                                    A.CCUSTO_PARA,
                                    A.MOTIVO_CI,
                                    trim(to_char(A.VALOR, '9999999999999.99')) VALOR,
                                    A.USUARIO,
                                    A.STATUS,
                                    A.FINALIDADE
                           FROM $this->_table A
                           $where");
        $result = $res->fetchAll();
        return $result[0];
    }
    
    
    public function fetchAllAprovar($de_ci=null, $ate_ci=null, $data=null, $ccusto_de=null, $ccusto_para=null, $status=null, $finalidade=null, $page=null, $lista=true) {
        
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
           $and .= " AND TO_CHAR(A.DATA, 'dd/mm/yyyy') = '$data'";
        }
        if(!is_null($ccusto_de)){
           $and .= " AND A.CCUSTO_DE = '$ccusto_de'";
        }
        if(!is_null($ccusto_para)){
           $and .= " AND A.CCUSTO_PARA = '$ccusto_para'";
        }
        if(!is_null($status)){
           $and .= " AND A.STATUS = '$status'";
        }
        if(!is_null($finalidade)){
           $and .= " AND A.FINALIDADE = '$finalidade'";
        }
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->ID;
        $login_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $sql = "SELECT DISTINCT   A.ID,
                                  TO_CHAR(A.DATA, 'DD/MM/YYYY') DATA,
                                  A.EMPRESA,
                                  A.DESC_CCUSTO_DE CCUSTO_DE,
                                  A.DESC_CCUSTO_PARA CCUSTO_PARA,
                                  A.DESC_FINALIDADE FINALIDADE,
                               -- A.MOTIVO_CI,
                               -- NVL(D.NOME,A.USUARIO) USUARIO,
                               -- CASE WHEN A.STATUS = 7 THEN NVL(J.NOME,I.LOGIN) ELSE NVL(D.NOME,A.USUARIO) END USUARIO,
                                  COALESCE(J.NOME,I.LOGIN,D.NOME,A.USUARIO) USUARIO,
                               -- NVL(J.NOME,I.LOGIN) USUARIO_APROVADOR,
                                  A.DESC_STATUS STATUS,
                                  trim(to_char(A.VALOR, '9999999999999.99')) VALOR
               FROM $this->_table A

               LEFT JOIN CI_CCUSTO_APROVADORES F
               ON A.CCUSTO_PARA = F.CD_CCUSTO

               LEFT JOIN CI_LOG G
               ON A.ID = G.CI
               AND G.USUARIO = $id_usuario

               LEFT JOIN V_USU_EMAIL_CW D
               ON A.USUARIO = D.CD_USU_BD

               LEFT JOIN CI_LOG H
               ON A.ID = H.CI
               AND H.STATUS = 7

               LEFT JOIN USUARIOS I
               ON I.ID = H.USUARIO
               
               LEFT JOIN V_USU_EMAIL_CW J
               ON J.CD_USU_BD = I.LOGIN
               
               WHERE 't' = (SELECT APROVADOR FROM USUARIOS WHERE ID = $id_usuario) 
                AND A.USUARIO <> '$login_usuario' 
                AND(    
                    ( 
                        ".$id_usuario." IN (SELECT L.APROVADOR FROM CI_FINALIDADE_VALOR_APROVADOR L
                        WHERE L.FINALIDADE_VALOR = (SELECT K.ID FROM CI_FINALIDADE_VALOR K WHERE K.FINALIDADE = A.FINALIDADE AND A.VALOR BETWEEN K.VALOR_INICIAL AND K.VALOR_FINAL))
                    )
                    OR(
                        0 = (SELECT COUNT(*) FROM CI_FINALIDADE_VALOR_APROVADOR L
                                WHERE L.FINALIDADE_VALOR = (SELECT K.ID FROM CI_FINALIDADE_VALOR K WHERE K.FINALIDADE = A.FINALIDADE AND A.VALOR BETWEEN K.VALOR_INICIAL AND K.VALOR_FINAL))
                                
                        AND (F.APROVADOR = ".$id_usuario." OR ".$id_usuario." IN (SELECT I.USUARIO FROM CI_VALOR_APROVADORES_GERAL I
                                                                            WHERE I.VALOR_APROVADORES = (SELECT J.ID FROM CI_VALOR_APROVADORES J WHERE A.VALOR BETWEEN J.VALOR_INICIAL AND J.VALOR_FINAL))
                        )
                        AND A.VALOR BETWEEN (SELECT USUARIOS.VALOR_INICIAL FROM USUARIOS WHERE USUARIOS.ID = ".$id_usuario.") AND (SELECT USUARIOS.VALOR_FINAL FROM USUARIOS WHERE USUARIOS.ID = ".$id_usuario.")
                    )
                )
                AND A.STATUS IN(1, 2, 7) 
                $and
                AND (G.USUARIO IS NULL OR G.STATUS = 7)
                UNION
                SELECT
                    E.ID,
                    TO_CHAR(E.DATA, 'DD/MM/YYYY') DATA,
                    E.EMPRESA,
                    E.DESC_CCUSTO_DE CCUSTO_DE,
                    E.DESC_CCUSTO_PARA CCUSTO_PARA,
                    E.DESC_FINALIDADE FINALIDADE,
                    -- E.MOTIVO_CI,
                    E.USUARIO,
                    -- NULL USUARIO_APROVADOR,
                    E.DESC_STATUS STATUS,
                    trim(to_char(E.VALOR, '9999999999999.99')) VALOR
                FROM CI E
                JOIN CI_FINALIDADE F ON E.FINALIDADE = F.ID AND F.PRE_APROVACAO = 's'
                JOIN USUARIOS G ON E.USUARIO = G.LOGIN AND G.PRE_APROVADOR = $id_usuario
                WHERE E.STATUS = 6 ".  str_replace("A.", "E.", $and)."
                ORDER BY ID ASC";
//        echo nl2br($sql);
        $res = $db->query($sql);
//        echo nl2br($sql);
//        Zend_Debug::dump($res);
        $result = $res->fetchAll();
        if($lista){
            return $this->setPaginator($result, $page, 15);
        }else{
            return $result;
        }
    }
    
    public function aprovacoesNecessarias($ci=null) {
        
        $db = Zend_Registry::get('db2');
        $sql = "SELECT B.*
                                            FROM $this->_table A
                                            JOIN CI_FINALIDADE_VALOR B
                                            ON B.FINALIDADE = A.FINALIDADE
                                            AND A.VALOR BETWEEN B.VALOR_INICIAL AND B.VALOR_FINAL
                                            JOIN CI_FINALIDADE_VALOR_APROVADOR C
                                            ON C.FINALIDADE_VALOR = B.ID
                                            WHERE A.ID = $ci";
//        if(Zend_Auth::getInstance()->getIdentity()->LOGIN == "ITDESK"){
//            echo nl2br($sql);
//        }
        $aprovadoresFinalidade = $db->query($sql);
        $result = $aprovadoresFinalidade->fetchAll();
        $nAprovadores = (int)@$result[0]["APROVADORES"];
        if($nAprovadores==0){
            $res = $db->query("SELECT *
                               FROM CI_VALOR_APROVADORES

                               WHERE (SELECT VALOR FROM $this->_table WHERE ID = $ci)
                                   BETWEEN VALOR_INICIAL AND VALOR_FINAL");
            $result = $res->fetchAll();
            $nAprovadores = (int)@$result[0]["APROVADORES"];
        }
        return $nAprovadores;
        
    }
    
}