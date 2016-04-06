<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Filter_Float extends Zend_Filter_LocalizedToNormalized
{
    public function __construct($options = null)
    {
        if (null === $options) {
            $options = array('precision' => 2);
        }
        parent::__construct($options);
    }
}