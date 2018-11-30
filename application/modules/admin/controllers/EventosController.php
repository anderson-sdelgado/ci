<?php
/**
 * Controller gerencia a pagina de eventos
 * @filesource		/application/modules/admin/controllers/EventosController.php
 * @author 		Allan Rett Ferreira
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Controllers
 * @package		Admin_EventosController
 * @subpackage		Zend_Controller_Action
 * @version		1.0
 * @since		28/06/2012
 */

class Admin_EventosController extends Zend_Controller_Action
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
        // action body
    }

    public function horasAction()
    {
        $page   = $this->_request->getParam("page",null);
        $extra  = explode('|', base64_decode($this->_request->getParam("p",null)));
        $id_cargo = '';
        $cd_ccusto = '';
        if ($this->_request->isPost()) {
            $id_cargo   = $this->_request->getPost('id_cargo', null);
            $cd_ccusto   = $this->_request->getPost('cd_ccusto', null);
        }elseif(strlen(@$extra[0]) > 0 or strlen(@$extra[1]) > 0){
            $id_cargo   = $extra[0];
            $cd_ccusto   = $extra[1];
        }

        if(strlen($id_cargo)==0)  $id_cargo  = null;
        if(strlen($cd_ccusto)==0)  $cd_ccusto  = null;

        $objPesquisa = new Admin_Model_HoraExtra();
        $resposta = $objPesquisa->fetchAllPesquisa($id_cargo,$cd_ccusto, $page);
        
        $this->view->paginator      = $resposta;
        $this->view->extra          = base64_encode("$id_cargo|$cd_ccusto");
        $this->view->id_cargo       = $id_cargo;
        $this->view->cd_ccusto      = $cd_ccusto;
    }

    public function insertAction()
    {
        // action body
    }

    public function importacaoAction()
    {
        $form = new Commit_Form_Cadastro();
        $imp = $form->importacao();
        if ($this->_request->isPost()) {
            
            $formData = $this->_request->getPost();
            //faz upload do arquivo
            if(empty($_FILES['arquivo']['error'])){

                $adapter = new Zend_File_Transfer_Adapter_Http();
                $caminho = UPLOAD_PATH . '/importacao';
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
                
                //LÊ O ARQUIVO ATÉ CHEGAR AO FIM
                $ponteiro = fopen ($adapter->getFileName(), "r");
                $erro = false;
                while (!feof ($ponteiro)) {
                    $arrayDados = array();
                    //LÊ UMA LINHA DO ARQUIVO
                    $linha = '';
                    $linha = trim(fgets($ponteiro, 4096));
                    $linha = explode(";",$linha);
                    
                    $arrayDados['COD_FUNCAO']   = $linha[0];
                    $arrayDados['CD_CCUSTO']    = $linha[1];
                    $arrayDados['MES']          = $linha[2];
                    $arrayDados['QUANTIDADE']   = $linha[3];
//                    Zend_Debug::dump($arrayDados);
//                    exit;
                    $obj = new Admin_Model_HoraExtra();
                    $retorno = $obj->save($arrayDados);
                    if (strlen($retorno[0]) > 0){
                        $erro = true;
                        $msgErro = $retorno[0];
                    }
                }//FECHA WHILE
                //FECHA O PONTEIRO DO ARQUIVO
                fclose ($ponteiro);
                
                //var_dump($retorno);exit;
                if ($erro == true){
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $msgErro) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }else{
                $this->view->mensagem = base64_encode( $this->getMensagem('error', 'ERRO AO FAZER UPLOAD') );
            }
        }//fim if post
        
        $this->view->formulario = $imp;
    }

    public function progressAction() {

        // check if a request is an AJAX request
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new Zend_Controller_Request_Exception('Not an AJAX request detected');
        }

        $uploadId = $this->getRequest()->getParam('id');

        // this is the function that actually reads the status of uploading
        $data = uploadprogress_get_info($uploadId);

        $bytesTotal = $bytesUploaded = 0;

        if (null !== $data) {
            $bytesTotal = $data['bytes_total'];
            $bytesUploaded = $data['bytes_uploaded'];
        }

        $adapter = new Zend_ProgressBar_Adapter_JsPull();
        $progressBar = new Zend_ProgressBar($adapter, 0, $bytesTotal, 'uploadProgress');

        if ($bytesTotal === $bytesUploaded) {
            $progressBar->finish();
        } else {
            $progressBar->update($bytesUploaded);
        }
    }
    
    public function successAction() {
  
    }

    public function digitacaoAction()
    {
        $form = new Commit_Form_Cadastro();
        $digi = $form->digitacao();
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($digi->isValid($formData)){
//                $arrayDados['ID_HORA_EXTRA']    = ($this->_request->getPost('id_hora_extra'));
                $arrayDados['COD_FUNCAO']    = ($this->_request->getPost('id_cargo'));
                $arrayDados['CD_CCUSTO']    = ($this->_request->getPost('centros'));
                $arrayDados['MES'] = ($this->_request->getPost('mes_ref'));
//                $arrayDados['DESCRICAO'] = ($this->_request->getPost('descr'));
                $arrayDados['QUANTIDADE'] = ($this->_request->getPost('qtd'));
                //Zend_Debug::dump($arrayDados);exit;

                $obj = new Admin_Model_HoraExtra();
                $retorno = $obj->save($arrayDados);

                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        $this->view->formulario = $digi;
    }

    public function updateAction()
    {
        $form = new Commit_Form_Cadastro();
        $digi = $form->digitacao('update');
        
        $id = base64_decode($this->_request->getParam("id",null));
        
        if ($this->_request->isPost()) {
            $arrayDados = array();
            $formData = $this->_request->getPost();
            
            if ($digi->isValid($formData)){
                $arrayDados['ID']    = $id;
                $arrayDados['ID_HORA_EXTRA']    = $this->_request->getPost('id_hora_extra');
                $arrayDados['COD_FUNCAO']    = ($this->_request->getPost('id_cargo'));
                $arrayDados['CD_CCUSTO']    = ($this->_request->getPost('centros'));
                $arrayDados['MES'] = ($this->_request->getPost('mes_ref'));
                $arrayDados['DESCRICAO'] = ($this->_request->getPost('descr'));
                $arrayDados['QUANTIDADE'] = ($this->_request->getPost('qtd'));

                $obj = new Admin_Model_HoraExtra();
                $retorno = $obj->save($arrayDados);
                if (strlen($retorno[0]) > 0){
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        
        $obj = new Admin_Model_HoraExtra();

        foreach ($obj->fetchAllUpdate($id) as $dados){
            $digi->populate(array( 'id' =>base64_encode($dados['ID']),
                                    'id_hora_extra' =>($dados['ID_HORA_EXTRA'])
                                        ,'id_cargo'   =>$dados['COD_FUNCAO']
                                        ,'centros'   => $dados['CD_CCUSTO']
                                        ,'mes_ref' => $dados['MES']
                                        ,'descr' => $dados['DESCRICAO']
                                        ,'qtd' => $dados['QUANTIDADE']
					));
        }
        $this->view->formulario = $digi;
    }

    public function deleteAction()
    {
        $id = base64_decode($this->_request->getParam("id",null));

        $arrayData['ID'] = $id;
        $obj = new Admin_Model_HoraExtra();
        $retorno = $obj->delete($id, false, $arrayData);
        if (strlen($retorno[0]) > 0){
            $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
        }else{
            $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
        }
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

