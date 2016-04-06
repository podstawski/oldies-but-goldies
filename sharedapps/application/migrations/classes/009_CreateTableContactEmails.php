
<?php
class CreateTableContactEmails extends Doctrine_Migration_Base {
	private $_tableName = 'contact_emails';
	private $_fkName1 = 'fk_contact_emails_contacts';

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
				'contact_id' => array(
					'type' => 'integer',
					'notnull' => true,
				),
				'email' => array(
					'type' => 'varchar(255)',
					'notnull' => true,
				),
			)
		);
		$this->createForeignKey(
			$this->_tableName,
			$this->_fkName1,
			array(
				'local' => 'contact_id',
				'foreign' => 'id',
				'foreignTable' => 'contacts',
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

