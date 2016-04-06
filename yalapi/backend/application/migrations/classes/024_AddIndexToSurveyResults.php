<?php

class AddColumnsToSurveyResults extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_results';
    private $_tableName2 = 'survey_detailed_results';

    public function up()
    {
        $definition = array(
            'fields' => array(
                'user_id' => array(),
                'survey_id' => array()
            ),
            'unique' => true
        );
        $this->createConstraint($this->_tableName, 'unique_user_survey_ids', $definition);

        $definition = array(
            'fields' => array(
                'answer_id' => array(),
                'survey_result_id' => array(),
                'question_id' => array()
            ),
            'unique' => true
        );
        $this->createConstraint($this->_tableName2, 'unique_user_survey_answer_ids', $definition);

    }

    public function down()
    {
        $this->dropConstraint($this->_tableName, 'unique_user_survey_ids_idx');
        $this->dropConstraint($this->_tableName2, 'unique_user_survey_answer_ids_idx');

    }
}



