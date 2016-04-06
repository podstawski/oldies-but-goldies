<?php

class GN_Validate_GreaterOrEqualThan extends GN_Validate_GreaterThan
{
    const NOT_GREATER_NOR_EQUAL = 'notGreaterOrEqualThan';

    protected $_messageTemplates = array(
        self::NOT_GREATER_NOR_EQUAL => "'%value%' is not greater nor equal than '%min%'"
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        try {
            if ($this->_min > Zend_Locale_Format::getNumber($value, array('locale' => 'en_US'))) {
                $this->_error(self::NOT_GREATER_NOR_EQUAL);
                return false;
            }
        } catch (Zend_Locale_Exception $e) {
            $this->_error(self::NOT_GREATER_NOR_EQUAL);
            return false;
        }
        return true;
    }
}
