<?php

class Zend_View_Helper_FormatDate extends Zend_View_Helper_Abstract
{
    public function formatDate($date, $format = null)
    {
        if ($date != null) {
            $helper = new Zend_Date($date);
            if ($format == null) {
                $format = 'd-m-Y';
            }
            return $helper->toString($format);
        }
        return "";
    }
}