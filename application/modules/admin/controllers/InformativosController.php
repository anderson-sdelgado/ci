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
*/
class Admin_InformativosController extends Zend_Controller_Action
{

    public function init()
    {
        $head = new Commit_Controller_Action_Helper_Compatibilidade();
        
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/admin.css')
                               ->appendStylesheet($head->getCssGeral('admin'));
        $this->view->headScript()->appendFile(PUBLIC_PATH. '_js/admin.js')
                                 ->appendFile($head->getJsGeral('admin'));
        
    }

    public function indexAction()
    {
        $this->_redirect('/admin/informativos/cadastro');
    }
    public function updateitemAction()
    {
        
        
        $id               = base64_decode($this->_request->getParam("id",null));
        
        $objInformativositem = new Admin_Model_Informativositem();
        $Informativositem = $objInformativositem->find($id);
        $id_informativo      = $Informativositem["ID_INFORMATIVO"];
        
        $objInformativos    = new Admin_Model_Informativos();
        $Informativo        = $objInformativos->find($id_informativo);
        $informativo        = $Informativo["DESCRICAO"];
        
        $defaultNamespace = new Zend_Session_Namespace('LINKS');
        $Links_s          = $defaultNamespace->Links;
        
        $form             = new Commit_Form_Cadastro();
        $informativositem = $form->informativositem("updateitem/id/".base64_encode($id));
        
        if ($this->_request->isPost()) {
            $arrayDadosProcessos = array();
            $formData = $this->_request->getPost();
            
            if ($informativositem->isValid($formData)){
                $arrayDadosProcessos['ID']    = $id;
                $arrayDadosProcessos['DESCRICAO_SUMARIA']    = ($this->_request->getPost('descricao_sumaria'));
                $arrayDadosProcessos['DESCRICAO_DETALHADA'] = ($this->_request->getPost('descricao_detalhada'));
                //Zend_Debug::dump($arrayDadosProcessos);exit;

                $retorno = $objInformativositem->save($arrayDadosProcessos);
                if (strlen($retorno[0]) == 0){
                    
                    
                    $objProcessosLink = new Admin_Model_Informativositemlink();
                    foreach ($Links_s as $key => $value) {
                        $Links["ID_INFORMATIVO_ITEM"]   = $id;
                        $Links["LINK"]          = $value["LINK"];
                        $Links["DESCRICAO"]     = $value["DESCRICAO"];
                        $retorno = $objProcessosLink->save($Links);
                        if (strlen($retorno[0]) > 0){
                            break;
                        }else{
                            unset($defaultNamespace->Links[$key]);
                        }
                    }
                }    
                if (strlen($retorno[0]) > 0){    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }else{
            unset($defaultNamespace->Links);
        }       

        foreach ($objInformativositem->fetchAllUpdate($id) as $dados){
            $informativositem->populate(array( 'id'      =>base64_encode($dados['ID'])
                                        ,'descricao_sumaria'   => str_replace("***","'",$dados['DESCRICAO_SUMARIA'])
                                        ,'descricao_detalhada' => str_replace("***","'",$dados['DESCRICAO_DETALHADA'])
					));
        }
        $this->view->formulario     = $informativositem;
        $this->view->informativo    = $informativo;
        $this->view->id_informativo = $id_informativo;
    }
    public function additemAction()
    {
        
        $id_informativo   = ($this->_request->getParam("id",null));
        
        $objInformativos    = new Admin_Model_Informativos();
        $Informativo        = $objInformativos->find(base64_decode($id_informativo));
        $informativo        = $Informativo["DESCRICAO"];        
        
        $defaultNamespace = new Zend_Session_Namespace('LINKS');
        $Links_s          = $defaultNamespace->Links;
        $form             = new Commit_Form_Cadastro();
        $informativositem = $form->informativositem("additem/id/$id_informativo");
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($informativositem->isValid($formData)){
                $arrayDados['ID_INFORMATIVO']      = base64_decode($id_informativo);
                $arrayDados['DESCRICAO_SUMARIA']   = ($this->_request->getPost('descricao_sumaria'));
                $arrayDados['DESCRICAO_DETALHADA'] = ($this->_request->getPost('descricao_detalhada'));

                $obj = new Admin_Model_Informativositem();
                $retorno = $obj->save($arrayDados,true);
                if (strlen($retorno[0]) == 0){
                    $id = $retorno[1];
                    $objProcessosLink = new Admin_Model_Informativositemlink();
                    foreach ($Links_s as $key => $value) {
                        $Links["ID_INFORMATIVO_ITEM"]   = $id;
                        $Links["LINK"]          = $value["LINK"];
                        $Links["DESCRICAO"]     = $value["DESCRICAO"];
                        $retorno = $objProcessosLink->save($Links);
                        if (strlen($retorno[0]) > 0){
                            break;
                        }else{
                            unset($defaultNamespace->Links[$key]);
                        }
                    }
                }
                if (strlen($retorno[0]) > 0){
//                    Zend_Debug::dump($retorno);
//                    exit;
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    $this->_redirect('admin/informativos/itens/id/'.$id_informativo);
                }
            }
        }else{
            unset($defaultNamespace->Links);
        }
        $this->view->formulario = $informativositem;
        $this->view->informativo    = $informativo;
        $this->view->id_informativo = $id_informativo;
    }
    public function itensAction()
    {
        $page           = $this->_request->getParam("page",null);
        $informativo    = base64_decode($this->_request->getParam("id",null));
        $extra          = explode('|', base64_decode($this->_request->getParam("p",null)));
        $descricao         = '';
        $Informativos["DESCRICAO"] = "Selecione um quadro de Informativos";
        $resposta = null;
        
        $form_pesquisa = new Commit_Form_Pesquisa();
        $form          = $form_pesquisa->informativositens();
        
        if ($this->_request->isPost()) {
            $informativo    = $this->_request->getPost('informativo', null);
            $descricao      = $this->_request->getPost('descricao', null);
        }else{
            $descricao      = @$extra[0];
//            $ccusto_de      = @$extra[1];
        }
            $descricao      = (strlen($descricao)>0) ? $descricao: null;
//            $ccusto_de      = (strlen($ccusto_de)>0) ? $ccusto_de: null;
        
        $form->populate(array('informativo' => $informativo
                             ,'descricao'   => $descricao));
        
        if ($this->_request->isPost()) {
            $descricao   = $this->_request->getPost('descricao', null);
        }elseif(strlen(@$extra[0]) > 0 or strlen(@$extra[1]) > 0){
            $descricao   = $extra[0];
        }

        if(strlen($descricao)==0)  $descricao  = null;

        if($informativo){
            $objPesquisa    = new Admin_Model_Informativositem();
            $resposta = $objPesquisa->fetchAllPesquisa($informativo, $descricao, $page);

            $objInformativos= new Admin_Model_Informativos();
            $Informativos   = $objInformativos->find($informativo);
        }
        
        
        $this->view->id_informativo = base64_encode($informativo);
        $this->view->Informativo    = $Informativos["DESCRICAO"];
        $this->view->formulario     = $form;
        $this->view->paginator      = $resposta;
        $this->view->descricao      = $descricao;
        $this->view->extra          = base64_encode("$descricao");
    }
    public function addlinkAction()
    {
        $this->_helper->layout()->disableLayout();
        $link       = $this->_request->getPost('link', null);
        $desc_link  = $this->_request->getPost('descricao', null);
        $defaultNamespace = new Zend_Session_Namespace('LINKS');
//        unset($defaultNamespace->Links);
        if($link && $desc_link){
            $defaultNamespace->Links[] = array("LINK" => $link, "DESCRICAO" => $desc_link);
        }
    }
    public function deletelinkAction()
    {
        $this->_helper->layout()->disableLayout();
        $link   = base64_decode($this->_request->getPost('link', null));
        
        $array_link = explode("-", $link);
        
        if($array_link[0]=="sessao"){
            $defaultNamespace = new Zend_Session_Namespace('LINKS');
            unset($defaultNamespace->Links[$array_link[1]]);
            echo "OK";
        }else{
            $objInformativos = new Admin_Model_Informativositemlink();
            $deleta       = $objInformativos->delete($array_link[1]);
            if(is_null($deleta[0])){
                echo "OK";
            }
        }
        
        
    }
    public function linksAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_processo        = base64_decode($this->_request->getPost('id', null));
        $visualizar         = base64_decode($this->_request->getPost('visualizar', null));
        $Links_base         = array();
        $Links_sessao       = array();
        if($id_processo){
            $objInformativos       = new Admin_Model_Informativositemlink();
            $Links_b         = $objInformativos->fetchAll($id_processo);
            foreach ($Links_b as $value) {
                $Links_base["base-".$value["ID"]]["LINK"]       = $value["LINK"];
                $Links_base["base-".$value["ID"]]["DESCRICAO"]  = $value["DESCRICAO"];
            }
        }
        $defaultNamespace   = new Zend_Session_Namespace('LINKS');
        $Links_s      = $defaultNamespace->Links;
        if($Links_s){
            foreach ($Links_s as $key => $value) {
                $Links_sessao["sessao-".$key]["LINK"]       = $value["LINK"];
                $Links_sessao["sessao-".$key]["DESCRICAO"]  = $value["DESCRICAO"];
            }
        }
        
        
        $Links = $Links_sessao + $Links_base ;
//        Zend_Debug::dump($Links);
        
        $this->view->Links      = $Links;
        $this->view->visualizar = $visualizar;
    }
    /**
     * Metodo que exibe registro na base de dados
     * @name	cadastroAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function cadastroAction()
    {
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        $descricao = '';
        if ($this->_request->isPost()) {
            $descricao   = $this->_request->getPost('descricao', null);
        }elseif(strlen(@$extra[0]) > 0 or strlen(@$extra[1]) > 0){
            $descricao   = $extra[0];
        }

        if(strlen($descricao)==0)  $descricao  = null;

        $objPesquisa = new Admin_Model_Informativos();
        $resposta = $objPesquisa->fetchAllPesquisa($descricao, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$descricao");
        $this->view->descricao   = $descricao;
    }
    /**
     * Metodo que atualiza registro na base de dados
     * @name	updateAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function updateAction()
    {
        
        $form = new Commit_Form_Cadastro();
        $informativos = $form->informativos('update');
        
        $id = base64_decode($this->_request->getParam("id",null));
        
        if ($this->_request->isPost()) {
            $arrayDadosInformativos = array();
            $formData = $this->_request->getPost();
            
            if ($informativos->isValid($formData)){
                $arrayDadosInformativos['ID']    = $id;
                $arrayDadosInformativos['DESCRICAO']    = $this->_request->getPost('descricao');
                $obj = new Admin_Model_Informativos();
                $retorno = $obj->save($arrayDadosInformativos);
                 
                if (strlen($retorno[0]) > 0){    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        $obj = new Admin_Model_Informativos();

        foreach ($obj->fetchAllUpdate($id) as $dados){
            $informativos->populate(array( 'id'      =>base64_encode($dados['ID'])
                                        ,'descricao'   =>str_replace("***","'",$dados['DESCRICAO'])
					));
        }
        $this->view->formulario = $informativos;
    }
    /**
     * Metodo que deleta registro na base de dados
     * @name	cadastroAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function deleteAction()
    {
        $id = base64_decode($this->_request->getParam("id",null));

        $arrayData['ID'] = $id;
        
//        
//        $objCargo = new Admin_Model_CargosInformativos();
//        $res = $objCargo->fetchAllDelete($id);
            $obj = new Admin_Model_Informativos();
            $retorno = $obj->delete($id, false, $arrayData);
//            Zend_Debug::dump($retorno);
        if(count($retorno[0]) <= 0){
            $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
            $Historico = new Zend_Session_Namespace('HISTORICO');
            $this->_redirect($Historico->Anterior);
        }else{
            $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
        }
        
        //$this->_redirect('admin/informativos/cadastro/');
    }
    
    /**
     * Metodo que deleta registro na base de dados
     * @name	cadastroAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function deleteitemAction()
    {
        $id = base64_decode($this->_request->getParam("id",null));

        $arrayData['ID'] = $id;
        
//        
            $obj = new Admin_Model_Informativositem();
            $retorno = $obj->delete($id, false, $arrayData);
//            Zend_Debug::dump($retorno);
        if(count($retorno[0]) <= 0){
            $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
            $Historico = new Zend_Session_Namespace('HISTORICO');
            $this->_redirect($Historico->Anterior);
        }else{
            $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
        }
        
        //$this->_redirect('admin/informativos/cadastro/');
    }
    
    /**
     * Metodo que relaciona cargos com informativos
     * @name	relacionamentoAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	09/10/2012
     */
    public function relacionamentoAction()
    {
        $id = base64_decode($this->_request->getParam("id",null));
        $objCargo  = new Admin_Model_Cargos();
        $retorno = $objCargo->fetchAllUpdate($id);
        $this->view->cargos = $retorno;
        
        
        $obj = new Admin_Model_Cargosinformativositem();
        
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            unset($formData['btnSalvar']);
            $arrayDados = array();
            $retorno = $obj->deleterelacionamentocargo($formData['selectCargosInformativos']);
            if(!empty($formData['checkInformativos'])){
                foreach($formData['checkInformativos'] as $informativos):
                    $arrayDados['COD_FUNCAO']           = $formData['selectCargosInformativos'];
                    $arrayDados['ID_INFORMATIVOS_ITEM'] = $informativos;
                    $retorno = $obj->save($arrayDados);
//            Zend_Debug::dump($retorno);
//            exit;
                endforeach;
            }
            if (strlen($retorno[0]) > 0){
                $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
            }else{
                $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
            }
        }
    }
    
    /**
     * Metodo que cadastra registro na base de dados
     * @name	insertAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function insertAction()
    {
        $form = new Commit_Form_Cadastro();
        $informativos = $form->informativos();
        if ($this->_request->isPost()) {
            $arrayDadosInformativos = array();
            $formData = $this->_request->getPost();
            
            if ($informativos->isValid($formData)){
                $arrayDadosInformativos['DESCRICAO']    = ($this->_request->getPost('descricao'));

                $obj = new Admin_Model_Informativos();
                $retorno = $obj->save($arrayDadosInformativos);
                
                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        $this->view->formulario = $informativos;
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

