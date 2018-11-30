<?php


class Admin_Model_Pedidos extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Pedidos();
        $this->_table   = "V_PC_CW";
	$this->_primary = 'PEDIDO';
    }
    
    
    public function Listar($data,$periodo=null,$comprador=null,$pedido=null,$fornecedor=null,$enviado=null,$confirmado=null,$observacao=null,$ordenacao=null,$ordenar_por=null,$ativo=null){
        $db = Zend_Registry::get('db2');
        $where_comprador    = "";
        $where_confirmado   = "";
        $where_data         = "";
        $where_enviado      = "";
        $where_ativo        = "";
        $where_fornecedor   = "";
        $where_observacao   = "";
        $where_pedido       = "";
        
        $data_pedido        = explode("|", $data);        
        $range_pedido       = explode("|", $pedido);   
        $range_fornecedor   = explode("|", $fornecedor);        
        if(count($data_pedido)==2){
            
            if($data_pedido[0] && $data_pedido[1]){
                    $where_data = "A.DT_PEDIDO BETWEEN TO_DATE('$data_pedido[0]','DD/MM/YY') AND  TO_DATE('$data_pedido[1]','DD/MM/YY')";
                }elseif($data_pedido[1]){
                    $where_data = " A.DT_PEDIDO <= TO_DATE('$data_pedido[1]','DD/MM/YY')";
                }else{
                    $where_data = " A.DT_PEDIDO >= TO_DATE('$data_pedido[0]','DD/MM/YY') ";
            }
        }elseif(!empty ($data)){
            if($periodo=='m'){
                $where_data = "TO_CHAR(A.DT_PEDIDO, 'mm/yyyy') = '$data'";
            }else{
                $where_data = "A.DT_PEDIDO = TO_DATE('$data','DD/MM/YY')";
            }
        }else{
                $where_data = "1 = 1";
            }     
        if(!empty($comprador)){
            $where_comprador = " AND A.CODIGO_COMPRADOR = $comprador ";
        }
        if(!empty($fornecedor)){
            if(count($range_fornecedor)==2){
                if($range_fornecedor[0] && $range_fornecedor[1]){
                    $where_fornecedor = " AND A.CODIGO_FORNEC BETWEEN $range_fornecedor[0] AND $range_fornecedor[1]";
                }elseif($range_fornecedor[1]){
                    $where_fornecedor = " AND A.CODIGO_FORNEC <= $range_fornecedor[1]";
                }else{
                    $where_fornecedor = " AND A.CODIGO_FORNEC >= $range_fornecedor[0]";
                }                
            }else{
                $where_fornecedor = " AND A.CODIGO_FORNEC = $range_fornecedor[0]";
            }
        }
        if(!empty($pedido)){
            if(count($range_pedido)==2){
                if($range_pedido[0] && $range_pedido[1]){
                    $where_pedido = " AND A.PEDIDO BETWEEN $range_pedido[0] AND $range_pedido[1]";
                }elseif($range_pedido[1]){
                    $where_pedido = " AND A.PEDIDO <= $range_pedido[1]";
                }else{
                    $where_pedido = " AND A.PEDIDO >= $range_pedido[0]";
                }
            }else{
                $where_pedido = " AND A.PEDIDO = $range_pedido[0]";
            }
        }
        if(!empty($enviado)){
            $where_enviado = " AND NVL(B.ENVIADO,'n') = '$enviado'";
        }
        if(!empty($ativo)){
            $where_ativo = " AND NVL(B.ATIVO,'S') = '$ativo'";
        }
        if(!empty($confirmado)){
            $where_confirmado = " AND NVL(B.CONFIRMADO,'n') = '$confirmado' ";
        }
        if($observacao == "s"){
            $where_observacao = " AND B.OBSERVACAO IS NOT NULL ";
        }elseif($observacao == "n"){
            $where_observacao = " AND B.OBSERVACAO IS NULL ";
        }
        if(empty($ordenar_por)){
            $ordenar_por = "A.DT_PEDIDO";
        }
        if($ordenacao == "decrescente"){
            $ordem = "ORDER BY $ordenar_por DESC";
        }else{
            $ordem = "ORDER BY $ordenar_por";
        }
        $query = "SELECT A.PEDIDO,
                         TO_CHAR(A.DT_PEDIDO, 'dd/mm/yyyy') DT_PEDIDO,
                         A.CODIGO_COMPRADOR,
                         A.NOME_COMPRADOR,
                         A.CODIGO_FORNEC,
                         A.NOME_FORNEC,
                         A.EMAIL_FORNEC,
                         to_char(A.VALOR, '99999999.99') VALOR,
                         A.OBS_PEDIDO,
                         A.PC_ID,
                         A.CODIGO_EMPRESA,
                         A.CONTAT_FORN,
                         to_char(A.IPI_INCL_PRECO, '999,999.99') IPI_INCL_PRECO,
                         to_char(A.VL_FRETE, '999,999.99') VL_FRETE,
                         to_char(A.OUTRAS_DESP_ACESS, '999,999,999.99') OUTRAS_DESP_ACESS,
                         to_char(A.PERC_ENCARG_FINANC, '999,999.99') PERC_ENCARG_FINANC,

                         B.ID_PEDIDO,
                         B.ID_FORNECEDOR,
                         B.ENVIADO,
                         B.ATIVO,
                         B.CONFIRMADO,
                         B.OBSERVACAO,
                         B.OBSERVACAO_LIDA,
                         B.LINK,
                         B.EMAIL_COPIA,
                         B.OBSERVACAO_COMPRADOR,
                         C.CNPJ_CPF ".'"FORN_CNPJ_CPF"'.", 
                         D.CNPJ_CPF 
                    FROM     $this->_table A
                    INNER JOIN V_FORN_CW C
                    ON A.CODIGO_FORNEC = C.CODIGO_FORNEC
                    INNER JOIN V_EMPRESA_CW D
                    ON A.CODIGO_EMPRESA = D.CODIGO_EMPRESA
                    LEFT JOIN PEDIDO B ON A.PEDIDO = B.ID_PEDIDO
                  WHERE  $where_data $where_comprador $where_fornecedor $where_pedido $where_enviado $where_confirmado $where_observacao $where_ativo
                  $ordem";
//        echo nl2br($query);
//        exit;
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
        
        /*$select = $this->_dbTable->getAdapter()->select()->from($this->_table, array('ID', 'LOGIN','TIPO'))->where('ID_USUARIO_ORACLE = ?', (int) $id);
        return $this->_dbTable->getAdapter()->fetchAll($select);*/
    }
    
    public function compradores(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT DISTINCT CODIGO_COMPRADOR, NOME_COMPRADOR FROM  $this->_table ORDER BY NOME_COMPRADOR";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    public function getPedido($id_pedido){
        $db = Zend_Registry::get('db2');
        $query = "SELECT A.PEDIDO,
                         TO_CHAR(A.DT_PEDIDO, 'dd/mm/yyyy') DT_PEDIDO,
                         A.CODIGO_COMPRADOR,
                         A.NOME_COMPRADOR,
                         A.CODIGO_FORNEC,
                         A.NOME_FORNEC,
                         A.EMAIL_FORNEC,
                         to_char(A.VALOR, '99999999.99') VALOR,
                         A.OBS_PEDIDO,
                         A.PC_ID,
                         A.CODIGO_EMPRESA,
                         A.CONTAT_FORN,
                         to_char(A.IPI_INCL_PRECO, '999,999.99') IPI_INCL_PRECO,
                         to_char(A.VL_FRETE, '999,999.99') VL_FRETE,
                         to_char(A.OUTRAS_DESP_ACESS, '999,999,999.99') OUTRAS_DESP_ACESS,
                         to_char(A.PERC_ENCARG_FINANC, '999,999.99') PERC_ENCARG_FINANC
                    FROM     $this->_table A
                    WHERE A.PEDIDO = $id_pedido";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno[0];
    }
    public function __save($data){
        return $this->_insert($data);
    }
}

