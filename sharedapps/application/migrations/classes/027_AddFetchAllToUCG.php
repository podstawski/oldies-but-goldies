<?php
class AddFetchAllToUserContactGroups extends Doctrine_Migration_Base {
	private $_tableName = 'user_contact_groups';
	private $_colName = 'fetch_all';

	public function up() {
		$this->addColumn($this->_tableName, $this->_colName, 'BOOLEAN DEFAULT TRUE', null, array('notnull' => true));
	}

	public function down() {
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
