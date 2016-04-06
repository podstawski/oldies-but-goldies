<?php

class GN_Form_Element_PlainText extends Zend_Form_Element_Xhtml
{
    public $helper = 'plainText';

    /*
     * SIM this elements' value is not posted
     * and original method sets empty value...
     * we don't want that.
     */
    public function isValid($value, $context = null)
    {
        return true;
    }
}