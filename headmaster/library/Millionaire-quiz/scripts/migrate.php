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

$config = include __DIR__ . '/db_config.php';

$dsn = $config['db.adapter'] . ':host='
     . $config['db.host'] . ';dbname='
     . $config['db.dbname'];

try {

    $pdo = new PDO($dsn, $config['db.username'], $config['db.password']);

    require_once __DIR__ . '/../../Doctrine/Doctrine.php';
    spl_autoload_register(array('Doctrine', 'autoload'));
    $conn = Doctrine_Manager::connection($pdo);

    $migration = new Doctrine_Migration(__DIR__ . '/../application/migrations/classes', $conn);
    $migration->setTableName('doctrine_migration_version');

    if (isset($argv[1])) {
        $version = intval($argv[1]);
    } else {
        $classesKeys = array_keys($migration->getMigrationClasses());
        $version = array_pop($classesKeys);
    }


    $current = $migration->getCurrentVersion();

    if ($version < $current) {
        for ($i = $current - 1; $i >= $version; --$i) {
            $migration->migrate($i);
        }
    } else {
        for ($i = $current + 1; $i <= $version; ++$i) {
            $migration->migrate($i);
        }
    }

    $currentVersion = $migration->getCurrentVersion();
    echo 'Migrated succesfully to version ' . $currentVersion . "\n";


  
} catch (Exception $e) {
    die($e->getMessage());
}

?>
