<?php

class Form_Compose extends Form_Abstract
{
    public function init()
    {
        $recipientList = new Zend_Form_Element_Text('recipient_list');
        $recipientList->setLabel('compose recipient list')
                      ->setRequired(true);

        $subject = new Zend_Form_Element_Text('subject');
        $subject->setLabel('compose subject')
                ->setRequired(true);

        $body = new Zend_Form_Element_Textarea('body');
        $body->setLabel('compose body')
             ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('compose submit')
               ->setAttrib('class', 'btn-orange');

        $this->addElements(array($recipientList, $subject, $body, $submit));

        $this->setTableLayout();
    }
}