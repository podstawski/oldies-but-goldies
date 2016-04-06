<?php
require_once APPLICATION_PATH . '/views/helpers/FlashMessenger.php';

class AbstractController extends GN_Controller {
	/**
	 * @var Model_UsersRow
	 */
	protected $user;
	protected $observer;

	protected static function getNow() {
		return strtotime(GN_Tools::switchTimezone(date('Y-m-d H:i:s'), GN_Tools::TZ_SERVER_TO_USER));
	}

	public static function benchmark() {
		static $then = null;
		if ($then === null) {
			$then = microtime(true);
		}
		$now = microtime(true);
		$diff = $now - $then;
		error_log(sprintf('benchmark: %.02f', $diff));
		$then = $now;
	}

	public function init() {
		parent::init();

		if (isset($_GET['redirect'])) {
			$_SESSION['redirect'] = urldecode($_GET['redirect']);
		}

		if (Zend_Auth::getInstance()->hasIdentity()) {
			$modelUsers = new Model_Users();
			$this->realUser = $this->view->realUser = $modelUsers->find(Zend_Auth::getInstance()->getIdentity())->current();
			if (isset($_SESSION['fake-user'])) {
				$this->user = $this->view->user = $modelUsers->getByEmail($_SESSION['fake-user']);
			}
			if (empty($this->user)) {
				$this->user = $this->view->user = $this->realUser;
			}
			Zend_Registry::set('real-user', $this->realUser);
			Zend_Registry::set('user', $this->user);
		}

		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
		Zend_Paginator::setDefaultItemCountPerPage(20);
		Zend_Paginator::setDefaultScrollingStyle('Sliding');

		$controller = Zend_Controller_Front::getInstance();
		$controllerName = $controller->getRequest()->getControllerName();
		$actionName = $controller->getRequest()->getActionName();

		if ($this->user and in_array($controllerName, array('labels', 'contacts'))) {
			try {
				$this->view->imapFolders = CRM_Core::getCachedImapFolders($this->user);
				$this->view->googleContactGroups = CRM_Core::getCachedContactGroups($this->user);
			} catch (GN_GClient_EmptyTokenException $e) {
				$_SESSION['exception'] = serialize($e);
				$this->_redirectExit('token-error', 'index');
			} catch (CRM_EmptyTokenException $e) {
				$_SESSION['exception'] = serialize($e);
				$this->_redirectExit('token-error', 'index');
			} catch (CRM_ImapException $e) {
				$_SESSION['exception'] = serialize($e);
				$this->_redirectExit('token-error', 'index');
			}
			$this->user->getDomain()->confirmAccessToken();
		}

		$this->view->miscOptions = $this->getInvokeArg('bootstrap')->getOption('misc');
		$this->initObserver();
	}


	protected function getObserver() {
		if ($this->observer) return $this->observer;
		return $this->initObserver();
	}


	protected function initObserver() {
		if ($this->user && !$this->observer) {
			$googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
			if (isset($googleapps['json_link']) && isset($googleapps['json_hash'])) {
				$this->observer = new GN_Observer(
					$googleapps['json_link'],
					$googleapps['json_hash'],
					$this->user->email,
					Zend_Registry::get('Zend_Locale')->getLanguage(),
					'sharedapps'
				);
				Zend_Registry::set('observer', $this->observer);
				return $this->observer;
			}
		}
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, $arguments)
	{
		$stopped = GN_Session::isStopped();
		if ($stopped) {
			GN_Session::restore();
		}
		if (substr($name, 0, 3) == 'add') {
			$messageType = strtolower(substr($name, 3));
			if ($messageType == 'error') {
				$this->view->errors = true;
			}
			if (in_array($messageType, Zend_View_Helper_FlashMessenger::$messageTypes)) {
				if (!isset($_SERVER['REMOTE_ADDR'])) {
					echo $messageType . ': ' . join(', ', $arguments) . PHP_EOL;
				} else {
					$this->_flash($arguments, $messageType);
				}
			}
		}
		if ($stopped) {
			GN_Session::stop();
		}
	}

}
