<?php
class FixStars extends Doctrine_Migration_Base
{
	private $_tableName1 = 'tests';
	private $_colName = 'starred';

	private $_fkName1 = 'fk_stars_users';
	private $_fkName2 = 'fk_stars_tests';

	private $_tableName2 = 'stars';

	public function up()
	{
		$this->removeColumn($this->_tableName1, $this->_colName);

		$this->createTable($this->_tableName2, array(
			'id' => array(
				'type' => 'integer',
				'notnull' => true,
				'primary' => true,
				'autoincrement' => true,
				),
			'user_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
			'test_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
			'star' => array(
				'type' => 'smallint default 0',
				'notnull' => true
			),
		));

		$this->createForeignKey(
			$this->_tableName2,
			$this->_fkName1,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);
		$this->createForeignKey(
			$this->_tableName2,
			$this->_fkName2,
			array(
				'local' => 'test_id',
				'foreign' => 'id',
				'foreignTable' => 'tests',
				'onDelete' => 'CASCADE',
			)
		);
	}

	public function down()
	{
		$this->dropForeignKey($this->_tableName2, $this->_fkName2);
		$this->dropForeignKey($this->_tableName2, $this->_fkName1);
		$this->dropTable($this->_tableName2);
		$this->addColumn($this->_tableName1, $this->_colName, 'smallint default 0', null, array('notnull' => false));
	}
}
