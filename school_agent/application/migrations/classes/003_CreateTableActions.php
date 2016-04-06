<?php
class CreateTableActions extends Doctrine_Migration_Base
{
	private $_tableName = 'actions';
	private $_fkName1 = 'fk_action_domains';

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
				'date_created' => array
				(
					'type' => 'timestamp',
					'notnull' => true,
				),
				'date_start' => array
				(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'date_end' => array
				(
					'type' => 'timestamp',
					'notnull' => false,
				),
				'active' => array
				(
					'type' => 'boolean',
					'notnull' => false,
				),
				'current_action_step' => array
				(
					'type' => 'integer',
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
				'local' => 'domain_id',
				'foreign' => 'id',
				'foreignTable' => 'domains',
				'onDelete' => 'CASCADE',
			)
		);
	}

	public function postUp()
	{
		Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date_created SET DEFAULT NOW()');
	}

	public function down()
	{
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropTable($this->_tableName);
	}
}

