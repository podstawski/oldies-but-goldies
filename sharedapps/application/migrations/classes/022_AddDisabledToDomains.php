<?php
class AddDisabledToDomains extends Doctrine_Migration_Base {
	private $_tableName = 'domains';
	private $_colName = 'disabled';

	public function up() {
		$this->addColumn($this->_tableName, $this->_colName, 'timestamp', null, array('notnull' => false));
	}

	public function down() {
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
