<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Validate_GreaterThan extends Zend_Validate_GreaterThan
{
    public function isValid($value)
    {
        $this->_setValue($value);

        try {
            if ($this->_min >= Zend_Locale_Format::getNumber($value, array('locale' => 'en_US'))) {
                $this->_error(self::NOT_GREATER);
                return false;
            }
        } catch (Zend_Locale_Exception $e) {
            $this->_error(self::NOT_GREATER);
            return false;
        }
        return true;
    }
}