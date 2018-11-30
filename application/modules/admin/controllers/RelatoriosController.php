<?php
/**
 * Controller gerencia a pagina de relatorios
 * @filesource		/application/modules/admin/controllers/RelatoriosController.php
 * @author 		Allan Rett Ferreira
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Controllers
 * @package		Admin_RelatoriosController
 * @subpackage		Zend_Controller_Action
 * @version		1.0
 * @since		10/03/2012
 * 
 */

class Admin_RelatoriosController extends Zend_Controller_Action
{

    public function init()
    {        
        $head = new Commit_Controller_Action_Helper_Compatibilidade();        
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_js/library/ui/jquery.ui.datepicker.css')
                               ->appendStylesheet(PUBLIC_PATH. '_css/admin.css')
                               ->appendStylesheet($head->getCssGeral('admin'))
                               ->appendStylesheet(PUBLIC_PATH. '_css/relatorios.css')
                               ->appendStylesheet($head->getCssGeral('relatorios'));
        $this->view->headScript()->appendFile(PUBLIC_PATH. '_js/library/ui/jquery.ui.datepicker.js')
                                 ->appendFile(PUBLIC_PATH. '_js/admin.js')
                                 ->appendFile($head->getJsGeral('admin'))
                                 ->appendFile(PUBLIC_PATH. '_js/relatorios.js')
                                 ->appendFile("https://www.google.com/jsapi");
    }

    public function indexAction()
    {
    }

    public function organogramaAction()
    {
        
    }
        
    public function logacessoAction()
    {
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        $codigo = '';
        
        $form_pesquisa = new Commit_Form_Pesquisa();
        $form          = $form_pesquisa->logacesso();
        if ($this->_request->isPost()) {
            $data   = $this->_request->getPost('data', null);
            $login  = $this->_request->getPost('login', null);
        }else{
            $data   = @$extra[0];
            $login  = @$extra[1];
        }
            $data   = (strlen($data)>0) ? $data: null;
            $login  = (strlen($login)>0) ? $login: null;
        
        $form->populate(array('data'    => $data
                             ,'login'   => $login));

        $objPesquisa = new Admin_Model_LogAcesso();
        $resposta = $objPesquisa->fetchAllPesquisa($data, $login, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$data|$login");
        $this->view->form_pesquisa  = $form;
    }

    /**
     * Metodo que exibe o organograma
     * @name	infoAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     *
     */
    public function infoAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_cargo    = $this->_request->getParam("id_cargo",null);
        $data    = $this->_request->getParam("data",null);
        $objCargo = new Admin_Model_Cargos();
        
        //lista as informações dos cargos e subordinados
        $res = $objCargo->fetchAllPesquisa($id_cargo,null,false);
        $subordinados = $objCargo->fetchCargosRel($id_cargo,$data);
        
        //lista os centros de custo dos cargos e os funcionarios alocados nesse centro
        $objCusto = new Admin_Model_CCusto();
        $centros = $objCusto->fetchCustoCargo($id_cargo);
        
        //lista os processos relacionados ao cargo
        $objProc = new Admin_Model_Processos();
        $proc = $objProc->fetchAllProcessosCargosRel($id_cargo);
        $objProcLink = new Admin_Model_Processoslink();
        $processos = array();
        foreach ($proc as $key => $value) {
            $processos[$key] = $proc[$key];
            $processos[$key]["LINKS"] = $objProcLink->fetchAll($value["ID"]);
        }
        
        //lista os informativos relacionados ao cargo
        $objInfo = new Admin_Model_Informativos();
        $info = $objInfo->fetchAllInformativosCargo($id_cargo);
        $objInfoLink = new Admin_Model_Informativositemlink();
        $informativos = array();
        foreach ($info as $key => $value) {
            $informativos[$value["ID"]]["DESCRICAO"] = $value["DESCRICAO"];
            $informativos[$value["ID"]]["CONTEUDO"][$key] = $info[$key];
            $informativos[$value["ID"]]["CONTEUDO"][$key]["LINKS"] = $objInfoLink->fetchAll($value["INFORMATIVOS_ITEM"]);
        }
        //lista todos os arquivos do cargo
        $objArquivo = new Admin_Model_Arquivos();
        $arq = $objArquivo->fetchAllArquivos($id_cargo);
        
        $this->view->cargo          = $res;
        $this->view->centro         = $centros;
        $this->view->subordinados   = $subordinados;
        $this->view->processos      = $processos;
        $this->view->informativos   = $informativos;
        $this->view->arquivos       = $arq;
        
    }

    /**
     * Metodo que exibe as permissoes do organograma
     * @name	infoAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	20/03/2012
     * 
     *
     */
    public function permissoesAction()
    {
        $obj = new Admin_Model_MenuAcesso();
        $retorno = $obj->fetchAllLista();
        $this->view->usuarios = $retorno;
    }

    /**
     * Metodo que atualiza permissoes no organograma
     * @name	updateAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	20/03/2012
     *
     */
    public function updateAction()
    {
        $id_usuario    = base64_decode($this->_request->getParam("id",null));
        $obj = new Admin_Model_MenuAcesso();
        $retorno = $obj->fetchAllLista($id_usuario);
        
        $objCargo = new Admin_Model_Cargos();
        $objCargo->
        
        $this->view->usuarios = $retorno;
    }

    public function funcionariosAction()
    {
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        if($id_usuario == 'ITDESK' || $id_usuario == 'ZEZE'){
//            $id_usuario = 'HEITOR';
            $id_usuario = null;
        }
        $objColab = new Admin_Model_Colabview();
        $id_colab = $objColab->fetchAllPesquisa($id_usuario);
        
        $id_cargo = @$id_colab[0]['CD_CARGO'];
        
        //lista as informações dos cargos e subordinados
        $objCargo = new Admin_Model_Cargos();
        $res = $objCargo->fetchAllPesquisa($id_cargo,null,false);
        $subordinados = $objCargo->fetchUsu($id_usuario);
        
        //lista os centros de custo dos cargos e os funcionarios alocados nesse centro
        $objCusto = new Admin_Model_CCusto();
        $centros = $objCusto->fetchCustoCargo($id_cargo);
        
        $this->view->cargo = $res;
        $this->view->centro = $centros;
        $this->view->subordinados = $subordinados;
    }
    public function afastadosccustoAction()
    {
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        if($id_usuario == 'ITDESK' || $id_usuario == 'ZEZE'){
            $id_usuario = null;
        }
        $objColab = new Admin_Model_Colabview();
        $id_colab = $objColab->fetchAllPesquisa($id_usuario);
        
        $id_cargo = @$id_colab[0]['CD_CARGO'];
        
        //lista as informações dos cargos e subordinados
        $objCargo = new Admin_Model_Cargos();
        $res = $objCargo->fetchAllPesquisa($id_cargo,null,false);
        $subordinados = $objCargo->fetchUsuAfastados($id_usuario);
//        Zend_Debug::dump($subordinados);
        //lista os centros de custo dos cargos e os funcionarios alocados nesse centro
//        $centros = array();
//        foreach ($subordinados as $value) {
//            $centros[$value["CD_CCUSTO"]]["CUSTO"] = $value["CD_CCUSTO"];
//            $centros[$value["CD_CCUSTO"]]["NOME_CCUSTO"] = $value["CD_CCUSTO"];
//        }
        
        $objCusto = new Admin_Model_CCusto();
        $centros = $objCusto->fetchCustoCargo($id_cargo);
//        Zend_Debug::dump($centros);
        $this->view->cargo = $res;
        $this->view->centro = $centros;
        $this->view->subordinados = $subordinados;
    }

    public function horasAction()
    {
        
    }
    
    public function enviociAction() {
        $page       = $this->_request->getParam("page",null);        
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        
        $form_pesquisa = new Commit_Form_Pesquisa();
        $form          = $form_pesquisa->envioci();
        if ($this->_request->isPost()) {
            $ultimos    = $this->_request->getPost('ultimos', null);
            $enviado    = $this->_request->getPost('enviado', null);
            $assunto    = $this->_request->getPost('assunto', null);
        }else{
            $ultimos    = @$extra[0];
            $enviado    = @$extra[1];
            $assunto    = @$extra[2];
        }
            $ultimos    = (strlen($ultimos)>0) ? $ultimos: 100;
            $enviado    = (strlen($enviado)>0) ? $enviado: null;
            $assunto    = (strlen($assunto)>0) ? $assunto: null;
        
        $form->populate(array('ultimos' => $ultimos
                             ,'enviado' => $enviado
                             ,'assunto' => $assunto));

        
        
        $objCiEnvio = new Admin_Model_CiEnvio();
        $todos = $objCiEnvio->fetchAll($ultimos, $enviado, $assunto, $page,true);
        $this->view->todos          = $todos;
        $this->view->extra          = base64_encode("$ultimos|$enviado|$assunto");
        $this->view->form_pesquisa  = $form;
    }

}