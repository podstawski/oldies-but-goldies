<?php
/**
 * Description
 * @author Radosław Benkel
 */
 
class CreateTableQuizzes extends Doctrine_Migration_Base
{
    private $_tableName = 'quizzes';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'text',
                'notnull' => false
            ),
            'time_limit' => array(
                'type' => 'int',
                'notnull' => true,
            ),
            'url' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
