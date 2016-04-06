<?php
class AddIdsToGroups extends Doctrine_Migration_Base {
	private $_tableName = 'contact_groups';
	private $_colName = 'group_id';

	public function up() {
		$this->addColumn($this->_tableName, $this->_colName, 'character varying(32)', null, array('notnull' => false));
	}

	public function down() {
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
