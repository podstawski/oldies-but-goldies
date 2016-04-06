<?php

class AddColumnActiveFromToTests extends Doctrine_Migration_Base
{
	private $_tableName = 'tests';
	private $_columnName1 = 'active_from';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_columnName1, 'timestamp', null, array(
			'default' => null
		));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_columnName1);
	}
}
