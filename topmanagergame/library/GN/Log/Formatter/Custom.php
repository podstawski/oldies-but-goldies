<?php

class GN_Log_Formatter_Custom extends Zend_Log_Formatter_Simple
{
    public function format($event)
    {
        $exception = $event['message'];

        if ($exception instanceof Exception) {

            $userId = isset($event['uid']) ? $event['uid'] : 0;
            $username = isset($event['username']) ? $event['username'] : 'Anonymous';

            return vsprintf(
                '%s: %s' . PHP_EOL
                . 'username: %s (%d)' . PHP_EOL
                . 'message: %s' . PHP_EOL
                . 'file: %s' . PHP_EOL
                . 'line: %d' . PHP_EOL
                . PHP_EOL,
                array(date('Y.m.d H:i:s'), $_SERVER['REQUEST_URI'],
                      $username, $userId,
                      $exception->getMessage(),
                      $exception->getFile(),
                      $exception->getLine()
                )
            );
        }
    }
}
