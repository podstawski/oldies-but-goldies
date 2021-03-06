<?php
class AddGroupToTests extends Doctrine_Migration_Base
{
	private $_tableName = 'tests';
	private $_colName = 'group_name';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'varchar(255)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
