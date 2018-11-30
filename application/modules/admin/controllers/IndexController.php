<?php
/**
 * Controller gerencia a pagina principal
 * @filesource		/application/modules/admin/controllers/IndexController.php
 * @author 		Allan Rett Ferreira
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Controllers
 * @package		Admin_IndexController
 * @subpackage		Zend_Controller_Action
 * @version		1.0
 * @since		10/03/2012
*/
class Admin_IndexController extends Zend_Controller_Action
{

    public function init(){
        $head = new Commit_Controller_Action_Helper_Compatibilidade();
        
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/admin.css')
                               ->appendStylesheet($head->getCssGeral('admin'));
        $this->view->headScript()->appendFile(PUBLIC_PATH. '_js/admin.js')
                                 ->appendFile($head->getJsGeral('admin'));
    }

    /**
     * Mostra a Action Index
     * @author 		Allan Rett Ferreira
     * @version		1.0
     * @since		10/03/2012
     */
    public function indexAction(){

    }
    
    public function permissaoAction(){
        // action body
    }
    /**
     * Mostra a Action Sair(realiza o logoff do sistema)
     * @author 		Allan Rett Ferreira
     * @version		1.0
     * @since		10/03/2012
     */
    public function sairAction(){
        $data_atual     = new Zend_Date();
        $data_logout    = $data_atual->toString('dd/MM/yyyy H:m');
        $dados = array("ID"         => Zend_Auth::getInstance()->getIdentity()->ID_LOG,
                       "DATA_LOGOUT"=> $data_logout);
        $objLogAcesso = new Admin_Model_LogAcesso();
        $objLogAcesso->__save($dados);
        $auth = Zend_Auth::getInstance ();
        $auth->getStorage()->write( null );
        $this->_redirect('/');
    }

}