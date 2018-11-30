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
class Commit_Validate_PasswordConfirmation extends Zend_Validate_Abstract {
 
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context['password'])
                && ($value == $context['password']))
            {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}
?>
