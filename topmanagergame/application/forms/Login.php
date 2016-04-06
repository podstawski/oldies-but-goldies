<?php
/**
 * Description
 *
 * @author Radosław Benkel
 */

class Form_Login extends Form_Abstract
{
    public function init()
    {
        $this->setAction($this->getView()->url(array(
            'action'     => 'login',
            'controller' => 'user'
        )));

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('login')
                 ->setRequired(true)
                 ->setAttrib('class', 'text');

        $pass = new Zend_Form_Element_Password('password');
        $pass->setLabel('password')
             ->setRequired(true)
             ->setAttrib('class','text');

        $this->addElements(array($username, $pass));

        $this->setElementDecorators(array(
            'Label',
            'ViewHelper',
            'Errors'
        ));
    }
}
