<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableQuestions extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_questions';
    private $_fkName = 'survey_survey_question';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'survey_id' => array(
                'type' => 'integer'
            ),
            'type' => array(
                'type' => 'varchar(256)'
            ),
            'title' => array(
                'type' => 'varchar(256)'
            ),
            'help' => array(
                'type' => 'varchar(256)'
            ),

            'required' => array(
                'type' => 'smallint'
            ),
            'position' => array(
                'type' => 'integer'    ,
                'default'=>0
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local'         => 'survey_id',
            'foreign'       => 'id',
            'foreignTable'  => 'surveys',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
