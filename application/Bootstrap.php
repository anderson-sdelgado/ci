<?php

/*
 * Controle Bootstrap
 * @filesource
 * @autor 		Julio Cesar Silva Nascimento
 * @copyrigth 		Commit
 * @package		Bootstrap
 * @subpackage		commit.application.modules
 * @version		1.1
 * @date                08/07/2011
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Inicializa a aplicacacao autoload
     *
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAppAutoload() {

        $autoloader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => 'Application',
                    'basePath' => dirname(__FILE__),
                ));
        return $autoloader;
    }
    
    /**
     * Inicializa a aplicacacao layout loader
     *
     * @return Zend_Controller_Action_HelperBroker
     */
    protected function _initLayoutHelper() {
        $this->bootstrap('frontController');
        $layout = Zend_Controller_Action_HelperBroker::addHelper(new Commit_Controller_Action_Helper_LayoutLoader());
    }

    /**
     * Inicializa a traducao das mensagens da aplicacao
     *
     * @return void
     */
    protected function _initLocate() {

        // Seta a localização
        $locale = new Zend_Locale('pt_BR');
        Zend_Registry::set('Zend_Locale', $locale);

        $translate = new Zend_Translate('array', APPLICATION_PATH . '/languages/pt_BR.php', 'pt');
        Zend_Form::setDefaultTranslator($translate);
        Zend_Validate_Abstract::setDefaultTranslator($translate);
    }

    /**
     * Inicializa e seta o banco de dados
     *
     * @return Zend_Db
     */
    protected function _initSetDb() {

        //$params = array('username' => 'itdesk', 'password' => 'itdesk1553', 'dbname' => '//192.168.1.1/stafe', 'charset' => 'AL32UTF8');
        //$params = array('username' => 'allan', 'password' => 'allan', 'dbname' => '//192.168.0.120/XE', 'charset' => 'AL32UTF8');
        $params = array('username' => DB_USER, 'password' => DB_PASS, 'dbname' => DB_NAME, 'charset' => 'AL32UTF8');
        $db = Zend_Db::factory('Oracle', $params);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('db2', $db);
    }

    /**
     * Inicializa a visao da aplicacao
     *
     * @return view
     */
    protected function _initSetView() {

        $this->bootstrap('view');

        $this->view = $this->getResource('view');
        $this->view->addHelperPath('Zend/Controller/Action/Helper, Zend_Controller_Action_Helper');
        $this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
    }

    /**
     * Inicializa as routers
     *
     * @return router
     */
    protected function _initRewrite() {

        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
        $router->addConfig($config, 'routes');
    }

    /**
     * Inicializao Meta Dado da aplicacao
     *
     * @return headMeta()
     */
    protected function _initHeadMeta() {
        $this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
                ->setName('keywords', 'Commit Consulting, Usina Santa Fe')
                ->setName('author', 'Commit Consulting')
                ->setName('language', 'pt-br')
                ->setName('description', 'Usina Santa Fe')
                ->setName('Expires', gmdate("D, d M Y H:i:s", time() + ((24 * 60 * 60) * 5)) . " GMT")
                ->setName('Cache-Control', 'must-revalidate, proxy-revalidate');
        ;
    }

    /**
     * Inicializao Script principla na aplicacao
     *
     * @return headScript()
     */
    protected function _initHeadScript() {
        $this->view->headScript()->appendFile(PUBLIC_PATH . '_js/jquery/jquery_new.min.js')
                ->appendFile(PUBLIC_PATH . '_js/jquery/jquery.corner.js')
                ->appendFile(PUBLIC_PATH . '_js/jquery/jquery.priceFormat.js')
                ->appendFile(PUBLIC_PATH . '_js/jquery/jquery.maskedinput.js')
                ->appendFile(PUBLIC_PATH . '_js/library/easyui/jquery.easyui.min.js')
//                ->appendFile(PUBLIC_PATH . '_js/library/littleTIP-v1.4/littletip-1.4.js')
                ->appendFile(PUBLIC_PATH . '_js/library/ui/jquery-ui-1.8.6.custom.min.js');
    }

    /**
     * Inicializao o style na aplicacao
     *
     * @return headLink()
     */
    protected function _initheadLink() {
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH . '_css/layout.css')
                ->headLink(array('rel' => 'shortcut icon', 'href' => PUBLIC_PATH . '_img/logo/favicon3.ico', 'type' => 'image/x-icon'))
                ->appendStylesheet(PUBLIC_PATH . '_js/library/easyui/themes/default/easyui.css')
                ->appendStylesheet(PUBLIC_PATH . '_js/library/easyui/themes/icon.css')
//                ->appendStylesheet(PUBLIC_PATH . '_js/library/littleTIP-v1.4/littletip.css')
                ->appendStylesheet(PUBLIC_PATH . '_js/library/ui/jquery-ui-1.8.18.custom.css');
    }

    /**
     * Inicializao o titulo principla da aplicacao
     *
     * @return void
     */
    protected function _initheadTitle() {
        //carrega os dados no form
        $this->view->headTitle()->prepend('Usina Santa Fe');
    }

}