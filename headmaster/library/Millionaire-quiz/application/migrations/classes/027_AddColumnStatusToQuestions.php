<?php

class AddColumnsStatusToQuestions extends Doctrine_Migration_Base
{
    private $_tableName = 'questions';
    private $_columnName1 = 'status';
    private $_columnName2 = 'flag';
    private $_columnName3 = 'flag_data';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer');
        $this->addColumn($this->_tableName, $this->_columnName2, 'integer');
        $this->addColumn($this->_tableName, $this->_columnName3, 'text');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
