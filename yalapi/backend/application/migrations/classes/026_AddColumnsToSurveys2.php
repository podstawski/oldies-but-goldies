<?php

class AddColumnsToSurveys2 extends Doctrine_Migration_Base
{
    private $_tableName = 'surveys';

    public function up()
    {
        $this->addColumn($this->_tableName, 'archived', 'smallint');
        $this->addColumn($this->_tableName, 'project_id', 'int');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, 'archived');
        $this->removeColumn($this->_tableName, 'project_id');
    }
}
