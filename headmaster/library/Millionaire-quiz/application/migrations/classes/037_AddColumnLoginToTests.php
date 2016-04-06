<?php

class AddColumnLoginToTests extends Doctrine_Migration_Base
{
	private $_tableName = 'tests';
	private $_columnName1 = 'login';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_columnName1, 'integer', null, array(
			'default' => 0
		));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_columnName1);
	}
}
