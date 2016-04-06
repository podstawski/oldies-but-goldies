<?php
class ModifyUserContactGroups extends Doctrine_Migration_Base {
	private $_tableName = 'user_contact_groups';
	private $_colName1 = 'agree_time';
	private $_colName2 = 'agree_hash';

	public function up() {
		$this->addColumn($this->_tableName, $this->_colName1, 'timestamp', null, array('notnull' => false));
		$this->addColumn($this->_tableName, $this->_colName2, 'character varying(100)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName1);
		$this->removeColumn($this->_tableName, $this->_colName2);
	}
}


