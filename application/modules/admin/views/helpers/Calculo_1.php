<?php

class Zend_View_Helper_Calculo extends Zend_View_Helper_Abstract{
    public $resposta;
    public $linha1;
    public $linha2;
    public $linha3;
    public $linha4;
    public $array = array();

    public function calculo($array=array()){
        $this->array = $array;
        $this->start();

        $this->resposta = array(
                                $this->linha1,
                                $this->linha2,
                                $this->linha3,
                                $this->linha4);
        return $this;
    }

    private function start(){
        $this->reordenaArray();

        $this->linhaUm();
        $this->linhaDois();
        $this->linhaTres();
        $this->linhaQuatro();
    }

    private function reordenaArray(){
        krsort($this->array);
    }
    
    
    private function linhaUm(){
        $xx = 1;
        foreach ($this->array as $key => $value) {
            if($xx > 3 and $xx <= 15){
                @$tempCampo1    += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo10[($value['CAMPO_12'] + $value['CAMPO_13'])] = ($value['CAMPO_12'] + $value['CAMPO_13']);
            }
            $xx++;
        }
        $this->linha1['campo1'] = $tempCampo1;
        $this->linha1['campo2'] = (($tempCampo2[1]*2)+($tempCampo2[2])+($tempCampo2[3]));
        $this->linha1['campo4'] = ($this->linha1['campo1'] + $this->linha1['campo2']);
        $this->linha1['campo5'] = (($this->linha1['campo1']/$this->linha1['campo4'])*100);
        $this->linha1['campo6'] = $tempCampo6;
        $this->linha1['campo7'] = $tempCampo7;
        $this->linha1['campo8'] = ($tempCampo6+$tempCampo7);
        $this->linha1['campo9'] = (($tempCampo6/$tempCampo7)*100);

        $this->linha1['campo10'] = $this->SomaOsQuatrosMaiores($tempCampo10);
        $this->linha1['campo11'] = $this->SomaOsQuatrosMenores($tempCampo10);
        $this->linha1['campo12'] = (($this->linha1['campo11']/$this->linha1['campo10'])*100);
        $this->linha1['campo13'] = ($this->linha1['campo8']/$this->linha1['campo1']);
    }

    private function linhaDois(){
        $xx = 1;
        foreach ($this->array as $key => $value) {
            if($xx > 2 and $xx <= 14){
                @$tempCampo1 += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo10[($value['CAMPO_12'] + $value['CAMPO_13'])]    = ($value['CAMPO_12'] + $value['CAMPO_13']);
            }
            $xx++;
        }
        $this->linha2['campo1'] = $tempCampo1;
        $this->linha2['campo2'] = (($tempCampo2[1]*2)+($tempCampo2[2])+($tempCampo2[3]));
        $this->linha2['campo4'] = ($this->linha2['campo1'] + $this->linha2['campo2']);
        $this->linha2['campo5'] = (($this->linha2['campo1']/$this->linha2['campo4'])*100);
        $this->linha2['campo6'] = $tempCampo6;
        $this->linha2['campo7'] = $tempCampo7;
        $this->linha2['campo8'] = ($tempCampo6+$tempCampo7);
        $this->linha2['campo9'] = (($tempCampo6/$tempCampo7)*100);

        $this->linha2['campo10'] = $this->SomaOsQuatrosMaiores($tempCampo10);
        $this->linha2['campo11'] = $this->SomaOsQuatrosMenores($tempCampo10);
        $this->linha2['campo12'] = (($this->linha2['campo11']/$this->linha2['campo10'])*100);
        $this->linha2['campo13'] = ($this->linha2['campo8']/$this->linha2['campo1']);
    }

    private function linhaTres(){
        $xx = 1;
        foreach ($this->array as $key => $value) {
            if($xx > 1 and $xx <= 13){
                @$tempCampo1 += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo10[($value['CAMPO_12'] + $value['CAMPO_13'])]    = ($value['CAMPO_12'] + $value['CAMPO_13']);
            }
            $xx++;
        }
        $this->linha3['campo1'] = $tempCampo1;
        $this->linha3['campo2'] = (($tempCampo2[1]*2)+($tempCampo2[2])+($tempCampo2[3]));
        $this->linha3['campo4'] = ($this->linha3['campo1'] + $this->linha3['campo2']);
        $this->linha3['campo5'] = (($this->linha3['campo1']/$this->linha3['campo4'])*100);
        $this->linha3['campo6'] = $tempCampo6;
        $this->linha3['campo7'] = $tempCampo7;
        $this->linha3['campo8'] = ($tempCampo6+$tempCampo7);
        $this->linha3['campo9'] = (($tempCampo6/$tempCampo7)*100);

        $this->linha3['campo10'] = $this->SomaOsQuatrosMaiores($tempCampo10);
        $this->linha3['campo11'] = $this->SomaOsQuatrosMenores($tempCampo10);
        $this->linha3['campo12'] = (($this->linha3['campo11']/$this->linha3['campo10'])*100);
        $this->linha3['campo13'] = ($this->linha3['campo8']/$this->linha3['campo1']);
    }

    private function linhaQuatro(){
        $xx = 1;
        foreach ($this->array as $key => $value) {
            if($xx <= 12){
                @$tempCampo1 += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo10[($value['CAMPO_12'] + $value['CAMPO_13'])]    = ($value['CAMPO_12'] + $value['CAMPO_13']);
            }
            $xx++;
        }
        $this->linha4['campo1'] = $tempCampo1;
        $this->linha4['campo2'] = (($tempCampo2[1]*2)+($tempCampo2[2])+($tempCampo2[3]));
        $this->linha4['campo4'] = ($this->linha4['campo1'] + $this->linha4['campo2']);
        $this->linha4['campo5'] = (($this->linha4['campo1']/$this->linha4['campo4'])*100);
        $this->linha4['campo6'] = $tempCampo6;
        $this->linha4['campo7'] = $tempCampo7;
        $this->linha4['campo8'] = ($tempCampo6+$tempCampo7);
        $this->linha4['campo9'] = (($tempCampo6/$tempCampo7)*100);

        $this->linha4['campo10'] = $this->SomaOsQuatrosMaiores($tempCampo10);
        $this->linha4['campo11'] = $this->SomaOsQuatrosMenores($tempCampo10);
        $this->linha4['campo12'] = (($this->linha4['campo11']/$this->linha4['campo10'])*100);
        $this->linha4['campo13'] = ($this->linha4['campo8']/$this->linha4['campo1']);
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
    
    private function SomaOsQuatrosMenores($array){
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
    
}
?>