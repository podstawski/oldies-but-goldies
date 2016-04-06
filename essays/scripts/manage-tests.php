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

error_reporting(error_reporting() & (~ (E_NOTICE | E_USER_NOTICE)));

function usage() {
	echo 'Usage: ' . PHP_EOL;
	echo basename(__FILE__) . '              lists all tests' . PHP_EOL;
	echo basename(__FILE__) . ' dispatch     starts or stops all tests (for cron)' . PHP_EOL;
	echo basename(__FILE__) . ' start [id]   starts specified test' . PHP_EOL;
	echo basename(__FILE__) . ' stop [id]    stops specified test' . PHP_EOL;
	echo PHP_EOL;
}

$request = new Zend_Controller_Request_Http();
$front = Zend_Controller_Front::getInstance();
if (count($argv) == 1) {
	usage();
	$front->setDefaultControllerName('dashboard');
	$front->setDefaultAction('index');
} else {
	if (count($argv) == 2 and $argv[1] == 'dispatch') {
		$front->setDefaultControllerName('test');
		$front->setDefaultAction('dispatch');
	} elseif (count($argv) == 3 and ($argv[1] == 'start' or $argv[1] == 'open')) {
		$request->setParam('test-id', $argv[2]);
		$front->setDefaultControllerName('test');
		$front->setDefaultAction('open');

	} elseif (count($argv) == 3 and ($argv[1] == 'stop' or $argv[1] == 'close')) {
		$request->setParam('test-id', $argv[2]);
		$front->setDefaultControllerName('test');
		$front->SetDefaultAction('close');
	} else {
		usage();
		die();
	}
}

$front->setRequest($request);
$front->getPlugin('GN_Plugin_Acl')->setRoleName(Model_Users::ROLE_CLI);

$application->run();
