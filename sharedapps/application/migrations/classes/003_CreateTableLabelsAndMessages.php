<?php
class CreateTableLabelsAndMessages extends Doctrine_Migration_Base {
	private $_tableName1 = 'labels';
	private $_tableName2 = 'messages';
	private $_tableName3 = 'user_messages';
	private $_tableName4 = 'user_labels';

	private $_fkName1_1 = 'fk_label_users';
	private $_fkName2_1 = 'fk_message_users';
	private $_fkName3_1 = 'fk_user_message_messages';
	private $_fkName3_2 = 'fk_user_message_users';
	private $_fkName4_1 = 'fk_user_label_labels';
	private $_fkName4_2 = 'fk_user_label_users';

	public function up() {


		$this->createTable(
			$this->_tableName1,
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
				'name' => array(
					'type' => 'character varying(256)',
					'notnull' => true,
				),
			)
		);


		$this->createForeignKey(
			$this->_tableName1,
			$this->_fkName1_1,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);


		$this->createTable(
			$this->_tableName2,
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
				'message_id' => array(
					'type' => 'character varying(100)',
					'notnull' => true,
				),
			)
		);


		$this->createForeignKey(
			$this->_tableName2,
			$this->_fkName2_1,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);


		$this->createTable(
			$this->_tableName3,
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
				'message_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
				'date' => array(
					'type' => 'timestamp',
					'notnull' => true,
				),
				

			)
		);

		$this->createForeignKey(
			$this->_tableName3,
			$this->_fkName3_1,
			array(
				'local' => 'message_id',
				'foreign' => 'id',
				'foreignTable' => 'messages',
				'onDelete' => 'CASCADE',
			)
		);

		$this->createForeignKey(
			$this->_tableName3,
			$this->_fkName3_2,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);



		$this->createTable(
			$this->_tableName4,
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
				'label_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
				'agree_time' => array(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'agree_hash' => array(
					'type' => 'character varying(100)',
					'notnull' => false,
				),
				

			)
		);


		$this->createForeignKey(
			$this->_tableName4,
			$this->_fkName4_1,
			array(
				'local' => 'label_id',
				'foreign' => 'id',
				'foreignTable' => 'labels',
				'onDelete' => 'CASCADE',
			)
		);

		$this->createForeignKey(
			$this->_tableName4,
			$this->_fkName4_2,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);




	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName3 . ' ALTER COLUMN date SET DEFAULT NOW()');	
	}


	public function down()
	{
		$this->dropTable($this->_tableName4);
		$this->dropTable($this->_tableName3);
		$this->dropTable($this->_tableName2);	
		$this->dropTable($this->_tableName1);
	}
}

