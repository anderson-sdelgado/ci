<?php

/**
 * Formulario manutencção nos parametros do sistema
 * @filesource		/jmduque/library/Commit/Form/Parametros.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Form
 * @package		Commit_Form_Parametros
 * @subpackage		Zend_Form
 * @version		1.0
 * @since		03/08/2011
 */
class Commit_Form_Parametros extends Zend_Form {

    /**
     * Contem os dados para montar o decorators para a descricao
     * @var array
     */
    private $Decorators = array('ViewHelper',array('Description', array('tag' => 'span')),'Errors',array('HtmlTag', array('tag' => 'dd')),array('Label',   array('tag' => 'dt')),);

    /**
     * Monta o formulario manutencao nos parametros do sistema
     *
     * @param   char $sAction
     * @return  Commit_Form_Parametros->sistema
     */
    public function sistema(){

        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/parametros/sistema');

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-parametros-sistema');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo de Text
        $parametro_01 = new Zend_Form_Element_Text('parametro_01');
        $parametro_01   ->setLabel('Máximo em Valores')
                        ->setAttrib('style','width: 300px;')
                        ->setAttrib('tabindex','1')
                        ->setAttrib('required','true')
                        ->setAttrib('class','easyui-validatebox')
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->setDescription('% Máxima em Valores Orçados X Valores Realizado!')
                        ->setDecorators($this->Decorators)
                        ->setRequired();

        // Cria um campo de Text
        $parametro_02 = new Zend_Form_Element_Text('parametro_02');
        $parametro_02   ->setLabel('E-mail Administração do sistema')
                        ->setAttrib('style','width: 300px;')
                        ->setAttrib('tabindex','2')
                        ->setAttrib('class','easyui-validatebox')
                        ->setAttrib('required','true')
                        ->setAttrib('validType','length[1,300]')
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->addValidator( new Zend_Validate_StringLength(1,300) )
                        ->setDescription('Caso seja mais de um separe com virgula Ex:.(email-1,email-2)!')
                        ->setDecorators($this->Decorators)
                        ->setRequired();

        // Cria um campo de Text
        $parametro_03 = new Zend_Form_Element_Text('parametro_03');
        $parametro_03   ->setLabel('Enviar E-mail')
                        ->setAttrib('style','width: 300px;')
                        ->setAttrib('tabindex','3')
                        ->setAttrib('class','easyui-validatebox')
                        ->setAttrib('required','true')
                        ->setAttrib('validType','length[1,3]')
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->addValidator( new Zend_Validate_StringLength(1,3) )
                        ->setDescription('Enviar e-mail [XX] dias antes do evento se não estiver aprovado!')
                        ->setDecorators($this->Decorators)
                        ->setRequired();

        // Cria um campo de Text
        $parametro_04 = new Zend_Form_Element_Text('parametro_04');
        $parametro_04   ->setLabel('E-mail do responsavel por todos eventos')
                        ->setAttrib('style','width: 300px;')
                        ->setAttrib('tabindex','4')
                        ->setAttrib('class','easyui-validatebox')
                        ->setAttrib('required','true')
                        ->setAttrib('validType','length[1,100]')
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->addValidator( new Zend_Validate_StringLength(1,100) )
                        ->setDescription('Caso seja mais de um separe com virgula Ex:.(email-1,email-2)!')
                        ->setDecorators($this->Decorators)
                        ->setRequired();

        // Cria um campo de Text
        $parametro_05 = new Zend_Form_Element_Text('parametro_05');
        $parametro_05   ->setLabel('Cancelar Evento')
                        ->setAttrib('style','width: 300px;')
                        ->setAttrib('tabindex','5')
                        ->setAttrib('class','easyui-validatebox')
                        ->setAttrib('required','true')
                        ->setAttrib('validType','length[1,3]')
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->addValidator( new Zend_Validate_StringLength(1,3) )
                        ->setDescription('Cancelar evento em após [XX] dias do envio de e-mail solicitando aprovação!')
                        ->setDecorators($this->Decorators)
                        ->setRequired();

        // Cria um campo de Text
        $parametro_06 = new Zend_Form_Element_Text('parametro_06');
        $parametro_06   ->setLabel('Finalizar Evento')
                        ->setAttrib('style','width: 300px;')
                        ->setAttrib('tabindex','6')
                        ->setAttrib('class','easyui-validatebox')
                        ->setAttrib('required','true')
                        ->setAttrib('validType','length[1,3]')
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->addValidator( new Zend_Validate_StringLength(1,3) )
                        ->setDescription('Finalizar evento em após [XX] dias do evento realizado!')
                        ->setDecorators($this->Decorators)
                        ->setRequired();


        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar');

        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
                 $parametro_01
                ,$parametro_02
                ,$parametro_03
                ,$parametro_04
                ,$parametro_05
                ,$parametro_06
                ,$submeter
                ,$id
        ));

        return $this;
    }

    /**
     * Monta o formulario manutencao no de envio de email
     *
     * @param   char $sAction
     * @return  Commit_Form_Parametros->email
     */
    public function email(){

        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/parametros/email');

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-parametros-email');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo Select
        $enviar_por = new Zend_Form_Element_Select('enviar_por');
        $enviar_por->setLabel('Enviar por')
              ->setAttrib('style','width: 400px;')
              ->setAttrib('tabindex','1')
              ->setAttrib('class','easyui-validatebox')
              ->addMultiOption('SMTP', 'SMTP')
              ->addMultiOption('SERVIDOR', 'SERVIDOR')
              ->setDescription('Tipo de conexão para envio de e-mail.')
              ->setDecorators($this->Decorators)
              ->setRequired();

        // Cria um campo de Text
        $smtp_servidor = new Zend_Form_Element_Text('smtp_servidor');
        $smtp_servidor->setLabel('URL do servidor SMTP')
                        ->setAttrib('style','width: 400px;')
                        ->setAttrib('tabindex','2')
                        ->setAttrib('class','easyui-validatebox')
                        ->setAttrib('validType','length[1,100]')
                        //->addValidator( new Commit_Validate_URL() )
                        ->addFilter( new Zend_Filter_StringTrim() )
                        ->setDescription('Ex:. http://servidor.com.br')
                        ->setDecorators($this->Decorators);

        // Cria um campo de Text
        $smtp_configuracao = new Zend_Form_Element_Textarea('smtp_configuracao');
        $smtp_configuracao  ->setLabel('Configuração do SMTP')
                            ->setAttrib('style','width: 400px;')
                            ->setAttrib('tabindex','3')
                            ->setAttrib('rows', 5)
                            ->setAttrib('class','easyui-validatebox')
                            ->setAttrib('validType','length[1,300]')
                            ->addValidator( new Zend_Validate_StringLength(1,300) )
                            ->addFilter( new Zend_Filter_StringTrim() )
                            ->setDescription('Ex:. "auth" => "login","username" => "myusername"')
                            ->setDecorators($this->Decorators);

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar');

        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
                 $enviar_por
                ,$smtp_servidor
                ,$smtp_configuracao
                ,$submeter
                ,$id
        ));

        return $this;
    }
}