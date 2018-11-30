<?php

class Admin_Model_Menu extends Admin_Model_Abstract{

    public function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Menu();
        $this->_table = "MENU";
        $this->_sequence = 'ID_MENU';
    }

    /**
     * Carrega o array com o todos os menus liberados para o usuario logado
     *
     * @return array
     */
    public function getMenu(){
        $db = Zend_Registry::get('db2');
        $query = "SELECT 
                                    A.ID, 
                                    A.FILHO,
                                    A.ORDEM,
                                    A.DESCRICAO,
                                    A.TITULO,
                                    A.CAMINHO,
                                    A.OPERACAO,
                                    B.VISUALIZAR,
                                    B.INSERIR,
                                    B.ALTERAR,
                                    B.EXCLUIR
                           FROM     MENU A
                           JOIN 
                                    MENU_ACESSO B
                           ON       B.MENU_ID = A.ID
                           WHERE    FILHO IS NULL
                           AND      B.USUARIOS_ID = '". (int)Zend_Auth::getInstance()->getIdentity()->ID."'
                           ORDER BY A.ID ASC";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        /*$select = $this->_dbTable->select()
                                 ->setIntegrityCheck(false)
                                 ->from(array('A' => 'MENU'),array('A.ID','A.FILHO','A.ORDEM','A.DESCRICAO','A.TITULO','A.CAMINHO','A.OPERACAO'))
                                 ->joinInner(array('B'=>'MENU_ACESSO'),"B.MENU_ID = A.ID",array('B.VISUALIZAR','B.INSERIR','B.ALTERAR','B.EXCLUIR'))
                                 ->order('ORDEM ASC')
                                 ->where('FILHO IS NULL')
                                 ->where('B.USUARIOS_ID=?', (int)Zend_Auth::getInstance()->getIdentity()->ID);

        //echo$select->__toString();exit;
	$retorno = $this->_dbTable->fetchAll($select)->toArray();
        */
        //Zend_Debug::dump($retorno);exit;
        return $this->getSubMenu($retorno);
    }

    /**
     * Busca a descricao do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    public function getDescricaoMenu($caminho){
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT DESCRICAO FROM MENU WHERE CAMINHO = '$caminho' ORDER BY ID ASC");
        $retorno = $res->fetchAll();
        /*$select     = $this->_dbTable->select()->where('CAMINHO=?', $caminho);
        $retorno    = $this->_dbTable->fetchAll($select)->toArray();*/
        $res = '';
        foreach ($retorno as $dados){
            $res = $dados['DESCRICAO'];
        }
        return @$res;
    }

    /**
     * Busca o ID do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    public function getDescricaoMenuId($caminho){
        
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT ID FROM MENU WHERE CAMINHO = '$caminho'");
        $retorno = $res->fetchAll();
        if(!empty($retorno)){
        	foreach ($retorno as $dados){
            	$res = $dados['ID'];
        	}
        	return @$res;
        }else{
		return '';
        }
    }
    /**
     * Busca a descricao do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    public function getDescricaoMenuOperacao($controller){
        $db = Zend_Registry::get('db2');
        $query = "SELECT CAMINHO FROM MENU WHERE OPERACAO = '$controller'";
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        /*
        $select     = $this->_dbTable->select()->where('OPERACAO=?', $controller);
        $retorno    = $this->_dbTable->fetchAll($select)->toArray();*/
        if(!empty($retorno)){
            foreach ($retorno as $dados){
                $res = $dados['CAMINHO'];
            }
            return @$res;
        }else{
            return '';
        }
    }

    /**
     * Busca a descricao do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    public function getIdMenu($caminho){
        $db = Zend_Registry::get('db2');
        $query = "SELECT ID FROM MENU WHERE CAMINHO = '$caminho'";
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        /*
        $select     = $this->_dbTable->select()->where('CAMINHO=?', $caminho);
        //var_dump($select->__toString());exit;
        $retorno    = $this->_dbTable->fetchAll($select)->toArray();*/
        $res = '';
        foreach ($retorno as $dados){
            $res = $dados['ID'];
        }
        return @$res;
    }

    /**
     * Busca id do menu com os dados da url
     * @param   char $caminho
     * @return  char
     */
    public function getIdMenuOperacao($controller){
        $db = Zend_Registry::get('db2');
        $res = $db->query("SELECT ID FROM MENU WHERE OPERACAO = '$controller'");
        $retorno = $res->fetchAll();
        
        /*$select     = $this->_dbTable->select()->where('OPERACAO=?', $controller);
        $retorno    = $this->_dbTable->fetchAll($select)->toArray();*/
        foreach ($retorno as $dados){
            $res = $dados['ID'];
        }
        return @$res;
    }

    public function getPermissaoMenu($id){
        
        $db = Zend_Registry::get('db2');
        $query = "SELECT   A.ID, 
                                    A.DESCRICAO,
                                    B.VISUALIZAR,
                                    B.INSERIR,
                                    B.ALTERAR,
                                    B.EXCLUIR                                    
                           FROM     MENU A 
                           LEFT JOIN MENU_ACESSO B
                           ON       B.MENU_ID = A.ID   AND B.USUARIOS_ID= '$id'
                           WHERE    A.FILHO IS NULL
                           ORDER BY A.ORDEM ASC";
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        /*
        $select = $this->_dbTable->select()
                                 ->from(array('A' => 'MENU'),array('ID', 'DESCRICAO'))
                                 ->setIntegrityCheck(false)
                                 ->joinLeft(array('B'=>'MENU_ACESSO'),"B.MENU_ID = A.ID AND B.USUARIOS_ID=$id",array('VISUALIZAR','INSERIR','ALTERAR','EXCLUIR'))
                                 ->order('A.ORDEM ASC')
                                 ->where('A.FILHO IS NULL');
        
	$retorno = $this->_dbTable->fetchAll($select)->toArray();*/
        return $this->getPermissaoSubMenu($retorno, $id);
    }

    public function getPermissaoSubMenu($menu, $id){
        $db = Zend_Registry::get('db2');

        foreach ($menu as $cont=>$dados){
            
            $query = "SELECT    A.ID, 
                                A.DESCRICAO,
                                B.VISUALIZAR,
                                B.INSERIR,
                                B.ALTERAR,
                                B.EXCLUIR                                    
                       FROM     MENU A 
                       LEFT JOIN MENU_ACESSO B
                       ON       B.MENU_ID = A.ID AND B.USUARIOS_ID = '$id'
                       WHERE    A.FILHO IS NOT NULL
                       AND      A.FILHO = '".$dados["ID"]."'
                       ORDER BY A.ORDEM ASC";
            $res = $db->query($query);
            $retorno = $res->fetchAll();
            
            //$retorno = $this->_dbTable->fetchAll($select)->toArray();
            $menu[$cont]['FILHO'] = $retorno;
        }

        return $menu;
    }



        /**
     * Carrega os subMenus no array de menus
     * @param array $menu
     * @return array
     */
    private function getSubMenu($menu){
        $db = Zend_Registry::get('db2');
        
        foreach ($menu as $cont=>$dados){
            
            /*$select = $this->_dbTable->select()
                                     ->setIntegrityCheck(false)
                                     ->from(array('A' => 'MENU'))
                                     ->joinInner(array('B'=>'MENU_ACESSO'),"B.MENU_ID = A.ID")
                                     ->order('ORDEM ASC')
                                     ->where('A.FILHO IS NOT NULL')
                                     ->where('B.USUARIOS_ID=?', (int)Zend_Auth::getInstance()->getIdentity()->ID)
                                     ->where('A.FILHO=?', (int)$dados['ID']);*/
            $query = "SELECT   A.*,
                               B.VISUALIZAR,
                               B.INSERIR,
                               B.ALTERAR,
                               B.EXCLUIR
                       FROM     MENU A 
                       INNER JOIN MENU_ACESSO B
                       ON       B.MENU_ID = A.ID                
                       WHERE    A.FILHO IS NOT NULL
                       AND      B.USUARIOS_ID = ".(int)Zend_Auth::getInstance()->getIdentity()->ID."
                       AND      A.FILHO = '".(int)$dados["ID"]."'  
                       ORDER BY A.ORDEM ASC";
            $res = $db->query($query);
            $retorno = $res->fetchAll();

            //$retorno = $this->_dbTable->fetchAll($select)->toArray();
            $menu[$cont]['FILHO'] = $retorno;
        }
        return $menu;
    }



}
