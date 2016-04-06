<?php

/**
 * Simple script for applying fixtures
 * @author Radosław Benkel
 */

require_once __DIR__ . '/../library/FixtureLoader.php';

$config = include __DIR__ . '/db_config.php';

if (isset($argv[1])) {
    $config['db.dbname'] = (!empty($config['db.prefix']) ? $config['db.prefix'] . '_' : '') . $argv[1];
}
$dsn =  $config['db.adapter'] . ':host=' . $config['db.host'] . ';dbname=' . $config['db.dbname'];

try {
    $pdo = new PDO($dsn, $config['db.username'], $config['db.password']);

    $loader = new FixtureLoader($pdo, __DIR__ . '/../application/migrations/fixtures/');
    $msg = $loader->applyFixtures();
    echo $msg . "\n";

} catch (Exception $e) {
    die($e->getMessage());
}

?>
