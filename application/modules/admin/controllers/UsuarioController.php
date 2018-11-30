<?php
/**
 * Controle de cadastros de usuario do sistema
 * @filesource          /application/modules/admin/controllers/UsuarioController.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit
 * @package		Zend_Controller_Action
 * @subpackage		Admin_UsuarioController
 * @version		1.0
 * @since		29/07/2011
*/
class Admin_UsuarioController extends Zend_Controller_Action{

    public function init(){
        $head = new Commit_Controller_Action_Helper_Compatibilidade();
        
        $this->view->headLink()->appendStylesheet(PUBLIC_PATH. '_css/admin.css')
                               ->appendStylesheet($head->getCssGeral('admin'));
        $this->view->headScript()->appendFile(PUBLIC_PATH. '_js/admin.js')
                                 ->appendFile($head->getJsGeral('admin'));
    }

    /**
     * Metodo que insere registro na base de dados
     * @name	insertAction()
     * @author 	Julio Cesar Silva Nascimento
     * @version	1.0
     * @since	29/07/2011
     */
    public function insertAction(){
        $formulario = new Commit_Form_Cadastro();
        $usuario = $formulario->usuario();

        if ($this->_request->isPost()) {
            $arrayDadosUsuario = array();
            $arrayDadosPessoal = array();
            $formData = $this->_request->getPost();
            if ($usuario->isValid($formData)){

                $arrayDadosUsuario['EMPRESA']  = (int) Zend_Auth::getInstance()->getIdentity()->EMPRESA;
                $arrayDadosUsuario['LOGIN']    = $this->_request->getPost('login');
                $arrayDadosUsuario['SENHA']    = md5($this->_request->getPost('senha'));
                $arrayDadosUsuario['ATIVO']    = $this->_request->getPost('status');
                $arrayDadosUsuario['ATIVACAO'] = $this->getGerarCodigo(20);

                $arrayDadosPessoal['NOME']     = $this->_request->getPost('nome');
                $arrayDadosPessoal['EMAIL']    = $this->_request->getPost('email');
                $arrayDadosPessoal['CELULAR']  = $this->_request->getPost('celular');
                $arrayDadosPessoal['RAMAL']    = $this->_request->getPost('ramal');

                //Zend_Debug::dump($arrayDadosPessoal);

                $objUsuario = new Admin_Model_Usuario();
                $retorno = $objUsuario->save($arrayDadosUsuario, $arrayDadosPessoal);
                //Zend_Debug::dump($retorno);
                if (strlen($retorno[0]) > 0){
                    
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }
        $this->view->formulario = $usuario;
    }

    /**
     * Metodo que altera o registro na base de dados
     * @name	updateAction()
     * @author 	Julio Cesar Silva Nascimento
     * @version	1.0
     * @since	29/07/2011
     */
    public function updateAction(){
        $formulario = new Commit_Form_Cadastro();
        $usuario = $formulario->usuario('update');

        $id = base64_decode($this->_request->getParam("id",null));

        if ($this->_request->isPost()) {
            $arrayDadosUsuario = array();
            $arrayDadosPessoal = array();
            $formData = $this->_request->getPost();
            if ($usuario->isValid($formData)){

                $arrayDadosUsuario['id']        = base64_decode($this->_request->getPost('id'));
                $arrayDadosUsuario['LOGIN']     = $this->_request->getPost('login');

                if(strlen($this->_request->getPost('senha')) > 0 ){
                    $arrayDadosUsuario['SENHA'] = md5($this->_request->getPost('senha'));
                }
                $arrayDadosUsuario['ATIVO']     = $this->_request->getPost('status');

                $arrayDadosPessoal['id']        = base64_decode($this->_request->getPost('id'));
                $arrayDadosPessoal['NOME']      = $this->_request->getPost('nome');
                $arrayDadosPessoal['EMAIL']     = $this->_request->getPost('email');
                $arrayDadosPessoal['CELULAR']   = $this->_request->getPost('celular');
                $arrayDadosPessoal['RAMAL']     = $this->_request->getPost('ramal');

                //Zend_Debug::dump($arrayDadosPessoal);exit;

                $objUsuario = new Admin_Model_Usuario();
                $retorno = $objUsuario->save($arrayDadosUsuario, $arrayDadosPessoal);


                if (strlen($retorno[0]) > 0){
                    $this->view->mensagem = base64_encode( $this->getMensagem('error', $retorno[0]) );
                }else{
                    $this->view->mensagem = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                }
            }
        }

        $objUsuarioForm             = new Admin_Model_Usuario();
        $objUsuarioPessoalForm      = new Admin_Model_UsuarioPessoal();

        foreach ($objUsuarioForm->fetchAllUpdate($id) as $value => $dados){
            $usuario->populate(array(    'id'      =>base64_encode($dados['ID'])
                                        ,'login'   =>$dados['LOGIN']
                                        ,'senha'   =>null
                                        ,'status'  =>$dados['ATIVO']
					));
        }

        foreach ($objUsuarioPessoalForm->fetchAllUpdate($id) as $value => $dados){
            $usuario->populate(array(    'nome'     =>$dados['NOME']
                                        ,'email'    =>$dados['EMAIL']
                                        ,'celular'  =>$dados['CELULAR']
                                        ,'ramal'    =>$dados['RAMAL']
					));
        }

        $this->view->formulario = $usuario;
    }

    /**
     * Metodo que apaga/esconde o registro na base de dados
     * @name	updateAction()
     * @author 	Julio Cesar Silva Nascimento
     * @version	1.0
     * @since	29/07/2011
     */
    public function deleteAction(){
       $id = base64_decode($this->_request->getParam("id",null));

       $arrayData['id'] = $id;
       $arrayData['EXCLUIDO'] = 't';

       $objUsuario = new Admin_Model_Usuario();
       $retorno = $objUsuario->delete($id, true, $arrayData);
       $this->_redirect('admin/cadastro/usuario/');
    }

    /**
     * Metodo que altera as permissoes de acesso os sistema
     * @name	permissaoAction()
     * @author 	Julio Cesar Silva Nascimento
     * @version	1.0
     * @since	29/07/2011
     */
    public function permissaoAction(){

        $id = base64_decode($this->_request->getParam("id",null));
        $id = base64_decode($this->_request->getParam("id",null));

        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();
            $id       = base64_decode($formData['id']);
            $sub_menu = @$formData['sub_menu'];

            $objMenuAcesso = new Admin_Model_MenuAcesso();
            $retornoDelete = $objMenuAcesso->__delete($id);

            if (strlen($retornoDelete[0]) > 0){
                $this->view->mensagemPermissaoSistema = base64_encode( $this->getMensagem('error', $retornoDelete[0]) );
            }else{
                if(is_array($sub_menu)){
                    foreach ($sub_menu as $cont=>$dados){
                        $post = explode('|', $dados);

                        $ret[$post[1]][$post[2]][] = 't';
                        $menu_pai[] = $post[0];
                    }

                    foreach ($ret as $cont=>$dados){
                        $array = array(
                             'EMPRESA'          =>(int) Zend_Auth::getInstance()->getIdentity()->EMPRESA
                            ,'EMPRESA_USUARIO'  =>$id
                            ,'EMPRESA_MENU'     =>$cont
                            ,'VISUALIZAR'       =>((@$dados['VISUALIZAR'][0]=='t')?'t':'f')
                            ,'INSERIR'          =>((@$dados['INSERIR'][0]=='t')?'t':'f')
                            ,'ALTERAR'          =>((@$dados['ALTERAR'][0]=='t')?'t':'f')
                            ,'EXCLUIR'          =>((@$dados['EXCLUIR'][0]=='t')?'t':'f')
                        );
                        $retornoInsert = $objMenuAcesso->save($array);
                    }

                    $menu_pai = array_unique($menu_pai);
                    foreach ($menu_pai as $dados){
                        $array = array(
                             'EMPRESA'          =>(int) Zend_Auth::getInstance()->getIdentity()->EMPRESA
                            ,'EMPRESA_USUARIO'  =>$id
                            ,'EMPRESA_MENU'     =>$dados
                            ,'VISUALIZAR'       =>'t'
                            ,'INSERIR'          =>'t'
                            ,'ALTERAR'          =>'t'
                            ,'EXCLUIR'          =>'t'
                        );

                        $retornoInsert = $objMenuAcesso->save($array);

                        if (strlen($retornoInsert[0]) > 0){
                            $mensagem .= $retornoInsert[0].'<br>';
                        }
                    }
                    if (strlen(@$mensagem) > 0){
                        $this->view->mensagemPermissaoSistema = base64_encode( $this->getMensagem('error', @$mensagem) );
                    }else{
                        $this->view->mensagemPermissaoSistema = base64_encode( $this->getMensagem('success', 'OPERACAOREALIZADASUCESSO'));
                    }
                }
            }
        }

        $objMenuPermissao       = new Admin_Model_Menu();

        $objUsuarioPessoalForm  = new Admin_Model_UsuarioPessoal();
        foreach ($objUsuarioPessoalForm->fetchAllUpdate($id) as $value => $dados){
            $nome_usuario= $dados['NOME'];
        }



        $this->view->Permissao      = $objMenuPermissao->getPermissaoMenu($id);
        $this->view->usuario        = base64_encode($id);
        $this->view->nome_usuario   = $nome_usuario;
    }

    /**
     * Gera um código aleatorio
     * @name	permissaoAction()
     * @author 	Julio Cesar Silva Nascimento
     * @version	1.0
     * @since	29/07/2011
     * @param int $quantidade Quantidade de caracter retorno
     */
    private function getGerarCodigo($quantidade){
        $CaracteresAceitos = 'abcdxywzABCDZYWZ0123456789';
        $password = null;
        for($i=0; $i < $quantidade; $i++) {
            $password .= $CaracteresAceitos{mt_rand(0, $quantidade)};
        }
        return $password;
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
    private function getMensagem($tipo, $retorno, $titulo='MENSAGEM DO SISTEMA'){
        $mensagem    = array('TIPO' => $tipo,'TITULO'=>$titulo,'MENSAGEM'=>$retorno, 'BASE'=>true);
        $objMensagem = new Commit_Controller_Action_Helper_Mensagem();
        return base64_decode( $objMensagem->start($mensagem) );

    }
}









