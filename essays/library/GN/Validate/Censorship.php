<?php
/**
 * @author Radosław Benkel
 */
 
class GN_Validate_Censorship extends Zend_Validate_Abstract
{
    const VULGAR_WORD  = 'vulgarWord';
    const INVALID      = 'digitsInvalid';

    /**
     * Censorship filter used for validation
     *
     * @var Zend_Filter_Digits
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::VULGAR_WORD   => "'%value%' containts word, which is considered to be vulgar",
        self::INVALID      => "Invalid type given. String, integer, float or array expected",
    );

    public function __construct($options)
    {
        self::$_filter = new GN_Filter_Censorship($options);
    }


    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value only contains digit characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue((string) $value);

        if ($this->_value !== self::$_filter->filter($this->_value)) {
            $this->_error(self::VULGAR_WORD);
            return false;
        }
        return true;
    }
}
