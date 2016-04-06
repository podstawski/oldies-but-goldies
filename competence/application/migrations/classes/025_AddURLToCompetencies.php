<?php
class AddURLToCompetencies extends Doctrine_Migration_Base
{
	private $_tableName = 'competencies';
	private $_colName = 'url';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'character varying(256)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
