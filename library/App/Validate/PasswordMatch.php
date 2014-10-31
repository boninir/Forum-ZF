<?php

class App_Validate_PasswordMatch extends Zend_Validate_Abstract
{
    const PASSWORD_MISMATCH = 'passwordMismatch';

     protected $_compare;

    public function __construct($compare)
    {
        $this->_compare = $compare;
    }

    protected $_messageTemplates = array(
        self::PASSWORD_MISMATCH => "Les mots de passe ne correspondent pas"
    );

    public function isValid($value)
    {
        $this->_setValue((string) $value);

        if ($value !== $this->_compare)  {
            $this->_error(self::PASSWORD_MISMATCH);
            return false;
        }

        return true;
    }

}

?>