<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

list (, $gameServerName, $gameServerAdmin) = $argv;

if (empty($gameServerName))
    die('please provide game name');

if (empty($gameServerAdmin))
    die('please provide game admin e-mail');

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

$modelGameServer = new Model_GameServer;
$gameServer = $modelGameServer->fetchGameServer($gameServerName);

if ($gameServer)
    die("Server '$gameServerName' already exists.'");

$mainDB = $modelGameServer->getAdapter();
$gameDB = Game_Server_Db::factory($gameServerName);

$gameDBname = $gameDB->getConfig();
$gameDBname = $gameDBname['dbname'];

try {
    if ($gameServerName != Game_Server::DEFAULT_NAME) {
        $mainDB->query('CREATE DATABASE ' . $gameDBname);

        require_once APPLICATION_PATH . '/../library/Doctrine/Doctrine.php';
        spl_autoload_register(array('Doctrine', 'autoload'));
        $connection = Doctrine_Manager::connection($gameDB->getConnection());
        $migration = new Doctrine_Migration(APPLICATION_PATH . '/migrations/classes', $connection);
        $migration->setTableName('doctrine_migration_version');
        $migration->migrate();

        $modelGameServer = new Model_GameServer($gameDB);
        $gameServer = $modelGameServer->createGameServer($gameServerName, $gameServerAdmin);
    }

    $modelGameServer = new Model_GameServer($mainDB);
    $gameServer = $modelGameServer->createGameServer($gameServerName, $gameServerAdmin);

    die("Server '$gameServerName' created." . PHP_EOL);
} catch (Exception $e) {
    if ($gameServerName != Game_Server::DEFAULT_NAME) {
        $mainDB->query('DROP DATABASE IF EXISTS ' . $gameDBname);
    }
    die($e->getMessage() . PHP_EOL);
}