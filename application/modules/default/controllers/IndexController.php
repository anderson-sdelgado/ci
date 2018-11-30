<?php

class Default_IndexController extends Zend_Controller_Action {

    public function init() {

        $this->view->headLink()->appendStylesheet(PUBLIC_PATH . '_css/default.css');
        $this->view->headScript()->appendFile(PUBLIC_PATH . '_js/default.js');
//        self::LimpaSessaoAcesso();
    }

    public function indexAction() {
        $this->_redirect('/autenticar');
    }
    
    public function enviarAction() {
        $id         = base64_decode($this->_request->getParam("id", null));
        if(strlen($id)==0){
            $id = null;
        }
        $teste      = ($this->_request->getParam("teste", false));
        $enviado    = ($this->_request->getParam("enviado", null));
        $registros  = ($this->_request->getParam("ultimos", null));
        $this->_helper->layout()->disableLayout();
        ini_set('max_execution_time', 55);
        $objCiEnvio = new Admin_Model_CiEnvio();
        if($teste){
            $tabela="<table border=1 cellpadding=5 ><tr><th>ID</th><th>Nome De</th><th>Nome Para</th><th>Email Para</th><th>Assunto</th><th>Enviado</th></tr>";
            $todos = $objCiEnvio->fetchAll($registros,$enviado);
            foreach ($todos as $value) {
                if($value["PARA_NOME"])
                $tabela.="<tr><td>{$value["ID"]}</td><td>{$value["DE_NOME"]}</td><td>{$value["PARA_NOME"]}</td><td>{$value["PARA_EMAIL"]}</td><td>{$value["ASSUNTO"]}</td><td>{$value["ENVIADO"]}</td></tr>";
            }
            echo $tabela.="</table>";
            exit;
        }
        $Envios     = $objCiEnvio->fetchAllEnviar($id);
        $i = 0;
        foreach ($Envios as $confgEmail) {
            $i++;
            $id_envio = $confgEmail["ID"];
            unset($confgEmail["ID"]);
            
            $email = new Commit_Controller_Action_Helper_EmailLoader($confgEmail);
            $mensagem = $email->Mensagem();

            if ($mensagem == 'enviada'){
                $array = array("ID" => $id_envio, "ENVIADO" => 'S');
                $objCiEnvio->save($array);
                sleep(2);
            } else {
			//Zend_Debug::dump($confgEmail);
                echo "Assunto: ".$confgEmail["ASSUNTO"]."<br/>";
                echo "DE_EMAIL: ".$confgEmail["DE_EMAIL"]."<br/>";
                echo "DE_NOME: ".$confgEmail["DE_NOME"]."<br/>";
                echo "PARA_EMAIL: ".$confgEmail["PARA_EMAIL"]."<br/>";
                echo "PARA_NOME: ".$confgEmail["PARA_NOME"]."<br/>";
                //echo "Mensagem:".$confgEmail["DE_TEXTO"]."<br/>";
                echo "<b>Erro: ".$mensagem."</b><br/><br/>";
            }
            if($i==5){
                break;
            }
        }
    }

    public function loginAction() {

        $form = new Commit_Form_Login();
        $this->view->formulario = $form->Administrador();

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {

                $login = str_replace(" ", "", $this->_request->getPost('username'));
                $senha = str_replace(" ", "", $this->_request->getPost('password'));

				/*
				$date = New Zend_Date();
				$conteudo = $date->get('dd/MM/yyyy HH:mm')." - | LOGIN = '".$login."' | SENHA = '".$senha."'";
				$fp = fopen(substr(UPLOAD_PATH, 0, -7)."_logs".DIRECTORY_SEPARATOR.'loginTemp.txt', "a");

				fwrite($fp, $conteudo.PHP_EOL);
				fclose($fp);
				*/
                $objLogin = new Commit_Controller_Action_Helper_Login();
                $verifica = $objLogin->setDados($login, $senha);
                
                if ($verifica === true) {
                    $data_atual     = new Zend_Date();
                    $data_login = $data_atual->toString('dd/MM/yyyy H:m');
                    $dados = array("ID_USUARIO" => Zend_Auth::getInstance()->getIdentity()->ID,
                                   "DATA_LOGIN" => $data_login);
                    $objLogAcesso = new Admin_Model_LogAcesso();
                    $save_log = $objLogAcesso->save($dados,true);
                    if(is_null($save_log[0])){
                        Zend_Auth::getInstance()->getIdentity()->ID_LOG = $save_log[1];
                    }else{
                        Zend_Auth::getInstance()->clearIdentity();
                    }                    
                    //$this->setLog($login, 'sucesso');
                    $this->_redirect('admin/index');
                } else {
                    $this->setLog($login, 'erro');
                    echo '<script>alert("' . ($verifica) . '");</script>';
                }
            }
        }
    }

    private function LimpaSessaoAcesso() {
        $auth = Zend_Auth::getInstance();
        $auth->getStorage()->write(null);
    }

    /*
      private function setLog($login, $status){
      $remote = ((strlen($_SERVER['REMOTE_ADDR'])<>0)?$_SERVER['REMOTE_ADDR']:'NAO LOCALIZADO');

      $data = array(
      'USERNAME'      => $login
      ,'REMOTE_AGENTE' => $remote
      ,'REMOTE_ADDR'   => $this->getBrowser()
      ,'STATUS'        => $status
      );

      $objLog  = new Default_Model_LogAcesso();
      $objLog->save($data);
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
      } */

    public function confirmarAction() {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $this->_helper->layout()->disableLayout();
        $id = ($this->_request->getParam("id", null));
        $id_decode = base64_decode(strrev(base64_decode($id)));
        $info = explode("_", $id_decode);
        $id_pedido = $info[0];

        $data_atual = new Zend_Date();
        $dt_confirmacao = $data_atual->toString('dd/MM/yyyy HH:mm');

        if (!$this->_request->isPost()) {
            $objPedidoControle = new Admin_Model_Pedidocontrole();
            $objPedidoHistorico = new Admin_Model_Pedidohistorico();
            $PedidoHistorico = $objPedidoHistorico->UltimoEnvio($id_pedido);
//            Zend_Debug::dump($PedidoHistorico);exit;
            if ((!is_null($PedidoHistorico) && strlen($PedidoHistorico["DATA_CONFIRMACAO"]) == 0) && is_null($auth)) {
//                if(!is_null($PedidoHistorico)){
//                    $confirmacao = $PedidoHistorico["CONFIRMACAO"];
//                }else{
//                    $confirmacao = 1;
//                }
                $dados = array(
                    "ID_PEDIDO" => $id_pedido
                    , "CONFIRMADO" => "s"
                );
                $salvar = $objPedidoControle->__save($dados);
                $dadosHistorico = array(
                    "PEDIDO" => $id_pedido
                    , "DATA_ENVIO" => $PedidoHistorico["DATA_ENVIO"]
                    , "DATA_CONFIRMACAO" => $dt_confirmacao
                    , "CONFIRMACAO" => $PedidoHistorico["CONFIRMACAO"]
                );
                $salvar = $objPedidoHistorico->__save($dadosHistorico);
//                Zend_Debug::dump($salvar);
            }

            $PedidoControle = $objPedidoControle->find($id_pedido);

            $this->view->id_pedido = $id_pedido;
            $this->view->observacao = $PedidoControle["OBSERVACAO"];
            $this->view->post = false;
        } else {
            $observacao = ($this->_request->getParam("observacao", null));
            $pedido = ($this->_request->getParam("pedido", null));
            if (strlen($observacao) > 0) {

                $objPedidoHistorico = new Admin_Model_Pedidohistorico();
                $PedidoHistorico = $objPedidoHistorico->UltimoEnvio($pedido);

                $dados = array(
                    "ID_PEDIDO" => $pedido
                    , "CONFIRMADO" => "s"
                    , "OBSERVACAO" => $observacao
                    , "OBSERVACAO_LIDA" => ""
                );

                $objPedidoControle = new Admin_Model_Pedidocontrole();
                $salvar = $objPedidoControle->__save($dados);

                if (strlen($PedidoHistorico["OBSERVACAO"]) == 0 && $PedidoHistorico["CONFIRMACAO"] > 1) {
                    $confirmacao = $PedidoHistorico["CONFIRMACAO"];
                } else {
                    $confirmacao = $PedidoHistorico["CONFIRMACAO"] + 1;
                }

                $dadosHistorico = array(
                    "PEDIDO" => $pedido
                    , "DATA_ENVIO" => $PedidoHistorico["DATA_ENVIO"]
                    , "DATA_CONFIRMACAO" => $dt_confirmacao
                    , "OBSERVACAO" => $observacao
                    , "ID_COMPRADOR" => (int) $PedidoHistorico["ID_COMPRADOR"]
                    , "COMPRADOR" => $PedidoHistorico["COMPRADOR"]
                    , "CONFIRMACAO" => $confirmacao
                );
                $salvar = $objPedidoHistorico->__save($dadosHistorico);
//                Zend_Debug::dump($salvar);
                if ($salvar[0] == null) {
                    $this->view->mensagem = "Pedido Enviado com Sucesso!";
                } else {
                    $this->view->mensagem = "Ocorreu algum problema ao confirmar o recebimento do pedido<br/>Tente novamente mais tarde.";
                }
                $this->view->post = true;
            }
        }

        $objPedido = new Admin_Model_Pedidos();
        $Pedido = $objPedido->getPedido($id_pedido);
        
        $objItempedido = new Admin_Model_Itempedido();
        $itens_pedido = $objItempedido->itens($id_pedido);
        
        $objEmpresa = new Admin_Model_Empresa();
        $Empresa = $objEmpresa->find($Pedido["CODIGO_EMPRESA"]);

        $objEmpresaCobranca = new Admin_Model_Empresacobranca();
        $EmpresaCobranca = $objEmpresaCobranca->find($Pedido["CODIGO_EMPRESA"]);

        $objFornecedor = new Admin_Model_Fornecedor();
        $Fornecedor = $objFornecedor->find($Pedido["CODIGO_FORNEC"]);
        
        $Transportadora = array();
        if(@$PedidoControle["ID_TRANSPORTADORA"]){
            $objTransportadora = new Admin_Model_Transportadora();
            $Transportadora = $objTransportadora->find($PedidoControle["ID_TRANSPORTADORA"]);
        }        

        $objPrazopagamento = new Admin_Model_Prazopagamento();
        $Prazo = $objPrazopagamento->Prazo_pedido($id_pedido);

        $objEntrega = new Admin_Model_Entrega();
        $Entrega = $objEntrega->find($id_pedido);
        
        if(@$PedidoControle["OBSERVACAO_COMPRADOR"]){
            
            $this->view->observacao_comprador = $PedidoControle["OBSERVACAO_COMPRADOR"];
        }else{
            $this->view->observacao_comprador = null;
        }
        
        $this->view->auth = $auth;
        $this->view->PedidoHistorico = $PedidoHistorico;
        $this->view->Entrega = $Entrega;
        $this->view->Prazo = $Prazo;
        $this->view->Fornecedor = $Fornecedor;
        $this->view->Transportadora = $Transportadora;
        $this->view->Empresa = $Empresa;
        $this->view->EmpresaCobranca = $EmpresaCobranca;
        $this->view->Pedido = $Pedido;
        $this->view->itens_pedido = $itens_pedido;
    }
    public function printAction() {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $this->_helper->layout()->disableLayout();
        $id = ($this->_request->getParam("id", null));
        $id_decode = base64_decode(strrev(base64_decode($id)));
        $info = explode("_", $id_decode);
        $id_pedido = $info[0];

//        $data_atual = new Zend_Date();
//        $dt_confirmacao = $data_atual->toString('dd/MM/yyyy HH:mm');

        if (!$this->_request->isPost()) {
            $objPedidoControle = new Admin_Model_Pedidocontrole();
            $objPedidoHistorico = new Admin_Model_Pedidohistorico();
            $PedidoHistorico = $objPedidoHistorico->UltimoEnvio($id_pedido);
//            Zend_Debug::dump($PedidoHistorico);exit;
            

            $PedidoControle = $objPedidoControle->find($id_pedido);

            $this->view->id_pedido = $id_pedido;
            $this->view->observacao = $PedidoControle["OBSERVACAO"];
            $this->view->post = false;
        } else {
        }

        $objPedido = new Admin_Model_Pedidos();
        $Pedido = $objPedido->getPedido($id_pedido);
        
        $objItempedido = new Admin_Model_Itempedido();
        $itens_pedido = $objItempedido->itens($id_pedido);
        
        $objEmpresa = new Admin_Model_Empresa();
        $Empresa = $objEmpresa->find($Pedido["CODIGO_EMPRESA"]);

        $objEmpresaCobranca = new Admin_Model_Empresacobranca();
        $EmpresaCobranca = $objEmpresaCobranca->find($Pedido["CODIGO_EMPRESA"]);

        $objFornecedor = new Admin_Model_Fornecedor();
        $Fornecedor = $objFornecedor->find($Pedido["CODIGO_FORNEC"]);
        
        $Transportadora = array();
        if($PedidoControle["ID_TRANSPORTADORA"]){
            $objTransportadora = new Admin_Model_Transportadora();
            $Transportadora = $objTransportadora->find($PedidoControle["ID_TRANSPORTADORA"]);
        }        

        $objPrazopagamento = new Admin_Model_Prazopagamento();
        $Prazo = $objPrazopagamento->Prazo_pedido($id_pedido);

        $objEntrega = new Admin_Model_Entrega();
        $Entrega = $objEntrega->find($id_pedido);
        
        if($PedidoControle["OBSERVACAO_COMPRADOR"]){
            
            $this->view->observacao_comprador = $PedidoControle["OBSERVACAO_COMPRADOR"];
        }else{
            $this->view->observacao_comprador = null;
        }
        
        $this->view->auth = $auth;
        $this->view->PedidoHistorico = $PedidoHistorico;
        $this->view->Entrega = $Entrega;
        $this->view->Prazo = $Prazo;
        $this->view->Fornecedor = $Fornecedor;
        $this->view->Transportadora = $Transportadora;
        $this->view->Empresa = $Empresa;
        $this->view->EmpresaCobranca = $EmpresaCobranca;
        $this->view->Pedido = $Pedido;
        $this->view->itens_pedido = $itens_pedido;
    }

}