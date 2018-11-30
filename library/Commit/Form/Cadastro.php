<?php

/**
 * Formulario manutencção nos cadastros do sistema
 * @filesource		/library/Commit/Form/Cadastro.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Form
 * @package		Commit_Form_Cadastro
 * @subpackage		Zend_Form
 * @version		1.0
 * @since		19/07/2011
 */
class Commit_Form_Cadastro extends Zend_Form {

    /**
     * Monta o formulario manutencao no cadastro de processos
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->processos
     */
    public function processos($sAction='insert'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/processos/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-processos');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo de Text
        $codigo = new Zend_Form_Element_Text('codigo');
        $codigo   ->setLabel('Código')
                  ->setAttrib('tabindex','1')
                  ->addValidator( new Zend_Validate_StringLength(1,24) )
                  ->addFilter( new Zend_Filter_StringTrim() )
                  ->setAttrib('maxLength', 24)
                  ->setRequired();

        // Cria um campo de Textarea
        $descSum = new Zend_Form_Element_Textarea('descricao_sumaria');
        $descSum->setLabel('Descrição Sumária')
             ->setAttrib('tabindex','2')
             ->addValidator( new Zend_Validate_StringLength(1,120) )
             ->setAttrib('maxLength', 120)
             ->setRequired();

        // Cria um campo de Textarea
        $descDet = new Zend_Form_Element_Textarea('descricao_detalhada');
        $descDet->setLabel('Descrição Detalhada')
             ->setAttrib('tabindex','3')
             ->addValidator( new Zend_Validate_StringLength(1,4000) )
             ->setAttrib('maxLength', 4000)
             ->setRequired();
        
        
        // Cria um campo de Text
        $link = new Zend_Form_Element_Text('link');
        $link ->setLabel('link')
              ->setAttrib('tabindex','4')
              ->setAttrib('style','width: 85%;')
              ->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('DtDdWrapper')
              ->addFilter( new Zend_Filter_StringTrim() );        
        
        // Cria um campo de Text
        $descricao = new Zend_Form_Element_Text('descricao');
        $descricao ->setLabel('descricao')
              ->setAttrib('tabindex','4')
              ->setAttrib('style','width: 85%;')
              ->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('DtDdWrapper')
              ->addFilter( new Zend_Filter_StringTrim() );
        
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnSalvar');

        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/processos.phtml'))));
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $codigo
            ,$descSum
            ,$descDet
            ,$link
            ,$descricao
            ,$submeter
            ,$id
        ));
        return $this;
    }
    /**
     * Monta o formulario para reprovar CI
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->processos
     */
    public function avaliarCI($sAction=''){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/avaliarci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form');
        $this->setAttrib('class', 'cadastro');
        $this->setAttrib('id', 'form-avaliar');

        // Cria um campo de Textarea
        $descDet = new Zend_Form_Element_Textarea('motivo');
        $descDet->setLabel('Motivo:')->removeDecorator('label')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 95%;height:100px')
                ->addValidator( new Zend_Validate_StringLength(1,4000) )
                ->setAttrib('maxLength', 4000)
                ->setRequired();
        
        // Cria um campo Hidden
        $status = new Zend_Form_Element_Hidden('status');
        $status_atual = new Zend_Form_Element_Hidden('status_atual');
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $descDet
            ,$id
            ,$status
            ,$status_atual
        ));
        return $this;
    }
    /**
     * Monta o formulario para reprovar CI
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->processos
     */
    public function reprovarCI($sAction=''){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/reprovarci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form');
        $this->setAttrib('class', 'cadastro');
        $this->setAttrib('id', 'form-reprovar');

        // Cria um campo de Textarea
        $descDet = new Zend_Form_Element_Textarea('motivo');
        $descDet->setLabel('Motivo:')->removeDecorator('label')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 95%;height:100px')
                ->addValidator( new Zend_Validate_StringLength(1,4000) )
                ->setAttrib('maxLength', 4000)
                ->setRequired();
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $descDet
            ,$id
        ));
        return $this;
    }
    /**
     * Monta o formulario para reprovar CI
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->processos
     */
    public function cancelarCI($sAction=''){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form');
        $this->setAttrib('class', 'cadastro');
        $this->setAttrib('id', 'form-cancelar');

        // Cria um campo de Textarea
        $descDet = new Zend_Form_Element_Textarea('motivo');
        $descDet->setLabel('Motivo:')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 95%;height:100px')
                ->addValidator( new Zend_Validate_StringLength(1,4000) )
                ->setAttrib('maxLength', 4000)
                ->setRequired();
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $descDet
            ,$id
        ));
        return $this;
    }
    /**
     * Monta o formulario para reprovar CI
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->processos
     */
    public function aprovarCI($sAction=''){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/aprovarci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form');
        $this->setAttrib('class', 'cadastro');
        $this->setAttrib('id', 'form-aprovar');

        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array($id));
        return $this;
    }
    /**
     * Monta o formulario manutencao no cadastro de processos
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->processos
     */
    public function informativositem($sAction='insert'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/informativos/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-processos');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo de Textarea
        $descSum = new Zend_Form_Element_Textarea('descricao_sumaria');
        $descSum->setLabel('Descrição Sumária')
             ->setAttrib('tabindex','2')
             ->addValidator( new Zend_Validate_StringLength(1,120) )
             ->setAttrib('maxLength', 120)
             ->setRequired();

        // Cria um campo de Textarea
        $descDet = new Zend_Form_Element_Textarea('descricao_detalhada');
        $descDet->setLabel('Descrição Detalhada')
             ->setAttrib('tabindex','3')
             ->addValidator( new Zend_Validate_StringLength(1,4000) )
             ->setAttrib('maxLength', 4000)
             ->setRequired();
        
        
        // Cria um campo de Text
        $link = new Zend_Form_Element_Text('link');
        $link ->setLabel('link')
              ->setAttrib('tabindex','4')
              ->setAttrib('style','width: 85%;')
              ->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('DtDdWrapper')
              ->addFilter( new Zend_Filter_StringTrim() );

        // Cria um campo de Text
        $descricao = new Zend_Form_Element_Text('descricao');
        $descricao ->setLabel('descricao')
              ->setAttrib('tabindex','4')
              ->setAttrib('style','width: 85%;')
              ->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('DtDdWrapper')
              ->addFilter( new Zend_Filter_StringTrim() );
        
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnSalvar');

        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/processos.phtml'))));
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $descSum
            ,$descDet
            ,$link
            ,$descricao
            ,$submeter
            ,$id
        ));
        return $this;
    }
    /**
     * Monta o formulario manutencao no cadastro de informativos
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->informativos
     */
    public function informativos($sAction='insert'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/informativos/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-informativos');
        $this->setAttrib('class', 'cadastro');

        // Cria um campo de Text
        $descricao = new Zend_Form_Element_Text('descricao');
        $descricao   ->setLabel('Descrição')
                    ->setAttrib('tabindex','1')
                    ->setAttrib('style','width: 50%;')
                    ->addFilter( new Zend_Filter_StringTrim() )
                    ->setRequired();

        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnSalvar');

        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $descricao
            ,$submeter
            ,$id
        ));
        return $this;
    }
    
    /**
     * Monta o formulario de arquivos cargos x processos
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->arquivos
     */
    public function arquivos($sAction='addarquivos'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/processos/'.$sAction);
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-arquivos');
        $this->setAttrib('class', 'cadastro');

        $id_cargo    = new Zend_Form_Element_Hidden('id_cargo');
        
        // Cria um campo de Text
        $arquivo = new Zend_Form_Element_File('arquivo');
        $arquivo   ->setLabel('Arquivo')
                   ->setAttrib('tabindex','1')
                   ->setDestination(UPLOAD_PATH)
                   ->setRequired();

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','2')->setAttrib('class','inputBtnSalvar');

        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/arquivos.phtml'))));
        
        $this->addElements(array(
            $id_cargo,
            $arquivo,
            $submeter
        ));
        return $this;
    }
    
    /**
     * Monta o formulario de digitacao de horas extras
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->digitacao
     */
    public function digitacao($sAction='digitacao'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/eventos/'.$sAction);
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-digitacao');
        $this->setAttrib('class', 'cadastro');
        
        // Cria um campo de Text
        $id_cargo = new Zend_Form_Element_Text('id_cargo');
        $id_cargo   ->setLabel('Cód. Cargo')
                  //->setAttrib('tabindex','1')
                  ->addValidator( new Zend_Validate_StringLength(1,24) )
                  ->addFilter( new Zend_Filter_StringTrim() )
                  ->setAttrib('maxLength', 24)
                  //->setOptions(array('belongsTo' => 'id_cargo'))
                  ->setRequired();
        
        //campo tipo selectbox
        $objCentro = new Admin_Model_CCusto();
        $custos = $objCentro->fetchAllPesquisa(null,null,null);
        
        $centros = new Zend_Form_Element_Select('centros'); 
        $centros->setLabel('Centro de Custo')
                //->setAttrib('tabindex','2')
                //->setOptions(array('belongsTo' => 'centros'))
                ->setAttrib('style','width: 90%;');
        
        foreach($custos as $c):
            $centros->addMultiOption($c['CD_CCUSTO'], $c['NOME_CCUSTO']);    
        endforeach;
        
        $mes = new Zend_Form_Element_Text('mes_ref');
        $mes   ->setLabel('Mês Ref.')
                  //->setAttrib('tabindex','3')
                  //->setOptions(array('belongsTo' => 'mes'))
                  ->addValidator( new Zend_Validate_StringLength(1,24) )
                  ->addFilter( new Zend_Filter_StringTrim() )
                  ->setAttrib('maxLength', 24)
                  ->setRequired();
        
        $descr = new Zend_Form_Element_Text('descr');
        $descr   ->setLabel('Descr. Evento')
                 //->setOptions(array('belongsTo' => 'descr'))
                  //->setAttrib('tabindex','4')
                  ->addValidator( new Zend_Validate_StringLength(1,24) )
                  ->addFilter( new Zend_Filter_StringTrim() )
                  ->setAttrib('maxLength', 24)
                  ->setRequired();
        
        $qtd = new Zend_Form_Element_Text('qtd');
        $qtd   ->setLabel('Qtd')
               //->setOptions(array('belongsTo' => 'qtd'))
               ->addValidator( new Zend_Validate_StringLength(1,24) )
               ->addFilter( new Zend_Filter_StringTrim() )
               ->setAttrib('maxLength', 24)
               ->setRequired();
        
        $id_extra = new Zend_Form_Element_Text('id_hora_extra');
        $id_extra   ->setLabel('ID')
               //->setOptions(array('belongsTo' => 'qtd'))
               ->addValidator( new Zend_Validate_StringLength(1,24) )
               ->addFilter( new Zend_Filter_StringTrim() )
               ->setAttrib('maxLength', 24)
               ->setRequired();
        
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')
                //->setAttrib('tabindex','6')
                ->setAttrib('class','inputBtnSalvar');

        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/digitacao.phtml'))));
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
//            $id_extra,
            $id_cargo,
            $mes,
//            $descr,
            $qtd,
            $centros,
            $submeter,
            $id
        ));
        return $this;
    }
    
    /**
     * Monta o formulario de digitacao de horas extras
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->digitacao
     */
    public function importacao($sAction='importacao'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/eventos/'.$sAction);
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-importacao');
        $this->setAttrib('class', 'cadastro');
        
        // Cria um campo de Text
        $arquivo = new Zend_Form_Element_File('arquivo');
        $arquivo   ->setLabel('Arquivo')
                   ->setAttrib('tabindex','1')
                   ->setDestination(UPLOAD_PATH.'/importacao')
                   ->setRequired();
        $arquivo->setValueDisabled(true);

        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')
                ->setAttrib('tabindex','2')
                ->setAttrib('class','inputBtnSalvar');

        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/importacao.phtml'))));

        $this->addElements(array(
            $arquivo,
            $submeter
        ));
        return $this;
    }
    
    /**
     * Monta o formulario para adicionar aprovador ao centro de custo
     * @param   int $ccusto
     * @return  Commit_Form_Cadastro->aprovadores
     */
    public function aprovadores($ccusto=null,$valor_id=null,$fin_valor=null){
        $Helper_mascaras = new Commit_Controller_Action_Helper_Mascaras();
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/addaprovador');
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-aprovadores');
        $this->setAttrib('class', 'cadastro');
        
        
        //campo tipo selectbox
        $objUsuario  = new Admin_Model_Usuario();
//        Zend_Debug::dump($valor_id);exit;
        $Aprovadores = $objUsuario->fetchAllAprovadores($ccusto,$valor_id,$fin_valor);
        
        $aprovadores = new Zend_Form_Element_Select('aprovador'); 
        $aprovadores->setLabel('Aprovador')
                ->setAttrib('style','width: 90%;');
        foreach($Aprovadores as $aprov):
            if(is_null($fin_valor)){
                $aprovadores->addMultiOption($aprov['ID'], $aprov['NOME']." - R$".$Helper_mascaras->ValorMoeda($aprov['VALOR_INICIAL'],1)." até R$".$Helper_mascaras->ValorMoeda($aprov['VALOR_FINAL'],1));    
            }else{
                $aprovadores->addMultiOption($aprov['ID'], $aprov['NOME']);    
            }
        endforeach;
        

        $id = new Zend_Form_Element_Hidden('ccusto');
        $id_valor = new Zend_Form_Element_Hidden('id_valor');
        $fin_valor = new Zend_Form_Element_Hidden('fin_valor');

        $this->addElements(array(
            $aprovadores,
            $id,
            $id_valor,
            $fin_valor
        ));
        return $this;
    }
    
    
    /**
     * Monta o formulario para adicionar visualizador/criador a finalidade
     * @param   int $finalidade
     * @param   char $action
     * @return  Commit_Form_Cadastro->usuarios
     */
    public function usuarios($finalidade = null, $tipo = 1, $action = 'admin/ci/addvisualizador'){        
        $this->setAction(FORM_PATH .$action);
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-visualizadores');
        $this->setAttrib('class', 'cadastro');        
        
        //campo tipo selectbox
        $objUsuario  = new Admin_Model_Usuario();
        if($tipo === 1){
            $Usuarios = $objUsuario->fetchAllVisualizadores($finalidade);
        }else{
            $Usuarios = $objUsuario->fetchAllCriadores($finalidade);
        }
        
        $usuarios = new Zend_Form_Element_Select('usuario'); 
        $usuarios->setLabel('Usuário')
                ->setAttrib('style','width: 90%;');
        
        foreach($Usuarios as $usu):
            $usuarios->addMultiOption($usu['ID'], $usu['NOME']);    
        endforeach;
        
//        $cadastra_ci = new Zend_Form_Element_MultiCheckbox('cadastra_ci', array(
//            'multiOptions' => array( 1 => 'Permissão para criar CIs da finalidade')
////            ,'style' => 'width:25px;'
//            ,'decorators' => array(
//                        array('ViewHelper'),
//                        array('Label', array('placement' => 'APPEND')),
//                        array('HtmlTag', array('tag' => 'div', 'class' => 'div_permissao_finalidade')),
//                    )
//        ));
//        $cadastra_ci->setSeparator(PHP_EOL);
        
        $finalidade = new Zend_Form_Element_Hidden('finalidade');

        $this->addElements(array(
            $usuarios,
//            $cadastra_ci,
            $finalidade
        ));
        return $this;
    }

    /**
     * Monta o formulario para adicionar despesas a finalidade
     * @param   char $action
     * @return  Commit_Form_Cadastro->usuarios
     */
    public function despesa($action='novadespesa'){
        
        $this->setAction(FORM_PATH .'admin/ajax/'.$action);
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-despesa');
        $this->setAttrib('class', 'cadastro');
        
        
        $tipo = new Zend_Form_Element_Select('tipo'); 
        $tipo->setLabel('Tipo:')
                ->setAttrib('style','width: 350px;')
                ->addMultiOption('1', 'Despesa')
                ->addMultiOption('2', 'Adiantamento');
        
        $descricao = new Zend_Form_Element_Text('descricao');
        $descricao->setLabel('Descrição:')
                ->setAttrib('style','width: 350px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->setRequired();
        
        $ordem = new Zend_Form_Element_Text('ordem');
        $ordem->setLabel('Ordem:')
                ->setAttrib('style','width: 50px;margin-top:20px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->setRequired()
                ->getDecorator('Label')->setOption('style', 'margin-top:20px;');
        
        $ativo = new Zend_Form_Element_Select('ativo'); 
        $ativo->setLabel('Ativo')
                ->setAttrib('style','width: 100px;')
                ->addMultiOption('t', 'Sim')
                ->addMultiOption('f', 'Não');

        $id = new Zend_Form_Element_Hidden('id');
        $finalidade = new Zend_Form_Element_Hidden('finalidade');

        if($action !== 'novadespesa'){
            $descricao->setAttrib('disabled','disabled');
            $tipo->setAttrib('disabled','disabled');
        }
        
        $this->addElements(array(
            $tipo,
            $id,
            $descricao,
            $finalidade,
            $ativo,
            $ordem,
        ));
        return $this;
    }
    
    /**
     * Monta o formulario para cadastro de CI
     * @param   char $Action
     * @return  Commit_Form_Cadastro->aprovadores
     */
    public function ci($Action = "cadastro"){
        // Setar a action do formulário
        $this->setAction(FORM_PATH ."admin/ci/$Action");
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-ci');
        $this->setAttrib('class', 'cadastro');
        
        
        $empresa = new Zend_Form_Element_Text('empresa');
        $empresa->setLabel('EMPRESA:')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 420px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->setRequired();
        $date = new Zend_Date();
        $hoje = $date->get('dd/MM/yyyy HH:mm');
        //problema definido como datepicker
        $data = new Zend_Form_Element_Hidden('data_ci');
        $data   ->setLabel($hoje)
                ->setAttrib('tabindex','2')
                ->setAttrib('style','width: 100px;')
                ->setValue($hoje)
                ->getDecorator('Label')->setOption('style', 'text-align: right;');
        
        //campo tipo selectbox
        $objCcusto  = new Admin_Model_CCusto();
        $Ccustos    = $objCcusto->fetchAllAnaliticos2();
        
        $ccusto_de  = new Zend_Form_Element_Select('ccusto_de'); 
        $ccusto_de  ->setLabel('DE:')
                    ->setAttrib('tabindex','4')
                    ->addMultiOption("", "")
                    ->setRequired()
                    ->setAttrib('style','width: 100%;');
        
        $ccusto_para = new Zend_Form_Element_Select('ccusto_para'); 
        $ccusto_para ->setLabel('PARA:')
                     ->setAttrib('tabindex','5')
                     ->addMultiOption("", "")
                     ->setRequired()
                     ->setAttrib('style','width: 100%;');
        
        foreach($Ccustos as $aprov):
            $ccusto_de->addMultiOption($aprov['CUSTO'], $aprov['NOME_CCUSTO']." - ".$aprov['CD_CCUSTO']);    
            $ccusto_para->addMultiOption($aprov['CUSTO'], $aprov['NOME_CCUSTO']." - ".$aprov['CD_CCUSTO']);    
        endforeach;
        if($Action == "cadastro"){
            $ativo = "s";
        }else{
            $ativo = null;
        }
        //campo tipo selectbox
        $objFinalidade  = new Admin_Model_Finalidade();
        $Finalidade     = $objFinalidade->fetchAllPesquisaPermissao(null,'s');
        $finalidade = new Zend_Form_Element_Select('finalidade'); 
        $finalidade ->setLabel('FINALIDADE:')
                    ->setAttrib('tabindex','3')
                    ->addMultiOption("", "")
                    ->setAttrib('style','width: 100%;')
                    ->setRequired();
        foreach($Finalidade as $fin):
            $finalidade->addMultiOption($fin['ID'], $fin['DESCRICAO']);
        endforeach;
        
        $motivo = new Zend_Form_Element_Textarea('motivo');
        $motivo->setLabel('MOTIVO DA SOLICITAÇÃO DO COMUNICADO INTERNO:')
               ->setAttrib('tabindex','6')
               ->addValidator( new Zend_Validate_StringLength(1,4000) )
               ->setAttrib('maxLength', 4000)
               ->setRequired();

        $valor = new Zend_Form_Element_Text('valor');
        $valor ->setLabel('VALOR TOTAL DA CI EM R$:')
                ->setAttrib('tabindex','7')
                ->setAttrib('style','width: 120px;')
//                ->setRequired()
                ->addFilter( new Zend_Filter_StringTrim() );
        $valor->getDecorator('Label')->setOption('style', 'width: 170px;margin:0px;');
        
        $id = new Zend_Form_Element_Hidden('id');
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','8')->setAttrib('class','inputBtnSalvar');
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/ci.phtml'))));

        $this->addElements(array(
            $empresa,
            $data,
            $ccusto_de,
            $ccusto_para,
            $finalidade,
            $motivo,
            $valor,
            $id,
            $submeter
        ));
        return $this;
    }    
    
    /**
     * Monta o formulario valor x aprovadores
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->qtdeaprovadores
     */
    public function qtdeaprovadores($sAction='insertqtdeaprovadores'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-qtdeaprovadores');
        $this->setAttrib('class', 'cadastro');
        
        // Cria um campo de Text
        $valor_inicial = new Zend_Form_Element_Text('valor_inicial');
        $valor_inicial ->setLabel('Valor Inicial:')
              ->setAttrib('tabindex','1')
              ->setAttrib('style','width: 25%;')
              ->addFilter( new Zend_Filter_StringTrim() )
                  ->setRequired();

        // Cria um campo de Text
        $valor_final = new Zend_Form_Element_Text('valor_final');
        $valor_final ->setLabel('Valor Final:')
              ->setAttrib('tabindex','2')
              ->setAttrib('style','width: 25%;')
              ->addFilter( new Zend_Filter_StringTrim() )
                  ->setRequired();

        // Cria um campo de Text
        $qtde = new Zend_Form_Element_Text('qtdeaprovadores');
        $qtde ->setLabel('Aprovadores:')
              ->setAttrib('tabindex','3')
              ->setAttrib('style','width: 5%;')
              ->addFilter( new Zend_Filter_StringTrim() )
                  ->setRequired();        
        
        
        $emails = new Zend_Form_Element_Textarea('emailcopia');
        $emails ->setLabel('Emails:')
                ->setAttrib('tabindex','3')
                ->setAttrib('style','width: 70%;height: 70px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->addValidator( new Zend_Validate_StringLength(1,250) )
                ->setAttrib('maxLength', 250)
                ->setDescription('* Emails devem ser separados por ponto e virgula');
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnSalvar');

//        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/processos.phtml'))));
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $valor_inicial
            ,$valor_final
            ,$qtde
            ,$emails
//            ,$submeter
            ,$id
        ));
        return $this;
    }
    
    
    /**
     * Monta o formulario valor x aprovadores
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->finalidade
     */
    public function finalidade($sAction='insertfinalidade'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-finalidade');
        $this->setAttrib('class', 'cadastro');
        
//        // Cria um campo de Text
//        $descricao = new Zend_Form_Element_Text('descricao');
//        $descricao ->setLabel('Descrição:')
//              ->setAttrib('tabindex','1')
//              ->setAttrib('style','width: 70%;')
//              ->addFilter( new Zend_Filter_StringTrim() )
//                  ->setRequired();
        
        $descricao = new Zend_Form_Element_Textarea('descricao');
        $descricao ->setLabel('Descrição:')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 70%;height: 40px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->addValidator( new Zend_Validate_StringLength(1,120) )
                ->setAttrib('maxLength', 120);
        
        
        $detalhes = new Zend_Form_Element_Textarea('detalhes');
        $detalhes ->setLabel('Detalhes:')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 70%;height: 100px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->addValidator( new Zend_Validate_StringLength(1,3000) )
                ->setAttrib('maxLength', 3000);
        
        $email_copia = new Zend_Form_Element_Textarea('email_copia');
        $email_copia ->setLabel('E-mail cópia:')
                ->setAttrib('tabindex','2')
                ->setAttrib('style','width: 70%;height: 100px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->addValidator( new Zend_Validate_StringLength(1,1000) )
                ->setAttrib('maxLength', 1000);
        
        $ativo = new Zend_Form_Element_Select('ativo'); 
        $ativo ->setLabel('Ativo:')
                     ->setAttrib('tabindex','3')
                     ->addMultiOption("s", "Sim")
                     ->addMultiOption("n", "Não")
                     ->setAttrib('style','width: 20%;margin-bottom:7px;');        
        
        $preaprovacao = new Zend_Form_Element_Select('pre_aprovacao'); 
        $preaprovacao->setLabel('Pré-aprovação:')
                     ->setAttrib('tabindex','3')
                     ->addMultiOption("n", "Não")
                     ->addMultiOption("s", "Sim")
                     ->setAttrib('style','width: 20%;margin-bottom:7px;');
        
        
        $valorobrigatorio = new Zend_Form_Element_Select('valor_obrigatorio'); 
        $valorobrigatorio->setLabel('Valor Obrigatório:')
                     ->setAttrib('tabindex','3')
                     ->addMultiOption("n", "Não")
                     ->addMultiOption("s", "Sim")
                     ->setAttrib('style','width: 20%;');
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnSalvar');

//        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/processos.phtml'))));
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $descricao
            ,$detalhes
            ,$email_copia
            ,$preaprovacao
            ,$ativo
            ,$valorobrigatorio
//            ,$submeter
            ,$id
        ));
        return $this;
    }
    
    
    /**
     * Monta o formulario finalidade x valores
     *
     * @param   char $sAction
     * @return  Commit_Form_Cadastro->finalidadevalores
     */
    public function finalidadevalores($sAction='insertfinalidadevalores'){
        // Setar a action do formulário
        $this->setAction(FORM_PATH .'admin/ci/'.$sAction);

        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-finalidadevalores');
        $this->setAttrib('class', 'cadastro');
        
        
        //campo tipo selectbox
        $objFinalidade  = new Admin_Model_Finalidade();
        $Finalidade     = $objFinalidade->fetchAllPesquisa(null,'s');
        $finalidade = new Zend_Form_Element_Select('finalidade'); 
        $finalidade ->setLabel('Finalidade:')
                    ->setAttrib('tabindex','1')
                    ->addMultiOption("", "")
                    ->setAttrib('style','width: 65%;margin-bottom:5px;')
                    ->setRequired();
        foreach($Finalidade as $fin):
            $finalidade->addMultiOption($fin['ID'], $fin['DESCRICAO']);
        endforeach;
        
        
        // Cria um campo de Text
        $valor_inicial = new Zend_Form_Element_Text('valor_inicial');
        $valor_inicial ->setLabel('Valor Inicial:')
              ->setAttrib('tabindex','2')
              ->setAttrib('style','width: 25%;')
              ->addFilter( new Zend_Filter_StringTrim() )
                  ->setRequired();

        // Cria um campo de Text
        $valor_final = new Zend_Form_Element_Text('valor_final');
        $valor_final ->setLabel('Valor Final:')
              ->setAttrib('tabindex','3')
              ->setAttrib('style','width: 25%;')
              ->addFilter( new Zend_Filter_StringTrim() )
                  ->setRequired();

        // Cria um campo de Text
        $qtde = new Zend_Form_Element_Text('qtdeaprovadores');
        $qtde ->setLabel('Aprovadores:')
              ->setAttrib('tabindex','4')
              ->setAttrib('style','width: 5%;')
              ->addFilter( new Zend_Filter_StringTrim() )
                  ->setRequired();        
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('submeter');
        $submeter->setLabel('Salvar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnSalvar');

//        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/processos.phtml'))));
        
        // Cria um campo Hidden
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
             $finalidade
            ,$valor_inicial
            ,$valor_final
            ,$qtde
//            ,$submeter
            ,$id
        ));
        return $this;
    }   
    
    
}