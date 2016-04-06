<?php
class CreateTableTests extends Doctrine_Migration_Base {
	private $_tableName = 'tests';
	private $_fkName = 'fk_test_users';

	public function up() {
		$this->createTable(
			$this->_tableName,
			array(
				'id' => array(
					'type' => 'integer',
					'notnull' => true,
					'primary' => true,
					'autoincrement' => true
				),
				'user_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
				'document_title' => array(
					'type' => 'character varying(256)',
					'notnull' => true,
				),
				'document_id' => array(
					'type' => 'character varying(256)',
					'notnull' => true,
				),
				'folder_uri' => array(
					'type' => 'character varying(256)',
					'notnull' => false,
				),
				'date_created' => array(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'scheduled_date_opening' => array(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'scheduled_date_closing' => array(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'date_opened' => array(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'date_closed' => array(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'status' => array(
					'type' => 'smallint',
					'notnull' => true,
				),
			)
		);
		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);
	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date_created SET DEFAULT NOW()');
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN status SET DEFAULT 0');
	}

	public function down() {
		$this->dropForeignKey($this->_tableName, $this->_fkName);
		$this->dropTable($this->_tableName);
	}
}
