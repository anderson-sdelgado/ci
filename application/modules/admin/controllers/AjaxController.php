<?php
/**
 * Controller gerencia os ajax do sistema
 * @filesource		usina/application/modules/admin/controllers/AjaxController.php
 * @author 		Allan Rett Ferreira
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Controllers
 * @package		Admin_AjaxController
 * @subpackage		Zend_Controller_Action
 * @version		1.0
 * @since		10/03/2012
 */
class Admin_AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    /**
     * Metodo buscar processos, centros de custo e arquivos relacionados aos cargos
     * @name	processosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function processosAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id_cargo    = $this->_request->getParam("cargo",null);
        $objProc     = new Admin_Model_Processos();
        $processos = $objProc->fetchAllProcessosCargos($id_cargo);

        $objCusto     = new Admin_Model_CCusto();
        $custos = $objCusto->fetchAllCustosCargos($id_cargo);
        
        $objArquivo = new Admin_Model_Arquivos();
        $resposta = $objArquivo->fetchAllArquivos($id_cargo);
        
        $this->view->id_cargo   = $id_cargo;
        $this->view->processos  = $processos;
        $this->view->custos     = $custos;
        $this->view->resposta   = $resposta;
    }
    
    public function processoslinksAction()
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
     * Metodo buscar informativos relacionados aos cargos
     * @name	informativosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function informativosAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id_cargo    = $this->_request->getParam("cargo",null);
        $objProc     = new Admin_Model_Informativos();
        $processos = $objProc->fetchAllInformativosCargos($id_cargo);
        
        $this->view->id_cargo   = $id_cargo;
        $this->view->processos  = $processos;
    }
    
    public function informativoslinksAction()
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
     * Metodo que deleta arquivos relacionados aos cargos
     * @name	arquivosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function arquivosAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $id_cargo    = $this->_request->getParam("id_cargo",null);
        $arquivo = base64_decode($this->_request->getParam("arquivo",null));
        $caminho = UPLOAD_PATH .'/'.$id_cargo.'/'.$arquivo;
        unlink($caminho);
        
        $obj = new Admin_Model_Arquivos();
        $retorno = $obj->_delete($id_cargo,$arquivo);
        
        if (strlen($retorno[0]) > 0){
            $res = ( $this->getMensagem('error', $retorno[0]) );
        }else{
            $res = ( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
        }
        echo $res;
    }

    /**
     * Metodo que busca hierarquia do organograma
     * @name	relatoriosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function relatoriosAction()
    {
        $this->_helper->layout()->disableLayout();

        $id_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        $server = $this->getRequest()->getServer();
        if(isset($server["COMPUTERNAME"]) && $server["COMPUTERNAME"] === "WNTVMIT" && $id_usuario == 'ITDESK'){
            $id_usuario = 'HEITOR';
        }elseif(isset($server["COMPUTERNAME"]) && $server["COMPUTERNAME"] <> "WNTVMIT" && $id_usuario == 'MARCEL'){
            $id_usuario = 'HEITOR';
        }
		
        $objColab = new Admin_Model_Colabview();
        $id_colab = $objColab->fetchAllPesquisa($id_usuario);
        $id_sup = isset($id_colab[0]['COD_FUNCAO_SUP']) ? $id_colab[0]['COD_FUNCAO_SUP'] : null;
        $data = $this->_request->getParam("data",null);
        
        $obj = new Admin_Model_Cargos();
        $resultado = $obj->fetchRelatorio($id_sup,'',$data);
        
        echo json_encode($resultado);
    }

    /**
     * Metodo que busca dados dos colaboradores do organograma
     * @name	colabAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function colabAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_cargo    = $this->_request->getParam("id_cargo",null);
        $obj = new Admin_Model_Cargos();
        $data = $this->_request->getParam("data",null);
        
        $resultado = $obj->fetchColab($id_cargo,$data);
        
        echo json_encode($resultado);
        exit;
    }
    /**
     * Metodo que busca detalhes dos processos
     * @name	detprocessosAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     * 
     */
    public function detprocessosAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_processo    = $this->_request->getParam("id_processo",null);
        
        $obj = new Admin_Model_Processos();
        $resposta = $obj->fetchAllUpdate($id_processo);        
        
        
        echo nl2br(str_replace("  ","&nbsp;&nbsp;",str_replace("***","'", ($resposta[0]['DESCRICAO_DETALHADA']))));
        
    }

    /**
     * Metodo que busca mao de obra dos cargos
     * @name	moAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function moAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_cargo    = $this->_request->getParam("cargo",null);
        
        $objMo = new Admin_Model_Mo();
        $resposta = $objMo->fetchAllPesquisa($id_cargo);
        echo isset($resposta[0]['MO']) ? $resposta[0]['MO'] : null;
    }

    /**
     * Metodo que busca dados dos compradores dos pedidos
     * @name	compradoresAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function compradoresAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $objPedidos = new Admin_Model_Pedidos();
        $compradores = $objPedidos->compradores();
        
//        Zend_Debug::dump($compradores);
        $this->view->compradores = $compradores;
        
    }

    /**
     * Metodo que busca Observação do Pedido
     * @name	obspedido( pedido )
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	10/03/2012
     */
    public function obspedidoAction()
    {
        $pedido    = (int)$this->_request->getParam("id_pedido",null);
        $this->_helper->layout()->disableLayout();
        $objPedidoControle = new Admin_Model_Pedidocontrole();
        $Pedido = $objPedidoControle->find($pedido);
        
        $dados = array(
              'ID_PEDIDO' => $pedido
            , 'OBSERVACAO_LIDA' => 's'
        );
        
        $objPedidoControle->__save($dados);
        echo nl2br($Pedido['OBSERVACAO']);
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
     * Metodo que busca dados dos cargos dos colaboradores
     * @name	detcargoAction()
     * @author 	Allan Rett Ferreira
     * @version	1.0
     * @since	27/06/2012
     */
    public function detcargoAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_cargo    = $this->_request->getParam("id_cargo",null);
        $objCargo = new Admin_Model_Cargos();
        $resposta = $objCargo->fetchAllPesquisa($id_cargo,null,false);
        $this->view->resposta = json_encode($resposta);
    }

    public function pdfAction()
    {
        $this->_helper->layout()->disableLayout();
        $id_cargo    = $this->_request->getParam("id_cargo",null);
        
        $objCargo = new Admin_Model_Cargos();
        
        //lista as informações dos cargos e subordinados
        $res = $objCargo->fetchAllPesquisa($id_cargo,null,false);
        $subordinados = $objCargo->fetchCargosRel($id_cargo);
        
        //lista os centros de custo dos cargos e os funcionarios alocados nesse centro
        $objCusto = new Admin_Model_CCusto();
        $centros = $objCusto->fetchCustoCargo($id_cargo);
        
        //lista os processos relacionados ao cargo
        $objProc = new Admin_Model_Processos();
        $proc = $objProc->fetchAllProcessosCargosRel($id_cargo);
        
        //lista todos os arquivos do cargo
        $objArquivo = new Admin_Model_Arquivos();
        $arq = $objArquivo->fetchAllArquivos($id_cargo);
        
        $this->view->cargo = $res;
        $this->view->centro = $centros;
        $this->view->subordinados = $subordinados;
        $this->view->processos = $proc;
        $this->view->arquivos = $arq;
    }

    public function horafuncAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/relatorios.css');
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        if(COMPUTERNAME == "WNTVMIT" && $id_usuario == 'ITDESK'){
            $id_usuario = 'HEITOR';
        }elseif(COMPUTERNAME <> "WNTVMIT" && $id_usuario == 'MARCEL'){
            $id_usuario = 'HEITOR';
        }
        $data = $this->_request->getParam("data",null);
        
        
        $objColab = new Admin_Model_Colabview();
        $id_colab = $objColab->fetchAllPesquisa($id_usuario);
        
        $id_cargo = $id_colab[0]['CD_CARGO'];
        
        //lista as horas extras da view por cargo
        $objHora = new Admin_Model_Horaview();
        $hora = $objHora->fetchAllPesquisa($id_cargo,$data);
        
        $obj = new Admin_Model_HoraExtra();
        $extra = $obj->fetchHoraCCusto($id_cargo,$data);
//        Zend_Debug::dump($extra);
        
        $array = array();
        $orcado = array();
        $eventos = array();
        $colabs = array();
        $ccustos = array();
        $func_colab = array();
        foreach($hora as $h):
        @$array[$h['COD_CCUSTO']][$h['CD_COLAB']][$h['EVENTO']]['REALIZADO'] += $h['REALIZADO'];
        $eventos[$h['EVENTO']] = $h['EVENTO'];
        $ccustos[$h['COD_CCUSTO']] = $h['NOME_CCUSTO'];
        $colabs[$h['CD_COLAB']] = $h['NOME_COLAB'];
        $func_colab[$h['CD_COLAB']] = $h['FUNCAO_ID'];
        endforeach;
        
        foreach($extra as $orc):
            @$orcado[$orc['CD_CCUSTO']][$orc['COD_FUNCAO']] = $orc['QTDE'];
            @$orcado[$orc['CD_CCUSTO']]["TOTAL"] += $orc['QTDE'];
            @$orcado["TOTAL"] += $orc['QTDE'];
        endforeach;
        
        $this->view->data = $data;
        $this->view->hora = $array;
        $this->view->evento = $extra;
        $this->view->orcado = $orcado;
        $this->view->colabs = $colabs;
        $this->view->ccustos = $ccustos;
        $this->view->eventos = $eventos;
        $this->view->func_colab = $func_colab;
        
    }

    public function horacargoAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/relatorios.css');
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->LOGIN;
        
        $data = $this->_request->getParam("data",null);
        
        
        $objColab = new Admin_Model_Colabview();
        $id_colab = $objColab->fetchAllPesquisa($id_usuario);
        
        $id_cargo = $id_colab[0]['CD_CARGO'];
        
        //lista as horas extras da view por cargo
        $objHora = new Admin_Model_Horaview();
        $hora = $objHora->fetchAllPesquisa($id_cargo,$data);
        
        $obj = new Admin_Model_HoraExtra();
        $extra = $obj->fetchHoraCCusto($id_cargo,$data);
//        Zend_Debug::dump($extra);
        
        $array = array();
        $orcado = array();
        $eventos = array();
        $colabs = array();
        $ccustos = array();
        $realizado = 0;
        foreach($hora as $h):
            if(@$h['REALIZADO']){
                $realizado = str_replace(",",".",@$h['REALIZADO']);
            }else{
                $realizado = 0;
            } 
            @$array[$h['COD_CCUSTO']][$h['FUNCAO_ID']][$h['EVENTO']]['REALIZADO'] += $realizado;
            @$array[$h['COD_CCUSTO']][$h['FUNCAO_ID']]['NOMES_FUNC'][$h['CD_COLAB']] = $h['NOME_COLAB'];
        
        $eventos[$h['EVENTO']] = $h['EVENTO'];
        $ccustos[$h['COD_CCUSTO']] = $h['NOME_CCUSTO'];
        $colabs[$h['FUNCAO_ID']] = $h['DESCR_FUNCAO'];
        endforeach;
        
        foreach($extra as $orc):
            @$orcado[$orc['CD_CCUSTO']][$orc['COD_FUNCAO']] = $orc['QTDE'];
            @$orcado[$orc['CD_CCUSTO']]["TOTAL"] += ($orc['QTDE']*count(@$array[$orc['CD_CCUSTO']][$orc['COD_FUNCAO']]['NOMES_FUNC']));
            @$orcado["TOTAL"] += ($orc['QTDE']*count(@$array[$orc['CD_CCUSTO']][$orc['COD_FUNCAO']]['NOMES_FUNC']));
        endforeach;
        
        $this->view->data = $data;
        $this->view->hora = $array;
        $this->view->evento = $extra;
        $this->view->orcado = $orcado;
        $this->view->colabs = $colabs;
        $this->view->ccustos = $ccustos;
        $this->view->eventos = $eventos;
    }
    
    /**
     * Metodo que busca os colaboradores que são aprovadores
     * @name	aprovadoresAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	03/10/2012
     */
    public function aprovadoresAction()
    {
        $this->_helper->layout()->disableLayout();
        $ccusto     = base64_decode($this->_request->getParam("ccusto",null));
        $id_valor   = base64_decode($this->_request->getParam("id_valor",null));
        $fin_valor  = base64_decode($this->_request->getParam("fin_valor",null));
        $mensagem = "";
        if(strlen($ccusto)==0){
            $ccusto =null;
            $mensagem = "Não é possível relacionar mais aprovadores a esta faixa de valor.<br/>Todos os aprovadores disponíveis já estão relacionados";
        }
        if(strlen($id_valor)==0){
            $id_valor =null;
            $mensagem = "Não é possível adicionar mais aprovadores ao centro de custo.<br/>Todos os aprovadores já estão relacionados a este centro de custo";
        }
        if(strlen($fin_valor)==0){
            $fin_valor =null;
            $mensagem = "Não é possível adicionar mais aprovadores ao centro de custo.<br/>Todos os aprovadores já estão relacionados a este centro de custo";
        }
        
        $form           = new Commit_Form_Cadastro();
        $aprovadores    = $form->aprovadores($ccusto,$id_valor,$fin_valor);
        
        $aprovadores->populate(array( 'ccusto'      =>base64_encode($ccusto) ,
                                      'fin_valor'   =>base64_encode($fin_valor) ,
                                      'id_valor'    =>base64_encode($id_valor) ));
        $objUsuario = new Admin_Model_Usuario();
        $resposta   = $objUsuario->fetchAllAprovadores($ccusto,$id_valor,$fin_valor);
        if(count($resposta)>0){
            $this->view->aprovadores = $aprovadores;
        }else{
            $this->view->aprovadores = $mensagem;
        }
        
    }

    
    
    
    /**
     * Metodo para adicionar novas despesas a uma finalidade
     * @name	novadespesaAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	06/10/2014
     */
    public function novadespesaAction()
    {
        $this->_helper->layout()->disableLayout();
        $finalidade = base64_decode($this->_request->getParam("finalidade",null));
        
        
        $form       = new Commit_Form_Cadastro();
        $formulario = $form->despesa();
        
        if ($this->_request->isPost()) {
            $array = array(
                "CI_FINALIDADE" => $finalidade,
                "TIPO"          => $this->_request->getPost('tipo', null),
                "DESCRICAO"     => $this->_request->getPost('descricao', null),
                "ATIVO"         => $this->_request->getPost('ativo', null),
                "ORDEM"         => $this->_request->getPost('ordem', null),
            );
            
            $objFinalidadeTipoDespesa = new Admin_Model_FinalidadeTipoDespesa();
            $retorno = $objFinalidadeTipoDespesa->save($array);
            if (strlen($retorno[0]) > 0){
                $res = ( $this->getMensagem('error', $retorno[0]) );
                echo $res;
            }else{
                $res = ( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
            }
            exit;
        }
        
        $formulario->populate(array( 'finalidade'  =>base64_encode($finalidade) ));
        
        $this->view->formulario = $formulario;
    }
    
    
    
    /**
     * Metodo para adicionar novas despesas a uma finalidade
     * @name	novadespesaAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	06/10/2014
     */
    public function updatedespesaAction()
    {
        $this->_helper->layout()->disableLayout();
        $id = base64_decode($this->_request->getParam("id",null));
        $objFinalidadeTipoDespesa = new Admin_Model_FinalidadeTipoDespesa();
        
        $arrayDados = $objFinalidadeTipoDespesa->find($id);
//        $finalidade = $arrayDados["CI_FINALIDADE"];
        
        $form       = new Commit_Form_Cadastro();
        $formulario = $form->despesa("updatedespesa/id/".base64_encode($id));
        
        if ($this->_request->isPost()) {
            $array = array(
                "ID"            => $id,
//                "CI_FINALIDADE" => $finalidade,
//                "TIPO"          => $this->_request->getPost('tipo', null),
//                "DESCRICAO"     => $this->_request->getPost('descricao', null),
                "ATIVO"         => $this->_request->getPost('ativo', null),
                "ORDEM"         => $this->_request->getPost('ordem', null),
            );
            
            $retorno = $objFinalidadeTipoDespesa->save($array);
            if (strlen($retorno[0]) > 0){
                $res = ( $this->getMensagem('error', $retorno[0]) );
                echo $res;
            }else{
                $res = ( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
            }
            exit;
        }else{
            $arrayPopulate = array( 
                'id'         => base64_encode($id),
                'finalidade' => $arrayDados["CI_FINALIDADE"],
                'tipo'       => $arrayDados["TIPO"],
                'descricao'  => $arrayDados["DESCRICAO"],
                'ativo'      => $arrayDados["ATIVO"],
                'ordem'      => $arrayDados["ORDEM"],
            );
        }
        
        $formulario->populate($arrayPopulate);
        
        $this->view->formulario = $formulario;
    }
    
    
    
    /**
     * Metodo que busca os colaboradores para relaciona-los como visualizadores/criadores de CI 
     * @name	listausuariosAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	29/01/2013
     */
    public function listausuariosAction()
    {
        $this->_helper->layout()->disableLayout();
        $finalidade = base64_decode($this->_request->getParam("finalidade",null));
        $tipo       = (int)($this->_request->getParam("tipo",null));
        $mensagem = "";
        if(strlen($finalidade)==0){
            $finalidade =null;
            $mensagem = "Não é possível adicionar mais usuários a esta finalidade.<br/>Todos os usuários já estão relacionados a esta finalidade";
        }
        
        $form           = new Commit_Form_Cadastro();
        $usuarios    = $form->usuarios($finalidade,$tipo);
        
        $usuarios->populate(array( 'finalidade'  =>base64_encode($finalidade) ));
        
        $objUsuario = new Admin_Model_Usuario();
        
        if($tipo === 1){
            $resposta = $objUsuario->fetchAllVisualizadores($finalidade);
        }else{
            $resposta = $objUsuario->fetchAllCriadores($finalidade);
        }
        if(count($resposta)>0){
            $this->view->usuarios = $usuarios;
        }else{
            $this->view->usuarios = $mensagem;
        }        
    }
    
    /**
     * Metodo que busca os colaboradores que são aprovadores para o centro de custo
     * @name	aprovadoresccustoAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	04/10/2012
     */
    public function aprovadoresccustoAction()
    {
        $this->_helper->layout()->disableLayout();
        $ccusto = ($this->_request->getParam("ccusto",null));
        $valor1  = ($this->_request->getParam("valor",null));
        
        if(COMPUTERNAME == "WNTVMIT" || COMPUTERNAME == "OCS030"){
            $valor  = (strlen($valor1)>0) ? str_replace(",", ".", str_replace(".", "", $valor1)): 0;
        }else{
            $valor  = (strlen($valor1)>0) ? str_replace(".", "", $valor1): 0;
        }
        
        $objUsuario = new Admin_Model_Usuario();
        $resposta   = $objUsuario->fetchAllAprovadores2($ccusto, $valor);
        
        if(count($resposta)>0){
            $this->view->aprovadores = $resposta;
        }else{
            $this->view->aprovadores = null;
        }
        
    }

    
    /**
     * Metodo que busca os colaboradores que são aprovadores para o centro de custo
     * @name	aprovadoresciAction()
     * @author 	Pedro Henrique Gonzales Lobo
     * @version	1.0
     * @since	04/10/2012
     */
    public function aprovadoresciAction()
    {
        $this->_helper->layout()->disableLayout();
        $ccusto     = ($this->_request->getParam("ccusto",null));
        $valor1     = ($this->_request->getParam("valor",null));
        $finalidade = ($this->_request->getParam("finalidade",null));
        $resposta   = array();
        
        if(COMPUTERNAME == "WNTVMIT" || COMPUTERNAME == "OCS030"){
            $valor  = (strlen($valor1)>0) ? str_replace(",", ".", str_replace(".", "", $valor1)): 0;
        }else{
            $valor  = (strlen($valor1)>0) ? str_replace(".", "", $valor1): 0;
        }
        if($finalidade && $ccusto){            
            $objUsuario = new Admin_Model_Usuario();
            $resposta   = $objUsuario->fetchAllAprovadoresFinalidade($finalidade, $valor);
            if(count($resposta)==0){
                $resposta   = $objUsuario->fetchAllAprovadores2($ccusto, $valor);            
            }
        }
        
        if(count($resposta)>0){
            $this->view->aprovadores = $resposta;
        }else{
            $this->view->aprovadores = null;
        }
        
    }

    
}