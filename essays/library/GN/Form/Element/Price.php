<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Form_Element_Price extends Zend_Form_Element_Text
{
    public function __construct($spec, $min = 0)
    {
        parent::__construct($spec);

        $this->addFilter(new GN_Filter_Float());
        $this->addValidator(new GN_Validate_Float());
        if ($min !== false) {
            $this->addValidator(new GN_Validate_GreaterThan($min));
        }
        $this->setAttrib('class', 'price price-pln');
    }
}