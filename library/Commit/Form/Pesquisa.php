<?php

/**
 * Formulario manutencção nos cadastros do sistema
 * @filesource		/library/Commit/Form/Pesquisa.php
 * @author 		Pedro Henrique Gonzales Lobo
 * @copyright 		Commit Consulting
 * @category		Form
 * @package		Commit_Form_Pesquisa
 * @subpackage		Zend_Form
 * @version		1.0
 * @since		05/10/2012
 */
class Commit_Form_Pesquisa extends Zend_Form {
    
    /**
     * Monta o formulario para pesquisa de CI
     * @param   int $ccusto
     * @return  Commit_Form_Pesquisa->ci
     */
    public function ci($Action = "consultar"){
        // Setar a action do formulário
        $this->setAction(FORM_PATH ."admin/ci/$Action");
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-pesquisa');
        $this->setAttrib('class', 'cadastro');
        
        
        $de_ci = new Zend_Form_Element_Text('de_ci');
        $de_ci   ->setLabel('De N°:')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 80%;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->getDecorator('Label')->setOption('style', 'width: 70px;');
        
        $ate_ci = new Zend_Form_Element_Text('ate_ci');
        $ate_ci   ->setLabel('Até N°:')
                ->setAttrib('tabindex','2')
                ->setAttrib('style','width: 80%;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->getDecorator('Label')->setOption('style', 'width: 70px;');
        
        $data = new Zend_Form_Element_Text('data');
        $data   ->setLabel('Data:')
                ->setAttrib('tabindex','3')
                ->setAttrib('style','width: 80%;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->getDecorator('Label')->setOption('style', 'width: 80px;');
        
        //campo tipo selectbox
        $objCcusto  = new Admin_Model_CCusto();
        $Ccustos    = $objCcusto->fetchAllAnaliticos();
        
        $ccusto_de  = new Zend_Form_Element_Select('ccusto_de'); 
        $ccusto_de  ->setLabel('De:')
                    ->setAttrib('tabindex','4')
                    ->setAttrib('style','width: 250px;margin-right:5px;')
                    ->addMultiOption("", "Selecione...");
        
        $ccusto_para = new Zend_Form_Element_Select('ccusto_para'); 
        $ccusto_para ->setLabel('Para:')
                     ->setAttrib('tabindex','5')
                     ->setAttrib('style','width: 250px;margin-right:5px;')
                     ->addMultiOption("", "Selecione...");
        
        foreach($Ccustos as $aprov):
            $ccusto_de->addMultiOption($aprov['CUSTO'], $aprov['NOME_CCUSTO']);    
            $ccusto_para->addMultiOption($aprov['CUSTO'], $aprov['NOME_CCUSTO']);    
        endforeach;
        
        
        //campo tipo selectbox
        $objStatus  = new Admin_Model_Cistatus();
        $Status     = $objStatus->fetchAll();
        
        $status = new Zend_Form_Element_Select('status'); 
        $status ->setLabel('Status:')
                     ->setAttrib('tabindex','6')
                     ->setAttrib('style','width: 100px;')
                     ->addMultiOption("", "Selecione...");
        
        foreach($Status as $value):
            $status->addMultiOption($value['ID'], $value['DESCRICAO']);
        endforeach;
        
        
        //campo tipo selectbox
        $objFinalidade  = new Admin_Model_Finalidade();
        $Finalidade     = $objFinalidade->fetchAllPesquisa();
        
        $finalidade = new Zend_Form_Element_Select('finalidade'); 
        $finalidade ->setLabel('Finalidade:')
                     ->setAttrib('tabindex','7')
                     ->setAttrib('style','width: 150px;;margin-right:5px;')
                     ->addMultiOption("", "Selecione...");
        
        foreach($Finalidade as $value):
            $finalidade->addMultiOption($value['ID'], $value['DESCRICAO']);
        endforeach;
        
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('btnPesquisar');
        $submeter->setLabel('Pesquisar')->setAttrib('tabindex','6')->setAttrib('class','inputBtnPesquisar');
        
        $gerar_xls = new Zend_Form_Element_Hidden('gerar_xls');
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/pesquisaci.phtml'))));

        $this->addElements(array(
            $de_ci,
            $ate_ci,
            $data,
            $ccusto_de,
            $ccusto_para,
            $status,
            $finalidade,
            $gerar_xls,
            $submeter
        ));
        return $this;
    }
    /**
     * Monta o formulario para pesquisa do log de acesso
     * @param   int $ccusto
     * @return  Commit_Form_Pesquisa->logacesso
     */
    public function logacesso(){
        // Setar a action do formulário
        $this->setAction(FORM_PATH ."admin/relatorios/logacesso");
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-pesquisa');
        $this->setAttrib('class', 'cadastro');
        
        
        $data = new Zend_Form_Element_Text('data');
        $data   ->setLabel('Data:')
                ->setAttrib('tabindex','2')
                ->setAttrib('style','width: 40%;')
                ->addFilter( new Zend_Filter_StringTrim() );
        
        $login = new Zend_Form_Element_Text('login');
        $login   ->setLabel('Login:')
                ->setAttrib('tabindex','3')
                ->setAttrib('style','width: 50%;')
                ->addFilter( new Zend_Filter_StringTrim() );
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('btnPesquisar');
        $submeter->setLabel('Pesquisar')->setAttrib('tabindex','4')->setAttrib('class','inputBtnPesquisar');
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/pesquisalog.phtml'))));

        $this->addElements(array(
            $data,
            $login,
            $submeter
        ));
        return $this;
    }
    
    /**
     * Monta o formulario para pesquisa de CI
     * @param   int $ccusto
     * @return  Commit_Form_Pesquisa->ci
     */
    public function informativositens($Action = ""){
        // Setar a action do formulário
        $this->setAction(FORM_PATH ."admin/informativos/itens/$Action");
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-pesquisa');
        $this->setAttrib('class', 'cadastro');
        
        
        $descricao = new Zend_Form_Element_Text('descricao');
        $descricao->setLabel('Descrição:')
                ->setAttrib('tabindex','2')
                ->setAttrib('style','width: 250px;')
                ->addFilter( new Zend_Filter_StringTrim() );
        
        //campo tipo selectbox
        $objInformativos = new Admin_Model_Informativos();
        $Informativos    = $objInformativos->getAll();
        
        $informativo = new Zend_Form_Element_Select('informativo'); 
        $informativo ->setLabel('Informativos:')
                     ->setAttrib('tabindex','1')
                     ->setAttrib('style','width: 250px;')
                     ->addMultiOption("", "Selecione...");
        
        foreach($Informativos as $value):
            $informativo->addMultiOption($value['ID'], $value['DESCRICAO']);
        endforeach;
        
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('btnPesquisar');
        $submeter->setLabel('Pesquisar')->setAttrib('tabindex','6')->setAttrib('class','inputBtnPesquisar');
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/pesquisaiformativos.phtml'))));

        $this->addElements(array(
            $informativo,
            $descricao,
            $submeter
        ));
        return $this;
    }
    
    /**
     * Monta o formulario para pesquisa de envios de CI
     * @return  Commit_Form_Pesquisa->envioci
     */
    public function envioci($Action = ""){
        // Setar a action do formulário
        $this->setAction(FORM_PATH ."admin/relatorios/envioci/$Action");
        
        // Setar o método (POST | GET)
        $this->setMethod('POST');
        $this->setAttrib('id', 'form-pesquisa');
        $this->setAttrib('class', 'cadastro');
        
        
        $assunto = new Zend_Form_Element_Text('assunto');
        $assunto->setLabel('Assunto:')
                ->setAttrib('tabindex','1')
                ->setAttrib('style','width: 250px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->getDecorator('Label')->setOption('style', 'width: 70px;');
        
        $ultimos = new Zend_Form_Element_Text('ultimos');
        $ultimos->setLabel('Ultimos:')
                ->setAttrib('tabindex','2')
                ->setAttrib('style','width: 60px;')
                ->addFilter( new Zend_Filter_StringTrim() )
                ->getDecorator('Label')->setOption('style', 'width: 70px;');
        
        //campo tipo selectbox
        $enviado = new Zend_Form_Element_Select('enviado'); 
        $enviado ->setLabel('Enviado:')
                     ->setAttrib('tabindex','3')
                     ->setAttrib('style','width: 100px;')
                     ->addMultiOption("", "Sim/Não")
                     ->addMultiOption("S", "Sim")
                     ->addMultiOption("N", "Não");
        
        // Cria um botao para submeter o formulario
        $submeter = new Zend_Form_Element_Submit('btnPesquisar');
        $submeter->setLabel('Pesquisar')->setAttrib('tabindex','3')->setAttrib('class','inputBtnPesquisar');
        
        $this->setDecorators(array(array('ViewScript', array('viewScript' => 'decorators/pesquisaenvioci.phtml'))));

        $this->addElements(array(
            $assunto,
            $ultimos,
            $enviado,
            $submeter
        ));
        return $this;
    }
    
    
}