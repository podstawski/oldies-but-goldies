<?php

class AddColumnVirginityToTests extends Doctrine_Migration_Base
{
	private $_tableName = 'tests';
	private $_columnName1 = 'virginity';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_columnName1, 'boolean', null, array(
			'default' => true
		));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_columnName1);
	}
}
