<?php

class CreateTableGroups extends Doctrine_Migration_Base
{
    private $_tableName = 'groups';
    private $_fkName1 = 'fk_groups_domains';

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
            'description' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'active' => array(
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
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
