<?php
require_once 'AbstractController.php';

class ProtectionController extends AbstractController
{
	public function ajaxSetAction()
	{
		header('Content-Type: application/json');

		$emails = array();
		if (!$this->_hasParam('e-mail'))
		{
			$this->addError($this->view->translate('protection_no_email_specified_error'));
			header('HTTP/1.1 400 Bad Request');
			echo json_encode(false);
			die();
		}
		if (!$this->_hasParam('protected'))
		{
			$this->addError($this->view->translate('protection_no_flag_specified_error'));
			header('HTTP/1.1 400 Bad Request');
			echo json_encode(false);
			die();
		}

		if (is_array($this->_getParam('e-mail')))
		{
			$emails = $this->_getParam('e-mail');
		}
		else
		{
			$emails = array($this->_getParam('e-mail'));
		}
		$protected = intval($this->_getParam('protected'));

		$modelProtected = new Model_Protected();
		foreach ($emails as $email)
		{
			if ($protected)
			{
				if ($modelProtected->fetchRow($modelProtected->select()->where('email = ?', $email)) === null) {
					$modelProtected->insert(array('email' => $email));
				}
			}
			else
			{
				if ($modelProtected->fetchRow($modelProtected->select()->where('email = ?', $email)) !== null) {
					$modelProtected->delete(array('email = ?' => $email));
				}
			}
		}

		header('HTTP/1.1 200 OK');
		echo json_encode(true);
		die();
	}
}
?>
