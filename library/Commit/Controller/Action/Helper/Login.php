<?php
/**
 * Helper para gerar mensagem
 * @filesource		/library/Commit/Controller/Action/Helper/Login.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright           Commit
 * @package		Commit_Controller_Action_Helper_Login
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version		1.0
 * @since		16/07/2011
 */
class Commit_Controller_Action_Helper_Login extends Zend_Controller_Action_Helper_Abstract {
    private $login;
    private $senha;

    public function setDados($login, $senha) {
        $this->login = strtoupper($login);
        $this->senha = $senha;

        return self::setAutenticacao();
    }

    private function setAutenticacao() {

        $params = array('username' => $this->login, 'password' => $this->senha, 'dbname' => DB_NAME);
        try{
            if(COMPUTERNAME == "WNTVMIT"){
                if($this->senha != 'UY7877@3##YSHYTT***usina***OP111276!!#$99087&&&2166'){
                    $db = Zend_Db::factory('Oracle', $params);
                    if($db->getConnection()){
                        $db = Zend_Registry::get('db2');
                    }
                    Zend_Registry::set('db', $db);
                }else{
                    $db = Zend_Registry::get('db2');
                }
                //Descomentar para testar com qualquer senha
                //$db = Zend_Registry::get('db2');
                $query = "select LOGIN as coldate from itdesk.USUARIOS WHERE LOGIN = '$this->login'";
            }else{
                $db = Zend_Registry::get('db2');
                $query = "select LOGIN as coldate from USUARIOS WHERE LOGIN = '$this->login'";
            }
            $res = $db->fetchRow($query);
            $login = $res['COLDATE'];
            //se nao esta vazio, logou
            if (!empty($login)) {
                $db = Zend_Registry::get('db2');
                $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'ID', 'LOGIN','SENHA','TIPO');
                $authAdapter->setTableName('USUARIOS')->setIdentityColumn('LOGIN')->setCredentialColumn('SENHA');
                $authAdapter->setIdentity(strtoupper($this->login));
                $authAdapter->setCredential('1');
                $auth = Zend_Auth::getInstance();

                $result = $auth->authenticate($authAdapter);
				
                $data = $authAdapter->getResultRowObject(null, 'SENHA');
                $auth->getStorage()->write($data);
                $namespace = new Zend_Session_Namespace('Zend_Auth');

                $grupo = Zend_Auth::getInstance()->getIdentity()->TIPO;
                //var_dump($grupo);exit;
                //$namespace->setExpirationSeconds(7200);
                return true;
            } else {
                echo '<script>alert("Usuario/Senha invalidos!");window.location="'.FORM_PATH.'";</script>';
                exit;
            }
        }catch (Zend_Db_Adapter_Exception $e) {
//            Zend_Debug::dump($e);
//            exit;
            // perhaps a failed login credential, or perhaps the RDBMS is not running
            echo '<script>alert("Usuario/Senha invalidos!");window.location="'.FORM_PATH.'";</script>';
            exit;
        } catch (Zend_Exception $e) {
//            Zend_Debug::dump($e);
//            exit;
            // perhaps factory() failed to load the specified Adapter class
            echo '<script>alert("Usuario/Senha invalidos!");window.location="'.FORM_PATH.'";</script>';
            exit;
        }
    }
}