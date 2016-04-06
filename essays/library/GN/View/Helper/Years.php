<?php

class GN_View_Helper_Years extends Zend_View_Helper_Abstract
{
    public function years($number)
    {
        return $this->view->pluralization($number, 'one year', 'two, three, four years', 'more years');
    }
}