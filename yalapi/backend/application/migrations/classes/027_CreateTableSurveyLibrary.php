<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableSurveyLibrary extends Doctrine_Migration_Base
{
    private $_tableName = 'surveys_library';
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
            'user_id' => array(
                'type' => 'integer'
            ),
            'readonly' => array(
                'type' => 'smallint'
            )
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
