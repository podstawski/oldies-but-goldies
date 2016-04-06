<?php
class AddUserIdToExams extends Doctrine_Migration_Base
{
	private $_tableName1 = 'exams';
	private $_tableName2 = 'users';
	private $_colName = 'user_id';
	private $_fkName = 'fk_exams_users';

	public function up()
	{
		$this->addColumn($this->_tableName1, $this->_colName, 'integer', null, array('notnull' => false));

		$this->createForeignKey($this->_tableName1, $this->_fkName, array(
					'local'         => $this->_colName,
					'foreign'       => 'id',
					'foreignTable'  => $this->_tableName2,
					'onDelete'      => 'CASCADE',
					'onUpdate'      => 'CASCADE'
					));
	}

	public function down()
	{
		$this->dropForeignKey($this->_tableName1, $this->_fkName);
		$this->removeColumn($this->_tableName1, $this->_colName);
	}
}
