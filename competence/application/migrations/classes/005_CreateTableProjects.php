<?php

class CreateTableProjects extends Doctrine_Migration_Base
{
    private $_tableName = 'projects';
    private $_fkName1 = 'fk_projects_users';
	private $_fkName2 = 'fk_projects_domains';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'file' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
			'user_id' => array(
				'type' => 'integer',
				'notnull' => false,
			),
			'domain_id' => array(
				'type' => 'integer',
				'notnull' => true,
			),
			'date' => array(
				'type' => 'timestamp',
				'notnull' => false,
			),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'domain_id',
             'foreign'       => 'id',
             'foreignTable'  => 'domains',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

	public function postUp()
	{
		Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date SET DEFAULT NOW()');
	}

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
