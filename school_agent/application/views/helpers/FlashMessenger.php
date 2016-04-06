<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gmail.com>
 */

class Zend_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
	/**
	 * @var array
	 */
	public static $messageTypes = array
	(
		'alert',
		'success',
		'error',
		'info',
	);

	public function flashMessenger()
	{
		/**
		 * @var Zend_Controller_Action_Helper_FlashMessenger $messenger
		 */
		$messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

		$html = '';

		foreach (self::$messageTypes as $namespace)
		{
			$messenger->setNamespace($namespace);
			$messages = $messenger->getMessages() + $messenger->getCurrentMessages();
			if (($namespace == 'info') and (!empty($messages)))
			{
				$html .= '<div class="expandable">';
				$html .= '<a class="expand-trigger">' . /*$this->view->translate*/('Notices') . '</a>';
				$html .= '<div class="expand-target">';
			}
			foreach ($messages as $message)
			{
				$html .= sprintf('<div class="alert-container"><p class="alert alert-%s">%s</p></div>' . PHP_EOL, $namespace, $message);
			}
			if (($namespace == 'info') and (!empty($messages)))
			{
				$html .= '</div>';
				$html .= '</div>';
			}
			$messenger->clearMessages();
			$messenger->clearCurrentMessages();
		}

		return $html;
	}
}
?>
