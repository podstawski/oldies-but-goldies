<?php
$tmp = error_reporting(0);
date_default_timezone_set(date_default_timezone_get());
error_reporting($tmp);

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'development');

set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'), get_include_path())));

require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap();


$request = new Zend_Controller_Request_Http();
$front = Zend_Controller_Front::getInstance();


$front->setDefaultControllerName('labels-cron');
if (isset($argv[1]))
{
    $request->setParam('label', $argv[1]);
    if (isset($argv[2])) $request->setParam('noflush',1);
    $front->setDefaultAction('migrate');
}
else
{
    $front->setDefaultAction('labels');
}


$front->setRequest($request);
$front->getPlugin('GN_Plugin_Acl')->setRoleName(Model_Users::ROLE_CLI);

$application->run();
