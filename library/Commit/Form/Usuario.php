<?php

/**
 * Formulario para alteracao dos dados pessoais do usuario
 * @filesource		/jmduque/library/Commit/Form/Usuario.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Form
 * @package		Commit_Form_Usuario
 * @subpackage		Zend_Form
 * @version		1.0
 * @since		19/07/2011
 */
class Commit_Form_Usuario extends Zend_Form {

    /**
     * Contem os dados para montar o decorators para a descricao
     * @var array
     */
    private $Decorators = array('ViewHelper',array('Description', array('tag' => 'span')),'Errors',array('HtmlTag', array('tag' => 'dd')),array('Label',   array('tag' => 'dt')),);

    /**
     * Monta o formulario manutencao no dados pessoais
     *
     * @return Commit_Form_Login->pessoal
     */
    public function pessoal(){

        // Setar a action do formulário
        $this->setAction(FORM_PATH .'/admin/index/minhaconta/t/pessoal');

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-minhaconta-pessoal');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo de Text
        $nome = new Zend_Form_Element_Text('nome');
        $nome   ->setLabel('Nome')
                ->setAttrib('style','width: 400px;')
                ->addValidator( new Zend_Validate_StringLength(5,60) )
                ->addFilter( new Zend_Filter_StringTrim() )
                ->setRequired();

        // Cria um campo de Text
        $email = new Zend_Form_Element_Text('email');
        $email   ->setLabel('Email')
                ->setAttrib('style','width: 400px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->addValidator( new Zend_Validate_StringLength(5,60) )
                ->addValidator( new Zend_Validate_EmailAddress())
                ->setRequired();

        // Cria um campo de Text
        $celular = new Zend_Form_Element_Text('celular');
        $celular->setLabel('Celular')
                ->setAttrib('style','width: 400px;')
                ->addValidator( new Zend_Validate_StringLength(12,12) )
                ->addFilter( new Zend_Filter_StringTrim() )
                ->setRequired();

        // Cria um campo de Text
        $ramal = new Zend_Form_Element_Text('ramal');
        $ramal->setLabel('Ramal')
              ->setAttrib('style','width: 400px;')
              ->addFilter( new Zend_Filter_StringTrim() )
              ->setRequired();

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('class','inputBtnSalvar');

        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
                 $nome
                ,$email
                ,$celular
                ,$ramal
                ,$submeter
                ,$id
        ));

        return $this;
    }

    /**
     * Monta o formulario para manutencao nos dados de acesso
     * @return Commit_Form_Usuario->acesso
     */
    public function acesso(){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'/admin/index/minhaconta/t/acesso');

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-minhaconta-acesso');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo de
        $login = new Zend_Form_Element_Text('login');
        $login->setLabel('Login')
              ->setAttrib('style','width: 400px;')
              ->setAttrib('readonly','readonly');

        // Cria um campo de senha
        $senha = new Zend_Form_Element_Password('senha');
        $senha->setLabel('Senha')
              ->setAttrib('style','width: 400px;')
              ->addValidator( new Zend_Validate_StringLength(6, 20) )
              ->addValidator( new Zend_Validate_StringLength(6, 20) )
              ->setDescription('Adicione a nova senha caso queira mudar')
              ->setDecorators($this->Decorators);

        // Cria um campo de senha
        $senha_repita = new Zend_Form_Element_Password('senha_repita');
        $senha_repita ->setLabel('Repita a Senha')
                      ->setAttrib('style','width: 400px;')
                      ->addValidator( new Zend_Validate_StringLength(6, 20) )
                      ->addFilter( new Zend_Filter_StringTrim() );

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('class','inputBtnSalvar');

        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
                 $login
                ,$senha
                ,$senha_repita
                ,$submeter
                ,$id
        ));

        return $this;
    }

    /**
     * Monta o formulario para os parametros do sistema, editado pelo usuario
     *
     * @return Commit_Form_Usuario->parametros
     */
    public function parametros(){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'/admin/index/minhaconta/t/parametros');

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-minhaconta-parametros');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo Select
        $calendario = new Zend_Form_Element_Select('calendario');
        $calendario->setLabel('Mostrar Calendário')
              ->setAttrib('style','width: 400px;')
              ->addMultiOption('t', 'Sim')
              ->addMultiOption('f', 'Não')
              ->setRequired();

        // Cria um campo Select
        $tempo = new Zend_Form_Element_Select('tempo_calendario');
        $tempo->setLabel('Visualização de eventos na agenda')
              ->setAttrib('style','width: 400px;')
              ->setAttrib('class','easyui-validatebox')
              ->addMultiOption('1', 'Mês atual')
              ->addMultiOption('3', 'Três meses')
              ->addMultiOption('6', 'Seis meses')
              ->addMultiOption('12', 'Um ano')
              ->setRequired();

        // Cria um campo Text
        $registros = new Zend_Form_Element_Text('registros_por_pagina');
        $registros->setLabel('Quantidade de registros por página')
              ->setAttrib('style','width: 400px;')
              ->addValidator( new Zend_Validate_StringLength(1,3) )
              ->addFilter( new Zend_Filter_StringTrim() )
              ->setRequired();

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('class','inputBtnSalvar');

        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
                $registros
                ,$submeter
                ,$id
        ));

        return $this;
    }

}