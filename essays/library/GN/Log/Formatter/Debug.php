<?php

class GN_Log_Formatter_Debug extends Zend_Log_Formatter_Simple
{
    public function format($event)
    {
        return date('Y.m.d H:i:s')
               . PHP_EOL
               . print_r($event['message'], true)
               . PHP_EOL;
    }
}
