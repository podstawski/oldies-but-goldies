<?php

$config = include __DIR__ . '/db_config.php';

$dsn = $config['db.adapter'] . ':host='
     . $config['db.host'] . ';dbname='
     . $config['db.dbname'];

return new PDO($dsn, $config['db.username'], $config['db.password']);