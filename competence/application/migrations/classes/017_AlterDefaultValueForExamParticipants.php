<?php
class AlterDefaultValueForExamParticipants extends Doctrine_Migration_Base
{
	private $_tableName = 'exam_participants';
	private $_colName = 'date_started';

	public function up()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' DROP NOT NULL');
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET DEFAULT NULL');
	}

	public function down()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET DEFAULT NOW()');
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET NOT NULL');
	}
}
?>
