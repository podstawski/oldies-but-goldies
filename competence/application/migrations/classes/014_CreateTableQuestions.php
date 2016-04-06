<?php

class CreateTableQuestions extends Doctrine_Migration_Base
{
    private $_tableName = 'questions';
    private $_fkName1 = 'fk_questions_competencies';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'competence_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'question' => array(
                'type' => 'character varying',
                'notnull' => true,
            ),
            'default_value' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'active' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'competence_id',
             'foreign'       => 'id',
             'foreignTable'  => 'competencies',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));


    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
