<?php
class CreateTableActionSteps extends Doctrine_Migration_Base
{
	private $_tableName = 'action_steps';
	private $_fkName1 = 'fk_action_steps_actions';

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
				'action_id' => array
				(
					'type' => 'integer',
					'notnull' => true,
				),
				'type' => array
				(
					'type' => 'varchar(20)',
					'notnull' => true
				),
				'date' => array
				(
					'type' => 'timestamp',
					'notnull' => true,
				),
				'email' => array
				(
					'type' => 'varchar(256)',
					'notnull' => false,
				),
				'params' => array
				(
					'type' => 'text',
					'notnull' => false,
				),
			)
		);
		$this->createForeignKey
		(
			$this->_tableName,
			$this->_fkName1,
			array
			(
				'local' => 'action_id',
				'foreign' => 'id',
				'foreignTable' => 'actions',
				'onDelete' => 'CASCADE',
			)
		);
	}

	public function postUp()
	{
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date SET DEFAULT NOW()');
	}

	public function down()
	{
		$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropTable($this->_tableName);
	}
}


