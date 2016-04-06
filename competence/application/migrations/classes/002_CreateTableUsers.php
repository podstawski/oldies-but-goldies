<?php

class CreateTableUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_fkName1 = 'fk_users_domains';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'email' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'token' => array(
                'type' => 'character varying',
                'notnull' => false,
            ),
            'active' => array(
                'type' => 'boolean',
                'notnull' => true,
            ),
			'role' => array(
				'type' => 'smallint',
				'notnull' => true,
			),
			'domain_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
            'local' => 'domain_id',
            'foreign' => 'id',
            'foreignTable' => 'domains',
            'onDelete' => 'CASCADE',
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT TRUE');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
