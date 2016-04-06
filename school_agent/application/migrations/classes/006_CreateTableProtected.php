<?php
class CreateTableProtected extends Doctrine_Migration_Base
{
	private $_tableName = 'protected';

	public function up()
	{
		$this->createTable
		(
			$this->_tableName,
			array
			(
				'id' => array
				(
					'type' => 'integer',
					'notnull' => true,
					'primary' => true,
					'autoincrement' => true,
				),
				'email' => array
				(
					'type' => 'varchar(256)',
					'notnull' => false,
				),
			)
		);
	}

	public function postUp()
	{
	}

	public function down()
	{
		$this->dropTable($this->_tableName);
	}
}
?>
