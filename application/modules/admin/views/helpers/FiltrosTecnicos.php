<?php

class Zend_View_Helper_Filtrostecnicos extends Zend_View_Helper_Abstract{
    public $resposta;
    public $linha;
    public $array     = array();
    public $clientes  = array();

    public function filtrostecnicos($array=array(), $unidades=null){
        $this->array    = $array;
        $this->unidades  = $unidades;
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
            $tempCampo17    = null;

            $Ultimo12Meses  = $objFaturaLista->fetchAllFiltrosTecnicos($this->unidades ,$data['inicio'], $data['fim']);
            //Zend_Debug::dump($Ultimo12Meses);
            foreach ($Ultimo12Meses as $value) {
                @$tempCampo1    += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo10[($value['CAMPO_12'] + $value['CAMPO_13'])] = ($value['CAMPO_12'] + $value['CAMPO_13']);
                
                if(is_null($tempCampo17)){
                    $tempCampo17 = $value['CAMPO_08'];
                }else{
                    if($value['CAMPO_08'] > $tempCampo14){
                        $tempCampo17 = $value['CAMPO_08'];
                    }
                }
                
            }

            $this->linha[$key]['data']   = $data['fim'];
            $this->linha[$key]['campo1'] = $tempCampo1;
            $this->linha[$key]['campo2'] = (($tempCampo2[1]*2)+($tempCampo2[2])+($tempCampo2[3]));
            $this->linha[$key]['campo4'] = ($this->linha[$key]['campo1'] + $this->linha[$key]['campo2']);
            $this->linha[$key]['campo5'] = (($this->linha[$key]['campo1']/$this->linha[$key]['campo4'])*100);
            $this->linha[$key]['campo6'] = $tempCampo6;
            $this->linha[$key]['campo7'] = $tempCampo7;
            $this->linha[$key]['campo8'] = ($tempCampo6+$tempCampo7);
            $this->linha[$key]['campo9'] = (($tempCampo6/$this->linha[$key]['campo8'])*100);

            $this->linha[$key]['campo10'] = $this->SomaOsQuatrosMaiores($tempCampo10);
            $this->linha[$key]['campo11'] = $this->SomaOsQuatrosMenores($tempCampo10);
            $this->linha[$key]['campo12'] = (($this->linha[$key]['campo11']/$this->linha[$key]['campo10'])*100);
            $this->linha[$key]['campo13'] = ($this->linha[$key]['campo8']/$this->linha[$key]['campo1']);
            
            $this->linha[$key]['campo14'] = $value['CAMPO_04'];
            $this->linha[$key]['campo15'] = $value['CAMPO_05'];
            $this->linha[$key]['campo16'] = $value['CAMPO_23'];
            $this->linha[$key]['campo17'] = $tempCampo17;
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
       
    private function SomaOsQuatrosMenores($array){
        asort($array);
        $xx = 1;
        foreach ($array as $key => $value){
            if($xx <= 4){
                @$retorno += $value;
            }
            $xx++;
        }
        return @$retorno;
    }
}
?>