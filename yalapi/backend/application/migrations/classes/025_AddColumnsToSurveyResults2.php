<?php

class AddColumnsToSurveyResults2 extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_results';

    public function up()
    {
       $this->addColumn($this->_tableName, "created", "timestamp", null);
    }
    public function down(){
         $this->removeColumn($this->_tableName, "created");
    }
}



