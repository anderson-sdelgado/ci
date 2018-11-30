<?php


class Admin_Model_Pedidohistorico extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Pedidohistorico();
        $this->_table   = "HISTORICO";
	$this->_primary = array('PEDIDO','DATA_ENVIO','CONFIRMACAO');
    }
    
    public function __save($data){
        
        if (count( $this->_find($data["PEDIDO"],$data["DATA_ENVIO"],$data["CONFIRMACAO"])) != 0) {
            return $this->_update($data, array("PEDIDO=?"=>$data["PEDIDO"],"DATA_ENVIO=?"=>$data["DATA_ENVIO"],"CONFIRMACAO=?"=>$data["CONFIRMACAO"]));
        } else {
            return $this->_insert($data);
        }
        
    }
    
    public function _find($id_pedido,$data_envio,$confirmacao){
        $db = Zend_Registry::get('db2');
        $query = "select PEDIDO,
                         ID_COMPRADOR,
                         COMPRADOR,
                         TO_CHAR(DATA_ENVIO,'DD/MM/YYYY HH24:MI') DATA_ENVIO,
                         OBSERVACAO,
                         TO_CHAR(DATA_CONFIRMACAO,'DD/MM/YYYY HH24:MI') DATA_CONFIRMACAO,
                         CONFIRMACAO
                  from $this->_table a
                  where a.pedido = $id_pedido
                  and a.data_envio = TO_DATE('$data_envio','DD/MM/YYYY HH24:MI')
                  and a.confirmacao = $confirmacao";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return @$retorno[0];
    }
    
    public function Envios($id_pedido){
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                  PEDIDO,
                  ID_COMPRADOR,
                  COMPRADOR,
                  TO_CHAR(DATA_ENVIO,'DD/MM/YYYY HH24:MI') DATA_ENVIO,
                  OBSERVACAO,
                  TO_CHAR(DATA_CONFIRMACAO,'DD/MM/YYYY HH24:MI') DATA_CONFIRMACAO,
                  CONFIRMACAO
                
                  FROM  $this->_table 
                  WHERE PEDIDO = $id_pedido
                  ORDER BY DATA_ENVIO, CONFIRMACAO";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
    }
    
    public function UltimoEnvio($id_pedido){
        $db = Zend_Registry::get('db2');
        $query = "select PEDIDO,
                         ID_COMPRADOR,
                         COMPRADOR,
                         TO_CHAR(DATA_ENVIO,'DD/MM/YYYY HH24:MI') DATA_ENVIO,
                         OBSERVACAO,
                         TO_CHAR(DATA_CONFIRMACAO,'DD/MM/YYYY HH24:MI') DATA_CONFIRMACAO,
                         CONFIRMACAO
                  from $this->_table a
                  where a.pedido = $id_pedido
                  and a.data_envio = (select MAX(b.data_envio) from $this->_table b where b.pedido = $id_pedido)
                  ORDER BY DATA_ENVIO DESC, CONFIRMACAO DESC";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return @$retorno[0];
    }
    
}

