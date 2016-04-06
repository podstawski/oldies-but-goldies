<?php

class AddColumnTestDataToAttempts extends Doctrine_Migration_Base
{
	private $_tableName = 'attempts';
	private $_columnName1 = 'test_data';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_columnName1, 'text', null, array(
			'notnull' => 0,
			'default' => null
		));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_columnName1);
	}
}
