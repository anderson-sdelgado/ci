<?php

class Commit_Controller_Action_Helper_LayoutLoader extends Zend_Controller_Action_Helper_Abstract 
{
	
	public function preDispatch() 
	{
		$bootstrap 	= $this->getActionController()->getInvokeArg('bootstrap');
		$config 	= $bootstrap->getOptions();
		$module 	= $this->getRequest()->getModuleName();
		if (isset($config[$module]['resources']['layout']['layout'])) {
			$layoutScript = $config[$module]['resources']['layout']['layout'];
			$this->getActionController()
				 ->getHelper('layout')
				 ->setLayout($layoutScript);
		}
                $Url = $this->getRequest()->getRequestUri();
                $BaseUrl = $this->getRequest()->getBaseUrl();
                $Historico = new Zend_Session_Namespace('HISTORICO');
                if($Historico->Atual && $Historico->Anterior <> $Historico->Atual){
                    $Historico->Anterior = $Historico->Atual;
                }
                $Historico->Atual = substr($Url, strlen($BaseUrl));
	}
	
}