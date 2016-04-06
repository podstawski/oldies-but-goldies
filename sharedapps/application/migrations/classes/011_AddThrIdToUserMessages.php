<?php
class AddThrIdToUserMessages extends Doctrine_Migration_Base {
	private $_tableName = 'user_messages';
	private $_colName = 'thrid';


	public function up() {

		$this->addColumn($this->_tableName, $this->_colName, 'BigInt', null, array('notnull' => false));


	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('CREATE INDEX user_messages_thrid_key ON user_messages(thrid,user_id)');	
	}


	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}


	

}

