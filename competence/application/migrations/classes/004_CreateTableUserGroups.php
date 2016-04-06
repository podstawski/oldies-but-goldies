<?php

class CreateTableUserGroups extends Doctrine_Migration_Base
{
    private $_tableName = 'user_groups';
    private $_fkName1 = 'fk_user_groups_users';
    private $_fkName2 = 'fk_user_groups_groups';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'user_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
            'group_id' => array(
                'type' => 'Integer',
                'notnull' => true,
            ),
			'owner' => array(
				'type' => 'boolean',
				'notnull' => false,
			)
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'group_id',
             'foreign'       => 'id',
             'foreignTable'  => 'groups',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

    }

	public function postUp()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN owner SET DEFAULT FALSE');
	}


    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropTable($this->_tableName);
    }


}
