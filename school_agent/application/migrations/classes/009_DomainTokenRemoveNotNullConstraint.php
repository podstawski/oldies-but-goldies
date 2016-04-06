<?php
class DomainTokenRemoveNotNullConstraint extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'token';

	public function up()
	{
		// Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' DROP NOT NULL');
	}

	public function down()
	{
		// Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET NOT NULL');
	}
}
?>
