<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_Admin_MapParam extends Form_Abstract
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('bname');
        $name->setLabel('Nazwa budynku')
             ->setRequired(true)
             ->addValidator(new Zend_Validate_StringLength(array('max' => 256)));

        $hint = new Zend_Form_Element_Textarea('bhint');
        $hint->setlabel('Tooltip');

        $url = new Zend_Form_Element_Text('burl');
        $url->setLabel('URL');
//        $url->addValidator(new GN_Validate_Url());

        $delete = new Zend_Form_Element_Checkbox('delete');
        $delete->setLabel('Usuń');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Zapisz')
               ->setAttrib('class', 'btn-orange');

        $this->addElements(array($name, $hint, $url, $delete, $submit));

        $this->setTableLayout();
    }
}