<?php
class CreateTableParticipants extends Doctrine_Migration_Base {
	private $_tableName = 'participants';
	private $_fkName = 'fk_participant_tests';

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
				'test_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
				'participant_email' => array(
					'type' => 'character varying(256)',
					'notnull' => true,
				),
				'participant_name' => array(
					'type' => 'character varying(256)',
					'notnull' => false,
				),
				'participant_group_id' => array(
					'type' => 'character varying(256)',
					'notnull' => false,
				),
				'document_id' => array(
					'type' => 'character varying(256)',
					'notnull' => false,
				),
				'share_flags' => array(
					'type' => 'integer',
					'notnull' => true,
				),
			)
		);
		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName,
			array(
				'local' => 'test_id',
				'foreign' => 'id',
				'foreignTable' => 'tests',
				'onDelete' => 'CASCADE',
			)
		);
	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN share_flags SET DEFAULT 0');
	}

	public function down() {
		$this->dropForeignKey($this->_tableName, $this->_fkName);
		$this->dropTable($this->_tableName);
	}
}

