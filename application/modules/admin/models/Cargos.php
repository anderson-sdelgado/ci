<?php

class Admin_Model_Cargos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Cargos();
        $this->_table = "V_CARGOS_ORG";
    }
    
    
    public function fetchAllUpdate($id) {
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT COD_FUNCAO, DESCR_FUNCAO FROM $this->_table ORDER BY DESCR_FUNCAO ASC");
        $resultado = $res->fetchAll();
        return $resultado;
    }
    
    public function fetchAllPesquisa($codigo=null, $page=null, $lista=true) {
        
        $and = '';
        if(!is_null($codigo)){
           $and = " WHERE COD_FUNCAO = '$codigo'";
        }
        
        $db = Zend_Registry::get('db2');
        $query = "SELECT COD_FUNCAO, DESCR_FUNCAO, DESCR_SUMARIA, DESCR_DETALH FROM $this->_table $and";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    /**
     * busca hierarquia do organograma
     * @param   numeric $id_sup, numeric $id_colab
     * @return  array
     */
    public function fetchRelatorio($id_sup = null,$id_colab = null,$data = null){
        $db = Zend_Registry::get('db2');
        $where = ' WHERE 1 = 1';
        if(!is_null($id_sup)){
            //$where .= ' AND cod_funcao_sup >= '.$id_sup;
        }
        
        if(!empty($data)){
            $where .= " AND DT_REF = TO_DATE('$data','DD-MM-YY')";
        }else{
            $where .= ' AND DT_REF = (SELECT MAX(DT_REF) FROM ORGANOGRAMA)';
        }
        $query = "SELECT * FROM  ORGANOGRAMA $where ORDER BY DT_REF DESC, NVL(COD_FUNCAO_SUP,0), COD_FUNCAO";
        $res = $db->query($query);
        $resultado = $res->fetchAll();
        return $resultado;
        
    }
    /**
     * busca dados do colaborador no organograma
     * @param   numeric $id_cargo
     * @return  array
     */
    public function fetchColab($id_cargo,$data=null){
        
        $where = '';
        if(!empty($data)){
            $where .= " DT_REF = to_date('$data','DD-MM-YY')";
        }else{
            $where .= " DT_REF = (SELECT MAX(DT_REF) FROM ORGANOGRAMA)";
        }
        $db = Zend_Registry::get('db2');
        $query = "SELECT   
                            (
                            SELECT      SUM(V.QT_ORCADA)
                            FROM        CARGOS_CCUSTO C
                            INNER JOIN  ORGANOGRAMA O
                            ON          C.COD_FUNCAO = O.COD_FUNCAO
                            INNER JOIN  V_QUADRO_ORG V
                            ON          V.CCUSTO = C.CD_CCUSTO
                            WHERE       O.COD_FUNCAO = '$id_cargo'
                            AND         O.DT_REF = (SELECT MAX(O2.DT_REF) FROM ORGANOGRAMA O2 WHERE O2.COD_FUNCAO = O.COD_FUNCAO)
                            ) AS ORCADO,
                            (
                            SELECT      SUM(V.QT_REAL)
                            FROM        CARGOS_CCUSTO C
                            INNER JOIN  ORGANOGRAMA O
                            ON          C.COD_FUNCAO = O.COD_FUNCAO
                            INNER JOIN  V_QUADRO_ORG V
                            ON          V.CCUSTO = C.CD_CCUSTO
                            WHERE       O.COD_FUNCAO = '$id_cargo'
                            AND         O.DT_REF = (SELECT MAX(O2.DT_REF) FROM ORGANOGRAMA O2 WHERE O2.COD_FUNCAO = O.COD_FUNCAO)
                            ) AS REAL
			    FROM        CARGOS_CCUSTO C
			    INNER JOIN	ORGANOGRAMA A
			    ON          C.COD_FUNCAO = A.COD_FUNCAO
                           INNER JOIN v_quadro_org V
                           ON to_number(replace(V.CCUSTO,'.','')) = to_number(replace(C.CD_CCUSTO,'.',''))
                           WHERE $where
                           AND A.COD_FUNCAO = '$id_cargo'
			   AND ROWNUM <= 1
                          ";
        $res = $db->query($query);
        $result = $res->fetchAll();
        
        return $result;
    }
    
    /**
     * busca dados do colaborador no organograma com hierarquia para mao de obra
     * @param   numeric $id_cargo
     * @return  array
     */
    public function fetchCargosRel($id_cargo,$data = null){
        $db = Zend_Registry::get('db2');
        
        $where = " WHERE 1 = 1";
        if(!empty($data)){
            $where .= " AND DT_REF = TO_DATE('$data','DD-MM-YY')";
        }else{
            $where .= ' AND DT_REF = (SELECT MAX(O2.DT_REF) FROM ORGANOGRAMA O2 where o2.cod_funcao = o.cod_funcao)';
        }
        
       $query ="SELECT DISTINCT o.cod_funcao   as cod_funcao_sup, 
                        v.cod_funcao   cod_funcao,
                        v.nome_funcao  as cargo,
                        --v.ccusto       cd_ccusto,
                        to_number(replace(v.ccusto,'.','')) as cd_ccusto,
                        v.nome_ccusto  nome_ccusto,
                        v.qt_real,
                        v.qt_orcada,
                        cm.mo
                --     , v.qt_orcada voltar quando for fazer o orcado x realizado
                 FROM cargos_ccusto   c 
                INNER JOIN organograma     o
                      ON c.cod_funcao  = o.cod_funcao
                 INNER JOIN  v_quadro_org    v
                      ON v.ccusto      = c.cd_ccusto
                 INNER JOIN  cargos_mo       cm
                      ON cm.cod_funcao = v.cod_funcao
                 WHERE  o.cod_funcao  = '$id_cargo'
                      and o.dt_ref      = (select max(o2.dt_ref) from organograma o2 where o2.cod_funcao = o.cod_funcao )
                ORDER BY v.nome_ccusto, v.nome_funcao";
        
        $res = $db->query($query);
        
        $resultado = $res->fetchAll();
        return $resultado;
    }
    
    public function fetchCargoUsu($id){
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT COD_FUNCAO_SUP FROM $this->_table WHERE COD_FUNCAO = '$codigo'");
        $result = $res->fetchAll();
        
        if($lista){
            return $this->setPaginator($result, $page, 10);
        }else{
            return $result;
        }
    }
    
    public function fetchUsu($id){
        $db = Zend_Registry::get('db2');
        $and = "";
        if(!is_null($id)){
           $and = "AND       v.LOGIN = '$id' ";
        }
        $query = "SELECT  DISTINCT  vc.*
                  FROM      v_usu_ccusto_org  v
                  INNER JOIN 
                            v_colab_org vc
                  ON        to_number(replace(V.CD_CCUSTO,'.','')) = to_number(replace(VC.CD_CCUSTO,'.',''))
                  $and
                  ORDER BY NOME_CCUSTO, NOME_CARGO, NOME_COLAB";
        /*$query = "SELECT        o.* 
                  FROM          organograma o
                  INNER JOIN    cargos_ccusto c
                  ON            to_number(replace(O.CD_CCUSTO,'.','')) = to_number(replace(C.CD_CCUSTO,'.',''))
                  WHERE         c.cod_funcao = '$cod_funcao'
                  AND           o.dt_ref      = (select max(o2.dt_ref) from organograma o2 where o2.cod_funcao = o.cod_funcao )";*/
        $res = $db->query($query);
        $result = $res->fetchAll();        
        
        return $result;
    }
    public function fetchUsuAfastados($id){
        $db = Zend_Registry::get('db2');
        $where = "";
        if(!is_null($id)){
           $where = "WHERE  V.LOGIN = '$id' ";
        }
        $query = "SELECT DISTINCT af.CD_CCUSTO
                        , af.CD_CARGO
                        , af.NOME_CARGO
                        , af.CD_COLAB
                        , af.nome NOME_COLAB
                        , TO_CHAR(AF.INICIO_AFASTAMENTO, 'dd/mm/yyyy') INICIO_AFASTAMENTO
                        , TO_CHAR(AF.FIM_AFASTAMENTO, 'dd/mm/yyyy') FIM_AFASTAMENTO
                        , AF.AFASTAMENTO
                        , AF.AUSENCIA
                        , AF.OBS
                        , AF.INICIO_AFASTAMENTO
                   FROM v_usu_ccusto_org v 
                   INNER JOIN v_afastados_ccusto AF ON to_number(replace(V.CD_CCUSTO,'.','')) = to_number(replace(AF.CD_CCUSTO,'.','')) 
                   $where
                   ORDER BY AF.CD_CARGO, AF.NOME, AF.INICIO_AFASTAMENTO";
        
        $res = $db->query($query);
        $result = $res->fetchAll();        
        
        return $result;
    }
}
