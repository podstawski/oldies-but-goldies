<?php

class Zend_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    public function flashMessenger($namespace = 'default')
    {
        $messenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        $messages  = $messenger->setNamespace($namespace)->getCurrentMessages();
        $html      = '';
        if (count($messages) > 0)
        {
            foreach ($messages as $k => $message) {
                if ($message instanceof Exception) {
                    $messages[$k] = $message->getMessage();
                }
            }

            $html .= '<div class="ui-state-error ui-corner-all">';
            $html .= '<table cellspacing="0" cellpadding="3">';
            foreach ($messages as $message) {
                $html .= '<tr>';
                $html .= '<td><span class="ui-icon ui-icon-alert"></span></td>';
                $html .= '<td>' . $message . '</span></td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            $html .= '</div>';
        }
        return $html;
    }
}
