<?php

class Zend_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    public function flashMessenger()
    {
        $messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages  = $messenger->getMessages() + $messenger->getCurrentMessages();
        $html      = '';
        if (count($messages) > 0) {
            $html .= '<div class="messages">';

            foreach ($messages as $k => $message) {
                if ($message instanceof Exception) {
                    $messages[$k] = $message->getMessage();
                }
            }

            if (count($messages) == 1) {
                $html .= $messages[0];
            }
            else
            {
                $html .= '<ul>';
                foreach ($messages as $message)
                    $html .= '<li>' . $message . '</li>';
                $html .= '</ul>';
            }
            $html .= '</div>';
        }

        $messenger->clearMessages();
        $messenger->clearCurrentMessages();

        // Return the final HTML string to use.
        return $html;
    }
}
