<?php

/**
 * Helper para adicionar ou retirar a mascara de numeros
 * @filesource		jmduque/library/Commit/Controller/Action/Helper/Compatibilidade.php
 * @author 		Julio
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Action_Helper_Compatibilidade
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version		1.0
 * @since		08/09/2011
*/

class Commit_Controller_Action_Helper_Compatibilidade extends Zend_Controller_Action_Helper_Abstract {


    public function getCssGeral($style){
        switch ($this->getBrowser()){
            case 'ie9':
                return PUBLIC_PATH . '_css/'.$style.'.css';
                break;
            case 'ie7':
                return PUBLIC_PATH . '_css/compativel/ie/'.$style.'-ie7.css';
                break;
            case 'ie8':
                return PUBLIC_PATH . '_css/compativel/ie/'.$style.'-ie8.css';
                break;
            case 'Firefox':
                return PUBLIC_PATH . '_css/'.$style.'.css';
                break;
            case 'Chrome':
                return PUBLIC_PATH . '_css/'.$style.'.css';
                break;
        }
    }
    
    public function getJsGeral($script){
        
        switch ($this->getBrowser()){
            case 'ie9':
                return PUBLIC_PATH . '_js/'.$script.'.js';
                break;
            case 'ie7':
                return PUBLIC_PATH . '_js/compativel/ie/'.$script.'-ie7.js';
                break;
            case 'ie8':
                return PUBLIC_PATH . '_js/compativel/ie/'.$script.'-ie8.js';
                break;
            case 'Firefox':
                return PUBLIC_PATH . '_js/'.$script.'.js';
                break;
            case 'Chrome':
                return PUBLIC_PATH . '_js/'.$script.'.js';
                break;
        }
    }
    
    public function getBrowser(){
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
    }

}

