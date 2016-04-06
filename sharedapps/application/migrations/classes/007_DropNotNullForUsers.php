<?php
class DropNotNullForDomainTokens extends Doctrine_Migration_Base
{
	private $_tableName = 'users';
	private $_colName1 = 'name';
	private $_colName2 = 'identity';

	public function up() {
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName1 . ' DROP NOT NULL');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName2 . ' DROP NOT NULL');
	}

	public function down() {
		Doctrine_Manager::connection()->exec('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName1 . ' = \'\' WHERE ' . $this->_colName1 . ' IS NULL');
		Doctrine_Manager::connection()->exec('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName2 . ' = \'\' WHERE ' . $this->_colName2 . ' IS NULL');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName1 . ' SET NOT NULL');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName2 . ' SET NOT NULL');
	}
}
