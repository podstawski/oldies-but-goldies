<?php

class CreateTableExamManagers extends Doctrine_Migration_Base
{
	private $_tableName = 'exam_managers';
	private $_fkName1 = 'fk_user_exams_users';
	private $_fkName2 = 'fk_user_exams_exams';

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
			'exam_id' => array
			(
				'type' => 'Integer',
				'notnull' => true,
			),
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
			'local'         => 'exam_id',
			'foreign'       => 'id',
			'foreignTable'  => 'exams',
			'onDelete'      => 'CASCADE',
			'onUpdate'      => 'CASCADE'
		));

	}

	public function down()
	{
		$this->dropForeignKey($this->_tableName, $this->_fkName1);
		$this->dropForeignKey($this->_tableName, $this->_fkName2);
		$this->dropTable($this->_tableName);
	}


}
