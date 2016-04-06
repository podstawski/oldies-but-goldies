<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Zend_View_Helper_Face extends Zend_View_Helper_Abstract
{
    public function face($employee)
    {
        return strtolower($employee->sex) . $employee->face;
    }
}