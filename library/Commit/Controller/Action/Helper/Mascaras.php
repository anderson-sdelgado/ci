<?php

/**
 * Helper para adicionar ou retirar a mascara de numeros
 * @filesource		jmduque/library/Commit/Controller/Action/Helper/Mascaras.php
 * @author 		Allan
 * @copyright 		Commit Consulting
 * @package		Commit_Controller_Action_Helper_Mascaras
 * @subpackage		Zend_Controller_Action_Helper_Abstract
 * @version		1.0
 * @since		04/08/2011
*/

class Commit_Controller_Action_Helper_Mascaras extends Zend_Controller_Action_Helper_Abstract {

    /**
     * Formata uma string para um valor aceito como moeda (REAIS)
     * @param   int $valor   numero a ser formatado
     * @param   int $formato tipo de formatacao, onde '0' para banco, '1' para mostrar na tela
     * @return  string
     * @example ValorMoeda('01.234.567,89',0)
     * @example ValorMoeda('01234567.00',1)
     *
     */
    public function ValorMoeda($valor, $formato, $casas=2, $relatorio=false){
        $valor = trim($valor);
        if(strlen($valor) <> 0){
        
            $m_limpar = '';

            if ($formato == 1){
                $valor = @number_format($valor,$casas,',','.');
                return $valor;
            }else{
                if (substr($valor,1,1) == ',' OR substr($valor,1,1) == '.') $valor = '0'.$valor;
                if (strlen($valor) == 0){
                  return false;
               }else{
                  $m_pos = -1;

                  while ($m_pos < strlen($valor)){
                     $m_pos ++;
                     $m_letra = substr($valor,$m_pos,1);

                     if (strpos("\,\.", $m_letra) > 0){
                        $m_letra = '*';
                        $m_aux   = $m_pos;
                     }
                     if ($m_letra <> '*') $m_limpar = $m_limpar . $m_letra;
                  }

                  if (@$m_aux > 0){
                     $m_aux = strlen($valor) - $m_aux;
                     $m_limpar  = $this->SoNumeros(substr($m_limpar,0,strlen($m_limpar)-$m_aux+1)) .".". $this->SoNumeros(substr($m_limpar,strlen($m_limpar)-$m_aux+1,$m_aux));
                     $m_retorno = $m_limpar;
                  }else{
                     $m_limpar  = $this->SoNumeros($m_limpar) .'.00';
                     $m_retorno = $m_limpar;
                  }
               }
               return $m_retorno;
            }
        }else{
            if(!$relatorio){
                if ($formato == 1){
                    $valor = @number_format(0,$casas,',','.');
                    return $valor;
                }
            }else{
                return '-';
            }
        }
    }

    /**
     * Pega uma string e retorna somente os numeros da mesma
     * @param   string $string  para ser retirado somente os numeros
     * @return  int
     * @example SoNumeros($string)
     */
    public function SoNumeros($string){
        $numeros = @ereg_replace("[^0-9]", "", $string);
        return trim($numeros);
      }

    /**
     * Pega uma um numero e verifica se é float caso não nao aplica a mascara
     * @param   string $valor  para ser alterada caso necessario
     * @return  float
     * @example ValorMoedaFloat($valor, $formato)
     */
    public function ValorMoedaFloat($valor, $formato){
        
        if(strlen($valor) <> 0){
            $parte = explode('.', $valor);
            if((int)$parte[1] <> 0){
                return $this->ValorMoeda($valor, $formato);
            }else{
                return $parte[0];
            }
        }else{
            return null;
        }
    }
}

