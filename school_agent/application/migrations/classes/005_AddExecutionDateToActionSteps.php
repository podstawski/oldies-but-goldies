<?php
class AddExecutionDateToActionSteps extends Doctrine_Migration_Base
{
	private $_tableName = 'action_steps';
	private $_colName = 'date_executed';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'timestamp', null, array('notnull' => false));;
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
