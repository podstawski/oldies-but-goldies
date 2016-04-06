<?php

require_once 'Zend/Log.php';

class GN_Logger extends Zend_Log
{
    public function __construct()
    {
        parent::__construct();

        $errorspath = ($logspath = APPLICATION_PATH . '/logs') . '/' . date('Y.m.d');
        if (!file_exists($errorspath)) {
            mkdir($errorspath, 0777, true);
        }

        $writer = new Zend_Log_Writer_Stream($errorspath . '/errors.log');
        $writer->setFormatter(new GN_Log_Formatter_Custom())
               ->addFilter(new Zend_Log_Filter_Priority(Zend_Log::ERR, '<='));

        @chmod($errorspath . '/errors.log', 0777);

        $this->addWriter($writer);

        $writer = new Zend_Log_Writer_Stream($errorspath . '/warnings.log');
        $writer->setFormatter(new GN_Log_Formatter_Custom())
               ->addFilter(new Zend_Log_Filter_Priority(Zend_Log::ERR, '>'))
               ->addFilter(new Zend_Log_Filter_Priority(Zend_Log::INFO, '<='));

        @chmod($errorspath . '/warnings.log', 0777);

        $this->addWriter($writer);

        $writer = new Zend_Log_Writer_Stream($logspath . '/debug.log');
        $writer->setFormatter(new GN_Log_Formatter_Debug())
               ->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG, '=='));

        $this->addWriter($writer);

        @chmod($logspath . '/debug.log', 0777);
    }
}