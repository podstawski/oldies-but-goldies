<?php

class AddColumnsUserIdToCategories extends Doctrine_Migration_Base
{
	private $_tableName = 'categories';
	private $_columnName1 = 'user_id';
	private $_columnName2 = 'status';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_columnName1, 'integer', 256, array(
			'notnull' => 1,
			'default' => 0
		));
		$this->addColumn($this->_tableName, $this->_columnName2, 'integer', 256, array(
			'notnull' => 1,
			'default' => 0
		));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_columnName1);
		$this->removeColumn($this->_tableName, $this->_columnName2);
	}
}
