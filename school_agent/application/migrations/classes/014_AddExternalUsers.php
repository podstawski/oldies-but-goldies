<?php
class AddExternalUsers extends Doctrine_Migration_Base
{
	private $_tableName1 = 'domains';
	private $_colName1 = 'org_name';
	private $_colName2 = 'oauth_token';
	private $_tableName2 = 'users';
	private $_colName3 = 'admin';

	public function up()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName1 . ' ALTER COLUMN ' . $this->_colName1 . ' DROP NOT NULL');
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName1 . ' ALTER COLUMN ' . $this->_colName2 . ' DROP NOT NULL');
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName2 . ' ADD COLUMN '.$this->_colName3 .' Integer DEFAULT 0');
	}

	public function down()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName1 . ' ALTER COLUMN ' . $this->_colName1 . ' SET NOT NULL');
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName1 . ' ALTER COLUMN ' . $this->_colName2 . ' SET NOT NULL');
		$this->removeColumn($this->_tableName3, $this->_colName3);
	}
}
?>
