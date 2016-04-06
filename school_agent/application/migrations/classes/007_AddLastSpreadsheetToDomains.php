<?php
class AddLastSpreadsheetToDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'last_spreadsheet';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'varchar(255)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
