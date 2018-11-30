<?php

class Admin_Model_DbTable_Pedidohistorico extends Zend_Db_Table_Abstract
{
    protected $_name = 'HISTORICO';
    protected $_primary = array('PEDIDO','DATA_ENVIO','CONFIRMACAO');
}
