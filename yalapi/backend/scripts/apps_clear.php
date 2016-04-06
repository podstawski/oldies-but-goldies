<?php
$config = include 'db_config.php';

$dsn =  $config['db.adapter'] . ':host=' . $config['db.host'] . ';dbname=' . $config['db.dbname'];

try {
    $pdo = new PDO($dsn, $config['db.username'], $config['db.password']);
    $sql = 'SELECT DISTINCT domain FROM apps';
	if (isset($argv[1])) {
        $sql .= " WHERE domain ~ '" . $argv[1] . "'";
    }
    $prefix = !empty($config['db.prefix']) ? $config['db.prefix'] . '_' : '';
	foreach ($pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN) AS $domain)
	{
		echo 'Usunąć domenę ' . $domain . '? ';
		$handle = fopen('php://stdin', 'r');
		$line = fgets($handle);
		fclose($handle);
		if (strtolower(substr(trim($line), 0, 1)) != 't') {
            continue;
        }
		$dbname = $prefix . preg_replace('/[^a-zA-Z0-9]/', '_', $domain);
		$sql = '';
		foreach ($pdo->query('SELECT usename FROM pg_user WHERE usename LIKE \'' . $dbname . '_%\'')->fetchAll(PDO::FETCH_COLUMN) AS $username) {
            $sql .= 'DROP USER "' . $username . '";' . PHP_EOL;
        }
		$sql .= 'DELETE FROM apps WHERE domain = \'' . $domain . '\';' . PHP_EOL;
		$pdo->exec($sql);
		$pdo->exec('DROP DATABASE ' . $dbname);
	}
} catch (Exception $e) {
    die($e->getMessage());
}

