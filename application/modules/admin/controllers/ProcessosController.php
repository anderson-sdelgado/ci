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
class Admin_ProcessosController extends Zend_Controller_Action
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
        $this->_redirect('/admin/processos/cadastro');
    }
    public function addlinkAction()
    {
        $this->_helper->layout()->disableLayout();
        $link       = $this->_request->getPost('link', null);
        $desc_link  = $this->_request->getPost('descricao', null);
        $defaultNamespace = new Zend_Session_Namespace('LINKS');
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
            $objProcessos = new Admin_Model_Processoslink();
            $deleta       = $objProcessos->delete($array_link[1]);
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
            $objProcessos       = new Admin_Model_Processoslink();
            $Links_b         = $objProcessos->fetchAll($id_processo);
            foreach ($Links_b as $value) {
                $Links_base["base-".$value["ID"]]["LINK"]       = $value["LINK"];
                $Links_base["base-".$value["ID"]]["DESCRICAO"]  = $value["DESCRICAO"];
            }
        }
        $defaultNamespace   = new Zend_Session_Namespace('LINKS');
        $Links_s      = $defaultNamespace->Links;
//        Zend_Debug::dump($Links_s);exit;
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
        $codigo = '';
        if ($this->_request->isPost()) {
            $codigo   = $this->_request->getPost('codigo', null);
        }elseif(strlen(@$extra[0]) > 0 or strlen(@$extra[1]) > 0){
            $codigo   = $extra[0];
        }

        if(strlen($codigo)==0)  $codigo  = null;

        $objPesquisa = new Admin_Model_Processos();
        $resposta = $objPesquisa->fetchAllPesquisa($codigo, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$codigo");
        $this->view->codigo   = $codigo;
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
        $processos = $form->processos('update');
        $defaultNamespace   = new Zend_Session_Namespace('LINKS');
        $Links_s            = $defaultNamespace->Links;
        
        $id = base64_decode($this->_request->getParam("id",null));
        
        if ($this->_request->isPost()) {
            $arrayDadosProcessos = array();
            $formData = $this->_request->getPost();
            
            if ($processos->isValid($formData)){
                $arrayDadosProcessos['ID']    = $id;
                $arrayDadosProcessos['CODIGO']    = $this->_request->getPost('codigo');
                $arrayDadosProcessos['DESCRICAO_SUMARIA']    = ($this->_request->getPost('descricao_sumaria'));
                $arrayDadosProcessos['DESCRICAO_DETALHADA'] = ($this->_request->getPost('descricao_detalhada'));
                //Zend_Debug::dump($arrayDadosProcessos);exit;

                $obj = new Admin_Model_Processos();
                $retorno = $obj->save($arrayDadosProcessos);
                if (strlen($retorno[0]) == 0){
                    
                    
                    $objProcessosLink = new Admin_Model_Processoslink();
                    foreach ($Links_s as $key => $value) {
                        $Links["ID_PROCESSO"]   = $id;
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
        
        $obj = new Admin_Model_Processos();

        foreach ($obj->fetchAllUpdate($id) as $dados){
            $processos->populate(array( 'id'      =>base64_encode($dados['ID'])
                                        ,'codigo'   =>$dados['CODIGO']
                                        ,'descricao_sumaria'   => str_replace("***","'",$dados['DESCRICAO_SUMARIA'])
                                        ,'descricao_detalhada' => str_replace("***","'",$dados['DESCRICAO_DETALHADA'])
					));
        }
        $this->view->formulario = $processos;
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

        
        $objCargo = new Admin_Model_CargosProcessos();
        $res = $objCargo->fetchAllDelete($id);
        if(count($res) <= 0){
            $obj = new Admin_Model_Processos();
            $obj->delete($id, false, $arrayData);
            $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
        }else{
            $this->view->mensagem = base64_encode( $this->getMensagem('error', 'PROCESSOS_CARGOS_FK') );
        }
        
        //$this->_redirect('admin/processos/cadastro/');
    }
    
    /**
     * Metodo que relaciona cargos com processos
     * @name	relacionamentoAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function relacionamentoAction()
    {
        $id = base64_decode($this->_request->getParam("id",null));
        $tipo_arquivo = ($this->_request->getParam("tipo_arquivo",null));
        $objCargo  = new Admin_Model_Cargos();
        $retorno = $objCargo->fetchAllUpdate($id);
        $this->view->cargos = $retorno;
        $arrayDados = array();
        
        
        $obj = new Admin_Model_CargosProcessos();
        
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            unset($formData['btnSalvar']);
            $retorno = $obj->deleterelacionamentocargo($formData['selectCargos']);
            if(!empty($formData['checkprocessos'])){
                foreach($formData['checkprocessos'] as $processos):
                    $arrayDados['COD_FUNCAO'] = $formData['selectCargos'];
                    $arrayDados['PROCESSOS_ID'] = $processos;
                    $retorno = $obj->save($arrayDados);
                endforeach;
            }
           
            $obj = new Admin_Model_CCustoCargos();
            $arrayDados = array();
            $arrayDados['COD_FUNCAO'] = '';
            $retorno = $obj->deleterelacionamentocargo($formData['selectCargos'],$arrayDados);
            if(!empty($formData['checkcustos'])){
                foreach($formData['checkcustos'] as $custos):
                    $arrayDados['CD_CCUSTO'] = $custos;
                    $arrayDados['COD_FUNCAO'] = $formData['selectCargos'];
                    $retorno = $obj->save($arrayDados);
//var_dump($retorno);exit;
                endforeach;
            }
            
            if(!empty($formData['MO'])){
                $obj = new Admin_Model_Mo();
                $arrayDados = array();
                $arrayDados['ID'] = $formData['selectCargos'];
                $retorno = $obj->_delete($arrayDados['ID'],false,$arrayDados);
                unset($arrayDados['ID']);
                $arrayDados['COD_FUNCAO'] = $formData['selectCargos'];
                $arrayDados['MO'] = $formData['MO'];
                $retorno = $obj->save($arrayDados);
            }
            
            //faz upload do arquivo
            if(empty($_FILES['arquivo']['error'])){
                $obj = new Admin_Model_Arquivos();
                $arrayDados = array();
                $id_cargo = $formData['selectCargos'];
                $adapter = new Zend_File_Transfer_Adapter_Http();

                $caminho = UPLOAD_PATH . '/'.$id_cargo;

                if(!file_exists($caminho)){
                    @mkdir($caminho,01777,true);
                }

                $adapter->setDestination($caminho);
                //pega dados do arquivo
                $info = $adapter->getFileInfo();
                //retira acentos
                $fileName = $this->retirar_acentos($info['arquivo']['name']);
                //renomeia o arquivo
                $adapter->addFilter('Rename',
                            array('target' => $caminho.'/'.$fileName,
                            'overwrite' => true));
                try {
                    $adapter->receive('arquivo');
                    @chmod($adapter->getFileName(), 01777);

                } catch (Zend_File_Transfer_Exception $e) {
                    //var_dump($e->getMessage());
                    throw new Exception($e->getMessage(), '', '');
                }

                $arrayDados['COD_FUNCAO']    = $id_cargo;
                $arrayDados['ARQUIVO'] = $fileName;
                $arrayDados['TIPO'] = $tipo_arquivo;
                //Zend_Debug::dump($arrayDados);exit;
                $retorno = $obj->save($arrayDados);
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
        $form               = new Commit_Form_Cadastro();
        $processos          = $form->processos();
        $defaultNamespace   = new Zend_Session_Namespace('LINKS');
        $Links_s            = $defaultNamespace->Links;
        if ($this->_request->isPost()) {
            $arrayDadosProcessos = array();
            $formData = $this->_request->getPost();
            
            if ($processos->isValid($formData)){
                $arrayDadosProcessos['CODIGO']    = ($this->_request->getPost('codigo'));
                $arrayDadosProcessos['DESCRICAO_SUMARIA']    = ($this->_request->getPost('descricao_sumaria'));
                $arrayDadosProcessos['DESCRICAO_DETALHADA'] = ($this->_request->getPost('descricao_detalhada'));
                //Zend_Debug::dump($arrayDadosProcessos);exit;

                $obj = new Admin_Model_Processos();
                $retorno = $obj->save($arrayDadosProcessos,true);
                if (strlen($retorno[0]) == 0){
                    $id = $retorno[1];
                    $objProcessosLink = new Admin_Model_Processoslink();
                    foreach ($Links_s as $key => $value) {
                        $Links["ID_PROCESSO"]   = $id;
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
        $this->view->formulario = $processos;
    }
    /**
     * Metodo que lista arquivos relacionados aos cargos
     * @name	arquivosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function arquivosAction()
    {
        
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        $desc = '';
        if ($this->_request->isPost()) {
            $desc   = $this->_request->getPost('desc', null);
        }elseif(strlen(@$extra[0]) > 0 or strlen(@$extra[1]) > 0){
            $desc   = $extra[0];
        }

        if(strlen($desc)==0)  $desc  = null;

        $objPesquisa = new Admin_Model_CargosProcessos();
        $resposta = $objPesquisa->fetchAllPesquisa($desc, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$desc");
        $this->view->desc   = $desc;
        
    }
    /**
     * Metodo que cadastra arquivos relacionados aos cargos
     * @name	addarquivosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function addarquivosAction()
    {
        $id_cargo = base64_decode($this->_request->getParam("idcargo",null));
        if(empty($id_cargo))
            $id_cargo = base64_decode($this->_request->getPost('id_cargo'));
        
        $id_processo = base64_decode($this->_request->getParam("idprocesso",null));
        if(empty($id_processo))
            $id_processo = base64_decode($this->_request->getPost('id_processo'));
        
        $objPesquisa = new Admin_Model_CargosProcessos();
        $resposta = $objPesquisa->fetchAllArquivos($id_cargo,null,null,$id_processo);
        @$this->view->cargo = $resposta[0]['DESCR_FUNCAO'];
        @$this->view->processo = $resposta[0]['DESCRICAO_SUMARIA'];
        
        $obj = new Admin_Model_Arquivos();
        
        $form = new Commit_Form_Cadastro();
        $arquivos = $form->arquivos();
        
        $arquivos->populate(
              array('id_cargo'      =>base64_encode($id_cargo),
                    'id_processo'   =>base64_encode($id_processo)
                    )
                );
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();

            $id_cargo = base64_decode($formData['id_cargo']);
            $id_processo = base64_decode($formData['id_processo']);
            
            $adapter = new Zend_File_Transfer_Adapter_Http();
            
            $caminho = UPLOAD_PATH . '/'.$id_cargo.'_'.$id_processo;
            
            if(!file_exists($caminho)){
                @mkdir($caminho,01777,true);
            }
            
            $adapter->setDestination($caminho);
            try {
                $adapter->receive();
                @chmod($adapter->getFileName(), 01777);
                
            } catch (Zend_File_Transfer_Exception $e) {
                throw new Exception($e->getMessage(), '', '');
            }
            
            $info = $adapter->getFileInfo();
            
            $arrayDados['COD_FUNCAO']    = $id_cargo;
            $arrayDados['PROCESSOS_ID']    = $id_processo;
            $arrayDados['ARQUIVO'] = $info['arquivo']['name'];
            //Zend_Debug::dump($arrayDados);exit;

            
            $retorno = $obj->save($arrayDados);
            if (strlen($retorno[0]) > 0){

                $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
            }else{
                $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
            }
            
        }
        $this->view->formulario = $arquivos;
        
        $objArquivo = new Admin_Model_Arquivos();
        $resposta = $objArquivo->fetchAllArquivos($id_cargo,$id_processo);
        $this->view->arquivos = $resposta;
        
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

    protected function _findexts($filename) {
        $filename = strtolower($filename);
        $exts = preg_split("/[.]/", $filename);
        $n = count($exts)-1;
        $exts = $exts[$n];
        return $exts;
    }
    /**
     * Metodo que retira acentos de strings
     * @name	retirar_acentos()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    protected function retirar_acentos($string){
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $string = utf8_decode($string);
        $string = strtr($string, utf8_decode($a), $b); // Retira os Acentos das Letras.
        $string = str_replace(" ","-",$string); // Retira os Espaços.
        $string = strtolower($string); // Transforma tudo para Minúsculo.
        return utf8_encode($string); // Retorna a String transformada
    }
    
}