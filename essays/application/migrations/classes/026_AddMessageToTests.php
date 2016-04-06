<?php
class AddMessageToTests extends Doctrine_Migration_Base
{
	private $_tableName = 'tests';
	private $_colName1 = 'message';


	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName1, 'Text', null, array('notnull' => false));

	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName1);

	}
}

