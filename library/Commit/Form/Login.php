<?php

/**
 * Controle formulario de login
 * @filesource		/jmduque/library/Commit/Form/login.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Form
 * @package		Commit_Form_Login
 * @subpackage		Zend_Form
 * @version		1.0
 * @since		08/07/2011
 */
class Commit_Form_Login extends Zend_Form {

    /**
     * Monta o formulario de login
     *
     * @return Commit_Form_Login
     */
    public function Administrador($pasta='vermelho'){

        $head = new Commit_Controller_Action_Helper_Compatibilidade();
        if($head->getBrowser() == 'ie7'){
            $width = 'width: 125px';
        }else{
            $width = 'width: 145px';
        }
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'autenticar');

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-login');

        // Cria um campo de
        $login = new Zend_Form_Element_Text('username');
        $login->setLabel('Usuário:')
              ->setAttrib('tabindex','1')
              ->setAttrib('style',$width)
              ->setAttrib('class','easyui-validatebox')
              ->setAttrib('required','true')
              ->addFilter( new Zend_Filter_StringTrim() )
              ->setRequired();

        // Cria um campo de senha
        $senha = new Zend_Form_Element_Password('password');
        $senha->setLabel('Senha:')
              ->setAttrib('tabindex','2')
              ->setAttrib('style',$width)
              ->setAttrib('class','easyui-validatebox')
              ->setAttrib('required','true')
              ->setRequired();

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Image('submeter');
        $submeter->setLabel('Acessar')
                 ->setAttrib('tabindex','3')
                 ->setAttrib('src',PUBLIC_PATH.'_img/background/login/bt_entrar.jpg');

        $this->addElements(array(
                $login,
                $senha,
                $submeter
        ));

        return $this;
    }
}