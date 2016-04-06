<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Validate_Float extends Zend_Validate_Float
{
    public function __construct()
    {
        parent::__construct('en_US');
    }
}