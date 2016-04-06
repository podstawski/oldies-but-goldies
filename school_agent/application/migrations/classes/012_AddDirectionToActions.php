<?php
class AddDirectionToActions extends Doctrine_Migration_Base
{
	private $_tableName = 'actions';
	private $_colName = 'last_direction';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName, 'varchar(10)', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}
}
?>
