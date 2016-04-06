<?php
class AddColumnsToTests extends Doctrine_Migration_Base
{
	private $_tableName = 'tests';
	private $_colName1 = 'mail_sent';
	private $_colName2 = 'time_zone';
	private $_colName3 = 'requested_date_opening';
	private $_colName4 = 'requested_date_closing';

	public function up()
	{
		$this->addColumn($this->_tableName, $this->_colName1, 'smallint default 0', null, array('notnull' => false));
		$this->addColumn($this->_tableName, $this->_colName2, 'integer', null, array('notnull' => false));
		$this->addColumn($this->_tableName, $this->_colName3, 'timestamp', null, array('notnull' => false));
		$this->addColumn($this->_tableName, $this->_colName4, 'timestamp', null, array('notnull' => false));
	}

	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName1);
		$this->removeColumn($this->_tableName, $this->_colName2);
		$this->removeColumn($this->_tableName, $this->_colName3);
		$this->removeColumn($this->_tableName, $this->_colName4);
	}
}

