<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

require_once APPLICATION_PATH . '/views/helpers/FlashMessenger.php';

abstract class AbstractController extends GN_Controller
{
	/**
	 * @var Model_UserRow
	 */
	protected $user;

	/**
     * @var GN_Observer
     */
	protected $observer;

	public function init()
	{
		parent::init();

		$controller = Zend_Controller_Front::getInstance();
		$controllerName = $controller->getRequest()->getControllerName();

		if (Zend_Auth::getInstance()->hasIdentity()) {
			$model = new Model_User();
			$this->user = $this->view->user = $model->fetchRow(array('email = ?' => Zend_Auth::getInstance()->getIdentity()));

		} elseif ($controllerName != 'index') {
            $this->_redirectExit('index', 'index');
		}

		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
		Zend_Paginator::setDefaultItemCountPerPage(20);
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		$this->messenger = new ClassGroup_Messenger();
		$this->view->messenger = $this->messenger;
	}

	public static function addCrashReport($e) {
		error_log('chrup exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
		foreach (explode("\n", $e->getTraceAsString()) as $part) {
			error_log('  ' . trim($part));
		}
		if (method_exists($e, 'getErrors')) {
			foreach ($e->getErrors() as $error) {
				error_log('  >Error encountered: ' . $error->getReason() . ' (' . $error->getErrorCode() . ')');
			}
		}
	}

	/*public static function addDebug($msg) {
		foreach (explode("\n", $msg) as $part) {
			error_log($part);
		}
	}*/

	/**
	 * @param string $name
	 * @param array $arguments
	 */
	public function __call($name, $arguments) {
		if (substr($name, 0, 3) == 'add') {
			$message = $arguments[0];
			$type = strtolower(substr($name, 3));
			if ($type == 'error') {
				$this->view->errors = true;
			}
			if (in_array($type, ClassGroup_Messenger::$messageTypes)) {
				$this->messenger->addMessage($message, $type);
			}
		}
	}
}
