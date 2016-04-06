<?php
class DropNotNullForDomains extends Doctrine_Migration_Base
{
	private $_tableName = 'domains';
	private $_colName = 'oauth_token';

	public function up() {
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' DROP NOT NULL');
	}

	public function down() {
		Doctrine_Manager::connection()->exec('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = \'\' WHERE ' . $this->_colName . ' IS NULL');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET NOT NULL');
	}
}
