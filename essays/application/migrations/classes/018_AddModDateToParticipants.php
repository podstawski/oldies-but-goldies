<?php
class AddModDateToParticipants extends Doctrine_Migration_Base
{
	private $_tableName = 'participants';
	private $_colName = 'date_modified';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'timestamp', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
