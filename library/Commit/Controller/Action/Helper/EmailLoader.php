<?php
//ini_set('default_charset', 'UTF-8');
/*
 * Helper para gerar mensagem
 * @filesource		library/Commit/Controller/Action/Helper/EmailLoader.php
 * @autor 			Julio Cesar Silva Nascimento
 * @copyrigth 		Commit
 * @package			Commit_Controller_Action_Helper_EmailLoader
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version			1.0
 * @since			15/02/2011
*/
class Commit_Controller_Action_Helper_EmailLoader extends Zend_Controller_Action_Helper_Abstract {

	/**
	 * Carrega os dados recebidos por parametro
	 * @var array
	 */
	private 	$_configMensagem 	= array();
	/**
	 * Carrega os dados para envio SMTP
	 * @var array
	 */
	protected 	$_configEnvio 		= array();
	/**
	 * Carrega o corpo para o tipo de email 
	 * @var array
	 */
	protected 	$_configTipoEmail 	= array();
	/**
	 * Variavel com o trasnporte do envio
	 * @var array
	 */
	private 	$_Transport;
	/**
	 * Variavel com a mensagem de retorno
	 * @var char
	 */
	private 	$_Erro;
	/**
	 * Fun��o construtora da classe
	 * @author 	Julio Cesar Silva Nascimento
	 * @since	16/05/2011
	 * @param array $configuracao
	 */
	public    function __construct($configuracao){
		$this->_configMensagem = $configuracao;
//		self::getBaseDeDados();
                self::getBaseDeDados();
                self::setTransport();
		self::setSend();
	}
	
	/**
	 * Fun��o que busca os dados do provedor na base de dados,
	 * depois de carregar o array com os dados busca os dados do email em seguida monta o tipo de transporte do email
	 * 
	 * @author 	Julio Cesar Silva Nascimento
	 * @since	16/05/2011
	 */
	private   function getBaseDeDados(){
           if(COMPUTERNAME=="OCS030"){
               $config = array(                    'auth' 		=> 'login',
		          				'username' 	=> "testeitxml@gmail.com",
		          				'email' 	=> $this->_configMensagem['DE_EMAIL'],
		          				'password'	=> "teste123!",
		          				'ssl' 		=> 'tls',
		          				'servidor_smtp' => 'smtp.gmail.com',
		          				'port' 		=> '587');
           }else{
               $config = array(                    
		          				'email' 	=> $this->_configMensagem['DE_EMAIL'],
		          				'servidor_smtp' => '192.168.1.86',
		          				'port' 		=> '25');
           }
                    
                    $this->_configEnvio = $config;
            
//		Zend_Loader::loadClass('Configuracoes');
//		$selecionaObjeto = new Configuracoes();
//		foreach ($selecionaObjeto->_Seleciona(1) as $valeu=>$dados){
//			$this->_configEnvio = $dados;
//			
//			//carrega o dados do email
//			self::getBaseDoEmail();
//			
//			//carrega o transporte caso tenha achado os dados no banco de dados
//			self::setTransport();
//		}
	}
	
	
	
	/**
	 * Fun��o que altera os dados do corpo, 
	 * EX.troca a palavra #VAGA# pela descri��o que vem como parametro
	 * 
	 * @author 	Julio Cesar Silva Nascimento
	 * @since	16/05/2011
	 */
 	private function getCorpoDoEmail(){
 		
 		$email_corpo 			= $this->_configMensagem['DE_TEXTO'];
 		
 		
		return $email_corpo;
 	} 

	/**
	 * Função que que monta o transporte do email, 
	 * neste caso esta sendo utilizado o metodo de SMTP para envio de email
	 * 
	 * @author 	Julio Cesar Silva Nascimento
	 * @since	16/05/2011
	 */
	protected function setTransport(){
            if(COMPUTERNAME=="OCS030"){
               $config = array(                    'auth' 		=> 'login',
		          				'username' 	=> "testeitxml@gmail.com",
		          				'email' 	=> $this->_configMensagem['DE_EMAIL'],
		          				'password'	=> "teste123!",
		          				'ssl' 		=> 'tls',
		          				'port' 		=> '587');
           }else{
               $config = array(                    
                                                        'auth' 		=> '',
		          				'username' 	=> $this->_configMensagem['DE_EMAIL'],
		          				'port' 		=> '25');
           }
		try{
			
			return $this->_Transport = new Zend_Mail_Transport_Smtp($this->_configEnvio['servidor_smtp'],$config);
			 
		}catch(Exception $e){
			return $e->getCode().'-'.$e->getMessage();
		}
	}

	/**
	 * Função que carrega todos os dados para envio do email
	 * 
	 * @author 	Julio Cesar Silva Nascimento
	 * @since	16/05/2011
	 * @return	mensagem de erro ou nulo
	 */
	protected function setSend(){
		try{

			$email_corpo 		= self::getCorpoDoEmail();			
			
			//Zend_debug::dump($this->_configMensagem);exit;
			if(COMPUTERNAME=="OCS030"){
				$para_email  		= $this->_configMensagem['PARA_EMAIL'] ? $this->_configMensagem['PARA_EMAIL'] : "pedro.lobo@onclicksistemas.com.br";
				$para_nome   		= $this->_configMensagem['PARA_NOME'] ? $this->_configMensagem['PARA_NOME'] : "Pedro Henrique Gonzales Lobo";
			}else{
				$para_email  		= $this->_configMensagem['PARA_EMAIL'] ? $this->_configMensagem['PARA_EMAIL'] : "anderson@usinasantafe.com.br";
				$para_nome   		= $this->_configMensagem['PARA_NOME'] ? $this->_configMensagem['PARA_NOME'] : "Anderson";
			}
			
                        
                        $mail = new Zend_Mail('UTF-8');
			$mail->setBodyHtml($email_corpo, 'UTF-8');
			
                        $email_para = explode(";", $para_email);
                        $nome_para  = explode(";", $para_nome);
                        if(count($email_para)>1){
                            foreach ($email_para as $key => $value) {
                                if(@$nome_para[$key]){
                                    $mail->addTo($value, $nome_para[$key]);
                                }else{
                                    $mail->addTo($value, $value);
                                }                                
                            }
                        }else{
                            $mail->addTo($para_email, $para_nome);
                        }
			
			
			$mail->setFrom($this->_configMensagem['DE_EMAIL'], $this->_configMensagem['DE_NOME']);
			
				
			$mail->setReturnPath($this->_configEnvio['email']);
			$mail->setSubject($this->_configMensagem['ASSUNTO']);
			$mail->send($this->_Transport);
			return $this->_Erro = 'enviada';
			//return null;
		}catch(Exception $e){
			return $this->_Erro = $e->getCode().'-'.$e->getMessage();
			//return $e->getCode().'-'.$e->getMessage();
		}
	}
	
	public function Mensagem(){
		return $this->_Erro;
	}
}