<?php
class ModifyContacts extends Doctrine_Migration_Base {
	private $_tableName = 'contacts';
	private $_colName1 = 'user_id';
	private $_colName2 = 'contact_group_id';
	private $_colName3 = 'date_synchronized';
	private $_fkName1 = 'fk_contact_users';
	private $_fkName2  = 'fk_contact_contact_groups';

	public function up() {
		Doctrine_Manager::connection()->exec('DELETE FROM ' . $this->_tableName);
		$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->removeColumn($this->_tableName, $this->_colName1);
		$this->addColumn($this->_tableName, $this->_colName2, 'integer', null, array('notnull' => true));
		$this->addColumn($this->_tableName, $this->_colName3, 'timestamp', null, array('notnull' => false));
		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName2,
			array(
				'local' => 'contact_group_id',
				'foreign' => 'id',
				'foreignTable' => 'contact_groups',
				'onDelete' => 'CASCADE',
			)
		);
	}

	public function down()
	{
		$this->dropForeignKey($this->_tableName, $this->_fkName2);
		$this->removeColumn($this->_tableName, $this->_colName3);
		$this->removeColumn($this->_tableName, $this->_colName2);
		$this->addColumn($this->_tableName, $this->_colName1, 'integer', null, array('notnull' => true));
		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName1,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);
	}
}

