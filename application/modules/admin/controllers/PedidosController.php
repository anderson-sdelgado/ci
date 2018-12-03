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
class Admin_PedidosController extends Zend_Controller_Action
{

    public function init(){
        $head = new Commit_Controller_Action_Helper_Compatibilidade();
        
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/admin.css')
                               ->appendStylesheet($head->getCssGeral('admin'))
                               ->appendStylesheet(PUBLIC_PATH. '_css/pedidos.css');
        $this->view->headScript()->appendFile(PUBLIC_PATH. '_js/admin.js')
                                 ->appendFile($head->getJsGeral('admin'))
                                 ->appendFile(PUBLIC_PATH. '_js/pedidos.js?v=3');
    }

    /**
     * Mostra a Action Index
     * @author 		Allan Rett Ferreira
     * @version		1.0
     * @since		10/03/2012
     */
    public function indexAction(){

    }
    
    public function diarioAction(){
        
        $objPedidos = new Admin_Model_Pedidos();
        $compradores = $objPedidos->compradores();   
        
        $data_atual     = new Zend_Date();
        
        $this->view->data_atual     = $data_atual->toString('dd/MM/yyyy');        
        $this->view->compradores = $compradores;
        
    }
    public function mensalAction(){
        $objPedidos = new Admin_Model_Pedidos();
        $compradores = $objPedidos->compradores();   
        
        $data_atual     = new Zend_Date();
        
        $this->view->data_atual     = $data_atual->toString('MM/yyyy');        
        $this->view->compradores = $compradores;
    }
    
    public function transportadorasAction(){
        $this->_helper->layout()->disableLayout();
        $objtransportadora  = new Admin_Model_Transportadora();
        $transportadoras    = $objtransportadora->fetchAll();   
//        Zend_Debug::dump($transportadoras);
        $this->view->transportadoras = $transportadoras;
    }
    
    public function filtrosAction(){
        $objPedidos = new Admin_Model_Pedidos();
        $compradores = $objPedidos->compradores();
        $this->view->compradores = $compradores;
    }
    public function historicoAction(){
        // action body
    }
    
    public function listarAction(){
        $this->_helper->layout()->disableLayout();
        
        $comprador          = ($this->_request->getParam("comprador",null));
        $de_pedido          = ($this->_request->getParam("de_pedido",null));
        $ate_pedido         = ($this->_request->getParam("ate_pedido",null));
        $de_data_pedido     = ($this->_request->getParam("de_data_pedido",null));
        $ate_data_pedido    = ($this->_request->getParam("ate_data_pedido",null));
        $de_fornecedor      = ($this->_request->getParam("de_fornecedor",null));
        $ate_fornecedor     = ($this->_request->getParam("ate_fornecedor",null));
        $enviado            = ($this->_request->getParam("enviado",null));
        $confirmado         = ($this->_request->getParam("confirmado",null));
        $observacao         = ($this->_request->getParam("observacao",null));
        $periodo            = ($this->_request->getParam("periodo",null));
        $ativo              = ($this->_request->getParam("ativo",null));
        $ordenacao          = ($this->_request->getParam("ordenacao",null));
        $ordenar_por        = ($this->_request->getParam("ordenar_por",null));
        
        
        
        if($de_data_pedido <> null && $de_data_pedido ==$ate_data_pedido){            
            if($periodo=='m'){
                $data_atual     = new Zend_Date("01/".$de_data_pedido );
                $data_anterior  = new Zend_Date("01/".$de_data_pedido );
                $proxima_data   = new Zend_Date("01/".$de_data_pedido );
            }else{
                $data_atual     = new Zend_Date($de_data_pedido);  
                $data_anterior  = new Zend_Date($de_data_pedido);  
                $proxima_data   = new Zend_Date($de_data_pedido);  
            }            
        }else{
            $data_atual     = new Zend_Date();
            $data_anterior  = new Zend_Date();
            $proxima_data   = new Zend_Date();
        }
            if($periodo=='m'){
                $data_anterior  = $data_anterior->subDay(1) ;
                $proxima_data   = $proxima_data->addMonth(1);                
                $this->view->data_atual     = $data_atual->toString('MM/yyyy');
                $this->view->data_anterior = $data_anterior->toString('MM/yyyy');
                $this->view->proxima_data  = $proxima_data->toString('MM/yyyy');
            }else{
                $data_anterior  = $data_anterior->subDay(1) ;
                $proxima_data   = $proxima_data->addDay(1);                
                $this->view->data_atual     = $data_atual->toString('dd/MM/yyyy');
                $this->view->data_anterior = $data_anterior->toString('dd/MM/yyyy');
                $this->view->proxima_data  = $proxima_data->toString('dd/MM/yyyy');
            }
        
        if(!empty($de_data_pedido) || !empty($ate_data_pedido)){
            if($de_data_pedido <> $ate_data_pedido){
                $data_pedido        = $de_data_pedido."|".$ate_data_pedido;
            }else{
                $data_pedido        = $de_data_pedido;
            }
        }else{
//            $data = getdate();
//            $data_pedido = str_pad($data["mon"], 2, "0", STR_PAD_LEFT)."-".$data["year"];
            $data_pedido = "";
        }        
        
        if(!empty($de_pedido) || !empty($ate_pedido)){
            $pedido             = $de_pedido."|".$ate_pedido;        
        }else{
            $pedido = null;
        }        
        
        if(!empty($de_fornecedor) || !empty($ate_fornecedor)){
            $fornecedor         = $de_fornecedor."|".$ate_fornecedor;
        }else{
            $fornecedor = null;
        }        
        if($periodo){
            $ativo = "S";
        }
                
        $objPedidos = new Admin_Model_Pedidos();
        $pedidos = $objPedidos->Listar($data_pedido,$periodo,$comprador,$pedido,$fornecedor,$enviado,$confirmado,$observacao,$ordenacao,$ordenar_por,$ativo); 
        $this->view->pedidos   = $pedidos;
        $this->view->periodo = $periodo;
        $this->view->comprador = $comprador;
    }
    
    public function detalheAction(){
        $this->_helper->layout()->disableLayout();
        
        $id_pedido          = ($this->_request->getParam("id_pedido",null));
        
        $objPedido = new Admin_Model_Pedidos();
        $Pedido = $objPedido->getPedido($id_pedido);
        
//        $id_pedido = $Pedido["PEDIDO"];
        $objItempedido = new Admin_Model_Itempedido();
        $itens_pedido = $objItempedido->itens($id_pedido);
        
        $objItempedido = new Admin_Model_Itempedido();
        $itens_pedido = $objItempedido->itens($id_pedido);
        
        $objPedidoHistorico = new Admin_Model_Pedidohistorico();
        $PedidoHistorico = $objPedidoHistorico->Envios($id_pedido);
        
//        Zend_Debug::dump($itens_pedido);
        
        $this->view->comprador      = $Pedido["NOME_COMPRADOR"];
        $this->view->pedido         = $Pedido["PEDIDO"];
        $this->view->data_pedido    = $Pedido["DT_PEDIDO"];
        $this->view->fornecedor     = $Pedido["NOME_FORNEC"];
        $this->view->valor          = $Pedido["VALOR"];       
        $this->view->itens_pedido   = $itens_pedido;        
        $this->view->Historico      = $PedidoHistorico;        
    }

    public function enviarAction(){
        
        $this->_helper->layout()->disableLayout();
        $id_pedido          = ($this->_request->getParam("pedido",null));
        $id_transportadora  = ($this->_request->getParam("transportadora",null));
        $flag_frete        = ($this->_request->getParam("frete",null));
        
        $objPedidocontrole  = new Admin_Model_Pedidocontrole();
        $Pedidocontrole     = $objPedidocontrole->find($id_pedido);
        
        $objPedido          = new Admin_Model_Pedidos();
        $Pedido             = $objPedido->find($id_pedido);
        
        $objEmpresa         = new Admin_Model_Empresa();
        $Empresa            = $objEmpresa->find($Pedido["CODIGO_EMPRESA"]);
        
        $objFornecedor      = new Admin_Model_Fornecedor();
        $Fornecedor         = $objFornecedor->find($Pedido["CODIGO_FORNEC"]);
        
//        $objTransportadora = new Admin_Model_Transportadora();
//        $Transportadora = $objTransportadora->find($id_transportadora);
        
        $LINK = $id_pedido."_".$Fornecedor["CNPJ_CPF"]."_".$Empresa["CNPJ_CPF"];
        
        if(COMPUTERNAME=="OCS030"){
            $LINK = "http://localhost/sistema/org/confirmar/?id=".base64_encode(strrev(base64_encode($LINK)));
        }else{
            $LINK = "http://pedidos.usinasantafe.com.br/org/confirmar/?id=".base64_encode(strrev(base64_encode($LINK)));
        }

        $data = array("ID_PEDIDO"           => $id_pedido,
                      "ID_FORNECEDOR"       => $Pedido["CODIGO_FORNEC"],
                      "LINK"                => $LINK,
                      "ID_TRANSPORTADORA"   => $id_transportadora,
                      "FLAG_FRETE"         => $flag_frete,
                      "ENVIADO"             => "s");
        
        $data_atual     = new Zend_Date();
        $dt_envio = $data_atual->toString('dd/MM/yyyy H:m');
        $usuario_login = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $id_usuario_oracle = (int)(Zend_Auth::getInstance()->getIdentity()->ID_USUARIO_ORACLE);
        $objUsuario = new Admin_Model_Usuario();
        $Usuario = $objUsuario->fetchIdOracle($id_usuario_oracle);
        
        if($Usuario[0]["EMAIL"]==null){
            $usuario_nome = $usuario_login;
            $usuario_email = "compras@usinasantafe.com.br";
        }else{
            $usuario_nome = $Usuario[0]["NOME"];
            $usuario_email = $Usuario[0]["EMAIL"];
        }
        
        $dadosHistorico = array("PEDIDO"        => $id_pedido,
                                "ID_COMPRADOR"  => $id_usuario_oracle,
                                "COMPRADOR"     => $usuario_nome,
                                "DATA_ENVIO"    => $dt_envio,
                                "CONFIRMACAO"   => 1);
        
//        if(strstr($Pedido["EMAIL_FORNEC"], "@onclicksistemas.com.br")){
//            $para = $Pedido["EMAIL_FORNEC"];
//            $para_nome = $Pedido["NOME_FORNEC"];
//            $para = "pedro.lobo@onclicksistemas.com.br";
//            $para_nome = "Pedro Henrique G. Lobo";
//        }else{
            if(COMPUTERNAME=="OCS030"){
                $para = "pedro.lobo@onclicksistemas.com.br";
                $para_nome = "Pedro Henrique G. Lobo";
            }else{
                $para = $Pedido["EMAIL_FORNEC"];
                $para_nome = $Pedido["NOME_FORNEC"];
            }
//        }
        
        if(@$Pedidocontrole["EMAIL_COPIA"]){
            $para.= ";".$Pedidocontrole["EMAIL_COPIA"];
        }
        
        $de = $usuario_email;
        $de_nome = $usuario_nome;
        $texto = 
        $Fornecedor["NOME_FORNEC"]."<br/>
        Att./ Responsável pelo departamento de vendas.<br/><br/>

        Prezado (a) Senhor (a)<br/><br/>

        Informamos que está disponível um novo pedido de compra de número $id_pedido, através da Internet. Clique no link para acessar a página deste pedido com os dados para faturamento:<br/> 
        $LINK<br/><br/>

        <u>Obs. Condições gerais de fornecimento:</u><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;1. Mencionar o número deste pedido na nota fiscal.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;2. Se houver pedido pendente , faturar junto deste informando o nº no corpo da nf.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;3. Solicitamos não emitir boletos bancários , enviar dados bancários para depósito.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;4. Não serão aceitas nfs. emitidas há mais de 5 dias e/ou vencidas.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;5. Não enviar material diferente do solicitado, respeitando sempre a quantidade informada, sem prévia autorização.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;6. Havendo quebra deste , efetuaremos devoluções c/reversão de frete e poderá haver multa de 0,3% ao dia , limitando a 5% do total do pedido , mais encargos sobre eventuais perdas e danos.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;7. É de responsabilidade do fornecedor a retirada e pronta substituição de material fora das especificações do pedido.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;8. Em caso de atraso na entrega , vencimento será prorrogado nas mesmas condições contratadas.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;9. Respeitar a transportadora informada , a não ser frete CIF.<br/></br>

		&nbsp;&nbsp;&nbsp;10. E-mails obrigatórios constar na nota fiscal e de envio :<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nfs. peças : nfeentrada@usinasantafe.com.br<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nfs. serviços : nfeservico@usinasantafe.com.br<br/></br>

		&nbsp;&nbsp;&nbsp;11. A partir de 01/02/17 as condições de pagamentos foram alteradas, sendo obrigatório cumprir a data negociada e impressa no pedido.<br/>
		&nbsp;&nbsp;&nbsp;12. O fornecedor será notificado caso tenha divergências nas datas de pagamentos antecipados ao vencimento acordado no pedido,  o mesmo será bloqueado até a correção.<br/><br/>
		&nbsp;&nbsp;&nbsp;13. A não contestação imediata das condições gerais deste pedido implica a sua aceitação.<br/><br/>

        Atenciosamente<br/><br/>

        $usuario_nome<br/>
        Departamento de Compras.<br/>
        USINA SANTA FE S/A";
        $email_assunto		= "Pedido de compra ".$id_pedido." - USINA SANTA FE S/A";
        $confgEmail = array(
              'ASSUNTO' => $email_assunto
            , 'DE_EMAIL' => ($de)
            , 'DE_NOME' => ($de_nome)
            , 'PARA_EMAIL' => ($para)
            , 'PARA_NOME' => ($para_nome)
            , 'DE_TEXTO' => ($texto)
        );
        
        $email = new Commit_Controller_Action_Helper_EmailLoader($confgEmail);
        $mensagem = $email->Mensagem();
//        $mensagem = 'enviada';

        if ($mensagem == 'enviada') {
            
//            echo "Pedido $id_pedido enviado com sucesso!";
            $objPedidocontrole->__save($data);
            
            $objPedidohistorico = new Admin_Model_Pedidohistorico();
            $insert = $objPedidohistorico->__save($dadosHistorico);
//            Zend_Debug::dump($insert);
            if($insert[0]){
                echo $insert[0];
            }
            sleep(5);
            exit;
        } else {
            echo Zend_Debug::dump($mensagem);
            exit;
        }
        
    }
    
    public function inativarAction(){
        $this->_helper->layout()->disableLayout();
        
        $pedidos    = ($this->_request->getParam("pedido",null));
        $retorno    = "";
        foreach ($pedidos as $id_pedido) {
            
            $objPedido = new Admin_Model_Pedidos();
            $Pedido = $objPedido->find($id_pedido);

            $data = array("ID_PEDIDO"       => $id_pedido,
                          "ID_FORNECEDOR"   => $Pedido["CODIGO_FORNEC"],
                          "ATIVO"           => "N");

            $data_atual     = new Zend_Date();
            $dt_envio = $data_atual->toString('dd/MM/yyyy H:m');
            $usuario_login = Zend_Auth::getInstance()->getIdentity()->LOGIN;
            $id_usuario_oracle = (int)(Zend_Auth::getInstance()->getIdentity()->ID_USUARIO_ORACLE);
            $objUsuario = new Admin_Model_Usuario();
            $Usuario = $objUsuario->fetchIdOracle($id_usuario_oracle);
            if($Usuario[0]["EMAIL"]==null){
                $usuario_nome = $usuario_login;
            }else{
                $usuario_nome = $Usuario[0]["NOME"];
            }
            $dadosHistorico = array("PEDIDO"        => $id_pedido,
                                    "ID_COMPRADOR"  => $id_usuario_oracle,
                                    "COMPRADOR"     => $usuario_nome,
                                    "DATA_ENVIO"    => $dt_envio,
                                    "TIPO_OPERACAO" => "I",
                                    "CONFIRMACAO"   => 1);        
        
            $objPedidocontrole = new Admin_Model_Pedidocontrole();
            $insert = $objPedidocontrole->__save($data);
            
            $objPedidohistorico = new Admin_Model_Pedidohistorico();
            $objPedidohistorico->__save($dadosHistorico);
            
            if($insert[0]){
                if($retorno==""){
                    $retorno= $id_pedido;
                }else{
                    $retorno.= ", ".$id_pedido;
                }                
            }
       
        }       
        echo $retorno;
        
        
    }
    
    /**
     * Action que atualiza ou adiciona emails em copia
     * @name	emailcopiaAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	14/09/2012
     */
    public function emailcopiaAction(){
        $this->_helper->layout()->disableLayout();
        $id_pedido          = ($this->_request->getParam("id_pedido",null));
        
        $objPedidoControle  = new Admin_Model_Pedidocontrole();

        if ($this->_request->isPost()) {
                $id_pedido                  = ($this->_request->getPost('id_pedido'));
                $email_copia                = ($this->_request->getPost('email_copia',null));
                if($email_copia){
                    $objPedido = new Admin_Model_Pedidos();
                    $Pedido = $objPedido->find($id_pedido);
                    $arrayDados = array("ID_PEDIDO"       => $id_pedido,
                                        "ID_FORNECEDOR"   => $Pedido["CODIGO_FORNEC"],
                                        "EMAIL_COPIA"     => $email_copia);
                    $retorno = $objPedidoControle->__save($arrayDados);
                }else{
                    $retorno[0] = "Favor, Informe um e-mail.";
                }
                
                if (strlen($retorno[0]) > 0){
                    echo $this->view->mensagem = $retorno[0];
                }else{
                    echo $this->view->mensagem = 'OK';
                    exit;
                }
        }
        $pedido = $objPedidoControle->find($id_pedido);
        
        $this->view->email_copia    = $pedido['EMAIL_COPIA'];
        $this->view->id_pedido      = $id_pedido;
    }
    
    /**
     * Action que atualiza ou adiciona observacoes do comprador
     * @name	observacaocompradorAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	14/09/2012
     */
    public function observacaocompradorAction(){
        $this->_helper->layout()->disableLayout();
        $id_pedido              = ($this->_request->getParam("id_pedido",null));
        
        $objPedidoControle  = new Admin_Model_Pedidocontrole();

        if ($this->_request->isPost()) {
                $id_pedido              = ($this->_request->getPost('id_pedido'));
                $observacao_comprador   = ($this->_request->getPost("observacao_comprador",null));
                if($observacao_comprador){
                    $objPedido = new Admin_Model_Pedidos();
                    $Pedido = $objPedido->find($id_pedido);

                    $arrayDados = array("ID_PEDIDO"             => $id_pedido,
                                        "ID_FORNECEDOR"         => $Pedido["CODIGO_FORNEC"],
                                        "OBSERVACAO_COMPRADOR"   => $observacao_comprador);
                    $retorno = $objPedidoControle->__save($arrayDados);
                }else{
                    $retorno[0] = "Favor, preencha o campo de observação.";
                }
                
                if (strlen($retorno[0]) > 0){
                    echo $this->view->mensagem = $retorno[0];
                }else{
                    echo $this->view->mensagem = 'OK';
                    exit;
                }
        }
        $pedido = $objPedidoControle->find($id_pedido);
        
        $this->view->observacao_comprador   = $pedido['OBSERVACAO_COMPRADOR'];
        $this->view->id_pedido              = $id_pedido;
    }
    
    public function impressaoAction(){
        $this->_helper->layout()->disableLayout();
        $id_pedido          = ($this->_request->getParam("id_pedido",null));
        
        $objPedido = new Admin_Model_Pedidos();
        $Pedido = $objPedido->find($id_pedido);
        
//        $id_pedido = $Pedido["PEDIDO"];
        $objItempedido = new Admin_Model_Itempedido();
        $itens_pedido = $objItempedido->itens($id_pedido);
//        Zend_Debug::dump($itens_pedido);
//        exit;
        $objEmpresa = new Admin_Model_Empresa();
        $Empresa = $objEmpresa->find(1);
        
        $objEmpresaCobranca = new Admin_Model_Empresacobranca();
        $EmpresaCobranca = $objEmpresaCobranca->find(1);
        
        $objFornecedor = new Admin_Model_Fornecedor();
        $Fornecedor = $objFornecedor->find($Pedido["CODIGO_FORNEC"]);
        
        $objPrazopagamento = new Admin_Model_Prazopagamento();
        $Prazo = $objPrazopagamento->Prazo_pedido($id_pedido);
        
        $objEntrega = new Admin_Model_Entrega();
        $Entrega = $objEntrega->find($id_pedido);
        
//        Zend_Debug::dump($Prazo);
//        exit;
        $this->view->Entrega         = $Entrega;
        $this->view->Prazo           = $Prazo;
        $this->view->Fornecedor      = $Fornecedor;
        $this->view->Empresa         = $Empresa;
        $this->view->EmpresaCobranca = $EmpresaCobranca;
        $this->view->Pedido          = $Pedido;
        $this->view->itens_pedido    = $itens_pedido;
    }

}