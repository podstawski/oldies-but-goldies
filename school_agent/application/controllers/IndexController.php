<?php
require_once 'AbstractController.php';

class IndexController extends AbstractController
{
	public function init() {
		parent::init();
		$this->view->messenger = new ClassGroup_Messenger();
	}

	public function indexAction() {
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirectExit('index', 'dashboard');
		}
	}

	public function provisioningApiAction() {
	}
}
