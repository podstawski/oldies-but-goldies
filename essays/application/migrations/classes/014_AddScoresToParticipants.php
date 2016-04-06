<?php
class AddScoresToParticipants extends Doctrine_Migration_Base
{
	private $_tableName = 'participants';
	private $_colName = 'score';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'varchar(10)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
