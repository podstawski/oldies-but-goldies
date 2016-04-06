<?php
class AddExpireToUsers extends Doctrine_Migration_Base {
	private $_tableName = 'users';
	private $_colName = 'expire';


	public function up() {

		$this->addColumn($this->_tableName, $this->_colName, 'Timestamp', null, array('notnull' => false));


	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN '.$this->_colName.' SET DEFAULT NOW()+\'14 days\'');
		Doctrine_Manager::connection()->execute('UPDATE '.$this->_tableName.' SET '.$this->_colName.'=NOW()+\'14 days\'');
	}


	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}


	

}

