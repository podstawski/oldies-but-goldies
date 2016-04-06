<?php
class CreateTableContacts extends Doctrine_Migration_Base {
	private $_tableName = 'contacts';
	private $_fkName1 = 'fk_contact_users';

	public function up() {
		$this->createTable(
			$this->_tableName,
			array(
				'id' => array(
					'type' => 'integer',
					'notnull' => true,
					'primary' => true,
					'autoincrement' => true,
				),
				'user_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
			)
		);
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

	public function down()
	{
		$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropTable($this->_tableName);
	}
}

