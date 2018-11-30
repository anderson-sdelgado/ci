<?php

/**
 * Description of Abstract
 *
 * @author wesleywillians
 */
abstract class Admin_Model_Abstract {

    protected $_dbTable;
    protected $_table;
    protected $_sequence;
    protected $_primary;

    public function save(array $data, $retorno_id = false) {
        
        $login = Zend_Auth::getInstance()->getIdentity()->LOGIN;
//        if($login == "ITDESK"){
            $date = New Zend_Date();
            $conteudo = $date->get('dd/MM/yyyy HH:mm')." - ".$login;
            foreach ($data as $key => $value) {
                if($key == "DE_TEXTO" || $key == "MOTIVO_CI"){
                    $value = "Ignorado";
                }
                $conteudo.= ' | '.$key.' = '.$value;
            }
            $fp = fopen(substr(UPLOAD_PATH, 0, -7)."_logs".DIRECTORY_SEPARATOR.$this->_table.$date->get('-yyyy_MM').'.txt', "a");

            fwrite($fp, $conteudo.PHP_EOL);
            fclose($fp);
//        }
        if (isset($data['ID']) && !is_null($data['ID']) && $data['ID'] != 0) {
            return $this->_update($data);
        } else {
            return $this->_insert($data, $retorno_id);
        }
    }

    public function find($id1,$id2=null,$id3=null) {
        $db = Zend_Registry::get('db2');
        
        if($id2 && $id3){
            $query = "SELECT * FROM  $this->_table 
                    WHERE ".$this->_primary[0]." = $id1
                    AND ".$this->_primary[1]." = $id2
                    AND ".$this->_primary[2]." = $id3";
        }elseif($id2){
            $query = "SELECT * FROM  $this->_table 
                    WHERE ".$this->_primary[0]." = $id1
                    AND ".$this->_primary[1]." = $id2";
        }else{
            $query = "SELECT * FROM  $this->_table 
                    WHERE $this->_primary = $id1";
        }
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return @$retorno[0];
    }

    public function findProximoId() {
        $db = Zend_Registry::get('db2');
        
        $query = "SELECT MAX(ID) AS MAX_ID FROM $this->_table";
        
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        $ret = current($retorno);
        if(isset($ret['MAX_ID'])){
            return $ret['MAX_ID']+1;
        }else{
            return 0;
        }
    }

    protected function _update(array $data) {
        $db = Zend_Registry::get('db2');
        
        try{
            if(isset($data['ID'])){
                $id = $data['ID'];
                unset($data['ID']);
            }else{
                $id = null;
            }            
            $sql = "UPDATE $this->_table SET ";
            $fields = array_keys($data);

            $total = count($data);
            for($i=0;$i<$total;$i++):
                if(strstr($fields[$i],'DT') && !empty($data[$fields[$i]]))
                    $sql .= $fields[$i] ." = ".$data[$fields[$i]].",";
                else if( strstr($fields[$i], 'DATA_') || strstr($fields[$i], 'DATA') )
                    $sql .= "$fields[$i]  = TO_DATE('".$data[$fields[$i]]."','DD/MM/YYYY HH24:MI'),";
                else
                    $sql .= $fields[$i] ." = '".str_replace("'","***",$data[$fields[$i]])."',";
            endfor;

            $fim = strlen($sql) -1;
            $sql = substr($sql,0,$fim);
            if(!$id){
                $sql .= " WHERE 1 = 1 ";
                foreach ($this->_primary as $value) {
                    if( strstr($value, 'DATA_') || strstr($value, 'DATA') )
                        $sql .= "AND $value  = TO_DATE('".$data[$value]."','DD/MM/YYYY HH24:MI') ";
                    else
                        $sql .= "AND ".$value ." = '".str_replace("'","***",$data[$value])."' ";
                }
            }else{
                $sql .= " WHERE ID = '".$id."'";
            }
            $res = $db->query($sql);
            
            return array(null,$res);            
            
            /*$retorno = $this->_dbTable->update($data, array("ID=?"=>$data['ID']));
            $retorno = array(null,$retorno);*/
        }catch (Exception $e){

            $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
	}
	return $retorno;
    }

    protected function _insert(array $dados, $retorno_id = false) {
        $data = $dados;
        $db = Zend_Registry::get('db2');
        try {
            if(!empty($this->_sequence)){
                $data['ID'] = $this->_sequence.'.nextval';
            }
            $sql = "INSERT INTO $this->_table (";
            $fields = array_keys($data);
            $array = implode(",",$fields);
            $sql .= $array;
            $sql .= ") VALUES (";

            //se for campo data insere mascara to_date, senao o campo vira uma string
            foreach($data as $key=>$valor):
                if((strstr($key, 'DT_') || strstr($key, '_DATA') || strstr($key, 'DATA_')||$key == "DATA") && !empty($valor))        
                    $sql .= "TO_DATE('$valor','DD/MM/YYYY HH24:MI'),";
                else if($key == 'ID')
                     $sql .= "$valor,";   
                else
                    $sql .= "'".str_replace("'","***",$valor)."',";
            endforeach;

            $fim = strlen($sql) -1;
            $sql = substr($sql,0,$fim);
            $sql .= ")";
            $res = $db->query($sql);
                       
            if($retorno_id){
                $sql_ultimo = "SELECT MAX(ID) AS ID FROM $this->_table WHERE ";
                foreach($dados as $key=>$valor):
                    if((strstr($key, 'DT_') || strstr($key, '_DATA') || strstr($key, 'DATA_')||$key == "DATA") && !empty($valor))
                        $sql_ultimo .= "$key = TO_DATE('$valor','DD/MM/YYYY HH24:MI') AND ";
                    elseif((strstr($key, 'DESCRICAO') || strstr($key, 'MOTIVO_CI')) && !empty($valor))
                        $sql_ultimo .= "$key LIKE '".str_replace("'","***",$valor)."' AND ";
                    else
                        $sql_ultimo .= "$key = '".str_replace("'","***",$valor)."' AND ";
                endforeach;


                $sql_ultimo = substr($sql_ultimo,0,strlen($sql_ultimo) -5);

                $res_ultimo = $db->query($sql_ultimo);
                $retorno_ultimo = $res_ultimo->fetchAll();

                return array(null,$retorno_ultimo[0]["ID"]);
            }else{
                return array(null,"");
            }
            
        }catch (Exception $e){

            $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
	}
        
	return $retorno;
    }
//    protected function _insert(array $data) {
//        $db = Zend_Registry::get('db2');
//        try {
//            if(!empty($this->_sequence)){
//                $data['ID'] = $this->_sequence.'.nextval';
//            }
//            $sql = "INSERT INTO $this->_table (";
//            $fields = array_keys($data);
//            $array = implode(",",$fields);
//            $sql .= $array;
//            $sql .= ") VALUES (";
//
//            //se for campo data não coloca aspas, senao o campo vira uma string
//            foreach($data as $key=>$valor):
//                if(strstr($key, 'DT') && !empty($valor))        
//                    $sql .= "$valor,";
//                else if($key == 'ID')
//                    $sql .= "$valor,";
//                else if(strstr($key, 'DATA_') && !empty($valor))
//                    $sql .= "TO_DATE('$valor','DD/MM/YYYY HH24:MI'),";
//                else
//                    $sql .= "'".str_replace("'","***",$valor)."',";
//            endforeach;
//
//            $fim = strlen($sql) -1;
//            $sql = substr($sql,0,$fim);
//
//
//		 $sql .= ")";
//            $res = $db->query($sql);
//            
//            return array(null,$res);
//            /*$retorno = $this->_dbTable->insert($data);
//            $retorno = array(null,$retorno);*/
//        }catch (Exception $e){
//
//            $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
//	}
//	return $retorno;
//    }

    public function delete($id, $esconder=false, $data=array()) {
        if(!$esconder){
            try{
                $db = Zend_Registry::get('db2');
                $return = $db->query("DELETE 
                                   FROM  $this->_table
                                   WHERE    ID = '$id'
                                   ");

                $retorno = array(null,$return);
            }catch (Exception $e){
                $retorno  = array($e->getCode().'-'.$e->getMessage(), null);
            }
            return $retorno;
        }else{
            return $this->_update($data);
        }
    }
    
    public function fetchAll() {
        $db = Zend_Registry::get('db2');
        $query = "SELECT * FROM  $this->_table";
        $res = $db->query($query);
        $retorno = $res->fetchAll();
        return $retorno;
//        return $this->_dbTable->fetchAll();
    }

    public function fetchPairs() {
        $select = $this->_dbTable->getAdapter()->select()->from($this->_table, array('ID', 'nome'));
        return $this->_dbTable->getAdapter()->fetchPairs($select);
    }

    public function setPaginator($result, $currentPage=1, $CountPerPage=10){
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
        $paginator = Zend_Paginator::factory($result);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage($CountPerPage);
        return $paginator;
    }
}