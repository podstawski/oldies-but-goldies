<?php

class CreateTableExamParticipants extends Doctrine_Migration_Base
{
	private $_tableName = 'exam_participants';
	private $_fkName1 = 'fk_user_exams_users';
	private $_fkName2 = 'fk_user_exams_groups';
	private $_fkName3 = 'fk_user_exams_exams';

	function up()
	{
		$this->createTable($this->_tableName, array
		(
			'id' => array
			(
				'type' => 'integer',
				'notnull' => true,
				'primary' => true,
				'autoincrement' => true,
			),
			'user_id' => array
			(
				'type' => 'Integer',
				'notnull' => true,
			),
			'group_id' => array
			(
				'type' => 'Integer',
				'notnull' => true,
			),
			'exam_id' => array
			(
				'type' => 'Integer',
				'notnull' => true,
			),
			'date_started' => array
			(
				'type' => 'timestamp',
				'notnull' => true
			),
			'date_finished' => array
			(
				'type' => 'timestamp',
				'notnull' => false
			)
		));

		$this->createForeignKey($this->_tableName, $this->_fkName1, array
		(
			'local'         => 'user_id',
			'foreign'       => 'id',
			'foreignTable'  => 'users',
			'onDelete'      => 'CASCADE',
			'onUpdate'      => 'CASCADE'
		));
		$this->createForeignKey($this->_tableName, $this->_fkName2, array
		(
			'local'         => 'group_id',
			'foreign'       => 'id',
			'foreignTable'  => 'groups',
			'onDelete'      => 'CASCADE',
			'onUpdate'      => 'CASCADE'
		));
		$this->createForeignKey($this->_tableName, $this->_fkName3, array
		(
			'local'         => 'exam_id',
			'foreign'       => 'id',
			'foreignTable'  => 'exams',
			'onDelete'      => 'CASCADE',
			'onUpdate'      => 'CASCADE'
		));

	}

	public function postUp()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date_started SET DEFAULT NOW()');
	}


	public function down()
	{
		$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropForeignKey($this->_tableName, $this->_fkName2);
		$this->dropForeignKey($this->_tableName, $this->_fkName3);
		$this->dropTable($this->_tableName);
	}


}
