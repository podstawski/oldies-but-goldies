<?php

class Zend_View_Helper_PlainText extends Zend_View_Helper_FormElement
{
    public function plainText($name, $value = null, $attribs = null)
    {
        if ($value !== null) {
            return $value;
        }
        return '';
    }
}