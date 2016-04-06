<?php

class GN_Log_Formatter_Custom extends Zend_Log_Formatter_Simple
{
    public function format($event)
    {
        $userID = 0;
        if (isset($event['uid']))
            $userID = $event['uid'];

        $username = 'anonymous';
        if (isset($event['username']))
            $username = $event['username'];

        $error = $event['message'];

		if (isset($_SERVER['REQUEST_URI'])) {
			$requestUri = $_SERVER['REQUEST_URI'];
		} else {
			$requestUri = null;
		}

        if ($error instanceof Zend_Gdata_Gapps_ServiceException)
            $error = (string) current($error->getErrors());

        if ($error instanceof Exception) {
            return vsprintf(
                '%s: %s' . PHP_EOL
                . 'username: %s (%d)' . PHP_EOL
                . 'message: %s' . PHP_EOL
                . 'file: %s' . PHP_EOL
                . 'line: %d' . PHP_EOL
                . PHP_EOL,
                array(date('Y.m.d H:i:s'), $requestUri,
                      $username, $userID,
                      $error->getMessage(),
                      $error->getFile(),
                      $error->getLine()
                )
            );
        }

        if (is_string($error)) {
            return vsprintf(
                '%s: %s' . PHP_EOL
                . 'username: %s (%d)' . PHP_EOL
                . 'message: %s' . PHP_EOL
                . PHP_EOL,
                array(date('Y.m.d H:i:s'), $requestUri,
                      $username, $userID,
                      $error,
                )
            );
        }
    }
}
