<?php
/**
 * Plugin de controle de permissao para acessar link
 * @filesource          /jmduque/library/Commit/Controller/Plugin/Permissao.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Plugin_Permissao
 * @subpackage		Zend_Controller_Plugin_Abstract
 * @version		1.0
 * @since		15/07/2011
*/

class Commit_Controller_Plugin_Permissao extends Zend_Controller_Plugin_Abstract {

    /*
     * Funcao automatica verifica identidade
     *
     * $return $void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $oRequest){

        $sModuloName        = trim($oRequest->getModuleName());
        $sControllerName    = trim($oRequest->getControllerName());
        $sActionName        = trim($oRequest->getActionName());

        if(empty($sModuloName))
            $sModuloName = 'default';
        if($sModuloName == 'admin'){
            
            if($sControllerName != 'ajax'){
                $objMenu    = new Admin_Model_MenuAcesso();
                $permissao  = $objMenu->getPermissao($sModuloName, $sControllerName, $sActionName);
                $operacao   = $objMenu->getPermissaoOperacao($sControllerName);
                $this->setPermissao($permissao);
                
                if (count($permissao) > 0){
                    foreach ($permissao as $dados){
                       if($dados['VISUALIZAR'] == 'f'){
                           $this->setErroPermissao();
                       }
                    }
                }else{
                    if (count($operacao) != 0){
                        $parametro = $this->getTipoPermissao($sActionName);
                        foreach ($operacao as $dados){
                           if($dados[$parametro] == 'f'){
                             $this->setErroPermissao();
                           }
                        }
                    }else{
                        $this->setErroPermissao();
                    }
                }
            }
        }
        //var_dump($permissao);
        //Zend_Debug::dump($permissao);exit;
        //echo $this->baseUrl;exit;
    }


    /**
     * Grava a sessão em eu novo NameSpace
     * @param array $permissao
     */
    private function setPermissao($permissao){
        $menuNamespace = new Zend_Session_Namespace('PERMISSAO');
        $menuNamespace->unsetAll();
        $menuNamespace->_permissao = $permissao;
    }

    private function setErroPermissao(){
        $this->getResponse()->setRedirect(FORM_PATH);
    }

    private function getTipoPermissao($sActionName){
        switch ($sActionName) {
            case 'insert':
                return "INSERIR";
                break;
            case 'update':
                return "ALTERAR";
                break;
            case 'delete':
                return "EXCLUIR";
                break;
            default :
                return "ALTERAR";
                break;
        }
    }
}