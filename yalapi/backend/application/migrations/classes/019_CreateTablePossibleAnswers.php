<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTablePossibleAnswers extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_possible_answers';
    private $_fkName = 'survey_question_id_fk';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'question_id' => array(
                'type' => 'integer'
            ),
            'content' => array(
                'type' => 'varchar(256)'
            ),
            'correct' => array(
                'type' => 'smallint'
            ),
            'selected_by_default' => array(
                'type' => 'smallint'
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local'         => 'question_id',
            'foreign'       => 'id',
            'foreignTable'  => 'survey_questions',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
