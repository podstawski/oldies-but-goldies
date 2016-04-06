<?php

/**
 * Simple script for applying fixtures
 * @author Radosław Benkel
 */

require_once __DIR__ . '/../../GN/FixtureLoader.php';

$config = include __DIR__ . '/db_config.php';

$dsn = $config['db.adapter'] . ':host='
     . $config['db.host'] . ';dbname='
     . $config['db.dbname'];

try {
    $pdo = new PDO($dsn, $config['db.username'], $config['db.password']);

    $loader = new FixtureLoader($pdo, __DIR__ . '/../../../application/migrations/fixtures/');
    $msg = $loader->applyFixtures();
    echo $msg . "\n";

} catch (Exception $e) {
    die($e->getMessage());
}

?>
