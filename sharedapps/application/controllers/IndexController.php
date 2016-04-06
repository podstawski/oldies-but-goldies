<?php
require_once 'AbstractController.php';

class IndexController extends AbstractController {
	public function indexAction() {
		if (Zend_Auth::getInstance()->hasIdentity() and !empty($this->user)) {
			if (isset($_SESSION['redirect'])) {
				$url = $_SESSION['redirect'];
				if (strpos($url, 'http://') === false) {
					$url = $this->view->baseUrl($url);
				}
				unset($_SESSION['redirect']);
			} else {
				$url = $this->view->url(array('controller' => 'labels', 'action' => 'index'), null, true);
			}
			GN_Session::detachBrowser($url);
			GN_Session::stop();

			CRM_Core::getCachedAutocomplete($this->user);
		}
	}

	public function resetTokenAction() {
		$this->user->resetAccessToken();
		$this->_redirectExit('logout', 'auth');
		die;
	}

	public function translationsAction() {
		$adapter = Zend_Registry::get('Zend_Translate')->getAdapter();
		$locale = Zend_Registry::get('Zend_Locale');
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($adapter->getMessages($locale));
		die;
	}

	public function saveExpandedAction() {
		if (!isset($_SESSION['expanded'])) {
			$_SESSION['expanded'] = array();
		}
		$_SESSION['expanded'][$this->_getParam('key')] = $this->_getParam('state') ? 1 : 0;
		die;
	}

	public function forcePersonalDomainAction() {
		$_SESSION['force-personal-domain'] = true;
		$this->_redirectExit('open-id', 'auth');
	}

	public function authErrorAction() {
	}

	public function ajaxUserAutocompleteAction() {
		header('Content-Type: application/json');
		$json = array();

		$filter = strtolower(trim($this->_getParam('filter')));
		$emails = CRM_Core::getCachedAutocomplete($this->user);

		$json['folks'] = array();
		foreach ($emails as $email) {
			if (preg_match('/(^|\W)' . $filter . '.*' . (strpos($filter, '@') === false ? '@' : '') . '/i', $email['e-mail'])) {
				$json['folks'] []= array('email' => $email['e-mail'], 'src' => $email['src']);
			}
		}

		echo json_encode($json);
		die;
	}
}
