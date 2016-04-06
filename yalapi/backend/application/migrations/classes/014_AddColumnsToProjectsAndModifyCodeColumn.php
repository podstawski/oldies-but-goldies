<?php

class AddColumnsToTraningCentersAndModifyCodeColumn extends Doctrine_Migration_Base
{
    private $_tableName = 'projects';

    public function up()
    {
        $this->addColumn($this->_tableName, 'start_date', 'date', null, array('notnull' => true, 'default' => '1970-01-01'));
        $this->addColumn($this->_tableName, 'end_date', 'date', null, array('notnull' => true, 'default' => '1970-01-01'));
        $this->changeColumn($this->_tableName, 'code', 'varchar', 256, array('notnull' => true));
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("UPDATE $this->_tableName SET code = SUBSTRING(code from 1 for 5)");
        $this->removeColumn($this->_tableName, 'start_date');
        $this->removeColumn($this->_tableName, 'end_date');
        $this->changeColumn($this->_tableName, 'code', 'varchar', 5, array('notnull' => true));
    }
}
