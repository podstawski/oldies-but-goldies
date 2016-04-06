<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableSurveyResults extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_results';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'user_id' => array(
                'type' => 'integer'
            ),
            'survey_id' => array(
                'type' => 'integer'
            ),
            'percent_result' => array(
                'type' => 'float'
            ),
            'completed'=>array(
                'type' => 'smallint'
            )
        ));

    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
