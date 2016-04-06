<?php

class Zend_View_Helper_ProductSerial extends Zend_View_Helper_Abstract
{
    public function productSerial($productData)
    {
        $date = Model_Day::gameDayIntoGameDate($productData->day);
        return sprintf('%02d%02d%02d', $date['day'], $date['month'], $date['year']);
    }
}
