<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableSurveyUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_users';
    private $_fkName = 'fk_survey_users_survey';
    private $_fkName2 = 'fk_survey_users_user';
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
            'filled' => array(
                'type' => 'smallint'
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local'         => 'survey_id',
            'foreign'       => 'id',
            'foreignTable'  => 'surveys',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
            'local'         => 'user_id',
            'foreign'       => 'id',
            'foreignTable'  => 'users',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
