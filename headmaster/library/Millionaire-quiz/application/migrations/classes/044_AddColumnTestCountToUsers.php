<?php

class AddColumnTestCountToUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_columnName1 = 'test_count';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer', 256, array(
			'notnull' => 1,
			'default' => 0
		));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
