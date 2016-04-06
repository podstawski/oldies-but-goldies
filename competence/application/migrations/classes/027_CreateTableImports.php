<?php

class CreateTableImportOperations extends Doctrine_Migration_Base
{
	private $_tableName = 'imports';

	function up()
	{
		$this->createTable($this->_tableName, array(
			'id' => array(
				'type' => 'integer',
				'notnull' => true,
				'primary' => true,
				'autoincrement' => true,
				),
			'type' => array(
				'type' => 'integer',
				'notnull' => false,
			),
			/*'active' => array(
				'type' => 'tinyint default 0',
				'notnull' => true
			),*/
			'started' => array(
				'type' => 'timestamp',
				'notnull' => false,
				),
			'ended' => array(
				'type' => 'timestamp',
				'notnull' => false,
				),
			));
	}

	public function down()
	{
		$this->dropTable($this->_tableName);
	}
}
