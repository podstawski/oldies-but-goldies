<?php
class CreateTableUserContactGroupContacts extends Doctrine_Migration_Base {
	private $_tableName = 'user_contact_group_contacts';
	private $_fkName1 = 'fk_user_contact_group_contacts_user_contact_group';
	private $_fkName2 = 'fk_user_contact_group_contacts_contacts';

	public function up() {
		$this->createTable($this->_tableName, array(
			'id' => array(
				'type' => 'integer',
				'notnull' => true,
				'primary' => true,
				'autoincrement' => true,
			),
			'user_contact_group_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
			'contact_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
			'google_contact_id' => array(
				'type' => 'character varying(256)',
				'notnull' => false,
			),
		));


		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName1,
			array(
				'local' => 'user_contact_group_id',
				'foreign' => 'id',
				'foreignTable' => 'user_contact_groups',
				'onDelete' => 'CASCADE',
			)
		);

		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName2,
			array(
				'local' => 'contact_id',
				'foreign' => 'id',
				'foreignTable' => 'contacts',
				'onDelete' => 'CASCADE',
			)
		);

	}

	public function preDown() {
		$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropForeignKey($this->_tableName, $this->_fkName2);
	}


	public function down() {
		$this->dropTable($this->_tableName);
	}
}
