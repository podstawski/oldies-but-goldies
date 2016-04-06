<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_SchoolClass extends Form_Abstract
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('school class name')
             ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('Submit');
        $submit->setLabel('save')
               ->setAttrib('class', 'btn-orange');

        $this->addElements(array($name, $submit));

        $this->setTableLayout();
    }
}