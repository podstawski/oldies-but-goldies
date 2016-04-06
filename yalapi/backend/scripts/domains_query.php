<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

list (, $sql, $domain) = $argv;

if (empty($sql))
    die('SQL is empty');

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
    $databases[$dbname] = $dbname;
} else if ($domain) {
    $databases[$domain] = Yala_User::getDbname($domain);
} else {
    $databases[$dbname] = $dbname;
    Yala_User::init();
    Yala_User::setIdentity('yala');
    foreach (App::all() as $appRow)
        $databases[$appRow->domain] = Yala_User::getDbname($appRow->domain);

}

foreach ($databases as $domain => $dbname) {
    Yala_User::init(null, $domain);
    Yala_User::setIdentity('admin');
    echo "Domain: " . $dbname . ", rows affected: " . AppModel::connection()->query($sql)->rowCount() . PHP_EOL;
}