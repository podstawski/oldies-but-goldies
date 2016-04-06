<?php

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));
define('APPLICATION_ENV', 'production');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
$config = $config->toArray();

$config['resources']['frontController']['defaultControllerName'] = 'administrator';
$config['resources']['frontController']['defaultAction']         = 'import-epu';

$application = new Zend_Application(APPLICATION_ENV, $config);
$application->bootstrap();
$application->run();
$ses->unsetAll();
