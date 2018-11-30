<?php

/**
 * Validador para URLs
 * @filesource		/jmduque/library/Commit/Validate/URL.php
 * @author 		Julio Cesar Silva Nascimento
 * @copyright 		Commit Consulting
 * @access		Privado para administrador
 * @category		Validate
 * @package		Commit_Validate_URL
 * @subpackage		Zend_Validate_Abstract
 * @version		1.0
 * @since		05/08/2011
 */
class Commit_Validate_URL extends Zend_Validate_Abstract {
    const INVALID_URL = 'invalidUrl';

    protected $_messageTemplates = array(
        self::INVALID_URL => "'%value%' is not a valid URL.",
    );

    public function isValid($value){
        $valueString = (string) $value;
        $this->_setValue($valueString);

        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }
        return true;
    }
}

?>
