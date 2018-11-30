<?php
/**
 * Controller gerencia a pagina de permissoes
 * @filesource		/application/modules/admin/controllers/PermissoesController.php
 * @author 		Allan Rett Ferreira
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Controllers
 * @package		Admin_PermissoesController
 * @subpackage		Zend_Controller_Action
 * @version		1.0
 * @since		10/03/2012
*/
class Admin_PermissoesController extends Zend_Controller_Action
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
        
    }

    public function cadastroAction()
    {
        $db = $this->conectaSys();        
        $sql = "SELECT * FROM dba_users WHERE account_status = 'OPEN' ORDER BY USERNAME ASC";

        $result = $db->fetchAll($sql);
        
        foreach ($result as $value1) {
            $usuarios[$value1['USERNAME']] = $value1;
            $usuarios[$value1['USERNAME']]['NOME'] = null;
            $usuarios[$value1['USERNAME']]['EMAIL'] = null;
        }
        
        $objUsuario = new Admin_Model_Usuario();
        $UsuEmail   = $objUsuario->fetchAllUsuEmail();
        
        foreach ($UsuEmail as $value) {
            if(isset($usuarios[$value['CD_USU_BD']])){
                $usuarios[$value['CD_USU_BD']]['NOME'] = $value['NOME'];
                $usuarios[$value['CD_USU_BD']]['EMAIL'] = $value['EMAIL'];                
            }
        }
        $this->view->paginator = $usuarios;
    }

    public function updateAction()
    {
        $id = base64_decode($this->_request->getParam("id",null));
        
        //cadastra novo usuario caso nao existir na base de dados
        
        $objUsuario = new Admin_Model_Usuario();
        $resultado1 = $objUsuario->fetchIdOracle($id);
        $resultado  = current($resultado1);
        $id_usuario_org = isset($resultado['ID']) ? $resultado['ID'] : $id;
        
        if (!$this->_request->isPost()) {
            if(empty($resultado)){

                $db = $this->conectaSys();
                $sql = "SELECT * FROM dba_users WHERE account_status = 'OPEN' AND USER_ID = '$id' ORDER BY USERNAME ASC";
                $result = $db->fetchAll($sql);
                
                $array = array();            
                foreach($result as $res):
                    $array['ID'] = $id;
                    $array['LOGIN'] = $res['USERNAME'];
                    $array['SENHA'] = 1;
                    $array['ID_USUARIO_ORACLE'] = $id;
                    $array['TIPO'] = 1;
                endforeach;

                $retorno = $objUsuario->__save($array);
            }
        }

        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();
            $id       = base64_decode($formData['id']);
            $sub_menu = isset($formData['sub_menu']) ? $formData['sub_menu'] : null;
            $menu     = isset($formData['menu']) ? $formData['menu'] : null;
            
            $objMenuAcesso = new Admin_Model_MenuAcesso();
            $retornoDelete = $objMenuAcesso->__delete($id_usuario_org);
            
            if (strlen($retornoDelete[0]) > 0){
                $this->view->mensagemPermissaoSistema = base64_encode( $this->getMensagem('error', $retornoDelete[0]) );
            }else{
                //insere permissao de menu pai
                if(is_array($menu)){
                    foreach($menu as $dados):
                        $array = array(
                            'USUARIOS_ID'  =>$id_usuario_org
                            ,'MENU_ID'     =>$dados
                            ,'VISUALIZAR'       =>'t'
                            ,'INSERIR'          =>'t'
                            ,'ALTERAR'          =>'t'
                            ,'EXCLUIR'          =>'t'
                        );
                        $retornoInsert = $objMenuAcesso->save($array);
                        if (strlen($retornoInsert[0]) > 0){
                            $mensagem .= $retornoInsert[0].'<br>';
                        }
                    endforeach;
                    
                }
                if(is_array($sub_menu)){
                    foreach ($sub_menu as $cont=>$dados){
                        $post = explode('|', $dados);

                        $ret[$post[1]][$post[2]][] = 't';
                        $menu_pai[] = $post[0];
                    }

                    foreach ($ret as $cont=>$dados){
                        $array = array(
                            'USUARIOS_ID'  =>$id_usuario_org
                            ,'MENU_ID'     =>$cont
                            ,'VISUALIZAR'       =>((isset($dados['VISUALIZAR'][0]) && $dados['VISUALIZAR'][0]=='t')?'t':'f')
                            ,'INSERIR'          =>((isset($dados['INSERIR'][0]) && $dados['INSERIR'][0]=='t')?'t':'f')
                            ,'ALTERAR'          =>((isset($dados['ALTERAR'][0]) && $dados['ALTERAR'][0]=='t')?'t':'f')
                            ,'EXCLUIR'          =>((isset($dados['EXCLUIR'][0]) && $dados['EXCLUIR'][0]=='t')?'t':'f')
                        );
                        $retornoInsert = $objMenuAcesso->save($array);
                    }
                    unset($resultado["EMAIL"]);
                    unset($resultado["NOME"]);
                    $resultado["APROVADOR"]       = ((isset($formData["aprovador"]) && $formData["aprovador"])?'t':'f');
                    $resultado["VISUALIZA_CI"]    = ((isset($formData["visualiza_ci"]) && $formData["visualiza_ci"])?'t':'f');
                    $resultado["PRE_APROVADOR"]   = $formData["pre_aprovador"];
                    if(COMPUTERNAME == "WNTVMIT" || COMPUTERNAME == "OCS030"){
                        $resultado["VALOR_INICIAL"]   = str_replace(",", ".", str_replace(".", "", $formData["valor_inicio"]));
                        $resultado["VALOR_FINAL"]     = str_replace(",", ".", str_replace(".", "", $formData["valor_fim"]));
                    }else{
                        $resultado["VALOR_INICIAL"]   = str_replace(".", "", $formData["valor_inicio"]);
                        $resultado["VALOR_FINAL"]     = str_replace(".", "", $formData["valor_fim"]);
                    }
                    
                    
                    $retornoInsert = $objUsuario->save($resultado);
                    $mensagem = isset($retornoInsert[0]) ? $retornoInsert[0] : null;
//                    exit;
                    
                    if (strlen($mensagem) > 0){
                        $this->view->mensagemPermissaoSistema = base64_encode( $this->getMensagem('error', $mensagem) );
                    }else{
                        $this->view->mensagemPermissaoSistema = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    }
                }
            }
            
        }
        
        $objMenuPermissao       = new Admin_Model_Menu();
        $this->view->Permissao  = $objMenuPermissao->getPermissaoMenu($id_usuario_org);
        $this->view->usuario    = base64_encode($id);
        $this->view->login      = $resultado['LOGIN'];
        $this->view->dados      = $resultado;
        $this->view->usuarios   = $objUsuario->fetchAllVisualizadores();
    }
    
    private function conectaSys(){
        try {
//            $params = array('username' => 'itdesk', 'password' => 'itdesk1553', 'dbname' => '//192.168.1.1/stafe' );
//            $params = array('username' => 'system', 'password' => 'system', 'dbname' => '//192.168.0.120/XE',
            $params = array('username' => DB_USER_SYSTEM, 'password' => DB_PASS_SYSTEM, 'dbname' => DB_NAME,
                'profiler' => array(
                'enabled' => true,
                'class' => 'Zend_Db_Profiler_Firebug'
            ));
            $db = Zend_Db::factory('Oracle', $params);
        }catch (Zend_Db_Adapter_Exception $e) {
            // talvez uma credencial de login falhou, ou talvez o SGBDR não está rodando
            throw ('Login Falhou');
        } catch (Zend_Exception $e) {
            // talvez factory() falhou em carregar a classe adaptadora especificada
            throw ('Factory Falhou');
        }
        
        return $db;
       
    }
    
    /**
     *  Funcao que cria a mesnagem do sistema
     * @example     tipo: notice, success, warning, error, validation
     * @param       char $tipo
     * @param       array $retorno
     * @param       char $titulo
     * @return      char
     * @tutorial    É utilizado para gerar as mensagens do sistema via Helper
     *
     */
    private function getMensagem($tipo, $retorno, $titulo = 'MENSAGEM DO SISTEMA')
    {
        $mensagem    = array('TIPO' => $tipo,'TITULO'=>$titulo,'MENSAGEM'=>$retorno, 'BASE'=>true);
        $objMensagem = new Commit_Controller_Action_Helper_Mensagem();
        return base64_decode( $objMensagem->start($mensagem) );

    }


}





