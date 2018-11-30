<?php

class Zend_View_Helper_Financeiro extends Zend_View_Helper_Abstract{
    public $resposta;
    public $linha;
    public $array = array();
    
    public function financeiro($array=array()){
        $this->array = $array;
        $this->start();
        
        $this->resposta = $this->linha;
        return $this;
    }

    private function start(){
        $this->linha();
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
            $tempCampo3     = null;
            $tempCampo4     = null;
            $tempCampo5     = null;
            $tempCampo6     = null;
            $tempCampo7     = null;
            $tempCampo8     = null;
            $tempCampo9     = null;
            $tempCampo10    = null;

            $Ultimo12Meses  = $objFaturaLista->fetchAllRelatorio($cliente, $unidade, $data['inicio'], $data['fim']);
            //Zend_Debug::dump($Ultimo12Meses);exit;
            foreach ($Ultimo12Meses as $value) {
                $tempCampo1    += $value['CAMPO_25'];
                $tempCampo2    += $value['CAMPO_26'];
                $tempCampo4    += $value['CAMPO_27'];
                $tempCampo5    += $value['CAMPO_28'];                
                $tempCampo6    += $value['CAMPO_29'];
                
                $tempCampo7[1] += $value['CAMPO_25']+$value['CAMPO_26']+$value['CAMPO_27']+$value['CAMPO_28']+$value['CAMPO_29'];
                $tempCampo7[2] += $value['CAMPO_17']+$value['CAMPO_18'];
                $tempCampo7[3] += $value['CAMPO_19'];

                $tempCampo8 += $value['CAMPO_30'];
                
                @$tempCampo9[1] += (($value['CAMPO_20']+$value['CAMPO_19'])-($value['CAMPO_17']+$value['CAMPO_18']));
                
                @$tempCampo9[2] += (@$value['CAMPO_12']+@$value['CAMPO_13']);
                
                @$tempCampo10    = @$value['CARGA'];
                @$tempCampo11[@$value['CAMPO_08']]  = @$value['CAMPO_08'];
                @$tempCampo12   += $value['CAMPO_20'];
            }
            
            $this->linha[$key]['data']   = $data['fim'];
            $this->linha[$key]['campo1'] = $tempCampo1;
            $this->linha[$key]['campo2'] = $tempCampo2;
            $this->linha[$key]['campo3'] = $this->linha[$key]['campo1'] + $this->linha[$key]['campo2'];
            $this->linha[$key]['campo4'] = $tempCampo4;
            $this->linha[$key]['campo5'] = $tempCampo5;
            $this->linha[$key]['campo6'] = $tempCampo6;
            $this->linha[$key]['campo7'] = $this->CalculaCampo07($tempCampo7);
            $this->linha[$key]['campo8'] = @$tempCampo12;

            $this->linha[$key]['campo9'] = $this->CalculaCampo09(@$tempCampo9);
            $this->linha[$key]['campo10']= ($tempCampo8 / count($Ultimo12Meses));
            $this->linha[$key]['campo11']= $this->CalculaCampo11($this->linha[$key]['campo9'], $this->linha[$key]['campo10']);

            $this->linha[$key]['campo12']= $this->CalculaCampo12($this->linha[$key]['campo1'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo13']= $this->CalculaCampo13($this->linha[$key]['campo2'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo14']= $this->CalculaCampo14($this->linha[$key]['campo3'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo15']= $this->CalculaCampo15($this->linha[$key]['campo4'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo16']= $this->CalculaCampo16($this->linha[$key]['campo5'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo17']= $this->CalculaCampo17($this->linha[$key]['campo6'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo18']= $value['CAMPO_05'];
            $this->linha[$key]['campo19']= $this->CalculaCampo18($tempCampo10);
            $this->linha[$key]['campo20']= $this->CalculaCampo19($tempCampo11);
            $this->linha[$key]['campo21']= $this->CalculaCampo20($tempCampo11);

            @$tempCampo12 = null;
        }
    }
   
    private function CalculaCampo07($valor){
        return (($valor[1]+$valor[2])-$valor[3]);
    }
   
    private function CalculaCampo09($valor){
        return ($valor[1] / $valor[2]);
        // =SOMA(Z13:Z24)/SOMA(N13:N24)
    }

    private function CalculaCampo11($valor1, $valor2){
        return @(($valor2/$valor1)*100);
        //=MÉDIA(AJ30:AJ41)/AI41*100
    }
   
    private function CalculaCampo12( $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AA30/AG30*100
    }
   
    private function CalculaCampo13($valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AB26/AG26*100
    }
   
    private function CalculaCampo14($valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AC28/AG28*100
    }
   
    private function CalculaCampo15($valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AC28/AG28*100
    }
   
    private function CalculaCampo16($valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AC28/AG28*100
    }
   
    private function CalculaCampo17($valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AF29/AG29*100
    }

    private function CalculaCampo18($valor=null){
        return $valor;
    }

    private function CalculaCampo19($array=array()){
        krsort($array);
        foreach ($array as $key => $value)
            $array_novo[] = $value;

        for ($index = 0; $index < 3; $index++)
            $resposta += $array_novo[$index];
        
        return ($resposta/3);
    }

    private function CalculaCampo20($array=array()){
        sort($array);

        for ($index = 0; $index < 3; $index++)
            $resposta += $array[$index];
        
        return ($resposta/3);
    }
    
}
?>