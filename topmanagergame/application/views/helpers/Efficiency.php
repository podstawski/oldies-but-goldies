<?php

class Zend_View_Helper_Efficiency extends Zend_View_Helper_Abstract
{
    public function efficiency($efficiency)
    {
        return $efficiency . $this->view->translate('employees efficiency unit');
    }
}
