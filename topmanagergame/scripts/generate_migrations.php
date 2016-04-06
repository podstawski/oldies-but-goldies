<?php

$tables = array(
    'day',
    'user',
    'company',
    'balance',
    'employee',
    'product',
    'queue',
);

$config = include __DIR__ . '/db_config.php';
$path   = __DIR__ . '/../application/migrations/generated';
//echo '<pre>';print_r($config['resources.db.adapter']);die();
@rmdir($path);
@mkdir($path);

$dsn = sprintf("%s:host=%s;dbname=%s", $config['resources.db.adapter'], $config['resources.db.params.host'], $config['resources.db.params.dbname']);
$pdo = new PDO($dsn, $config['resources.db.params.username'], $config['resources.db.params.password']);

echo "This is simer's migrations generation script!\n";

echo "Found " . count($tables) . " tables...\n";
foreach ($tables as $k => $tableName)
{
    $className = 'CreateTable';
    foreach (explode('_', $tableName) as $part) {
        $className .= ucfirst($part);
    }
    $sql = $pdo->query('SHOW CREATE TABLE ' . $tableName)->fetchColumn(1);
    echo '<pre>';print_r($sql);die();
    $code = <<<CODE
<?php

class $className extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->exec('

CODE;


}

echo "Finished creating migration files.\n";
exit;


