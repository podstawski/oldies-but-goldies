<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableSurveyDetailedResults extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_detailed_results';
    private $_fkName = 'survey_survey_result';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'survey_result_id' => array(
                'type' => 'integer'
            ),
            'question_id' => array(
                'type' => 'integer'
            ),
            'answer_id' => array(
                'type' => 'integer'
            ),
            'answer_content' => array(
                'type' => 'text'
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local'         => 'survey_result_id',
            'foreign'       => 'id',
            'foreignTable'  => 'survey_results',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
