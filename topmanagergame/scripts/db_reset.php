<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 *
 * @var PDO $pdo
 */

$pdo = include __DIR__ . '/db_pdo.php';
$pdo->exec('TRUNCATE TABLE users CASCADE');
$pdo->exec('TRUNCATE TABLE school CASCADE');
$pdo->exec('TRUNCATE TABLE analyst');
$pdo->exec('UPDATE day SET day = 1');
//$pdo->prepare('DELETE FROM game_data WHERE key = ?')->execute(array('LOGIN_EMAILS'));