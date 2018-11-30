<?php

class Admin_Model_MenuAcesso extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_MenuAcesso();
        $this->_table   = "MENU_ACESSO";
        $this->_sequence = 'ID_MENU_ACESSO';
    }

    /**
     * Busca se o usuario tem permissao para a action
     * 
     * @return array
     */
    public function getPermissao($module, $controller, $action){
        if($action == 'index')
            $caminho = "/$module/$controller";
        else
            $caminho = "/$module/$controller/$action";
        
        $usuario = @(int) Zend_Auth::getInstance()->getIdentity()->ID;
        
        $menu    = @$this->getMenuID($caminho);
        
        if(strlen($usuario) > 0 and strlen($menu) > 0 ){
            /*
            $select     = $this->_dbTable->select()->where('MENU_ID=?', $menu)->where('USUARIOS_ID=?', $usuario);
            $retorno    = $this->_dbTable->fetchAll($select)->toArray();*/
            $db = Zend_Registry::get('db2');
            $res = $db->query("SELECT
                               *
                               FROM     MENU_ACESSO                               
                               WHERE    USUARIOS_ID = '$usuario'
                               AND      MENU_ID = '$menu'");
            $retorno = $res->fetchAll();
            
            return @$retorno;
        }else{
            return null;
        }
    }
    
    /**
     * Busca permissao para a operacao
     * 
     * @return array
     */
    public function getPermissaoOperacao($controller){
        $usuario = @(int) Zend_Auth::getInstance()->getIdentity()->ID;
        $menu    = @$this->getiDMenuOperacao($controller);
        if(strlen($usuario) > 0 and strlen($menu) > 0 ){
            /*
            $select     = $this->_dbTable->select()->where('MENU_ID=?', $menu)->where('USUARIOS_ID=?', $usuario);
            $retorno    = $this->_dbTable->fetchAll($select)->toArray();*/
            $db = Zend_Registry::get('db2');
            $res = $db->query("SELECT
                               *
                               FROM     MENU_ACESSO                               
                               WHERE    USUARIOS_ID = '$usuario'
                               AND      MENU_ID = '$menu'");
            $retorno = $res->fetchAll();
            return @$retorno;
        }else{
            return null;
        }
    } 
    
    
    /**
     * Busca a descricao do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    private function getMenuID($caminho){
        $objMenu = new Admin_Model_Menu();
        $retorno = $objMenu->getIdMenu($caminho);
        return $retorno;
    } 
    
    /**
     * Busca o id do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    private function getiDMenuOperacao($controller){
        $objMenu = new Admin_Model_Menu();
        return $objMenu->getIdMenuOperacao($controller);
    } 

    /**
     * deleta as permissoes dos usuarios
     * @param   char $usuario
     * @return  array
     */
    public function __delete($usuario) {
        try {
            /*
            $where  = $this->_dbTable->getAdapter()->quoteInto('USUARIOS_ID = ?', $usuario);
            $return = $this->_dbTable->delete($where);*/
            $db = Zend_Registry::get('db2');
            $return = $db->query("DELETE 
                               FROM  MENU_ACESSO
                               WHERE    USUARIOS_ID = '$usuario'
                               ");
            
            return array(null,$return);
        }catch (Exception $e){
            return array($e->getCode().'-'.$e->getMessage(), null);
	}
    }
    /**
     * busca os usuarios com permissao para visualizar o organograma
     * @param   numeric $id
     * @return  array
     */
    public function fetchAllLista($id = null) {
        /*$select = $this->_dbTable   ->getAdapter()
                                    ->select()
                                    ->from(array('A' => $this->_table), array('A.ID','A.USUARIOS_ID'))
                                    ->join(array('B' => 'USUARIOS'),"A.USUARIOS_ID = B.ID" ,array('LOGIN'))
                                    ->Where("A.MENU_ID = '12'");*/
        if(!is_null($id)){
           //$select->Where("A.USUARIOS_ID = '$id'");
            $and = " AND A.USUARIOS_ID = '$id'";
        }
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT
                                    A.ID,
                                    A.USUARIOS_ID,
                                    B.LOGIN
                           FROM     MENU_ACESSO A                              
                           INNER JOIN USUARIOS B
                           ON       A.USUARIOS_ID = B.ID
                           WHERE    MENU_ID = '12'
                           $and
                           ");
        $retorno = $res->fetchAll();
        //echo $select->__toString();
        //$result = $this->_dbTable->getAdapter()->fetchAll($select);
        return $retorno;
    }
    
    
}
