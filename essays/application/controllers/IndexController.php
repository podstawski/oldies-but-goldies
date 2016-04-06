<?php
require_once 'AbstractController.php';

class IndexController extends AbstractController
{
	public function indexAction()
	{
		if (Zend_Auth::getInstance()->hasIdentity() and !empty($this->user)) {
			$this->_redirectExit('index', 'dashboard');
		} else {
			$auth = new Zend_Session_Namespace('auth');
			$auth->unsetAll();
		}
	}

	public function translationsAction() {
		$adapter = Zend_Registry::get('Zend_Translate')->getAdapter();
		$locale = Zend_Registry::get('Zend_Locale');
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($adapter->getMessages($locale));
		die;
	}

	public function forcePersonalDomainAction() {
		$_SESSION['force-personal-domain'] = true;
		$this->_redirectExit('open-id', 'auth');
	}

	public function authErrorAction() {
	}
}
