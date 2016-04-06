<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_School extends Form_Abstract
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('school name')
             ->setRequired(true);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('school description');

        $address = new Zend_Form_Element_Textarea('address');
        $address->setLabel('school address');

        $submit = new Zend_Form_Element_Submit('Submit');
        $submit->setLabel('save')
               ->setAttrib('class', 'btn-orange');

        $this->addElements(array($name, $description, $address, $submit));

        $this->setTableLayout();
    }
}