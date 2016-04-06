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

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('APPLICATION_ENV', 'development');

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
$modelGameServer = new Model_GameServer;
foreach ($modelGameServer->fetchAll() as $gameServer)
    $databases[] = $gameServer->name;

require_once __DIR__ . '/../library/Doctrine/Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));

$version = @$argv[1];

echo 'Performing migrations...' . PHP_EOL;

foreach (array_unique($databases) as $gameServerName)
{
    try {
        $conn = Doctrine_Manager::connection(Game_Server_Db::factory($gameServerName)->getConnection());

        $migration = new Doctrine_Migration(__DIR__ . '/../application/migrations/classes', $conn);
        $migration->setTableName('doctrine_migration_version');

        if ($version === null || $version < 0)
            $version = array_pop(array_keys($migration->getMigrationClasses()));

        if ($migration->getCurrentVersion() != $version) {
            $migration->migrate($version);
            echo $gameServerName . ': database succesfully migrated to version ' . $migration->getCurrentVersion() . PHP_EOL;
        } else {
            echo $gameServerName . ': database already at version ' . $migration->getCurrentVersion() . PHP_EOL;
        }
        $conn->close();

    } catch (Exception $e) {
        die($e->getMessage());
    }
}


?>
