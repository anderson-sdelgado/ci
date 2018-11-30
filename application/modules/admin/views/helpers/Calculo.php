<?php

class Zend_View_Helper_Calculo extends Zend_View_Helper_Abstract{
    public $resposta;
    public $linha;
    public $array = array();

    public function calculo($array=array()){
        $this->array = $array;
        $this->start();
        
        $this->resposta = $this->linha;
        return $this;
    }

    private function start(){
        $this->reordenaArray();
        $this->linha();
    }

    private function reordenaArray(){
        krsort($this->array);
    }
    
    private function linha(){
        $objFaturaLista = new Admin_Model_ClienteFatura();
        $mascara        = new Commit_Controller_Action_Helper_Mascaras();
        
        foreach ($this->array as $key => $value) {
            $cliente        = $value['EMPRESA_CLIENTE'];
            $unidade        = $value['EMPRESA_CLIENTE_UNIDADE'];
            
            $data_inicio    = new Zend_Date($value['CAMPO_01']);
            $data_fim       = new Zend_Date($value['CAMPO_01']);

            $data['inicio'] = $data_inicio->subMonth(11)->get('MM/yyyy');
            $data['fim']    = $data_fim->get('MM/yyyy');
            $data['periodo']= $value['CAMPO_01'];

            $tempCampo1     = null;
            $tempCampo2     = null;
            $tempCampo6     = null;
            $tempCampo7     = null;
            $tempCampo10    = null;

            $Ultimo12Meses  = $objFaturaLista->fetchAllRelatorio($cliente, $unidade, $data['inicio'], $data['fim']);
            //Zend_Debug::dump($Ultimo12Meses);
            foreach ($Ultimo12Meses as $key => $value) {
                @$tempCampo1    += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo10[($value['CAMPO_12'] + $value['CAMPO_13'])] = ($value['CAMPO_12'] + $value['CAMPO_13']);
            }

            $this->linha[$key]['data']   = $data['fim'];
            $this->linha[$key]['campo1'] = $tempCampo1;
            $this->linha[$key]['campo2'] = (($tempCampo2[1]*2)+($tempCampo2[2])+($tempCampo2[3]));
            $this->linha[$key]['campo4'] = ($this->linha[$key]['campo1'] + $this->linha[$key]['campo2']);
            $this->linha[$key]['campo5'] = (($this->linha[$key]['campo1']/$this->linha[$key]['campo4'])*100);
            $this->linha[$key]['campo6'] = $tempCampo6;
            $this->linha[$key]['campo7'] = $tempCampo7;
            $this->linha[$key]['campo8'] = ($tempCampo6+$tempCampo7);
            $this->linha[$key]['campo9'] = (($tempCampo6/$tempCampo7)*100);

            $this->linha[$key]['campo10'] = $this->SomaOsQuatrosMaiores($tempCampo10);
            $this->linha[$key]['campo11'] = $this->SomaOsQuatrosMenores($tempCampo10);
            $this->linha[$key]['campo12'] = (($this->linha[$key]['campo11']/$this->linha[$key]['campo10'])*100);
            $this->linha[$key]['campo13'] = ($this->linha[$key]['campo8']/$this->linha[$key]['campo1']);
        }
    }

    private function SomaOsQuatrosMaiores($array){
        arsort($array);
        $xx = 1;
        foreach ($array as $key => $value) {
            if($xx <= 4){
                @$retorno += $value;
            }
            $xx++;
        }
        return $retorno;
    }
    
    private function SSomaOsQuatrosMenores($array){
        arsort($array);      
        $xx = 1;
        foreach ($array as $key => $value) {
            if($xx > 8){
                @$retorno += $value;
            }
            $xx++;
        }
        return @$retorno;
    }
    
    private function SomaOsQuatrosMenores($array){
        asort($array);
        //Zend_Debug::dump($array);
        $xx = 1;
        foreach ($array as $key => $value){
            if($xx <= 4){
                @$retorno += $value;
                $a[] = $value;
            }
            $xx++;
        }
        //Zend_Debug::dump($a);
        return @$retorno;
    }

    private function getTarifa($cliente, $unidade, $periodo, $tarifa_grupo=null, $tarifa_subgrupo=null, $posicao=0, $campo=null){
        
        //$TabelaTarifa   = $this->getTarifa($cliente, $unidade, $data['periodo'], 1, 1, 0, 'VALOR_05');
        //Zend_Debug::dump($TabelaTarifa);exit;
        
        $unidade_cliente = new Admin_Model_ClienteUnidade();
        $unidade_cliente = $unidade_cliente->fetchAllPesquisa($unidade, $cliente, null, null, null, null, null, null, null, false);

        $tarifa  = new Admin_Model_ConcessionariaTarifa();
        $retorno = $tarifa->fetchAllFatura($unidade_cliente[0]['EMPRESA_CONCESSIONARIA'], $periodo);

        $tarifa_valores  = new Admin_Model_ConcessionariaTarifaValor();
        $tarifa_valores  = $tarifa_valores->fetchAllFatura($retorno[0]['ID'], $tarifa_grupo, $tarifa_subgrupo);
        
        if(strlen($campo) > 0 ){
            return $tarifa_valores[$posicao][$campo];
        }else{
            return $tarifa_valores;
        }
    }
    
}
?>