<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap{
	
    /**
    * Inicializa a visao da aplicacao
    * 
    * @return view
    */
    protected function _initSetView(){
        
        $bootstrap  = $this->getApplication();       
        $this->view = $bootstrap->getResource('view');
        $this->view->addHelperPath('Zend/Controller/Action/Helper, Zend_Controller_Action_Helper');   
    }

    /**
    * Inicializao o style na aplicacao
    * 
    * @return headLink()
    */
    protected function _initheadLink(){
        
    } 

    /**
     * Inicializao Script principla na aplicacao
     * @return headScript()
     */
    protected function _initHeadScript(){     	

    }

}