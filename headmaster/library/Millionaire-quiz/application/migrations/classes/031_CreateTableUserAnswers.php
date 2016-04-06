<?php

class CreateUserAnswers extends Doctrine_Migration_Base
{
    private $_tableName = 'user_answers';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'profile_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'question_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'answer_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'is_joker' => array(
                'type' => 'smallint',
                'notnull' => true
            ),
            'is_correct' => array(
                'type' => 'smallint',
                'notnull' => true
            ),
            'level_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'region_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'subject_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'time' => array(
                'type' => 'timestamp',
                'notnull' => true
            )
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN time SET DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
