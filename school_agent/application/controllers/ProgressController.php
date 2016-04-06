<?php
require_once 'AbstractController.php';

class ProgressController extends AbstractController {
	public function ajaxGetAction() {
		$this->_helper->layout->disableLayout();
		header('Content-Type: application/json');

		$json = array();
		if (!$this->_hasParam('progress-id')) {
			header('HTTP/1.1 400 Bad Request');
		} else {
			$progressID = $this->_getParam('progress-id');
			$json = ClassGroup_Progress::get($progressID);
			$json['percent'] = sprintf('%.0f', $json['current'] * 100.0 / max(1, $json['max']));
			if ((!isset($json['finished']) or $json['finished'] == false) and ($json['percent'] == 100)) {
				$json['percent'] = '99';
			}
			$json['messenger'] = array();
			foreach (ClassGroup_Messenger::$messageTypes as $namespace) {
				$messages = $this->messenger->getMessages($namespace);
				if (!empty($messages)) {
					$json['messenger'][$namespace] = array_reverse($messages);
				}
			}
		}
		echo json_encode($json);
		die();
	}

	public function ajaxDetachedAction() {
		$this->_helper->layout->disableLayout();
		header('Content-Type: text/plain; charset=utf-8');
		echo 'ok';
		die();
	}
}
