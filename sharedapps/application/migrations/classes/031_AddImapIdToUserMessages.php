<?php
class AddImapIdToUserMessages extends Doctrine_Migration_Base {


	private $_tableName = 'user_all_messages';
	private $_fkName1 = 'fk_massages_all_user';
	private $_fkName2 = 'fk_massages_all_label';
	

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
				'label_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),				
				'imap_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
                        	'date' => array (
                                	'type' => 'timestamp default now()',
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
		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName2,
			array(
				'local' => 'label_id',
				'foreign' => 'id',
				'foreignTable' => 'labels',
				'onDelete' => 'CASCADE',
			)
		);		


	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('CREATE UNIQUE INDEX user_all_messages_imapid_key ON user_all_messages(imap_id,user_id,label_id)');	
	}


	public function down()
	{
		//$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropTable($this->_tableName);
	}


	

}

