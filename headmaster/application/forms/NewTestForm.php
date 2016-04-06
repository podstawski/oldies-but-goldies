<?php
class forms_NewTestForm extends Zend_Form 
{ 
    public function __construct($options = null) 
    { 
        parent::__construct($options);
        $this->setName('contact_us');
        
        $title = new Zend_Form_Element_Select('title');
        $title->setLabel('Title')
              ->setMultiOptions(array('mr'=>'Mr', 'mrs'=>'Mrs'))
              ->setRequired(true)->addValidator('NotEmpty', true);
        
        $firstName = new Zend_Form_Element_Text('firstName');
        $firstName->setLabel('First name')
                  ->setRequired(true)
                  ->addValidator('NotEmpty');

        $lastName = new Zend_Form_Element_Text('lastName');
        $lastName->setLabel('Last name')
                 ->setRequired(true)
                 ->addValidator('NotEmpty');
             
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email address')
              ->addFilter('StringToLower')
              ->setRequired(true)
              ->addValidator('NotEmpty', true)
              ->addValidator('EmailAddress'); 
              
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Contact us');
        
        $this->addElements(array($title, $firstName, 
            $lastName, $email, $submit));
        
    } 
}

?>
