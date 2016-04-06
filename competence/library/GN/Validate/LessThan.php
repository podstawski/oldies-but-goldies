<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Validate_LessThan extends Zend_Validate_LessThan
{
    public function isValid($value)
    {
        $this->_setValue($value);

        try {
            if ($this->_min < Zend_Locale_Format::getNumber($value, array('locale' => 'en_US'))) {
                $this->_error(self::NOT_LESS);
                return false;
            }
        } catch (Zend_Locale_Exception $e) {
            $this->_error(self::NOT_LESS);
            return false;
        }
        return true;
    }
}