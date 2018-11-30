<?php

/**
 * Helper para envio de email
 * @filesource		jmduque/library/Commit/Controller/Action/Helper/Email.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Action_Helper_Email
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version		1.0
 * @since		05/08/2011
*/

class Commit_Controller_Action_Helper_Email extends Zend_Controller_Action_Helper_Abstract {

   /**
    * Variavel com array para quem será enviado o email
    * @var      array $_para
    * @example  $this->_para = array('nome'=>'email');
    */
    public $_para  = null;
   /**
    * Variavel com array para quem será enviado a copia do email
    * @var      array $_cc
    * @example  $this->_cc = array('nome'=>'email', 'nome'=>'email');
    */
    public $_cc    = null;
   /**
    * Variavel com array para quem será enviado uma copia oculta do email
    * @var      array $_cco
    * @example  $this->_cc = array('nome'=>'email', 'nome'=>'email');
    */
    public $_cco   = null;
   /**
    * Variavel com array os dados de quem enviou o email
    * @var      array $_de
    * @example  $this->_de = array('nome'=>'email');
    */
    public $_de   = null;
   /**
    * Variavel com o tipo de codificacao
    * @var      char $_encode
    * @example  $this->_encode = 'UTF-8';
    */
    public $_encode   = 'UTF-8';

   /**
    * Objeto Zend_Mail
    * @var      char $_smtpServidor
    */
    protected $_email = null;
   /**
    * Configuracao do servidor smtp
    * @var      char $_smtpServidor
    */
    protected $_configuracao = null;
   /**
    * Mensagem de erro caso ocorra algum
    * @var      char $_erro
    */
    private $_erro = array('0'=>false);


    public function __construct() {
        $this->_email = new Zend_Mail($this->_encode);
        $this->getConfiguracao();
    }

    private function getConfiguracao() {
        $configuracao = new Admin_Model_ParametrosEmail();
        foreach ($configuracao->fetchAllPesquisa(null, false) as $dados){
            $this->_configuracao['TIPO_ENVIO']      = $dados['ENVIAR_POR'];
            $this->_configuracao['SERVIDOR']        = $dados['SMTP_SERVIDOR'];

            foreach (explode(',', base64_decode($dados['SMTP_CONFIGURACAO'])) as $count=>$dados){
                $array = explode('=', $dados);
                $this->_configuracao['CONFIGURACAO'][$array[0]] = $array[1];
            }
        }
        $this->setTransport();
    }

    private function setTransport(){
	try{
            $this->_configuracao['TRANSPORT'] = new Zend_Mail_Transport_Smtp($this->_configuracao['SERVIDOR'],$this->_configuracao['CONFIGURACAO']);
            return null;

	}catch(Exception $e){
            $this->_erro[0] =  true;
            $this->_erro[1] =  $e->getCode().'-'.$e->getMessage();
            $this->_erro[3] = 'function = setTransport()';
	}
    }

    public function send(){
        try{
            $this->_email->setBodyHtml('julio');
            $this->_email->addTo('sys.php@gmail.com');

            //$mail->addCc($dados['EMAIL'],$dados['NOME']);
            //$mail->addBcc($dados[1], $dados[0]);


            $this->_email->setFrom('julio.nascimento@commit.inf.br');
            $this->_email->setReturnPath('julio.nascimento@commit.inf.br');
            $this->_email->setSubject('EMAIL TESTE');
            $this->_email->send($this->_configuracao['TRANSPORT']);
            $this->_erro[4] = 'enviado';
        }catch(Exception $e){
            $this->_erro[0] =  true;
            $this->_erro[1] =  $e->getCode().'-'.$e->getMessage();
            $this->_erro[3] = 'function = send()';
        }
    }


    public function getErro($tipo='texto'){
        if($tipo=='texto'){
            echo $this->_erro[1];
        }elseif ($tipo=='dump') {
            Zend_Debug::dump($this->_erro);
        }elseif ($tipo=='objeto') {
            Zend_Debug::dump($this->_email);
        }
        exit (0);
    }

}