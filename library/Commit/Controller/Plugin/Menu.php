<?php
/**
 * Plugin de controle de menu selecionado
 * @filesource
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Plugin_Menu
 * @subpackage		Zend_Controller_Plugin_Abstract
 * @version		1.0
 * @since		15/07/2011
*/

class Commit_Controller_Plugin_Menu extends Zend_Controller_Plugin_Abstract {

    public function postDispatch(Zend_Controller_Request_Abstract $oRequest) {
        $sModuloName        = trim($oRequest->getModuleName());
        $sControllerName    = trim($oRequest->getControllerName());
        $sActionName        = trim($oRequest->getActionName());

        if($sModuloName == 'admin'){
            $this->getScriptJquery($sModuloName, $sControllerName, $sActionName);
        }
    }

    /**
     * Função que gera o script para o menu corrente
     * @param char $sModuloName
     * @param char $sControllerName
     * @param char $sActionName
     */
    private function getScriptJquery($sModuloName, $sControllerName, $sActionName){

        $xMenu    = "/$sModuloName/$sControllerName";
        $xMenuSub = "/$sModuloName/$sControllerName/$sActionName";

        //echo $xMenuSub;
        //exit;
        
        $descricaoMenu      = base64_encode($this->getDescricaoMenu($xMenu));
        $descricaoSubMenu   = base64_encode($this->getDescricaoMenu($xMenuSub));

        if(strlen($descricaoMenu) == 0 and strlen($descricaoSubMenu) == 0){
            $name = $this->getDescricaoMenuOperacao($sControllerName);
            $Menu    = explode('/', $name);
            if(count($Menu) > 1){
                $descricaoMenu      = base64_encode($this->getDescricaoMenu("/$Menu[1]/$Menu[2]"));
                $descricaoSubMenu   = base64_encode($this->getDescricaoMenu("/$Menu[1]/$Menu[2]/$Menu[3]"));
            }else{
                $descricaoMenu      = base64_encode('index');
                $descricaoSubMenu   = base64_encode('index2');
            }
        }
        
        $browser = new Commit_Controller_Action_Helper_Compatibilidade();

        $aspas     = "'";
        $retorno   = null;
        $retorno  .= '$(document).ready(function(){';


        if($browser->getBrowser() == 'ie7'){
            
            $idMenu = $this->getDescricaoMenuId($xMenu);
            $retorno  .= '$("div[lang='.$aspas. $descricaoMenu .$aspas.']").css({"background":"#1f5a7c","color":"#fff","border":"0px solid #fff"});';
            $retorno  .= '$("#MainSubMenu'.$idMenu.'").css({"display":"block"});';
            $retorno  .= '$("div[lang='.$aspas. $descricaoSubMenu .$aspas.']").css({"background":"#003366","border":"0px solid #787878"});';

        }else if($browser->getBrowser() == 'ie8'){

            $retorno  .= '$("div[lang='.$aspas. $descricaoMenu .$aspas.']").addClass("btn-menu-select");';
            $retorno  .= '$("div[dir ='.$aspas. $descricaoMenu .$aspas.']").show();';
            $retorno  .= '$("div[lang='.$aspas. $descricaoSubMenu .$aspas.']").addClass("btn-menu-sub-select");';

        }else{
            $retorno  .= '$("div[lang='.$aspas. $descricaoMenu .$aspas.']").css({"background":"#1f5a7c","color":"#fff","border":"0px solid #fff"});';
            $retorno  .= '$("div[dir ='.$aspas. $descricaoMenu .$aspas.']").show();';
            $retorno  .= '$("div[lang='.$aspas. $descricaoSubMenu .$aspas.']").css({"background":"#003366","border":"0px solid #787878"});';
        }
        $retorno  .= '});';

        $this->setScript($retorno);
    }

    /**
     * Busca a descricao do Menu
     * @param   char $caminho
     * @return  char descricao
     */
    private function getDescricaoMenu($caminho){
        $objMenu = new Admin_Model_Menu();
        return $objMenu->getDescricaoMenu($caminho);
    }

    /**
     * Busca a descricao do Menu
     * @param   char $caminho
     * @return  char descricao
     */
    private function getDescricaoMenuId($caminho){
        $objMenu = new Admin_Model_Menu();
        return $objMenu->getDescricaoMenuId($caminho);
    }

    /**
     * Busca a descricao do Menu
     * @param   char $caminho
     * @return  char descricao
     */
    private function getDescricaoMenuOperacao($controller){
        $objMenu = new Admin_Model_Menu();
        return $objMenu->getDescricaoMenuOperacao($controller);
    }

    /**
     * Grava a sessão em eu novo NameSpace
     * @param script $script
     */
    private function setScript($script){
        $menuNamespace = new Zend_Session_Namespace('MENU');
        //$menuNamespace->unsetAll();
        $menuNamespace->_script = $script;
    }
}