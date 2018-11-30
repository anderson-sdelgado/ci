<?php 
/**
 * Plugin de controle de acesso ao sistema
 * @filesource
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Plugin_Acesso
 * @subpackage		Zend_Controller_Plugin_Abstract
 * @version		1.0
 * @since		14/07/2011
*/

class Commit_Controller_Plugin_Acesso extends Zend_Controller_Plugin_Abstract {
	
    protected $baseUrl;
    
    /*
     * Funcao automatica verifica identidade
     * 
     * $return $void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $oRequest){

	$sModuloName 	= trim($oRequest->getModuleName());
	$oAuth 		= Zend_Auth::getInstance();        
        $namespace = new Zend_Session_Namespace('Zend_Auth');
        $namespace->setExpirationSeconds(3600);
        
	if (!$oAuth->hasIdentity() ) 
            if( $sModuloName == 'admin')
                $this->getResponse()->setRedirect('/autenticar');
        else
            self::PermissaoModulo($sModuloName);

    }
	
    private function PermissaoModulo($modulo){
        
        if(Zend_Auth::getInstance()->getIdentity()){
            $grupo = Zend_Auth::getInstance()->getIdentity()->TIPO;
            //var_dump($grupo);exit;  
            switch ($grupo){           
                case '1' :{
                    if ($modulo != 'admin')
                        $this->getResponse()->setRedirect('/autenticar');
                    break;
                }

                case '2' :
                    if ($modulo != 'admin')					
                        $this->getResponse()->setRedirect('/autenticar');
                    break;
            }
        }
    }
}