<?php

/**
 * Plugin para gerar o menu do usuario
 * @filesource		jmduque/library/Commit/Controller/Action/Helper/Menu.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Action_Helper_Menu
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version		1.0
 * @since		13/07/2011
*/

class Commit_Controller_Action_Helper_Menu extends Zend_Controller_Action_Helper_Abstract {

    private $_arrayMenu;

    public function __construct($menu) {
        $this->_arrayMenu  = $menu;
    }

    public function getMenuHTML(){

        $sBaseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $retorno  = null;
        $retorno .= '<div id="MainMenu">';
        foreach ($this->_arrayMenu as $cont=>$dados){
            if($dados['ID'] == 1){
                $onclick  = "OpenSelf('". $sBaseUrl.$dados['CAMINHO'] ."');";
            }else{
                $onclick = '';
            }
            if($dados['VISUALIZAR'] == 't'){
                $retorno .= '  <div id="MainMenuTexto'.$dados['ID'].'" lang="'.base64_encode($dados['DESCRICAO']).'" 
                                    accesskey="'.$dados['ID'].'" class="btn-menu" title="'.$dados['TITULO'].'" onclick="'.$onclick.'">';
                $retorno .=        $dados['DESCRICAO'];
                $retorno .= '  </div>';
           }
        }
        $retorno .= '</div>';
        return $retorno;
   }

   public function getSubMenuHTML(){
        $retorno  = null;
        foreach ($this->_arrayMenu as $cont=>$dados){
            if(!empty($dados['VISUALIZAR'])){
               if($dados['VISUALIZAR'] == 't'){
                   $retorno .= '<div id="MainSubMenu'.$dados['ID'].'" dir="'.base64_encode($dados['DESCRICAO']).'" class="MainSubMenu" style="display: none;">';
                   foreach ($dados['FILHO'] as $contFilho=>$dadosFilho){
                        if($dadosFilho['VISUALIZAR'] == 't'){

                            $sBaseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                            $onclick  = "OpenSelf('". $sBaseUrl.$dadosFilho['CAMINHO'] ."');";

                            $retorno .= '  <div class="btn-menu-sub" title="'.$dadosFilho['TITULO'].'" lang="'.base64_encode($dadosFilho['DESCRICAO']).'" onclick="'.$onclick.'">';
                            $retorno .=        $dadosFilho['DESCRICAO'];
                            $retorno .= '  </div>';
                            $retorno .= '  <div id="btn-separador"></div>';
                        }
                   }
                   $retorno .= '</div>';
               }
            }
        }
        return $retorno;
   }
}

?>
