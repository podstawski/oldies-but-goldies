<?php
class CreateTableUsers extends Doctrine_Migration_Base
{
	private $_tableName = 'users';
	private $_fkName1 = 'fk_user_domains';

	public function up()
	{
		$this->createTable
		(
			$this->_tableName,
			array
			(
				'id' => array
				(
					'type' => 'integer',
					'notnull' => true,
					'primary' => true,
					'autoincrement' => true,
				),
				'domain_id' => array
				(
					'type' => 'integer',
					'notnull' => true,
				),
				'name' => array
				(
					'type' => 'character varying(256)',
					'notnull' => true,
				),
				'email' => array
				(
					'type' => 'varchar(256)',
					'notnull' => true,
				),
			)
		);
		$this->createForeignKey
		(
			$this->_tableName,
			$this->_fkName1,
			array
			(
				'local' => 'domain_id',
				'foreign' => 'id',
				'foreignTable' => 'domains',
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

