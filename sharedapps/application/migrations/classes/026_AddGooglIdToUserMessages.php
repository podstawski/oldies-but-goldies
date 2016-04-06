<?php
class AddGooglIdToUserMessages extends Doctrine_Migration_Base {
	private $_tableName = 'user_messages';
	private $_colName = 'googleid';


	public function up() {

		$this->addColumn($this->_tableName, $this->_colName, 'BigInt', null, array('notnull' => false));


	}

	public function postUp() {
		Doctrine_Manager::connection()->execute('CREATE UNIQUE INDEX user_messages_googleid_key ON user_messages(googleid,user_id)');	
	}


	public function down()
	{
		$this->removeColumn($this->_tableName, $this->_colName);
	}


	

}

