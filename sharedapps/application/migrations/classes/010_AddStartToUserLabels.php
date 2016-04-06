<?php
class AddStartToUserLabels extends Doctrine_Migration_Base {
	private $_tableName = 'user_labels';
	private $_colName = 'start';


	public function up() {

		$this->addColumn($this->_tableName, $this->_colName, 'date', null, array('notnull' => false));


	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN '.$this->_colName.' SET DEFAULT CURRENT_DATE-1');	
	}


	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}

