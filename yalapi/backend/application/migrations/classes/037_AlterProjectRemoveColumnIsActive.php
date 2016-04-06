<?php

class AlterProjectRemoveColumnIsActive extends Doctrine_Migration_Base
{
    private $_tableName  = 'projects';
    private $_columnName = 'is_active';

    public function up()
    {
        $this->removeColumn($this->_tableName, $this->_columnName);
    }

    public function down()
    {
        $this->addColumn($this->_tableName, $this->_columnName, 'integer', null, array('notnull' => true, 'default' => 1));
    }
}



