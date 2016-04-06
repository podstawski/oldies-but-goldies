<?php

class AddColumnPointsToProfiles extends Doctrine_Migration_Base
{
    private $_tableName = 'profiles';
    private $_columnName1 = 'points';
    private $_columnName2 = 'cash';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer');
        $this->addColumn($this->_tableName, $this->_columnName2, 'integer');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
        $this->removeColumn($this->_tableName, $this->_columnName2);
    }
}
