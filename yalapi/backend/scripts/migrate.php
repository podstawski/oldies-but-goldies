<?php

/**
 * Migration runner, usage:
 *
 * php migrate.php [version number] [environment]
 *
 * php migrate.php => upgrading db to newest version
 * php migrate.php [version number] => upgrading/downgrading database to specified version number
 * php migrate.php test => upgrading database defined in application.ini in "testing : development" section
 * php migrate.php [version number] [env] => as above, but to specified version number
 *
 * @author Radosław Benkel
 */

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
$dbconfig = $application->getBootstrap()->getOption('db');
$dbname = Yala_User::getDbname();

if ($googleapps['singledb'] == true) {
    $databases[] = $dbname;
} else if (isset($argv[2])) {
    $domain = $argv[2];
    $databases[$domain] = Yala_User::getDbname($domain);
} else {
    $databases[] = $dbname;

    Yala_User::init();
    Yala_User::setIdentity('yala');

    foreach (App::all() as $appRow) {
        $databases[$appRow->domain] = Yala_User::getDbname($appRow->domain);
    }
}

try {

    require_once __DIR__ . '/../library/Doctrine/Doctrine.php';
    spl_autoload_register(array('Doctrine', 'autoload'));

    $version = @$argv[1];

    echo 'Performing migrations...' . PHP_EOL;

    foreach ($databases as $domain => $dbname)
    {
        $dsn = $dbconfig['adapter'] . ':host=' . $dbconfig['host'] . ';dbname=' . $dbname;
        $pdo = new PDO($dsn, $dbconfig['username'], $dbconfig['password']);
        $conn = Doctrine_Manager::connection($pdo);
        $migration = new Doctrine_Migration(__DIR__ . '/../application/migrations/classes', $conn);
        $migration->setTableName('doctrine_migration_version');

        if ($version === null || $version < 0) {
            $version = array_pop(array_keys($migration->getMigrationClasses()));
        }

        if ($migration->getCurrentVersion() != $version) {
            $migration->migrate($version);
            echo 'Database succesfully migrated to version ' . $migration->getCurrentVersion() . ': ' . $dbname . PHP_EOL;
        } else {
            echo 'Database already at version ' . $migration->getCurrentVersion() . ': ' . $dbname . PHP_EOL;
        }
        $conn->close();
    }

} catch (Exception $e) {
    die('Database ' . $dbname . ':' . PHP_EOL . $e->getMessage());
}