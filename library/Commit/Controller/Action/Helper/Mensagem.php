<?php

/**
 * Plugin para gerar mensagem de erro
 * @filesource		/library/Commit/Controller/Action/Helper/Mensagem.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Action_Helper_Mensagem
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version		1.0
 * @since		13/07/2011
*/

class Commit_Controller_Action_Helper_Mensagem extends Zend_Controller_Action_Helper_Abstract {

    private $_tipo;
    private $_titulo;
    private $_mensagem;

    public function start($mensagem){

        if (count($mensagem) > 0){
            $this->_tipo     = $mensagem['TIPO'];
            $this->_titulo   = $mensagem['TITULO'];
            $this->_mensagem = $mensagem['MENSAGEM'];
            
            if(@$mensagem['BASE']){
                $this->_mensagem = $this->getErroNaMensagem();
            }

            return base64_encode($this->setMensagemHTML());
        }
    }

    private function getErroNaMensagem(){
        $mensagem       = $this->_mensagem;
        $achou          = false;
        
        //set erro
        $this->setLog($mensagem);

        if (strpos($mensagem, 'OPERACAOREALIZADASUCESSO')       !== false) {$achou = true;return ($this->setMensagemArray('0000'));}
        if (strpos($mensagem, 'CARGOS_PROCESSOS_ARQUIVOS_PK')   !== false) {$achou = true;return ($this->setMensagemArray('0001'));}
        if (strpos($mensagem, 'PROCESSOS_CARGOS_FK')            !== false) {$achou = true;return ($this->setMensagemArray('0002'));}
        if (strpos($mensagem, 'EXISTE_INICIAL')                 !== false) {$achou = true;return ($this->setMensagemArray('0003'));}
        if (strpos($mensagem, 'EXISTE_FINAL')                   !== false) {$achou = true;return ($this->setMensagemArray('0004'));}
        if (strpos($mensagem, 'EXISTE_AMBOS')                   !== false) {$achou = true;return ($this->setMensagemArray('0005'));}
        if (strpos($mensagem, 'PARTE_COMPREENDIDO')             !== false) {$achou = true;return ($this->setMensagemArray('0007'));}
        if (strpos($mensagem, 'ERRO_EMAIL_CI')                  !== false) {$achou = true;return ($this->setMensagemArray('0008'));}
        if (strpos($mensagem, 'CRIAR_CI_SEM_APROVADOR')         !== false) {$achou = true;return ($this->setMensagemArray('0009'));}
        if (strpos($mensagem, 'ALTERAR_CI_SEM_APROVADOR')       !== false) {$achou = true;return ($this->setMensagemArray('0010'));}
        if (strpos($mensagem, 'FINAL_MENOR_INICIAL')            !== false) {$achou = true;return ($this->setMensagemArray('0011'));}
        if (strpos($mensagem, 'CI Já avaliada')                 !== false) {$achou = true;return array($mensagem);}

        if(!$achou){
            foreach ($this->setMensagemArray('0006') as $dados){
                $retorno[] = $dados;
            }
            $retorno[] = $mensagem;
            return $retorno;
        }
    }

    private function setMensagemHTML(){

        $retorno  = null;
        $retorno .= '<div id="PluginFlashMessenger" class="'.$this->_tipo.'">';
        $retorno .= '  <div id="PluginFlashMessengerTitulo">';
        $retorno .= '    <label>'.$this->_titulo.'</label>&nbsp;';
        $retorno .= '  </div>';
        $retorno .= '  <div id="PluginFlashMessengerCorpo">';
        $retorno .= '    <ul>';

        foreach ($this->_mensagem as $value)
        $retorno .= '       <li>'.$value.'</li>';

        $retorno .= '    </ul>';
        $retorno .= '  </div>';
        $retorno .= '</div>';

        return $retorno;
    }

    private function setMensagemArray($id){

        //$retorno['0001'] = array('Não foi possível localizar usuário ou senha no sistema.','Caso o problema persistir utilize a opção de recuperação de senha.');

        $retorno['0000'] = array('Operação realizada com sucesso.');
        $retorno['0001'] = array('Este arquivo já consta para esse cargo x processo.','Não é possível incluir este registro.');
        $retorno['0002'] = array('Este processo está relacionado à um cargo.','Não é possível excluir este registro.');
        $retorno['0003'] = array('O valor inicial já está compreendido em outro registro.');
        $retorno['0004'] = array('O valor final já está compreendido em outro registro.');
        $retorno['0005'] = array('Os valores inicial e final já estão compreendidos em um ou mais registros.');
        $retorno['0006'] = array('Um erro foi constatado.','Caso persisitir contate o administrador do sistema.');
        $retorno['0007'] = array('O valor inicial e/ou final que você está cadastrando já existe em outra faixa.');
        $retorno['0008'] = array('Operação realizada, porém ocorreu um erro ao enviar os emails.');
        $retorno['0009'] = array('CI não pode ser criado pois não existe usuário aprovador disponível para este centro de custo e/ou valor.');
        $retorno['0010'] = array('CI não pode ser alterado pois não existe usuário aprovador disponível para os novos dados.');
        $retorno['0011'] = array('Valor final não pode ser menor que o valor inicial.');
        $retorno['1000'] = array(" "," ");
        

        return $retorno[$id];

    }

    private function setLog($mensagem){
        
        if($this->_tipo != 'success'){
        
            $remote_addr = ((strlen($_SERVER['REMOTE_ADDR'])<>0)?$_SERVER['REMOTE_ADDR']:'NAO LOCALIZADO');
            $remote_uri  = ((strlen($_SERVER['REQUEST_URI'])<>0)?$_SERVER['REQUEST_URI']:'NAO LOCALIZADO');
            $nome        = ((strlen(Zend_Auth::getInstance()->getIdentity()->LOGIN)<>0)?Zend_Auth::getInstance()->getIdentity()->LOGIN:'NAO LOCALIZADO');
            $mensagem    = substr($mensagem, 0, 300);

            $data = array(
                 'NOME'             => $nome
                ,'REMOTE_AGENTE'    => $this->getBrowser()
                ,'REMOTE_ADDR'      => $remote_addr
                ,'REQUEST_URI'      => $remote_uri
                ,'MENSAGEM'         => $mensagem
            );

            $objLog  = new Admin_Model_LogErro();
            $objLog->save($data);
        }
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
    }
}

?>
