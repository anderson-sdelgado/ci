<?php

class Zend_View_Helper_Quantidadedias extends Zend_View_Helper_Abstract{
    public  $verifica;

    public function quantidadedias($periodo){
        $this->verifica = $this->getFatura($periodo);
        return $this;
    }

    private function getFatura($periodo){
        $session = $this->getSession();

        $objFaturaLista     = $objFatura = new Admin_Model_ClienteFatura();
        $retornoFaturaLista = $objFaturaLista->fetchAllRelatorio($session['cliente_id'], $this->getUnidade($session['cliente_id']), $periodo, $periodo);
        return $retornoFaturaLista[0]["CAMPO_01"];
    }

    private function getSession(){
        $namespace = new Zend_Session_Namespace('relatorio');
        return $namespace->campos;
    }

    private function getUnidade($cliente){
        $objUC = new Admin_Model_ClienteUnidade();
        $retUC = $objUC->fetchAllUpadate($cliente);

        if(count($retUC) > 0){
            return $retUC[0]['ID'];
        }
    }
}
?>