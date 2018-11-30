<?php

class Default_ErrorController extends Zend_Controller_Action
{

    public function errorAction(){
    	
	//desabilita o layout
    	$this->_helper->layout()->disableLayout();
        
        $errors = $this->_getParam('error_handler');
	$errors = explode('Stack trace',$errors->exception);
      	
        //$this->setLog($errors[0]);

        //zend_debug::dump($errors->exception);
        
        switch (@$errors->type) {
            //case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            	$this->getResponse()->setHttpResponseCode(404);
            	$this->view->message 	= 'Página não localizada';
            	$this->view->descricao 	= 'CONTROLLER NÃO FOI LOCALIZADO';
            	//$log->warn('CONTROLLER N�O FOI LOCALIZADO');
            	break;

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message 	= 'Página não localizada';
                $this->view->descricao 	= 'ACTION NÃO FOI LOCALIZADA';
                //$log->warn('ACTION N�O FOI LOCALIZADA');
                break;

	case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message 	= 'Página com problema';
                $this->view->descricao 	= 'NÃO FOI LOCALIZADO O ERRO DA APLICAÇÃO';
                //$log->err($errors[0]);
                break;

	case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message 	= 'Página não localizada';
                $this->view->descricao 	= 'NÃO FOI LOCALIZADA A ROTA';
                $log->warn('NÃO FOI LOCALIZADA A ROTA');
                break;

        default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message    = 'Página com problema';
                $this->view->descricao 	= 'ERROGRAVADO NO LOG';
                $this->view->errors     =  $errors[0];
                //$log->err($errors[0]);
                
                break;
        }
    }
 
    
/*
    private function setLog($mensagem){
        $remote_addr = ((strlen($_SERVER['REMOTE_ADDR'])<>0)?$_SERVER['REMOTE_ADDR']:'NAO LOCALIZADO');
        $remote_uri  = ((strlen($_SERVER['REQUEST_URI'])<>0)?$_SERVER['REQUEST_URI']:'NAO LOCALIZADO');
        $nome        = ((strlen(Zend_Auth::getInstance()->getIdentity()->NOME)<>0)?Zend_Auth::getInstance()->getIdentity()->NOME:'NAO LOCALIZADO');
        $email       = ((strlen(Zend_Auth::getInstance()->getIdentity()->EMAIL)<>0)?Zend_Auth::getInstance()->getIdentity()->EMAIL:'NAO LOCALIZADO');
        $telefone    = ((strlen(Zend_Auth::getInstance()->getIdentity()->CELULAR)<>0)?Zend_Auth::getInstance()->getIdentity()->CELULAR:'NAO LOCALIZADO');
        $mensagem    = str_replace(array("'", '"'), array(" ", " "), substr($mensagem, 0, 300));
        
        $data = array(
             'NOME'             => $nome
            ,'EMAIL'            => $email
            ,'TELEFONE'         => $telefone
            ,'REMOTE_AGENTE'    => $this->getBrowser()
            ,'REMOTE_ADDR'      => $remote_addr
            ,'REQUEST_URI'      => $remote_uri
            ,'MENSAGEM'         => $mensagem
        );
        
        $objLog  = new Admin_Model_LogErro();
        $objLog->save($data);
    }

    private function getBrowser(){
       $browser_cliente = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  

        if(strpos($browser_cliente, 'MSIE 9.0') !== false){  
            return 'ie9';
        }
        if(strpos($browser_cliente, 'MSIE 7.0') !== false){  
            return 'ie7';
        }
        if(strpos($browser_cliente, 'MSIE 8.0') !== false){  
            return 'ie8';
        }
        if(strpos($browser_cliente, 'Chrome') !== false){
            return 'Chrome';
        }  
        if(strpos($browser_cliente, 'Firefox') !== false){
            return 'Firefox';
        }
    }*/
}
 

