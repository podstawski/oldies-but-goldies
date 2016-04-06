<?php
class AddResultsToActionSteps extends Doctrine_Migration_Base
{
	private $_tableName = 'action_steps';
	private $_colName = 'result';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'integer', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
