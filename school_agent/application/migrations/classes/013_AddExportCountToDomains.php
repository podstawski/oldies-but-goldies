<?php
class AddExportCountToDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'export_count';

	public function up()
	{
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN '.$this->_colName.' Integer DEFAULT 0');
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
