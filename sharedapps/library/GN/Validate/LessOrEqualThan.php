<?php

class GN_Validate_LessOrEqualThan extends Zend_Validate_LessThan
{
    const NOT_LESS_NOR_EQUAL = 'notLessOrEqualThan';

    protected $_messageTemplates = array(
        self::NOT_LESS_NOR_EQUAL => "'%value%' is not less nor equal than '%max%'"
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        try {
            if ($this->_max < Zend_Locale_Format::getNumber($value, array('locale' => 'en_US'))) {
                $this->_error(self::NOT_LESS_NOR_EQUAL);
                return false;
            }
        } catch (Zend_Locale_Exception $e) {
            $this->_error(self::NOT_LESS_NOR_EQUAL);
            return false;
        }
        return true;
    }
}
