<?php

class Form_CreateUser extends Zend_Form
{
    public function init()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $domainOptions = $db->fetchPairs(
            $db->select()->from('domains', array('id', 'org_name'))
        );

        $domain = new Zend_Form_Element_Select('domain_id');
        $domain->setLabel('Domena')
               ->setMultiOptions($domainOptions);

        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->setLabel('Imię')
                  ->setRequired(true)
                  ->addValidator(new GN_Validate_Gapps_String());

        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->setLabel('Nazwisko')
                 ->setRequired(true)
                 ->addValidator(new GN_Validate_Gapps_String());

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Hasło')
                 ->setRequired(true)
                 ->addValidator(new Zend_Validate_StringLength(array('min' => 8)), true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Dodaj');

        $this->addElements(array($domain, $firstName, $lastName, $password, $submit));
    }
}