<?php

class AddColumnsToSurveys extends Doctrine_Migration_Base
{
    private $_tableName = 'surveys';

    public function up()
    {
        $this->addColumn($this->_tableName, 'type', 'varchar', 256, array('notnull' => false));
        $this->addColumn($this->_tableName, 'deadline', 'date');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, 'type');
        $this->removeColumn($this->_tableName, 'deadline');
    }
}
