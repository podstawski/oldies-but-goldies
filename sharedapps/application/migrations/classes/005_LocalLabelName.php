<?php
class LocalLabelName extends Doctrine_Migration_Base {
	private $_tableName = 'user_labels';
	private $_colName = 'local_name';

	public function up() {
		$this->addColumn($this->_tableName, $this->_colName, 'varchar(256)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}

