<?php

class Zend_View_Helper_FiltrosFinanceiro extends Zend_View_Helper_Abstract{
    public $resposta;
    public $linha;
    public $array = array();
    
    public function filtrosfinanceiro($array=array()){
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
        //Zend_Debug::dump($this->array);
   
        
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
            $tempCampo8     = null;
            $tempCampo9     = null;
            $tempCampo10    = null;

            $Ultimo12Meses  = $objFaturaLista->fetchAllRelatorio($cliente, $unidade, $data['inicio'], $data['fim']);
            //Zend_Debug::dump($Ultimo12Meses);exit;
            foreach ($Ultimo12Meses as $value) {
                @$tempCampo1    += $value['CAMPO_08'];
                @$tempCampo2[1] += $value['CAMPO_09'];
                @$tempCampo2[2] += $value['CAMPO_10'];
                @$tempCampo2[3] += $value['CAMPO_11'];
                @$tempCampo6    += $value['CAMPO_12'];
                @$tempCampo7    += $value['CAMPO_13'];
                @$tempCampo8[1] += $value['CAMPO_14']+$value['CAMPO_15'];
                @$tempCampo8[2] += $value['CAMPO_16'];
                //@$tempCampo9[1] += $value['CAMPO_20'];
                @$tempCampo9[1] += (($value['CAMPO_20']-($value['CAMPO_17']+$value['CAMPO_18']))+$value['CAMPO_19']);
                @$tempCampo9[2] += (@$value['CAMPO_12']+@$value['CAMPO_13']);
            }
            
            //configuracao para buscar a tarifa correta
            $tarifa = $this->getTarifa($cliente, $unidade, $data['periodo']);
            $config = array('tarifa' => $tarifa);
            $this->linha[$key]['data']   = $data['fim'];
            
            $this->linha[$key]['campo1'] = $this->CalculaCampo01($config, $tempCampo1);
            $this->linha[$key]['campo2'] = $this->CalculaCampo02($config, $tempCampo2);
            $this->linha[$key]['campo3'] = $this->linha[$key]['campo1'] + $this->linha[$key]['campo2'];
            $this->linha[$key]['campo4'] = $this->CalculaCampo04($config, @$tempCampo6);
            $this->linha[$key]['campo5'] = $this->CalculaCampo05($config, @$tempCampo7);
            $this->linha[$key]['campo6'] = $this->CalculaCampo06($config, @$tempCampo8);
            $this->linha[$key]['campo7'] = $this->linha[$key]['campo3']+$this->linha[$key]['campo4']+$this->linha[$key]['campo5']+$this->linha[$key]['campo6'];
            $this->linha[$key]['campo8'] = $value['CAMPO_20'];
            $this->linha[$key]['campo9'] = $this->CalculaCampo09($config, @$tempCampo9);
            $this->linha[$key]['campo10']= $this->CalculaCampo10($config);
            $this->linha[$key]['campo11']= $this->CalculaCampo11($config, $this->linha[$key]['campo9'], $this->linha[$key]['campo10']);
            $this->linha[$key]['campo12']= $this->CalculaCampo12($config, $this->linha[$key]['campo1'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo13']= $this->CalculaCampo13($config, $this->linha[$key]['campo2'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo14']= $this->CalculaCampo14($config, $this->linha[$key]['campo3'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo15']= $this->CalculaCampo15($config, $this->linha[$key]['campo4'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo16']= $this->CalculaCampo16($config, $this->linha[$key]['campo5'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo17']= $this->CalculaCampo17($config, $this->linha[$key]['campo6'], $this->linha[$key]['campo7']);
            $this->linha[$key]['campo18']= $value['CAMPO_05'];
            
        }
    }

    private function CalculaCampo01($config, $valor){
        $tarifa = $config['tarifa'];
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        switch ($grupo) {
            case 5:
                return ($valor * $tarifa[0]['VALOR_05']);
                break;
        }
    }

    private function CalculaCampo02($config, $valor){
        $tarifa = $config['tarifa'];
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        switch ($grupo) {
            case 5:
                return ($valor[1] * $tarifa[5]['VALOR_05'])+($valor[2] * $tarifa[0]['VALOR_10'])+($valor[3] * $tarifa[0]['VALOR_05']);exit;
                break;
        }
        //=SOMA(I11:I22)* Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MATRIZ TARIFAS DISTRIBUIDORAS.xls]Plan1'!$I$25
        //+SOMA(J11:J22)* Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MATRIZ TARIFAS DISTRIBUIDORAS.xls]Plan1'!$N$20
        //+SOMA(K11:K22)* Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MATRIZ TARIFAS DISTRIBUIDORAS.xls]Plan1'!$I$20        
    }
    
    private function CalculaCampo04($config, $valor){
        $tarifa = $config['tarifa'];
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        switch ($grupo) {
            case 5:
                return ($valor)*((($tarifa[1]['VALOR_05']*7)+($tarifa[3]['VALOR_05']*5))/12); 
                break;
        }

        // =SOMA(L13:L24)
        // *(Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MTD.xls]Plan1'!$I$21*7
        // +(Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MTD.xls]Plan1'!$I$23*5))
        // /12
    }
    
    private function CalculaCampo05($config, $valor){
        $tarifa = $config['tarifa'];
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        switch ($grupo) {
            case 5:
                return ($valor)*((($tarifa[2]['VALOR_05']*7)+($tarifa[4]['VALOR_05']*5))/12); 
                break;
        }
        // =SOMA(M13:M24)
        // *(
        //   (Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MTD.xls]Plan1'!$I$22*7)
        //  +(Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MTD.xls]Plan1'!$I$24*5)
        //  )/12
    }
   
    private function CalculaCampo06($config, $valor){
        $tarifa = $config['tarifa'];
        
        $I10 = $this->getTarifaDireta($tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA'], 1, 1, 0, 'VALOR_05');
        
        //Zend_Debug::dump($tarifaDireta);exit;
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        switch ($grupo) {
            case 5:
                //return ($valor)*((($tarifa[2]['VALOR_05']*7)+($tarifa[4]['VALOR_05']*5))/12); 
                return (($valor[1]*$I10)+($valor[2]*$tarifa[0]['VALOR_05']));
                break;
        }
        
        // =SOMA(P13:Q24)
        // * Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MATRIZ TARIFAS DISTRIBUIDORAS.xls]Plan1'!$I$10
        // + SOMA(R13:R24)
        // * Servidor\meus documentos\NOVA JM\SOFTWARE DE GESTÃO\[MATRIZ TARIFAS DISTRIBUIDORAS.xls]Plan1'!$I$20
    }
   
    private function CalculaCampo09($config, $valor){
        $tarifa = $config['tarifa'];
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        switch ($grupo) {
            case 5:
                return ($valor[1] / $valor[2]);
                //return ($valor[1]);
                break;
        }
        
        // =SOMA(Z13:Z24)/SOMA(N13:N24)
    }
   
    private function CalculaCampo10($config){
        $tarifa = $config['tarifa'];
        $grupo  = $tarifa[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO'];
        //5-THS VERDE
        $demanda = $this->array[0]['DEMANDA'];
        switch ($grupo) {
            case 5:
                $I20 = $tarifa[0]['VALOR_05'];
                $I21 = $tarifa[1]['VALOR_05'];
                $I22 = $tarifa[2]['VALOR_05'];
                $I23 = $tarifa[3]['VALOR_05'];
                $I24 = $tarifa[4]['VALOR_05'];
                $I25 = $tarifa[5]['VALOR_05'];

                return ($demanda * $I20 + 2400*($I21*7/12 + $I23*5/12 )+26200*( $I22*7/12+ $I24*5/12))/(2400+26200);
                break;
        }
        //(155* $I$20 + 2400*($I$21*7/12 + $I$23*5/12 )+26200*( $I$22*7/12+ $I$24*5/12))/(2400+26200);
    }
   
    private function CalculaCampo11($config, $valor1, $valor2){
        return @(($valor2/$valor1)*100);
        //=MÉDIA(AJ30:AJ41)/AI41*100
    }
   
    private function CalculaCampo12($config, $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AA30/AG30*100
    }
   
    private function CalculaCampo13($config, $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AB26/AG26*100
    }
   
    private function CalculaCampo14($config, $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AC28/AG28*100
    }
   
    private function CalculaCampo15($config, $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AC28/AG28*100
    }
   
    private function CalculaCampo16($config, $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AC28/AG28*100
    }
   
    private function CalculaCampo17($config, $valor1, $valor2){
        return @(($valor1/$valor2)*100);
        //=AF29/AG29*100
    }
    
    private function getTarifa($cliente, $unidade, $periodo){
        
        //$TabelaTarifa   = $this->getTarifa($cliente, $unidade, $data['periodo'], 1, 1, 0, 'VALOR_05');
        //Zend_Debug::dump($TabelaTarifa);exit;
        
        $unidade_cliente = new Admin_Model_ClienteUnidade();
        $unidade_cliente = $unidade_cliente->fetchAllPesquisa($unidade, $cliente, null, null, null, null, null, null, null, false);
        
        $tarifa  = new Admin_Model_ConcessionariaTarifa();
        $retorno = $tarifa->fetchAllFatura($unidade_cliente[0]['EMPRESA_CONCESSIONARIA'], $periodo);
       
        $tarifa_valores  = new Admin_Model_ConcessionariaTarifaValor();
        $tarifa_valores  = $tarifa_valores->fetchAllFatura($retorno[0]['ID'], $unidade_cliente[0]['EMPRESA_CONCESSIONARIA_TARIFA_GRUPO']);
        
        $tarifa_valores['subgrupo'][] = $unidade_cliente[0]['SUBGRUPO'];
        //Zend_Debug::dump($tarifa_valores);exit;
        return $tarifa_valores;
    }
    
    private function getTarifaDireta($tarifa, $grupo, $subgrupo, $posicao=null, $campo=null){
  
        $tarifa_valores  = new Admin_Model_ConcessionariaTarifaValor();
        $tarifa_valores  = $tarifa_valores->fetchAllFatura($tarifa, $grupo, $subgrupo);
  
        if(is_null($posicao) and is_null($campo)){
            return $tarifa_valores;
        }else{
            return $tarifa_valores[$posicao][$campo];
        }
    }
    
}
?>