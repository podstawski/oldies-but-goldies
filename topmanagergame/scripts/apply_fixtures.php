<?php

/**
 * Simple script for applying fixtures
 * @author Radosław Benkel
 */

require_once __DIR__ . '/../library/FixtureLoader.php';

try {
    $pdo = include __DIR__ . '/db_pdo.php';

    $loader = new FixtureLoader($pdo, __DIR__ . '/../application/migrations/fixtures/');
    $msg = $loader->applyFixtures();
    echo $msg . "\n";

} catch (Exception $e) {
    die($e->getMessage());
}

?>
