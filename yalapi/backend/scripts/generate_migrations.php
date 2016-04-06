<?php

$functions = array(
    'acl_has_right',
    'acl_delete_cascade',
    'acl_insert_cascade',
    'create_acl_table',
    'create_acl_view',
    'drop_acl_view',
    'update_password',
    'create_user',
    'delete_user'
);

$tables = array(
    'roles',
    'poland',
    'training_centers',
    'rooms',
    'users',
    'user_profile',
    'groups',
    'group_users',
    'projects',
    'courses',
    'course_units',
    'lessons',
    'resource_types',
    'resources',
    'reports',
    'files',
    'messages',
    'message_users',
    'message_attachments',
    'quizzes',
    'quiz_users',
    'quiz_scores',
    'surveys',
    'survey_users',
    'survey_questions',
    'survey_possible_answers',
    'survey_results',
    'survey_detailed_results',
    'lesson_presence',
    'exams',
    'exam_grades',
    'course_schedule',
    'google_tokens',
);

$config = include __DIR__ . '/db_config.php';
$path   = __DIR__ . '/../application/migrations/generated';

@rmdir($path);
@mkdir($path);

$dsn = sprintf("%s:host=%s;dbname=%s", $config['db.adapter'], $config['db.host'], $config['db.dbname']);
$pdo = new PDO($dsn, $config['db.username'], $config['db.password']);

$stmtFunctions   = $pdo->prepare("SELECT pg_get_functiondef(oid) FROM pg_proc WHERE proname IN ('" . implode("','", $functions) . "')");
$stmtColumns     = $pdo->prepare("SELECT * from INFORMATION_SCHEMA.COLUMNS WHERE table_name = ?");
$stmtConstraints = $pdo->prepare("SELECT tc.constraint_name,
    tc.constraint_type,
    tc.table_name,
    kcu.column_name,
    rc.update_rule AS on_update,
    rc.delete_rule AS on_delete,
    ccu.table_name AS foreign_table,
    ccu.column_name AS foreign_name
    FROM information_schema.table_constraints tc
    LEFT JOIN information_schema.key_column_usage kcu ON tc.constraint_catalog = kcu.constraint_catalog AND tc.constraint_schema = kcu.constraint_schema AND tc.constraint_name = kcu.constraint_name
    LEFT JOIN information_schema.referential_constraints rc ON tc.constraint_catalog = rc.constraint_catalog AND tc.constraint_schema = rc.constraint_schema AND tc.constraint_name = rc.constraint_name
    LEFT JOIN information_schema.constraint_column_usage ccu ON rc.unique_constraint_catalog = ccu.constraint_catalog AND rc.unique_constraint_schema = ccu.constraint_schema AND rc.unique_constraint_name = ccu.constraint_name
    WHERE tc.table_name = ?
");

echo "This is simer's migrations generation script!\n";

$stmtFunctions->execute();
$dbFunctions = $stmtFunctions->fetchAll(PDO::FETCH_COLUMN);
echo "Found " . count($dbFunctions) . " functions...\n";
$code = <<<CODE
<?php
class CreateAclFunctions extends Doctrine_Migration_Base
{
    private \$_aclFunctions = array(

CODE;

    foreach ($dbFunctions as $query)
    {
        $query = addslashes(str_replace('$function$', '$$', $query));
        $code .= <<<CODE
"$query",

CODE;
    }

    $code .= <<<CODE
    );

    public function up()
    {
        foreach (\$this->_aclFunctions as \$query) {
            Doctrine_Manager::connection()->execute(stripslashes(\$query));
        }
    }

    public function down()
    {

    }
}
CODE;

file_put_contents($path . '/001_CreateAclFunctions.php', $code);
echo "* migration 001: CreateAclFunctions - done!\n";

echo "Found " . count($tables) . " tables...\n";
foreach ($tables as $k => $tableName)
{
    $stmtColumns->execute(array($tableName));
    $stmtConstraints->execute(array($tableName));

    $defaults = array();
    $constraints = array();

    $className = 'CreateTable';
    foreach (explode('_', $tableName) as $part) {
        $className .= ucfirst($part);
    }

    $code = <<<CODE
<?php

class $className extends Doctrine_Migration_Base
{
    public function up()
    {
        \$this->createTable('$tableName', array(

CODE;

    foreach ($stmtColumns->fetchAll(PDO::FETCH_OBJ) as $columnSchema)
    {
        $name = $columnSchema->column_name;
        $type = $columnSchema->data_type;
        if ($columnSchema->character_maximum_length != null) {
            $type .= '(' . $columnSchema->character_maximum_length . ')';
        }
        $notnull = $columnSchema->is_nullable == 'YES' ? 'false' : 'true';

        $code .= <<<CODE
            '$name' => array(
                'type' => '$type',
                'notnull' => $notnull,

CODE;

        if ($columnSchema->column_default != null) {
            if (substr($columnSchema->column_default, 0, 7) == 'nextval') {
                $code .= <<<CODE
                'primary' => true,
                'autoincrement' => true,

CODE;
            } else {
                $defaults[$name] = $columnSchema->column_default;
            }
        }

        $code .= <<<CODE
            ),

CODE;
    }

    $code .= <<<CODE
        ));

CODE;

    foreach ($stmtConstraints->fetchAll(PDO::FETCH_OBJ) as $constraint)
    {
        if (!isset($constraints[$constraint->constraint_type])) {
            $constraints[$constraint->constraint_type] = array();
        }

        $t = & $constraints[$constraint->constraint_type][$constraint->constraint_name];
        switch ($constraint->constraint_type)
        {
            case 'FOREIGN KEY':
                $t = $constraint;
                break;

            case 'UNIQUE':
                $t[] = $constraint->column_name;
                break;
        }
    }

    foreach ($constraints as $constraintType => $constraintArr)
    {
        foreach ($constraintArr as $constraintName => $constraint)
        {
            switch ($constraintType)
            {
                case 'FOREIGN KEY':
                    $code .= <<<CODE

        \$this->createForeignKey('$tableName', '$constraintName', array(
             'local'         => '$constraint->column_name',
             'foreign'       => '$constraint->foreign_name',
             'foreignTable'  => '$constraint->foreign_table',
             'onDelete'      => '$constraint->on_delete',
             'onUpdate'      => '$constraint->on_update'
        ));

CODE;
                    break;

                case 'UNIQUE':
                    $code .= <<<CODE

        \$this->createConstraint('$tableName', '$constraintName', array(
            'unique' => true,
            'fields' => array(

CODE;
                    foreach ($constraint as $columnName) {
                        $code .= <<<CODE

                '$columnName' => null,

CODE;
                    }
                    $code .= <<<CODE

            ),
        ));

CODE;
                    break;
            }
        }
    }

    $code .= <<<CODE
    }

    public function postUp()
    {

CODE;

    foreach ($defaults as $columnName => $defaultValue) {
        $code .= <<<CODE

        Doctrine_Manager::connection()->execute("ALTER TABLE $tableName ALTER COLUMN $columnName SET DEFAULT $defaultValue");

CODE;
    }

    $code .= <<<CODE

        Doctrine_Manager::connection()->execute("SELECT create_acl_table('$tableName')");
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute("SELECT drop_acl_table('$tableName')");
    }

    public function down()
    {

CODE;

    foreach ($constraints as $constraintType => $constraintArr)
    {
        foreach ($constraintArr as $constraintName => $constraint)
        {
            switch ($constraintType)
            {
                case 'FOREIGN KEY':
                    $code .= <<<CODE

        \$this->dropForeignKey('$tableName', '$constraintName');

CODE;
                    break;

                case 'UNIQUE':
                    $code .= <<<CODE

        \$this->dropConstraint('$tableName', '$constraintName');

CODE;
                        break;

            }
        }
    }

    $code .= <<<CODE

        \$this->dropTable('$tableName');
    }
}

CODE;

    $migrationNumber = str_pad(($k + 2), 3, '0', STR_PAD_LEFT);
    $fileName = sprintf('%s_%s.php', $migrationNumber, $className);
    file_put_contents($path . '/' . $fileName, $code);
    echo sprintf("* migration %s: %s - done!\n", $migrationNumber, $className);
}

echo "Finished creating migration files.\n";
exit;


