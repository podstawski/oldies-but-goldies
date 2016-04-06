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

$config2 = new Zend_Config_Ini(APPLICATION_PATH . '/configs/local.ini', APPLICATION_ENV);
$config2 = $config2->toArray();

$config = array_merge($config,$config2);


$application = new Zend_Application(APPLICATION_ENV, $config);
$application->bootstrap();

$dbUser = new Model_User;
$user = $dbUser->findByMail('bohdan.bobrowski@gammanet.pl');
$client = GN_Gapps::getClientFromToken($user->access_token,$config['googleapps']['consumerKey'],$config['googleapps']['consumerSecret']);

if (!isset($argv[1]))
{
    $lista = GN_Gapps::getSpreadsheetList($client);
    
    echo $argv[0]." ID [nazwa_arkusza]\n   lista ID-ków:\n";
    foreach ($lista AS $k=>$v) echo "      - $k  [$v]\n";
}
else
{
    if (!isset($argv[2])) $argv[2]='';
    $arr=GN_Gapps::getWorksheet($client,$argv[1],$argv[2]);
    print_r($arr);
}
