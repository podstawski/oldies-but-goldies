<?php

class AddIndexToSurveysUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_users';
    private $_constraintName = 'uq_survey_for_user';

    public function up()
    {
        $this->createConstraint($this->_tableName, $this->_constraintName, array(
            'fields' => array(
               'user_id' => array(),
               'survey_id' => array()
            ),
            'unique' => true
        ));
    }

    public function down()
    {
        $this->dropConstraint($this->_tableName, $this->_constraintName . '_idx');
    }
}







