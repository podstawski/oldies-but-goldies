<?php

class IndexController extends Yala_Controller
{
    public function init()
    {
        $this->_helper->layout->setLayout('index');
        parent::init();
    }

    public function indexAction()
    {
        $options = Zend_Registry::get('oauth_options');

        if ($options['enabled'] && !isset($_SESSION['OPENID'])) {
            header('Location: ' . $this->_getBaseUrl() . '/auth/open-id');
            exit;
        }
    }
}