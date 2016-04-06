<?php
class ExpirationFields extends Doctrine_Migration_Base
{
	private $_tableName1 = 'domains';
	private $_tableName2 = 'users';
	private $_colName1 = 'expire';
	private $_colName2 = 'trial_count';

	public function up() {
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName1 . ' ADD COLUMN ' . $this->_colName1 . ' Timestamp DEFAULT CURRENT_TIMESTAMP');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName1 . ' ADD COLUMN ' . $this->_colName2 . ' Integer DEFAULT 0');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName2 . ' ADD COLUMN ' . $this->_colName1 . ' Timestamp DEFAULT CURRENT_TIMESTAMP');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName2 . ' ADD COLUMN ' . $this->_colName2 . ' Integer DEFAULT 0');
	}

	public function down() {
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName2 . ' DROP COLUMN ' . $this->_colName2 );
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName2 . ' DROP COLUMN ' . $this->_colName1 );
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName1 . ' DROP COLUMN ' . $this->_colName2 );
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName1 . ' DROP COLUMN ' . $this->_colName1 );
	}
}
