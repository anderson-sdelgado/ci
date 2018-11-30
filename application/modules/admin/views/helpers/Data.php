<?php
/*
 * Helper que retorna o uma data em vários formatos diferentes.
 * ex: echo $this->Data('2009-18-11')->extenso;
 * ex: echo $this->Data('2009-18-11')->pt_br;
 * ex: echo $this->Data('2009-18-11 09:11:00')->hora;
 * @author Nivaldo Arruda - nivaldo@gmail.com
 * @see www.nivaldoarruda.com.br
 * @version 1.0
*/
class Zend_View_Helper_Data extends Zend_View_Helper_Abstract
{
    public $extenso;
    public $pt_br;
    public $hora;

    public function data($data)
    {
        list($ano, $mes, $dia) = explode("-", substr($data, 0, 10));

        $this->extenso = $this->diasemana("$ano-$mes-$dia");
        $this->pt_br = "$dia/$mes/$ano";

        if(strlen($data)>10){
            list($hora, $minuto, $segundo) = explode(":", substr($data, 11, 8));
            $this->hora = "$hora:$minuto:$segundo";
        }

        return $this;
    }

    /**
     * Retorna o dia da semana, por extenso e em português, correspondente
     * a data informada por parametro (no padrão aaaa-mm-dd).
     *
     * @param Date $data
     * @return String
     */
    public function diasemana($data){
        list($ano, $mes, $dia) = explode("-", $data);

        $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));

        switch($diasemana) {
            case 0: $diasemana = "Domingo";
                    break;
            case 1: $diasemana = "Segunda-Feira";
                    break;
            case 2: $diasemana = "Terça-Feira";
                    break;
            case 3: $diasemana = "Quarta-Feira";
                    break;
            case 4: $diasemana = "Quinta-Feira";
                    break;
            case 5: $diasemana = "Sexta-Feira";
                    break;
            case 6: $diasemana = "Sábado";
                    break;
        }

        return $diasemana;

    }
}
?>
