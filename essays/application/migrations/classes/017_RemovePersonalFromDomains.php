<?php
class RemovePersonalFromDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'personal';

	public function up()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}

	public function down()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'smallint default 0', null, array('notnull' => false));
	}
}
