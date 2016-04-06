<?php
class ClassGroup_Messenger extends Zend_View_Helper_Abstract
{
	public static $messageTypes = array
	(
		'alert',
		'success',
		'error',
		'info',
		'debug',
	);

	public function addMessage($text, $type) {
		$stopped = ClassGroup_Session::isStopped();
		if ($stopped) {
			ClassGroup_Session::restore();
		}
		if (!isset($_SESSION['messenger']) or !is_array($_SESSION['messenger'])) {
			$_SESSION['messenger'] = array();
		}
		$_SESSION['messenger'] []= compact('text', 'type');
		if ($stopped) {
			ClassGroup_Session::stop();
		}
	}

	public function getMessages($namespace = false) {
		$messages = array();
		if (isset($_SESSION['messenger'])) {
			foreach ($_SESSION['messenger'] as $message) {
				if (($namespace === false) or ($message['type'] == $namespace)) {
					$messages []= $message['text'];
				}
			}
		}
		return $messages;
	}

	public function clearMessages() {
		$_SESSION['messenger'] = array();
	}


	public function render()
	{
		$html = '';

		foreach (self::$messageTypes as $namespace)
		{
			if ($namespace == 'debug') {
				continue;
			}
			$messages = $this->getMessages($namespace);
			if (($namespace == 'info') and (!empty($messages)))
			{
				$html .= '<div class="expandable">';
				$html .= '<a class="expand-trigger">' . /*$this->view->translate*/('Notices') . '</a>';
				$html .= '<div class="expand-target">';
			}
			foreach ($messages as $message)
			{
				$html .= sprintf('<div class="alert-container"><div class="alert alert-%s">%s</div></div>' . PHP_EOL, $namespace, $message);
			}
			if (($namespace == 'info') and (!empty($messages)))
			{
				$html .= '</div>';
				$html .= '</div>';
			}
		}
		$this->clearMessages();
		return $html;
	}
}
