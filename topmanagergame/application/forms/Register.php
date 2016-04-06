<?php
/**
 * @author Radosław Szczepaniak
 */

class Form_Register extends Form_Company
{
    public function init()
    {
        parent::init();

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('username')
                 ->setDescription('username desc')
                 ->setRequired(true)
                 ->setAttrib('class', 'text')
                 ->addValidator(new Zend_Validate_NotEmpty(), true)
                 ->addValidator(new Zend_Validate_Alnum(false), true)
                 ->addValidator(new Zend_Validate_StringLength(0, 256), true)
                 ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
                     'table' => 'users',
                     'field' => 'username',
                 )), true);

        $username->getValidator('Db_NoRecordExists')
                 ->setMessage("user '%value%' already exists", Zend_Validate_Db_RecordExists::ERROR_RECORD_FOUND);

        $pass = new Zend_Form_Element_Password('password');
        $pass->setLabel('password')
             ->setDescription('password desc')
             ->setRequired(true)
             ->setAttrib('class', 'text')
             ->addValidator(new Zend_Validate_NotEmpty(), true)
             ->addValidator(new Zend_Validate_StringLength(6, 256), true);

        $pass2 = new Zend_Form_Element_Password('retype_password');
        $pass2->setLabel('retype password')
              ->setDescription('retype password desc')
              ->setRequired(true)
              ->setAttrib('class', 'text')
              ->addValidator(new Zend_Validate_NotEmpty(), true)
              ->addValidator(new Zend_Validate_Identical('password'), true);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('email adress')
              ->setDescription('email adress desc')
              ->setRequired(true)
              ->setAttrib('class', 'text')
              ->addValidator(new Zend_Validate_NotEmpty(), true)
              ->addValidator(new Game_Validate_EmailAddress(), true)
              ->addValidator(new Zend_Validate_StringLength(0, 256), true);

        $this->getElement('name')->setAttrib('class', 'text');
        $this->addElements(array($email, $pass, $pass2));
    }

    /**
     * @return Form_Company
     */
    public function setTableLayout()
    {
        $this->_order = array_flip(array('email', 'password', 'retype_password', 'name', 'buttons'));

        parent::setTableLayout();

        $this->getElement('table_header')
             ->setValue($this->getView()->translate('form register table header'));

        return $this;
    }
}
