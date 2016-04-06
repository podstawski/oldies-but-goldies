<?php
class AddLanguageToUsers extends Doctrine_Migration_Base
{
	private $_tableName = 'users';
	private $_colName = 'language';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'varchar(6)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
