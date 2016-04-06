<?php
class AddRefererToUsers extends Doctrine_Migration_Base {
	private $_tableName = 'users';
	private $_colName = 'referer';

	public function up() {
		$this->addColumn($this->_tableName, $this->_colName, 'text', null, array('notnull' => false));
	}

	public function down() {
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
