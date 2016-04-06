<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initLocalConfig()
    {
        $globalConfig = new Zend_Config($this->getOptions(), true);
        try
	{
		$localConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/local.ini');
		$globalConfig->merge($localConfig);
		$globalConfig = $globalConfig->toArray();
		$this->setOptions($globalConfig);
	}
	catch (Zend_Config_Exception $e)
	{
		throw new Exception('File /configs/local.ini not found. Create it, it can be empty.');
        }
    }
}

