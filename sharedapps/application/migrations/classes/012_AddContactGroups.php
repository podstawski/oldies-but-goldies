<?php
class AddContactGroups extends Doctrine_Migration_Base {
	private $_tableName1 = 'contact_groups';
	private $_tableName2 = 'user_contact_groups';
	private $_fkName1 = 'fk_contact_groups_users';
	private $_fkName2_1 = 'fk_user_contact_groups_contact_groups';
	private $_fkName2_2 = 'fk_user_contact_groups_users';

	public function up()
	{
		$this->createTable($this->_tableName1, array(
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
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
        ));

		$this->createForeignKey(
			$this->_tableName1,
			$this->_fkName1,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);

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
			'contact_group_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
			'local_name' => array(
				'type' => 'character varying(256)',
				'notnull' => false
			),
		));

		$this->createForeignKey(
			$this->_tableName2,
			$this->_fkName2_1,
			array(
				'local' => 'contact_group_id',
				'foreign' => 'id',
				'foreignTable' => $this->_tableName1,
				'onDelete' => 'CASCADE',
			)
		);

		$this->createForeignKey(
			$this->_tableName2,
			$this->_fkName2_2,
			array(
				'local' => 'user_id',
				'foreign' => 'id',
				'foreignTable' => 'users',
				'onDelete' => 'CASCADE',
			)
		);


	}

	public function preDown() {
        $this->dropForeignKey($this->_tableName1, $this->_fkName1);
        $this->dropForeignKey($this->_tableName2, $this->_fkName2_1);
        $this->dropForeignKey($this->_tableName2, $this->_fkName2_2);
	}

	public function down()
	{
		$this->dropTable($this->_tableName1);
		$this->dropTable($this->_tableName2);
	}
}
