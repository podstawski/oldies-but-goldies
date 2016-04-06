<?php
class AddSettingsToDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'settings';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'text', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
