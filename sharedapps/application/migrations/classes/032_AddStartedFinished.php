<?php
class AddStartedFninished extends Doctrine_Migration_Base {
	private $_tableName1 = 'labels';
	private $_tableName2 = 'contact_groups';
	private $_colName1 = 'started';
	private $_colName2 = 'finished';


	public function up() {

		$this->addColumn($this->_tableName1, $this->_colName1, 'Integer', null, array('notnull' => false));
		$this->addColumn($this->_tableName1, $this->_colName2, 'Integer', null, array('notnull' => false));
		$this->addColumn($this->_tableName2, $this->_colName1, 'Integer', null, array('notnull' => false));
		$this->addColumn($this->_tableName2, $this->_colName2, 'Integer', null, array('notnull' => false));
	}



	public function down()
	{
		$this->removeColumn($this->_tableName1, $this->_colName2);
		$this->removeColumn($this->_tableName1, $this->_colName1);
		$this->removeColumn($this->_tableName2, $this->_colName2);
		$this->removeColumn($this->_tableName2, $this->_colName1);
	}
}

