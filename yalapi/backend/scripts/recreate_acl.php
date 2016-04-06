<?php

define('APPLICATION_ENV', 'development');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$databases = array();
$googleapps = Zend_Registry::get('oauth_options');
$dbname = Yala_User::getDbname();

if ($googleapps['singledb'] == true) {
    $databases[] = $dbname;
} else if (isset($argv[1])) {
    $domain = $argv[1];
    $databases[$domain] = Yala_User::getDbname($domain);
} else {
    $databases[] = $dbname;

    Yala_User::init();
    Yala_User::setIdentity('yala');

    foreach (App::all() as $appRow) {
        $databases[$appRow->domain] = Yala_User::getDbname($appRow->domain);
    }
}

foreach ($databases as $domain => $dbname)
{
    Yala_User::init(null, $domain);
    Yala_User::setIdentity('admin');
    Acl::after_startup();
    Acl::recreateDefault();
    Acl::before_end();
    echo 'Recreated ACL: ' . $dbname . PHP_EOL;
}
