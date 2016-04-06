<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gmail.com>
 */

class Zend_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    /**
     * @var array
     */
    public static $messageTypes = array(
        'success', 'error', 'info', 'alert'
    );

    public function flashMessenger()
    {
        /**
         * @var Zend_Controller_Action_Helper_FlashMessenger $messenger
         */
        $messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        $html = '';

        foreach (self::$messageTypes as $namespace) {
            $messenger->setNamespace($namespace);
            $messages = $messenger->getMessages() + $messenger->getCurrentMessages();
            foreach ($messages as $message) {
                $html .= sprintf('<div class="alert alert-%s">%s</div>' . PHP_EOL, $namespace, $message);
            }
            $messenger->clearMessages();
            $messenger->clearCurrentMessages();
        }

        return $html;
    }
}

