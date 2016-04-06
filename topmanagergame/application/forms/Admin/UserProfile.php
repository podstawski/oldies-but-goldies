<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_Admin_UserProfile extends Form_Profile
{
    public function __construct($userID, array $options = array())
    {
        $this->_userID = $userID;
        parent::__construct($options);
    }

    public function init()
    {
        parent::init();

        $view = $this->getView();
        $this->getElement('table_header')->setValue($view->translate('user %s profile', $this->_userData['username']));

        $view->jQuery()->addOnLoad(<<< JS

    $("input.cancel").click(function(e){
        document.location = "{$view->url(array('action' => 'schools', 'controller' => 'admin'), null, true)}";
        e.preventDefault();
        return false;
    });

JS
        );
    }
}