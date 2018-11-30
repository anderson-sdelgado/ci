<?php
/**
 * Controller gerencia a pagina de relatorios
 * @filesource		usina/application/modules/admin/controllers/RelatoriosController.php
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

class Admin_CiController extends Zend_Controller_Action
{
    
    public function init()
    {
        $head = new Commit_Controller_Action_Helper_Compatibilidade();
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/relatorios.css')
                   ->appendStylesheet(PUBLIC_PATH. '_js/library/ui/jquery.ui.datepicker.css')
                   ->appendStylesheet(PUBLIC_PATH. '_css/admin.css')
                   ->appendStylesheet($head->getCssGeral('admin'))
                   ->appendStylesheet(PUBLIC_PATH. '_css/relatorios.css')
                   ->appendStylesheet($head->getCssGeral('relatorios'));
        $this->view->headScript()->appendFile(PUBLIC_PATH. '_js/library/ui/jquery.ui.datepicker.js')
                                 ->appendFile(PUBLIC_PATH. '_js/admin.js')
                                 ->appendFile($head->getJsGeral('admin'))
                                 ->appendFile(PUBLIC_PATH. '_js/relatorios.js');                                         
    }

    public function indexAction()
    {
    }
    
    public function consultarAction()
    {
        $gerar_xls              = $this->_request->getPost('gerar_xls', false);
        if($gerar_xls)
        $this->_helper->layout()->setLayout("xls");
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        $codigo = '';
        
        $form_pesquisa = new Commit_Form_Pesquisa();
        $form          = $form_pesquisa->ci();
        if ($this->_request->isPost()) {
            $de_ci          = $this->_request->getPost('de_ci', null);
            $ate_ci         = $this->_request->getPost('ate_ci', null);
            $data           = $this->_request->getPost('data', null);
            $ccusto_de      = $this->_request->getPost('ccusto_de', null);
            $ccusto_para    = $this->_request->getPost('ccusto_para', null);
            $status         = $this->_request->getPost('status', null);
            $finalidade     = $this->_request->getPost('finalidade', null);
        }else{
            $de_ci          = @$extra[0];
            $ate_ci         = @$extra[1];
            $data           = @$extra[2];
            $ccusto_de      = @$extra[3];
            $ccusto_para    = @$extra[4];
            $status         = @$extra[5];
            $finalidade     = @$extra[6];
        }
            $de_ci          = (strlen($de_ci)>0) ? $de_ci: null;
            $ate_ci         = (strlen($ate_ci)>0) ? $ate_ci: null;
            $data           = (strlen($data)>0) ? $data: null;
            $ccusto_de      = (strlen($ccusto_de)>0) ? $ccusto_de: null;
            $ccusto_para    = (strlen($ccusto_para)>0) ? $ccusto_para: null;
            $status         = (strlen($status)>0) ? $status: null;
            $finalidade     = (strlen($finalidade)>0) ? $finalidade: null;
        
        $form->populate(array('de_ci'       => $de_ci
                             ,'ate_ci'      => $ate_ci
                             ,'data'        => $data
                             ,'ccusto_de'   => $ccusto_de
                             ,'ccusto_para' => $ccusto_para
                             ,'finalidade'  => $finalidade
                             ,'status'      => $status));

        $objPesquisa = new Admin_Model_Ci();
        if($gerar_xls){
            $resposta = $objPesquisa->fetchAllPesquisa($de_ci, $ate_ci, $data, $ccusto_de, $ccusto_para, $status, $finalidade, $page, false);
        }else{
            $resposta = $objPesquisa->fetchAllPesquisa($de_ci, $ate_ci, $data, $ccusto_de, $ccusto_para, $status, $finalidade, $page);
        }
        
        $this->view->usuario_logado = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$de_ci|$ate_ci|$data|$ccusto_de|$ccusto_para|$status|$finalidade");
        $this->view->codigo         = $codigo;
        $this->view->form_pesquisa  = $form;
        $this->view->gerar_xls      = $gerar_xls;
    }    
     public function detalhesAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id         = base64_decode($this->_request->getPost("id",null));        
        $objCI      = new Admin_Model_Ci();
        $CI         = $objCI->getCI($id);
        
        $objCILog   = new Admin_Model_Cilog();
        $LogCI      = $objCILog->getLogCI($id);
        
        $visualizar     = true;
        $Anexos_base     = array();
        if($id){
            $objCiAnexo = new Admin_Model_CiAnexo();
            $Anexos_b    = $objCiAnexo->fetchAll($id);
            foreach ($Anexos_b as $value) {
                $Anexos_base["base-".$value["ID"]]["CI"]            = $value["CI"];
                $Anexos_base["base-".$value["ID"]]["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                $Anexos_base["base-".$value["ID"]]["NOME_FISICO"]   = $value["NOME_FISICO"];
                $Anexos_base["base-".$value["ID"]]["TAMANHO"]       = $value["TAMANHO"];
                $Anexos_base["base-".$value["ID"]]["DESCRICAO"]     = $value["DESCRICAO"];
            }
        }
        
        $objUsuario  = new Admin_Model_Usuario();
        $Aprovadores = $objUsuario->fetchAllAprovadores2($CI['CD_CCUSTO_PARA'], $CI['VALOR_ORIGINAL'], $CI['USUARIO_LOGIN']);
        
        $Log         = array();
        if($CI["ID_STATUS"] != 3 && $CI["ID_STATUS"] != 4 && $CI["ID_STATUS"] != 5 ){
            foreach ($Aprovadores as $value1) {
                $Log[$value1["LOGIN"]]["DATA"] = $CI["DATA"];
                $Log[$value1["LOGIN"]]["LOGIN"] = $value1["LOGIN"];
                $Log[$value1["LOGIN"]]["APROVADOR"] = $value1["NOME"];
                $Log[$value1["LOGIN"]]["STATUS"] = "Aguardando Aprovação";
                $Log[$value1["LOGIN"]]["ID_STATUS"] = '0';
                $Log[$value1["LOGIN"]]["MOTIVO_CANCELAMENTO"] = null;
            }
        }
        foreach ($LogCI as $value2) {
            $Log[$value2["LOGIN"]]["DATA"] = $value2["DATA"];
            $Log[$value2["LOGIN"]]["LOGIN"] = $value2["LOGIN"];
            $Log[$value2["LOGIN"]]["APROVADOR"] = $value2["APROVADOR"];
            $Log[$value2["LOGIN"]]["STATUS"] = $value2["STATUS"];
            $Log[$value2["LOGIN"]]["ID_STATUS"] = $value2["ID_STATUS"];
            $Log[$value2["LOGIN"]]["MOTIVO_CANCELAMENTO"] = $value2["MOTIVO_CANCELAMENTO"];
        }
        
        $this->view->Anexos      = $Anexos_base;
        $this->view->visualizar = $visualizar;
        
        $this->view->CI     = $CI;
        $this->view->LogCI  = $Log;
    }
     public function imprimirAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id         = base64_decode($this->_request->getParam("id",null));        
        $objCI      = new Admin_Model_Ci();
        $CI         = $objCI->getCI($id);
        
        $objCILog   = new Admin_Model_Cilog();
        $LogCI      = $objCILog->getLogCI($id);
        
        $objCiAnexo = new Admin_Model_CiAnexo();
        $Anexos     = $objCiAnexo->fetchAll($id);
        
        $this->view->CI     = $CI;
        $this->view->LogCI  = $LogCI;
        $this->view->Anexos = $Anexos;
    }
    public function aprovarAction()
    {
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
//        $codigo = '';
        
        $form_pesquisa = new Commit_Form_Pesquisa();
        $form          = $form_pesquisa->ci("aprovar");
        if ($this->_request->isPost()) {
            $de_ci          = $this->_request->getPost('de_ci', null);
            $ate_ci         = $this->_request->getPost('ate_ci', null);
            $data           = $this->_request->getPost('data', null);
            $ccusto_de      = $this->_request->getPost('ccusto_de', null);
            $ccusto_para    = $this->_request->getPost('ccusto_para', null);
            $status         = $this->_request->getPost('status', null);
            $finalidade     = $this->_request->getPost('finalidade', null);
        }else{
            $de_ci          = @$extra[0];
            $ate_ci         = @$extra[1];
            $data           = @$extra[2];
            $ccusto_de      = @$extra[3];
            $ccusto_para    = @$extra[4];
            $status         = @$extra[5];
            $finalidade     = @$extra[5];
        }
            $de_ci          = (strlen($de_ci)>0) ? $de_ci: null;
            $ate_ci         = (strlen($ate_ci)>0) ? $ate_ci: null;
            $data           = (strlen($data)>0) ? $data: null;
            $ccusto_de      = (strlen($ccusto_de)>0) ? $ccusto_de: null;
            $ccusto_para    = (strlen($ccusto_para)>0) ? $ccusto_para: null;
            $status         = (strlen($status)>0) ? $status: null;
            $finalidade     = (strlen($finalidade)>0) ? $finalidade: null;
        
        $form->populate(array('de_ci'       => $de_ci
                             ,'ate_ci'      => $ate_ci
                             ,'data'        => $data
                             ,'ccusto_de'   => $ccusto_de
                             ,'ccusto_para' => $ccusto_para
                             ,'finalidade'  => $finalidade
                             ,'status'      => $status));

        $objPesquisa = new Admin_Model_Ci();
        $resposta = $objPesquisa->fetchAllAprovar($de_ci, $ate_ci, $data, $ccusto_de, $ccusto_para, $status, $finalidade, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$de_ci|$ate_ci|$data|$ccusto_de|$ccusto_para|$status|$finalidade");
//        $this->view->codigo         = $codigo;
        $this->view->form_pesquisa  = $form;
    }
    
    /**
     * Metodo para aprovar CI
     * @name	aprovarciAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	08/10/2012
     */
    public function aprovarciAction()
    {        
        $this->_helper->layout()->disableLayout();
        $ci     = base64_decode($this->_request->getParam('id'));
        $data   = new Zend_Date();
        $motivo = null;
        $form_pesquisa = new Commit_Form_Cadastro();
        $form          = $form_pesquisa->aprovarCI("id/".  base64_encode($ci));
        if ($this->_request->isPost()) {
            $ci     = $this->_request->getPost('id', null);
            $motivo = $this->_request->getPost('motivo', null);
        }
        
        $form->populate(array('id'      => $ci));
        
        $objCI = new Admin_Model_Ci();
        $CI    = $objCI->getCI($ci);        
        
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData)){
                $arrayDados['USUARIO']  = Zend_Auth::getInstance()->getIdentity()->ID;
                $arrayDados['CI']       = $ci;
                $arrayDados['STATUS']   = 3;
                $arrayDados['DATA']     = $data->toString('dd/MM/YYYY H:m');
                $arrayDadosCI['ID']     = $ci;        

                $objCI      = new Admin_Model_Ci();
                $necessario = $objCI->aprovacoesNecessarias($ci);
        //        Zend_Debug::dump($necessario);
                $obj = new Admin_Model_Cilog();
                $aprovados = $obj->fetchAllAprovados($ci);
        //        Zend_Debug::dump($aprovados);
                if(@$aprovados[0]["STATUS"] == 4){
                    $arrayDadosCI['STATUS'] = 4;
                    echo "Já está reprovado";
                }elseif($necessario<=count($aprovados)){
                    $arrayDadosCI['STATUS'] = 3;
                    echo "Já está aprovado";
                }elseif($necessario-count($aprovados)==1){
                    $arrayDadosCI['STATUS'] = 3;
                }else{
                    $arrayDadosCI['STATUS'] = 2;
                }

                $retorno = $obj->_save($arrayDados);
                if (strlen($retorno[0]) == 0){
                    $objCI                       = new Admin_Model_Ci();
                    $objCiStatus                 = new Admin_Model_Cistatus();
                    $CiStatus                    = $objCiStatus->find($arrayDadosCI['STATUS']);
                    $arrayDadosCI['DESC_STATUS'] = $CiStatus["DESCRICAO"];
                    $retorno = $objCI->save($arrayDadosCI);
                }
                if (strlen($retorno[0]) > 0){

                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $mensagem = $this->emailcopia($ci);
                    if($mensagem == 'enviada'){
                        $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    }else{
                        $this->view->mensagem = base64_encode( $this->getMensagem('warning', 'ERRO_EMAIL_CI'));
                    }
                }
            }
        }        
        $Anexos_base     = array();
        if($ci){
            $objCiAnexo = new Admin_Model_CiAnexo();
            $Anexos_b    = $objCiAnexo->fetchAll($ci);
            foreach ($Anexos_b as $value) {
                $Anexos_base["base-".$value["ID"]]["CI"]            = $value["CI"];
                $Anexos_base["base-".$value["ID"]]["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                $Anexos_base["base-".$value["ID"]]["NOME_FISICO"]   = $value["NOME_FISICO"];
                $Anexos_base["base-".$value["ID"]]["TAMANHO"]       = $value["TAMANHO"];
                $Anexos_base["base-".$value["ID"]]["DESCRICAO"]     = $value["DESCRICAO"];
            }
        }
        $this->view->Anexos = $Anexos_base;
        $this->view->form   = $form;
        $this->view->CI     = $CI;
    }
    
    
    /**
     * Metodo para reprovar CI
     * @name	reprovarciAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	08/10/2012
     */
    public function reprovarciAction()
    {
        $this->_helper->layout()->disableLayout();
        $ci     = base64_decode($this->_request->getParam('id'));
        $data   = new Zend_Date();
        $motivo = null;
        $form_pesquisa = new Commit_Form_Cadastro();
        $form          = $form_pesquisa->reprovarCI("id/".  base64_encode($ci));
        if ($this->_request->isPost()) {
            $ci     = $this->_request->getPost('id', null);
            $motivo = $this->_request->getPost('motivo', null);
        }
        
        $form->populate(array('id'      => $ci
                             ,'motivo'  => $motivo));
        
        
        $objCI = new Admin_Model_Ci();
        $CI    = $objCI->getCI($ci);
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData)){
                $arrayDados['USUARIO']              = Zend_Auth::getInstance()->getIdentity()->ID;
                $arrayDados['CI']                   = $ci;
                $arrayDados['STATUS']               = 4;
                $arrayDados['DATA']                 = $data->toString('dd/MM/YYYY H:m');
                $arrayDados['MOTIVO_CANCELAMENTO']  = $motivo;

                $obj = new Admin_Model_Cilog();
                $retorno = $obj->save($arrayDados);
                if (strlen($retorno[0]) == 0){
                    $id_status                   = 4;
                    $objCiStatus                 = new Admin_Model_Cistatus();
                    $CiStatus                    = $objCiStatus->find($id_status);
                    $arrayDadosCI['ID']          = $ci;
                    $arrayDadosCI['STATUS']      = $id_status;
                    $arrayDadosCI['DESC_STATUS'] = $CiStatus["DESCRICAO"];
                    $retorno = $objCI->save($arrayDadosCI);
                }
                if (strlen($retorno[0]) > 0){

                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $mensagem = $this->emailcopia($ci);
                    if($mensagem == 'enviada'){
                        $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    }else{
                        $this->view->mensagem = base64_encode( $this->getMensagem('warning', 'ERRO_EMAIL_CI'));
                    }
                }
            }
        }        
        $Anexos_base     = array();
        if($ci){
            $objCiAnexo = new Admin_Model_CiAnexo();
            $Anexos_b    = $objCiAnexo->fetchAll($ci);
            foreach ($Anexos_b as $value) {
                $Anexos_base["base-".$value["ID"]]["CI"]            = $value["CI"];
                $Anexos_base["base-".$value["ID"]]["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                $Anexos_base["base-".$value["ID"]]["NOME_FISICO"]   = $value["NOME_FISICO"];
                $Anexos_base["base-".$value["ID"]]["TAMANHO"]       = $value["TAMANHO"];
                $Anexos_base["base-".$value["ID"]]["DESCRICAO"]     = $value["DESCRICAO"];
            }
        }
        $this->view->Anexos = $Anexos_base;
        $this->view->form = $form;
        $this->view->CI   = $CI;
    }
    
    /**
     * Metodo para avaliar CI
     * @name	reprovarciAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	08/10/2012
     */
    public function avaliarciAction()
    {
        $this->_helper->layout()->disableLayout();
        $ci     = base64_decode($this->_request->getParam('id'));
        $data   = new Zend_Date();
        $motivo = null;
        $form_pesquisa = new Commit_Form_Cadastro();
        $form          = $form_pesquisa->avaliarCI("id/".  base64_encode($ci));
        if ($this->_request->isPost()) {
            $ci     = $this->_request->getPost('id', null);
            $motivo = $this->_request->getPost('motivo', null);
            $status = $this->_request->getPost('status', null);
        }
        
        $form->populate(array('id'      => $ci
                             ,'motivo'  => $motivo));
        
        
        $objCI = new Admin_Model_Ci();
        $CI    = $objCI->getCI($ci);
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            if($formData['status']==3){
                $form->motivo->setRequired(false);
            }
            if ($form->isValid($formData)){
                $arrayDados['USUARIO']              = Zend_Auth::getInstance()->getIdentity()->ID;
                $arrayDados['CI']                   = $ci;
                $arrayDados['STATUS']               = $status;
                $arrayDados['DATA']                 = $data->toString('dd/MM/YYYY H:m');
                $arrayDados['MOTIVO_CANCELAMENTO']  = $motivo;

                $objCI      = new Admin_Model_Ci();
                $necessario = $objCI->aprovacoesNecessarias($ci);
                $obj        = new Admin_Model_Cilog();
                $aprovados  = $obj->fetchAllAprovados($ci);
                    
                $retorno = $obj->save($arrayDados);
                
                if (strlen($retorno[0]) == 0 && $status == 4){
                    $objCiStatus                 = new Admin_Model_Cistatus();
                    $CiStatus                    = $objCiStatus->find($status);
                    $arrayDadosCI['ID']          = $ci;
                    $arrayDadosCI['STATUS']      = $status;
                    $arrayDadosCI['DESC_STATUS'] = $CiStatus["DESCRICAO"];
                    $retorno                     = $objCI->save($arrayDadosCI);
                }
                if (strlen($retorno[0]) == 0 && $status == 3){
                    $arrayDadosCI['ID']     = $ci;
                    if(@$aprovados[0]["STATUS"] == 4){
                        $arrayDadosCI['STATUS'] = 4;
                        echo "Já está reprovado";
                    }elseif($necessario<=count($aprovados)){
                        $arrayDadosCI['STATUS'] = 3;
                        echo "Já está aprovado";
                    }elseif($necessario-count($aprovados)==1){
                        $arrayDadosCI['STATUS'] = 3;
                    }else{
                        $arrayDadosCI['STATUS'] = 2;
                    }                    
                    $objCiStatus                 = new Admin_Model_Cistatus();
                    $CiStatus                    = $objCiStatus->find($arrayDadosCI['STATUS']);
                    $arrayDadosCI['DESC_STATUS'] = $CiStatus["DESCRICAO"];
                    $retorno = $objCI->save($arrayDadosCI);
                }
                if (strlen($retorno[0]) > 0){

                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $mensagem = $this->emailcopia($ci);                    
//                    $mensagem = 'enviada';
                    if($mensagem == 'enviada'){
                        $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    }else{
                        $this->view->mensagem = base64_encode( $this->getMensagem('warning', 'ERRO_EMAIL_CI'));
                    }
                }
            }
        }        
        $Anexos_base     = array();
        if($ci){
            $objCiAnexo = new Admin_Model_CiAnexo();
            $Anexos_b    = $objCiAnexo->fetchAll($ci);
            foreach ($Anexos_b as $value) {
                $Anexos_base["base-".$value["ID"]]["CI"]            = $value["CI"];
                $Anexos_base["base-".$value["ID"]]["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                $Anexos_base["base-".$value["ID"]]["NOME_FISICO"]   = $value["NOME_FISICO"];
                $Anexos_base["base-".$value["ID"]]["TAMANHO"]       = $value["TAMANHO"];
                $Anexos_base["base-".$value["ID"]]["DESCRICAO"]     = $value["DESCRICAO"];
            }
        }
        $this->view->Anexos = $Anexos_base;
        $this->view->form = $form;
        $this->view->CI   = $CI;
    }
    
    public function logAction()
    {        
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        
        $form_pesquisa = new Commit_Form_Pesquisa();
        $form          = $form_pesquisa->ci("log");
        if ($this->_request->isPost()) {
            $de_ci          = $this->_request->getPost('de_ci', null);
            $ate_ci         = $this->_request->getPost('ate_ci', null);
            $data           = $this->_request->getPost('data', null);
            $ccusto_de      = $this->_request->getPost('ccusto_de', null);
            $ccusto_para    = $this->_request->getPost('ccusto_para', null);
            $status         = $this->_request->getPost('status', null);
            $finalidade     = $this->_request->getPost('finalidade', null);
        }else{
            $de_ci          = @$extra[0];
            $ate_ci         = @$extra[1];
            $data           = @$extra[2];
            $ccusto_de      = @$extra[3];
            $ccusto_para    = @$extra[4];
            $status         = @$extra[5];
            $finalidade     = @$extra[6];
        }
            $de_ci          = (strlen($de_ci)>0) ? $de_ci: null;
            $ate_ci         = (strlen($ate_ci)>0) ? $ate_ci: null;
            $data           = (strlen($data)>0) ? $data: null;
            $ccusto_de      = (strlen($ccusto_de)>0) ? $ccusto_de: null;
            $ccusto_para    = (strlen($ccusto_para)>0) ? $ccusto_para: null;
            $status         = (strlen($status)>0) ? $status: null;
            $finalidade     = (strlen($finalidade)>0) ? $finalidade: null;
        
        $form->populate(array('de_ci'       => $de_ci
                             ,'ate_ci'      => $ate_ci
                             ,'data'        => $data
                             ,'ccusto_de'   => $ccusto_de
                             ,'ccusto_para' => $ccusto_para
                             ,'finalidade'  => $finalidade
                             ,'status'      => $status));

        $objPesquisa = new Admin_Model_Cilog();
        $resposta = $objPesquisa->fetchAllPesquisa($de_ci, $ate_ci, $data, $ccusto_de, $ccusto_para, $status, $finalidade, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$de_ci|$ate_ci|$data|$ccusto_de|$ccusto_para|$status|$finalidade");
        $this->view->form_pesquisa  = $form;
    }
    
    public function qtdeaprovadoresAction()
    {
        $objPesquisa = new Admin_Model_Valoraprovadores();
        $resposta = $objPesquisa->fetchAllPesquisa();
        
        $objPesquisa1= new Admin_Model_Valoraprovadoresgeral();
        $resposta1 = $objPesquisa1->fetchAllPesquisa();
        $retorno1 = array();
        
        foreach ($resposta1 as $value1) {
            $retorno1[$value1["VALOR_APROVADORES"]][] = $value1;
        }
        
        foreach ($resposta as $value) {
            $retorno[$value["ID"]] = $value;
            if(isset($retorno1[$value["ID"]])){
                $retorno[$value["ID"]]["USUARIOS"] = $retorno1[$value["ID"]];
            }else{
                $retorno[$value["ID"]]["USUARIOS"] = null;
            }            
        }
        
//        Zend_Debug::dump($resposta);
//        Zend_Debug::dump($retorno);
//        Zend_Debug::dump($retorno1);
        $this->view->paginator      = $retorno;
    }
    
    public function finalidadeAction()
    {
        $objPesquisa = new Admin_Model_Finalidade();
        $resposta = $objPesquisa->fetchAllPesquisa();
        
        $objFinalidadevisualizadores = new Admin_Model_Finalidadevisualizadores();
        $resposta1 = $objFinalidadevisualizadores->fetchAllUsuarios();
        $retorno1 = array();
        
        foreach ($resposta1 as $value1) {
//            Zend_Debug::dump($value1->toArray());
            $retorno1[$value1["FINALIDADE"]][] = $value1;
        }
        
        foreach ($resposta as $value) {
            $retorno[$value["ID"]] = $value;
            if(isset($retorno1[$value["ID"]])){
                $retorno[$value["ID"]]["USUARIOS"] = $retorno1[$value["ID"]];
            }else{
                $retorno[$value["ID"]]["USUARIOS"] = null;
            }            
        }
//        Zend_Debug::dump($retorno);
//        exit;
        $this->view->paginator      = $retorno;
    }
    
    /**
     * Metodo que cadastra valores x emails em copia
     * @name	insertfinalidadeAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	13/11/2012
     */
    public function insertfinalidadeAction()
    {
        $this->_helper->layout()->setLayout("dialog");
//        $this->_helper->layout()->disableLayout();
        $form = new Commit_Form_Cadastro();
        $finalidade = $form->finalidade();
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($finalidade->isValid($formData)){
                $arrayDados['DESCRICAO']    = ($this->_request->getPost('descricao'));
                $arrayDados['EMAIL_COPIA']  = ($this->_request->getPost('email_copia'));
                $arrayDados['ATIVO']        = ($this->_request->getPost('ativo'));
                
                $obj     = new Admin_Model_Finalidade();
                $retorno = $obj->save($arrayDados);
                
                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        $this->view->formulario = $finalidade;
    }
    
    
    
    /**
     * Metodo que edita valor x email em copia
     * @name	insertAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	04/10/2012
     */
    public function updatefinalidadeAction()
    {
        $this->_helper->layout()->setLayout("dialog");
        $id              = $this->_request->getParam('id');
        $obj             = new Admin_Model_Finalidade();
        $form            = new Commit_Form_Cadastro();
        $finalidade = $form->finalidade("updatefinalidade/id/$id");
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($finalidade->isValid($formData)){
                $arrayDados['ID']           = base64_decode($id);
                $arrayDados['DESCRICAO']    = ($this->_request->getPost('descricao'));
                $arrayDados['EMAIL_COPIA']  = ($this->_request->getPost('email_copia'));
                $arrayDados['ATIVO']        = ($this->_request->getPost('ativo'));
                
                $obj     = new Admin_Model_Finalidade();
                $retorno = $obj->save($arrayDados);
                
                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }else{
            $arrayDados = $obj->_find(base64_decode($id));
        }
        $finalidade->populate(array('id'            => base64_encode($arrayDados["ID"])
                                    ,'descricao'    =>$arrayDados["DESCRICAO"]
                                    ,'email_copia'  =>$arrayDados["EMAIL_COPIA"]
                                    ,'ativo'        =>$arrayDados["ATIVO"] ));
        
        $this->view->formulario = $finalidade;
    }
    
    public function deletefinalidadeAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id                     = base64_decode($this->_request->getParam("id",null));        
        $objCcustoAprovadores   = new Admin_Model_Finalidade();
        $save                   = $objCcustoAprovadores->delete($id);
//        Zend_Debug::dump($save);
        echo $save[0];
    }


    
    /**
     * Metodo que cadastra qtde x aprovadores
     * @name	insertAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	04/10/2012
     */
    public function insertqtdeaprovadoresAction()
    {
        $this->_helper->layout()->setLayout("dialog");
        $form = new Commit_Form_Cadastro();
        $qtdeaprovadores = $form->qtdeaprovadores();
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($qtdeaprovadores->isValid($formData)){
                if($_SERVER["COMPUTERNAME"] == "WNTVMIT"){
                    $arrayDados['VALOR_INICIAL']    = str_replace(",", ".", str_replace(".", "", $this->_request->getPost('valor_inicial')));
                    $arrayDados['VALOR_FINAL']      = str_replace(",", ".", str_replace(".", "", $this->_request->getPost('valor_final')));
                }else{
                    $arrayDados['VALOR_INICIAL']    = str_replace(".", "", $this->_request->getPost('valor_inicial'));
                    $arrayDados['VALOR_FINAL']      = str_replace(".", "", $this->_request->getPost('valor_final'));
                }
                $arrayDados['APROVADORES']      = ($this->_request->getPost('qtdeaprovadores'));
                $arrayDados['EMAILCOPIA']           = ($this->_request->getPost('emailcopia'));

                $obj = new Admin_Model_Valoraprovadores();
                
                $pesquisa_inicial   = $obj->fetchAllPesquisa(null, $arrayDados['VALOR_INICIAL']);
                $pesquisa_final     = $obj->fetchAllPesquisa(null, null, $arrayDados['VALOR_FINAL']);
                $pesquisa_total     = $obj->fetchAllPesquisa(null, $arrayDados['VALOR_INICIAL'], $arrayDados['VALOR_FINAL']);
                
                if(count($pesquisa_total)>0){
                    $retorno[0] = "PARTE_COMPREENDIDO";
                }elseif(count($pesquisa_inicial)>0 && count($pesquisa_final)>0){
                    $retorno[0] = "EXISTE_AMBOS";
                }elseif(count($pesquisa_inicial)>0){
                    $retorno[0] = "EXISTE_INICIAL";
                }elseif(count($pesquisa_final)>0){
                    $retorno[0] = "EXISTE_FINAL";
                }else{
                    $retorno = $obj->save($arrayDados);
                }       
                
                
                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        $this->view->formulario = $qtdeaprovadores;
    }
    
    /**
     * Metodo que cadastra CI
     * @name	cadastroAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	04/10/2012
     */
    public function cadastroAction()
    {
        $form = new Commit_Form_Cadastro();
        $form_ci            = $form->ci();
        $defaultNamespace   = new Zend_Session_Namespace('ANEXOS');
        $Anexos_s           = $defaultNamespace->Anexos;
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($form_ci->isValid($formData)){
                $arrayDados['EMPRESA']      = ($this->_request->getPost('empresa'));
                $arrayDados['DATA']         = ($this->_request->getPost('data_ci'));
                $arrayDados['CCUSTO_DE']    = ($this->_request->getPost('ccusto_de'));
                $arrayDados['CCUSTO_PARA']  = ($this->_request->getPost('ccusto_para'));
                $arrayDados['FINALIDADE']   = ($this->_request->getPost('finalidade'));
                $arrayDados['MOTIVO_CI']    = ($this->_request->getPost('motivo'));
                $valor                   = ($this->_request->getPost('valor'));
                $valor_ci                = strlen($valor)>0 ? $valor : '0,00';
                if($_SERVER["COMPUTERNAME"] == "WNTVMIT"){
                    $arrayDados['VALOR']    = str_replace(",", ".", str_replace(".", "", $valor_ci));
                }else{
                    $arrayDados['VALOR']    = str_replace(".", "", $valor_ci);
                }
                $arrayDados['USUARIO']      = Zend_Auth::getInstance()->getIdentity()->LOGIN;
                $objUsuario  = new Admin_Model_Usuario();
                $arpovadores = $objUsuario->fetchAllAprovadores2($arrayDados['CCUSTO_PARA'], $arrayDados['VALOR']);

                if(count($arpovadores)>0){
                    $obj            = new Admin_Model_Ci();
                    $objCcusto      = new Admin_Model_CCusto();
                    $objFinalidade  = new Admin_Model_Finalidade();
                    $objCiStatus    = new Admin_Model_Cistatus();
                    $ccusto_de      = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_DE']);
                    $ccusto_para    = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_PARA']);
                    $finalidade     = $objFinalidade->find($arrayDados['FINALIDADE']);
                    $status         = $objCiStatus->find(1);

                    $arrayDados['DESC_CCUSTO_DE']   = $ccusto_de["NOME_CCUSTO"];
                    $arrayDados['DESC_CCUSTO_PARA'] = $ccusto_para["NOME_CCUSTO"];
                    $arrayDados['DESC_FINALIDADE']  = $finalidade["DESCRICAO"];
                    $arrayDados['DESC_STATUS']      = $status["DESCRICAO"];
                    
                    $retorno = $obj->save($arrayDados,true);
                    $id = $retorno[1];
                    if (strlen($retorno[0]) == 0 && $Anexos_s){                    
                        $objCiAnexo = new Admin_Model_CiAnexo();
                        foreach ($Anexos_s as $key => $value) {
                            $Anexos["CI"]            = $id;
                            $Anexos["NOME_FISICO"]   = $value["NOME_FISICO"];
                            $Anexos["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                            $Anexos["TAMANHO"]       = $value["TAMANHO"];
                            $Anexos["DESCRICAO"]     = $value["DESCRICAO"];
                            $retorno = $objCiAnexo->save($Anexos);
                            if (strlen($retorno[0]) > 0){
                                break;
                            }else{
                                unset($defaultNamespace->Anexos[$key]);
                            }
                        }
                    }
                    if (strlen($retorno[0]) == 0){
//                        Zend_Debug::dump($retorno);
                        $emailAprovar = $this->emailaprovar($id);
                        $mensagem = $emailAprovar['mensagem'];
                        if($mensagem == 'enviada'){
                            $mensagem = $this->emailcopia($id,null,$emailAprovar['enviados']);
                        }
                    }
                }else{
                    $retorno[0] = "CRIAR_CI_SEM_APROVADOR";
                }
                
                if (strlen($retorno[0]) > 0){                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    if($mensagem == 'enviada'){
                        $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    }else{
                        $this->view->mensagem = base64_encode( $this->getMensagem('warning', 'ERRO_EMAIL_CI'));
                    }
                    $form_ci->reset();
                    $date = new Zend_Date();
                    $hoje = $date->get('dd/MM/yyyy HH:mm');
                    $form_ci->populate(array('data_ci' => $hoje));
                }
            }
        }
        $this->view->formulario = $form_ci;
    }
    /**
     * Metodo que atualiza CI
     * @name	updateAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	23/11/2012
     */
    public function updateAction()
    {
        $usuario_logado = Zend_Auth::getInstance()->getIdentity()->LOGIN;
//        $this->_helper->layout()->setLayout("dialog");
        $id         = base64_decode($this->_request->getParam("id",null));
        $form = new Commit_Form_Cadastro();
        $form_ci    = $form->ci('update/id/'.  base64_encode($id));
        
        $objCI      = new Admin_Model_Ci();
        $CI         = $objCI->findCI($id);
//        Zend_Debug::dump($CI);        
        if($CI["USUARIO"] == $usuario_logado ){
            $defaultNamespace   = new Zend_Session_Namespace('ANEXOS');
            $Anexos_s           = $defaultNamespace->Anexos;
            if ($this->_request->isPost()) {
                $arrayDados = array();
                $formData = $this->_request->getPost();

                if ($form_ci->isValid($formData)){
                    
                    
                    $id                         = base64_decode($this->_request->getPost("id",null));
                    $mensagem                   = "";
                    $arrayDados['ID']           = $id;
                    $arrayDados['EMPRESA']      = ($this->_request->getPost('empresa'));
                    $arrayDados['DATA']         = ($this->_request->getPost('data_ci'));
                    $arrayDados['CCUSTO_DE']    = ($this->_request->getPost('ccusto_de'));
                    $arrayDados['CCUSTO_PARA']  = ($this->_request->getPost('ccusto_para'));
                    $arrayDados['FINALIDADE']   = ($this->_request->getPost('finalidade'));
                    $arrayDados['MOTIVO_CI']    = ($this->_request->getPost('motivo'));
                    $valor                   = ($this->_request->getPost('valor'));
                    $valor_ci                = strlen($valor)>0 ? $valor : '0,00';
                    if($_SERVER["COMPUTERNAME"] == "WNTVMIT"){
                        $arrayDados['VALOR']    = str_replace(",", ".", str_replace(".", "", $valor_ci));
                    }else{
                        $arrayDados['VALOR']    = str_replace(".", "", $valor_ci);
                    }
                    $arrayDados['USUARIO']      = $usuario_logado;

                    $objUsuario  = new Admin_Model_Usuario();
                    $arpovadores = $objUsuario->fetchAllAprovadores2($arrayDados['CCUSTO_PARA'], $arrayDados['VALOR']);
                    
                    if(count($arpovadores)>0){
                        $obj            = new Admin_Model_Ci();
                        $objCcusto      = new Admin_Model_CCusto();
                        $objFinalidade  = new Admin_Model_Finalidade();
                        $ccusto_de      = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_DE']);
                        $ccusto_para    = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_PARA']);
                        $finalidade     = $objFinalidade->find($arrayDados['FINALIDADE']);
                        
                        $arrayDados['DESC_CCUSTO_DE']   = $ccusto_de["NOME_CCUSTO"];
                        $arrayDados['DESC_CCUSTO_PARA'] = $ccusto_para["NOME_CCUSTO"];
                        $arrayDados['DESC_FINALIDADE']  = $finalidade["DESCRICAO"];
                    
                        $retorno = $obj->save($arrayDados);
                        if (strlen($retorno[0]) == 0 && $Anexos_s){                    
                            $objCiAnexo = new Admin_Model_CiAnexo();
                            foreach ($Anexos_s as $key => $value) {
                                $Anexos["CI"]            = $id;
                                $Anexos["NOME_FISICO"]   = $value["NOME_FISICO"];
                                $Anexos["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                                $Anexos["TAMANHO"]       = $value["TAMANHO"];
                                $Anexos["DESCRICAO"]     = $value["DESCRICAO"];
                                $retorno = $objCiAnexo->save($Anexos);
                                if (strlen($retorno[0]) > 0){
                                    break;
                                }else{
                                    unset($defaultNamespace->Anexos[$key]);
                                }
                            }
                        }
                        if (strlen($retorno[0]) == 0){
                                $mensagem = $this->emailcopia($id,"Alterada");
                        }
                    }else{
                        $retorno[0] = "ALTERAR_CI_SEM_APROVADOR";
                    }

                    if (strlen($retorno[0]) > 0){                    
                        $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                    }else{
                        if($mensagem == 'enviada'){
                            $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                        }else{
                            $this->view->mensagem = base64_encode( $this->getMensagem('warning', 'ERRO_EMAIL_CI'));
                        }
                    }
                    $CI = $arrayDados;
                }
            }
            $form_ci->data_ci->setLabel($CI['DATA']);
            $form_ci->populate(array('id'           => base64_encode($CI['ID']),
                                     'empresa'      => $CI['EMPRESA'],
//                                     'data'         => $CI['DATA'],
                                     'ccusto_de'    => $CI['CCUSTO_DE'],
                                     'ccusto_para'  => $CI['CCUSTO_PARA'],
                                     'finalidade'   => $CI['FINALIDADE'],
                                     'motivo'       => $CI['MOTIVO_CI'],
                                     'valor'        => $CI['VALOR']));
            $this->view->formulario = $form_ci;
        }else{
            $this->view->formulario = "Voc&ecirc; n&atilde;o tem Permiss&atilde;o para editar este Registro";
        }
        $this->view->numero_ci      = $id;
    }
    
    /**
     * Metodo que cancela a CI
     * @name	cancelarciAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	29/01/2013
     */
    public function cancelarciAction()
    {
        $this->_helper->layout()->setLayout("dialog");
        $usuario_logado = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $id             = base64_decode($this->_request->getParam("id",null));
        $form           = new Commit_Form_Cadastro();
        $form_cancelar  = $form->cancelarCI('cancelarci/id/'.  base64_encode($id));
        $data           = new Zend_Date();
        $objCI          = new Admin_Model_Ci();
        $CI             = $objCI->findCI($id);
        if($CI["USUARIO"] == $usuario_logado ){
            $arrayDadosCI   = array();
            $arrayDados     = array();
            if ($this->_request->isPost()) {
                $formData       = $this->_request->getPost();

                if ($form_cancelar->isValid($formData)){                    
                    $id                         = base64_decode($this->_request->getPost("id",null));
                    $motivo                     = ($this->_request->getPost('motivo'));
                    $mensagem                   = "";
                    $arrayDadosCI['ID']         = $id;
                    $arrayDadosCI['STATUS']     = 5;
                    $objCiStatus                = new Admin_Model_Cistatus();
                    $CiStatus                   = $objCiStatus->find($arrayDadosCI['STATUS']);
                    $arrayDadosCI['DESC_STATUS'] = $CiStatus["DESCRICAO"];
                    $retorno = $objCI->save($arrayDadosCI);
                    
                    if (strlen($retorno[0]) == 0){
                        $arrayDados['USUARIO']              = Zend_Auth::getInstance()->getIdentity()->ID;
                        $arrayDados['CI']                   = $id;
                        $arrayDados['STATUS']               = 5;
                        $arrayDados['DATA']                 = $data->toString('dd/MM/YYYY H:m');
                        $arrayDados['MOTIVO_CANCELAMENTO']  = $motivo;

                        $obj = new Admin_Model_Cilog();
                        $retorno = $obj->save($arrayDados);
                    }
                    if (strlen($retorno[0]) == 0){
                            $mensagem = $this->emailcopia($id,"Cancelada");
                    }

                    if (strlen($retorno[0]) > 0){                    
                        $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                    }else{
                        if($mensagem == 'enviada'){
                            $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                        }else{
                            $this->view->mensagem = base64_encode( $this->getMensagem('warning', 'ERRO_EMAIL_CI'));
                        }
                    }
                    $CI = $arrayDados;
                }
            }
            $form_cancelar->populate(array('id'     => base64_encode($CI['ID']),
                                           'motivo' => @$arrayDados['MOTIVO_CANCELAMENTO']));
            $this->view->formulario = $form_cancelar;
        }else{
            $this->view->formulario = "Voc&ecirc; n&atilde;o tem Permiss&atilde;o para cancelar esta CI";
        }
        $this->view->numero_ci      = $id;
    }
    
    private function emailaprovar($id_ci) {
        ini_set('max_execution_time', 120);
        $Helper_mascaras = new Commit_Controller_Action_Helper_Mascaras();
        $objCi       = new Admin_Model_Ci();

        $arrayDados  = $objCi->findCI($id_ci);
        $objCcusto   = new Admin_Model_CCusto();
        $Ccusto_de   = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_DE']);
        $Ccusto_para = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_PARA']);        
        
        $usuario_login = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $id_usuario  = (int)(Zend_Auth::getInstance()->getIdentity()->ID_USUARIO_ORACLE);

        $objUsuario  = new Admin_Model_Usuario();
        $Usuario     = $objUsuario->fetchIdOracle($id_usuario);
        
        if($_SERVER["COMPUTERNAME"] <> "WNTVMIT"){
            $arrayDados['VALOR']    = str_replace(".", ",", $arrayDados['VALOR']);
        }
        
        $arpovadores = $objUsuario->fetchAllAprovadores2($arrayDados['CCUSTO_PARA'], $arrayDados['VALOR']);
//                                    exit;
        if($Usuario[0]["EMAIL"]==null){
            $usuario_nome = $usuario_login;
            $usuario_email = "ci@usinasantafe.com.br";
        }else{
            $usuario_nome = $Usuario[0]["NOME"];
            $usuario_email = $Usuario[0]["EMAIL"];
        }
        $de = $usuario_email;
        $de_nome = $usuario_nome; 

        if($_SERVER["COMPUTERNAME"] == "WNTVMIT"){
            $arrayDados['VALOR']    = str_replace(",", "", $arrayDados['VALOR']);
        }else{
            $arrayDados['VALOR']    = str_replace(",", ".", str_replace(".", "", $arrayDados['VALOR']));
        }        
        $objFinalidade = new Admin_Model_Finalidade();
        $Finalidade  = $objFinalidade->find($arrayDados['FINALIDADE']);
        $arrayDados['FINALIDADE'] = $Finalidade["DESCRICAO"];
        $enviados = array();
        $mensagem = 'enviada';
        foreach ($arpovadores as $aprovador) {

            if($_SERVER["COMPUTERNAME"]=="COMMIT-PEDRO"){
                $para = "pedro.lobo@commit.inf.br";
                $para_nome = $aprovador["NOME"];
//                $para_nome = "Pedro Henrique G. Lobo";
            }else{
                $para = $aprovador["EMAIL"];
                $para_nome = $aprovador["NOME"];
            }

            $texto = 
            "Prezado (a) Senhor (a) $para_nome<br/><br/>

            Informamos que está disponível uma nova CI(Comunicação Interna) a ser Aprovada.  Abaixo seguem os dados da CI.<br/><br/>

            N° CI: ".$arrayDados['ID']."<br/>
            Empresa: ".$arrayDados['EMPRESA']."<br/>
            Data da inclusão: ".$arrayDados['DATA']."<br/>
            De: ".$Ccusto_de["CD_CCUSTO"]." - ".$Ccusto_de["NOME_CCUSTO"]."<br/>
            Para: ".$Ccusto_para["CD_CCUSTO"]." - ".$Ccusto_para["NOME_CCUSTO"]."<br/>
            Finalidade: <br/>".$arrayDados['FINALIDADE']."<br/><br/>
            Motivo: <br/>".$arrayDados['MOTIVO_CI']."<br/><br/>
            Valor: ".$Helper_mascaras->ValorMoeda($arrayDados['VALOR'],1)."<br/><br/>

            Click no link para analisar esta e outras CI : <a href=".'"'."http://wntvmit.usinasantafe.com.br/org/admin/ci/aprovar/".'"'.">http://wntvmit.usinasantafe.com.br/org/admin/ci/aprovar/</a><br/><br/>

            Atenciosamente<br/><br/>

            $de_nome<br/>
            USINA SANTA FE S/A";

            $email_assunto		= "Nova CI de N° ".$arrayDados['ID']." disponivel para Avaliação";
            $confgEmail = array(
                  'ASSUNTO' => $email_assunto
                , 'DE_EMAIL' => ($de)
                , 'DE_NOME' => ($de_nome)
                , 'PARA_EMAIL' => ($para)
                , 'PARA_NOME' => ($para_nome)
                , 'DE_TEXTO' => ($texto)
            );

            $objCiEnvio = new Admin_Model_CiEnvio();
            if($para){
                $retorno    = $objCiEnvio->save($confgEmail);
            }else{
                $retorno = "";
            }
            if (strlen($retorno[0]) > 0){
                $mensagem .= $retorno[0];
            }
//            
//            $email = new Commit_Controller_Action_Helper_EmailLoader($confgEmail);
//            $mensagem = $email->Mensagem();
////                    $mensagem = 'enviada';
//                $enviados[] = $para;
//            if ($mensagem == 'enviada'){
////                sleep(2);
//            } else {
////                return $mensagem;
//            }
        }
        return array('mensagem' => $mensagem, 'enviados' =>$enviados );
    }
    private function emailcopia($id_ci, $descricao_status = null, $enviados = null) {
        ini_set('max_execution_time', 120);
        $Helper_mascaras = new Commit_Controller_Action_Helper_Mascaras();
        $objCi       = new Admin_Model_Ci();
        $objCistatus = new Admin_Model_Cistatus();
        $arrayDados  = $objCi->findCI($id_ci);
        $objCcusto   = new Admin_Model_CCusto();
        $Ccusto_de   = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_DE']);
        $Ccusto_para = $objCcusto->fetchAllUpdate($arrayDados['CCUSTO_PARA']);
        
        $usuario_login = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $id_usuario = (int)(Zend_Auth::getInstance()->getIdentity()->ID_USUARIO_ORACLE);

        $objUsuario = new Admin_Model_Usuario();
        $Usuario = $objUsuario->fetchIdOracle($id_usuario);

        if($Usuario[0]["EMAIL"]==null){
            $usuario_nome = $usuario_login;
            $usuario_email = "ci@usinasantafe.com.br";
        }else{
            $usuario_nome = $Usuario[0]["NOME"];
            $usuario_email = $Usuario[0]["EMAIL"];
        }
        $de = $usuario_email;
        $de_nome = $usuario_nome; 
        if(!$descricao_status){
            $status             = $objCistatus->find($arrayDados['STATUS']);
            $descricao_status   = $status['DESCRICAO'];            
        }
        if($_SERVER["COMPUTERNAME"] == "WNTVMIT"){
            $valor_ci                   = $arrayDados['VALOR'];
            $arrayDados['VALOR']    = str_replace(",", "", $valor_ci);
        }else{
            $valor_ci                   = str_replace(".", ",", $arrayDados['VALOR']);
            $arrayDados['VALOR']    = str_replace(",", ".", str_replace(".", "", $valor_ci));
        }
        
        $emails     = $objUsuario->fetchAllEmailCopia($arrayDados['CCUSTO_DE'], $valor_ci,$arrayDados['USUARIO']);
        $mensagem   = 'enviada';
        if($arrayDados['FINALIDADE']){
            $objFinalidade            = new Admin_Model_Finalidade();
            $Finalidade               = $objFinalidade->find($arrayDados['FINALIDADE']);
            $arrayDados['FINALIDADE'] = $Finalidade["DESCRICAO"];
            $indice                   = count($emails);
            $emails[$indice]["LOGIN"] = null;
            $emails[$indice]["NOME"]  = $Finalidade["EMAIL_COPIA"];
            $emails[$indice]["EMAIL"] = $Finalidade["EMAIL_COPIA"];
        }
        $array_email = array();
        foreach ($emails as $value) {
            if($value["LOGIN"]){
                if($value["EMAIL"]){
                    $para_email     = $value["EMAIL"];
                    $para_nome      = $value["NOME"];
                }else{
                    if($_SERVER["COMPUTERNAME"]=="COMMIT-PEDRO"){
                        $para_email = $value["EMAIL"] ? $value["EMAIL"] : "pedro.lobo@commit.inf.br";
                        $para_nome  = $value["NOME"] ? $value["NOME"] : $value["LOGIN"];
                    }else{
                        $para_email = $value["EMAIL"] ? $value["EMAIL"] : "heitor@usinasantafe.com.br";
                        $para_nome  = $value["NOME"] ? $value["NOME"] : $value["LOGIN"];
                    }
                }
                if(is_null($enviados)){
                    $enviados = array();
                }
                if(!in_array($para_email, $enviados))
                $array_email[$para_email] = $para_nome;
            }else{
                $explode_emails = explode(";", $value["EMAIL"]);
                foreach ($explode_emails as $value2) {
                    if(!isset($array_email[trim($value2)]))
                    $array_email[trim($value2)] = null;
                }
            }
        }
        foreach ($array_email as $email_copia => $nome) {
            $para = $email_copia;
            $para_nome = $nome ? $nome: $email_copia;

            if($_SERVER["COMPUTERNAME"]=="COMMIT-PEDRO" && $nome){
                $para = "pedro.lobo@commit.inf.br";
            }
            
            $texto = 
            "Prezado (a) Senhor (a) <br/><br/>

            Informamos que a CI(Comunicação Interna) abaixo foi $descricao_status.<br/><br/>

            N° CI: ".$arrayDados['ID']."<br/>
            Empresa: ".$arrayDados['EMPRESA']."<br/>
            Data da inclusão: ".$arrayDados['DATA']."<br/>
            De: ".$Ccusto_de["CD_CCUSTO"]." - ".$Ccusto_de["NOME_CCUSTO"]."<br/>
            Para: ".$Ccusto_para["CD_CCUSTO"]." - ".$Ccusto_para["NOME_CCUSTO"]."<br/>
            Finalidade: <br/>".$arrayDados['FINALIDADE']."<br/><br/>
            Motivo: <br/>".$arrayDados['MOTIVO_CI']."<br/><br/>
            Valor: ".$Helper_mascaras   ->ValorMoeda($arrayDados['VALOR'],1)."<br/><br/>

            Atenciosamente<br/><br/>

            $de_nome<br/>
            USINA SANTA FE S/A";

            $email_assunto		= "CI N° ".$arrayDados['ID']." $descricao_status";
            $confgEmail = array(
                  'ASSUNTO' => $email_assunto
                , 'DE_EMAIL' => ($de)
                , 'DE_NOME' => ($de_nome)
                , 'PARA_EMAIL' => ($para)
                , 'PARA_NOME' => ($para_nome)
                , 'DE_TEXTO' => ($texto)
            );

            $objCiEnvio = new Admin_Model_CiEnvio();
            if($para){
                $retorno    = $objCiEnvio->save($confgEmail);
            }else{
                $retorno = "";
            }
            
            if (strlen($retorno[0]) > 0){
                $mensagem .= $retorno[0];
            }
            
//            $email = new Commit_Controller_Action_Helper_EmailLoader($confgEmail);
//            $mensagem = $email->Mensagem();
//
//            if ($mensagem == 'enviada'){
////                sleep(2);
//            } else {
////                return $mensagem;
//            }
        }
        return $mensagem;
    }
    
    public function anexarAction(){      
        
        $this->_helper->layout()->disableLayout();
//        $objAnexo = new Admin_Model_CiAnexo();
        $ci               = ($this->_request->getPost("ci",null));
        $desc_arquivo     = ($this->_request->getPost("desc_arquivo",null));
        $defaultNamespace = new Zend_Session_Namespace('ANEXOS');
        
        $upload = new Zend_File_Transfer_Adapter_Http();
        $arquivo = $upload->getFileInfo();
//        Zend_Debug::dump($desc_arquivo);
        $mensagem = "";
        $enviado  = "";
        $nada     = false;
        if( isset( $arquivo ) ) {
          $i = 0;    
          foreach( $arquivo as $file ) {
              $array_microtime = explode( ' ', microtime());
              $Unix_timestamp = $array_microtime[1];
              $microtime = substr($array_microtime[0], 2);
//              echo strlen($file["name"]);
//              echo "<br/>";
              if(strlen($file["name"])>0){

                $diretorio_tmp = UPLOAD_PATH.DIRECTORY_SEPARATOR.'tmp';
                $diretorio_ci  = UPLOAD_PATH.DIRECTORY_SEPARATOR.'ci';
                if(!file_exists($diretorio_tmp)){
                    @mkdir($diretorio_tmp,01777,true);
                }
                if(!file_exists($diretorio_ci)){
                    @mkdir($diretorio_ci,01777,true);
                }
                $arquivo_name  = $diretorio_tmp.DIRECTORY_SEPARATOR.$file["name"];

                $arquivo_file = 'arquivo_'.$i.'_';                
                $upload->setDestination($diretorio_tmp, $arquivo_file);
                
                $enviado = null;
                if ($upload->receive($arquivo_file)) {
                    @chmod($upload->getFileName(), 0777);
                    $enviado = 1;
                }
                $tamanhoanexo = $file["size"]/1024;
                $tamanhoAnexo = explode('.', $tamanhoanexo);
                if(strlen($tamanhoAnexo[0])<2){
                    $tamanho = round($tamanhoanexo, 2);
                }else{
                    $tamanho = round($tamanhoanexo, 0);
                }
                $extensao_arq = pathinfo($file["name"], PATHINFO_EXTENSION);
                $nome_arq_novo = $Unix_timestamp.'-'.$microtime.'.'.$extensao_arq;
                
                $arrayDados = array('CI'=>$ci ,
                                    'NOME_FISICO'   =>$nome_arq_novo,
                                    'NOME_ORIGINAL' =>$file["name"],
                                    'DESCRICAO'     =>(strlen($desc_arquivo[$i])>0) ? $desc_arquivo[$i]: $file["name"],
                                    'TAMANHO'       =>$tamanho);
                if($enviado){
                    $defaultNamespace->Anexos[] = $arrayDados;
//                    $retorno = $objAnexo->__save($arrayDados);
                }

                $arquivo_name_novo = UPLOAD_PATH.DIRECTORY_SEPARATOR.'ci'.DIRECTORY_SEPARATOR.$nome_arq_novo;
                if(copy($arquivo_name, $arquivo_name_novo)) {
                    
                    @chmod($arquivo_name_novo, 0777);
                  @unlink($arquivo_name);
                }else{
//                  $conta_arquivo=$i+1;
                  $mensagem .= "<br/>".$file["name"];
                }
              }else{
                  $nada = true;
              }
            $i++;
            
          }
         
        }
//        exit;
        if($mensagem != ""){
            $mensagem = "Os seguntes arquivos não foram enviados:".$mensagem;
        }elseif($enviado){
            $mensagem = "OK";
        }elseif($nada){
            $mensagem = "<br/><br/><h1>Dados incompletos...</h1>";
        }
        $this->view->mensagem = $mensagem;
        $this->view->ci = $ci;
        $data_atual     = new Zend_Date();
        $this->view->data_atual     = $data_atual->toString('MM/YYYY');        
    }
    
    
    
    
    
    public function deleteanexoAction()
    {
        $this->_helper->layout()->disableLayout();
        $anexo   = base64_decode($this->_request->getPost('anexo', null));
        
        $array_anexo = explode("-", $anexo);
        if($array_anexo[0]=="sessao"){
            $defaultNamespace   = new Zend_Session_Namespace('ANEXOS');
            $nome_arq           = $defaultNamespace->Anexos[$array_anexo[1]]["NOME_FISICO"];
            $arquivo            = UPLOAD_PATH.DIRECTORY_SEPARATOR.'ci'.DIRECTORY_SEPARATOR.$nome_arq;
            unset($defaultNamespace->Anexos[$array_anexo[1]]);
            unlink($arquivo);
//            Zend_Debug::dump($array_anexo);
//            exit;
            echo "OK";
        }else{
            $objProcessos = new Admin_Model_CiAnexo();
            $deleta       = $objProcessos->delete($array_anexo[1]);
            if(is_null($deleta[0])){
                echo "OK";
            }
        }
        
        
    }
    
    
    
    public function downloadAction()
    {
        $this->_helper->layout()->disableLayout();
        $anexo   = base64_decode($this->_request->getPost('anexo', null));
        
        $array_anexo = explode("-", $anexo);
        if($array_anexo[0]=="sessao"){
            $defaultNamespace   = new Zend_Session_Namespace('ANEXOS');
            $nome_fisico        = $defaultNamespace->Anexos[$array_anexo[1]]["NOME_FISICO"];
            $nome_original      = $defaultNamespace->Anexos[$array_anexo[1]]["NOME_ORIGINAL"];
        }else{
            $objProcessos = new Admin_Model_CiAnexo();
            $anexo          = $objProcessos->find($array_anexo[1]);
            $nome_fisico    = $anexo["NOME_FISICO"];
            $nome_original  = $anexo["NOME_ORIGINAL"];
        }
        
        $fullPath = UPLOAD_PATH.DIRECTORY_SEPARATOR.'ci'.DIRECTORY_SEPARATOR.$nome_fisico;
        $extensao_arq = pathinfo($nome_fisico, PATHINFO_EXTENSION);
        
        while (ob_get_level())
            ob_end_clean();
        header("Content-Encoding: None", true);
        
////        $content_type = mime_content_type($fullPath);
////        $file = $fullPath;
//        header("Cache-Control: public");
//        header('Content-type: application/octet-stream');
//        header("Content-Description: File Transfer");
//        header('Content-Disposition: attachment; filename="'.$nome_original);
//        header("Content-Transfer-Encoding: binary");
//        header('Expires: 0');
//        header('Pragma: public');
////        header('Content-Length: ' . filesize($file));
//        readfile($fullPath);
        
        
//  // Must be fresh start 
//  if( headers_sent() ) 
//    die('Headers Sent'); 
//
//  // Required for some browsers 
//  if(ini_get('zlib.output_compression')) 
//    ini_set('zlib.output_compression', 'Off'); 
//
//  // File Exists? 
//  if( file_exists($fullPath) ){ 
//    
//    // Parse Info / Get Extension 
//    $fsize = filesize($fullPath); 
//    $path_parts = pathinfo($fullPath); 
//    $ext = strtolower($path_parts["extension"]); 
//    
//    // Determine Content Type 
//    switch ($ext) { 
//      case "pdf": $ctype="application/pdf"; break; 
//      case "exe": $ctype="application/octet-stream"; break; 
//      case "zip": $ctype="application/zip"; break; 
//      case "doc": $ctype="application/msword"; break; 
//      case "xls": $ctype="application/vnd.ms-excel"; break; 
//      case "ppt": $ctype="application/vnd.ms-powerpoint"; break; 
//      case "gif": $ctype="image/gif"; break; 
//      case "png": $ctype="image/png"; break; 
//      case "jpeg": 
//      case "jpg": $ctype="image/jpg"; break; 
//      default: $ctype="application/force-download"; 
//    } 
//
//    header("Pragma: public"); // required 
//    header("Expires: 0"); 
//    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
//    header("Cache-Control: private",false); // required for certain browsers 
//    header("Content-Type: $ctype"); 
//    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" ); 
//    header("Content-Transfer-Encoding: binary"); 
//    header("Content-Length: ".$fsize); 
//    ob_clean(); 
//    flush(); 
//    readfile( $fullPath ); 
//
//  } else 
//    die('File Not Found'); 

        
        
        
        
        
        
//        switch($extensao_arq) { 
//         case "gz": $type = "application/x-gzip";  break; 
//         case "tgz": $type = "application/x-gzip"; break; 
//         case "zip": $type = "application/zip"; break; 
//         case "pdf": $type = "application/pdf"; break; 
//         case "png": $type = "image/png"; break; 
//         case "gif": $type = "image/gif"; break; 
//         case "jpg": $type = "image/jpeg"; break; 
//         case "txt": $type = "text/plain"; break; 
//         case "htm": $type = "text/html"; break; 
//         case "html": $type = "text/html"; break; 
//         default: $type = "application/octet-stream"; break;} 
//        header("Content-disposition: attachment; filename=".$nome_fisico.";" ); 
//        header("Content-Type: application/octet-stream");  
//        header("Content-Type: application/force-download");  
//        header("Content-Type: application/download"); 
//        header("Content-Transfer-Encoding: $type\n");  
//        header("Content-Length: ".filesize($fullPath));  
//        header("Pragma: no-cache");  
//        header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");  
//        header("Expires: 0"); 
//        readfile($fullPath);
        
        
        
        
        
        
//        if (file_exists($fullPath)) {
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/save');
//            header('Content-Disposition: attachment; filename='.basename($fullPath));
//            header('Content-Transfer-Encoding: binary');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($fullPath));
//            ob_clean();
//            flush();
//            readfile($fullPath);
//            exit;
//        }
        
//        exit;
        header("Content-Type: application/$extensao_arq"); 
        header('Content-Disposition: attachment; filename="'.$nome_original.'"');
        readfile($fullPath);        
    }
    public function anexosAction()
    {
        $this->_helper->layout()->disableLayout();
        $ci             = base64_decode($this->_request->getPost('id', null));
        $visualizar     = base64_decode($this->_request->getPost('visualizar', null));
        $Anexos_base     = array();
        $Anexos_sessao   = array();
        if($ci){
            $objCiAnexo = new Admin_Model_CiAnexo();
            $Anexos_b    = $objCiAnexo->fetchAll($ci);
            foreach ($Anexos_b as $value) {
                $Anexos_base["base-".$value["ID"]]["CI"]            = $value["CI"];
                $Anexos_base["base-".$value["ID"]]["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                $Anexos_base["base-".$value["ID"]]["NOME_FISICO"]   = $value["NOME_FISICO"];
                $Anexos_base["base-".$value["ID"]]["TAMANHO"]       = $value["TAMANHO"];
                $Anexos_base["base-".$value["ID"]]["DESCRICAO"]     = $value["DESCRICAO"];
            }
        }
        $defaultNamespace   = new Zend_Session_Namespace('ANEXOS');
        $Anexos_s      = $defaultNamespace->Anexos;
//        Zend_Debug::dump($Anexos_s);exit;
        if($Anexos_s){
            foreach ($Anexos_s as $key => $value) {
                $Anexos_sessao["sessao-".$key]["CI"]            = $value["CI"];
                $Anexos_sessao["sessao-".$key]["NOME_ORIGINAL"] = $value["NOME_ORIGINAL"];
                $Anexos_sessao["sessao-".$key]["NOME_FISICO"]   = $value["NOME_FISICO"];
                $Anexos_sessao["sessao-".$key]["TAMANHO"]       = $value["TAMANHO"];
                $Anexos_sessao["sessao-".$key]["DESCRICAO"]     = $value["DESCRICAO"];
            }
        }
        
        
        $Anexos = $Anexos_sessao + $Anexos_base ;
//        Zend_Debug::dump($Anexos);
        
        $this->view->Anexos      = $Anexos;
        $this->view->visualizar = $visualizar;
    }
    
    
    
    
    
    
    /**
     * Metodo que cadastra qtde x aprovadores
     * @name	insertAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	04/10/2012
     */
    public function updateqtdeaprovadoresAction()
    {
        $this->_helper->layout()->setLayout("dialog");
        $id              = $this->_request->getParam('id');
        $obj             = new Admin_Model_Valoraprovadores();
        $form            = new Commit_Form_Cadastro();
        $qtdeaprovadores = $form->qtdeaprovadores("updateqtdeaprovadores/id/$id");
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($qtdeaprovadores->isValid($formData)){
                $arrayDados['ID']               = base64_decode($id);
                if($_SERVER["COMPUTERNAME"] == "WNTVMIT"){
                    $arrayDados['VALOR_INICIAL']    = str_replace(",", ".", str_replace(".", "", $this->_request->getPost('valor_inicial')));
                    $arrayDados['VALOR_FINAL']      = str_replace(",", ".", str_replace(".", "", $this->_request->getPost('valor_final')));
                }else{
                    $arrayDados['VALOR_INICIAL']    = str_replace(".", "", $this->_request->getPost('valor_inicial'));
                    $arrayDados['VALOR_FINAL']      = str_replace(".", "", $this->_request->getPost('valor_final'));
                }
                $arrayDados['APROVADORES']          = ($this->_request->getPost('qtdeaprovadores'));
                $arrayDados['EMAILCOPIA']           = ($this->_request->getPost('emailcopia'));
                
                $pesquisa_inicial   = $obj->fetchAllPesquisa($arrayDados['ID'], $arrayDados['VALOR_INICIAL']);
                $pesquisa_final     = $obj->fetchAllPesquisa($arrayDados['ID'], null, $arrayDados['VALOR_FINAL']);
                $pesquisa_total     = $obj->fetchAllPesquisa($arrayDados['ID'], $arrayDados['VALOR_INICIAL'], $arrayDados['VALOR_FINAL']);
                
                if(count($pesquisa_total)>0){
                    $retorno[0] = "PARTE_COMPREENDIDO";
                }elseif(count($pesquisa_inicial)>0 && count($pesquisa_final)>0){
                    $retorno[0] = "EXISTE_AMBOS";
                }elseif(count($pesquisa_inicial)>0){
                    $retorno[0] = "EXISTE_INICIAL";
                }elseif(count($pesquisa_final)>0){
                    $retorno[0] = "EXISTE_FINAL";
                }else{
                    $retorno = $obj->save($arrayDados);
                }       
                
                
                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }else{
            $arrayDados = $obj->_find(base64_decode($id));
        }
        $qtdeaprovadores->populate(array('id'               => base64_encode($arrayDados["ID"])
                                        ,'valor_inicial'    =>$arrayDados["VALOR_INICIAL"]
                                        ,'valor_final'      =>$arrayDados["VALOR_FINAL"]
                                        ,'emailcopia'       =>$arrayDados["EMAILCOPIA"]
                                        ,'qtdeaprovadores'  =>$arrayDados["APROVADORES"] ));
        
        $this->view->formulario = $qtdeaprovadores;
    }
    
    public function deleteqtdeaprovadoresAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id                     = base64_decode($this->_request->getParam("id",null));        
        $objCcustoAprovadores   = new Admin_Model_Valoraprovadores();
        $save                   = $objCcustoAprovadores->delete($id);
//        Zend_Debug::dump($save);
        echo $save[0];
    }


    
    public function addaprovadorAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $ccusto     = base64_decode($this->_request->getPost("ccusto",null));
        $id_valor   = base64_decode($this->_request->getPost("id_valor",null));
        $aprovador  = ($this->_request->getPost("aprovador",null));
        if(strlen($ccusto)>0){
            $dados = array( "CD_CCUSTO" => $ccusto
                           ,"APROVADOR" => $aprovador);
//                          Zend_Debug::dump($dados);
            $objCcustoAprovadores = new Admin_Model_Ccustoaprovadores();
            $save = $objCcustoAprovadores->save($dados);
        }elseif(strlen($id_valor)>0){
            $dados = array( "VALOR_APROVADORES" => $id_valor
                           ,"USUARIO"           => $aprovador);
//                          Zend_Debug::dump($dados);
            $objValoraprovadoresgeral = new Admin_Model_Valoraprovadoresgeral();
            $save = $objValoraprovadoresgeral->save($dados);
        }
        
         
        echo $save[0];
    }
    
    
    public function addvisualizadorAction(){
        $this->_helper->layout()->disableLayout();
        
        $finalidade = base64_decode($this->_request->getPost("finalidade",null));
        $usuario    = ($this->_request->getPost("usuario",null));
        if(strlen($finalidade)>0){
            $dados = array( "FINALIDADE" => $finalidade
                           ,"USUARIO"    => $usuario);
            $objFinalidadevisualizadores = new Admin_Model_Finalidadevisualizadores();
            $save = $objFinalidadevisualizadores->save($dados);
        }
        echo $save[0];
    }
    
    public function deleteaprovadorAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id                     = base64_decode($this->_request->getPost("id",null));        
        $objCcustoAprovadores   = new Admin_Model_Ccustoaprovadores();
        $save                   = $objCcustoAprovadores->delete($id);
         
        echo $save[0];
    }
    public function deleteaprovadorgeralAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id                         = base64_decode($this->_request->getPost("id",null));        
        $objValoraprovadoresgeral   = new Admin_Model_Valoraprovadoresgeral();
        $save                       = $objValoraprovadoresgeral->delete($id);
         
        echo $save[0];
    }
    public function deletevisualizadorfinalidadeAction()
    {
        $this->_helper->layout()->disableLayout();        
        $id                         = base64_decode($this->_request->getPost("id",null));        
        $objValoraprovadoresgeral   = new Admin_Model_Finalidadevisualizadores();
        $save                       = $objValoraprovadoresgeral->delete($id);         
        echo $save[0];
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


    public function ccustoaprovadoresAction()
    {
        $objCargo = new Admin_Model_Ccustoaprovadores();
        $subordinados = $objCargo->fetchAllAprovadores();
        
        $objCusto = new Admin_Model_CCusto();
        $centros = $objCusto->fetchAllAnaliticos();
        
        $this->view->centro = $centros;
        $this->view->subordinados = $subordinados;
    }
    
    
    
    
    /**
     *  Funcao que cria a mesnagem do sistema
     * @example     tipo: notice, success, warning, error, validation
     * @param       char $tipo
     * @param       array $retorno
     * @param       char $titulo
     * @return      char
     * @tutorial    É utilizado para gerar as mensagens do sistema via Helper
     */
    private function getMensagem($tipo, $retorno, $titulo = 'MENSAGEM DO SISTEMA')
    {
        $mensagem    = array('TIPO' => $tipo,'TITULO'=>$titulo,'MENSAGEM'=>$retorno, 'BASE'=>true);
        $objMensagem = new Commit_Controller_Action_Helper_Mensagem();
        return base64_decode( $objMensagem->start($mensagem) );
    }

    
}