<?php

class Zend_View_Helper_CourseGroupStatus extends Zend_View_Helper_Abstract
{
    public function courseGroupStatus($status)
    {
        return $status ? 'potwierdzony' : 'oczekujący';
    }
}
