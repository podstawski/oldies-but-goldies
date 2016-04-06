<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Game_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
    public function getMessages()
    {
        $messages = parent::getMessages();
        if (!empty($messages)) {
            return array(self::INVALID => $this->getTranslator()->translate('Invalid e-mail address'));
        }
        return array();
    }
}