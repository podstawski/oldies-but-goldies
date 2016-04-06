<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
abstract class Report_Abstract
{
    /**
     * Filter rules for @see Zend_Filter_Input
     * @var array
     */
    protected $_filterRules = array();

    /**
     * Validation rules for @see Zend_Filter_Input
     * @var array
     */
    protected $_validationRules = array();

    /**
     * Checks if data is valid with specyfic rules
     * @param array $data
     * @return mixed true if its valid or array with messages when it's not
     */
    public function isValid(array $data = array())
    {
        $filters = $this->_filterRules ?: array();
        $validators = $this->_validationRules ?: array();
        $input = new Zend_Filter_Input($filters, $validators, $data);

        if ($input->isValid()) {
            return true;
        } else {
            return $input->getMessages();
        }
    }
}
