<?php

class CreateTableUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    // private $_fkName = 'fk_users_domain';
    
    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'domain_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'email' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'active' => array(
                'type' => 'smallint',
                'notnull' => false,
            ),
            'user_role' => array(
                'type' => 'smallint',
                'notnull' => false
            )
        ));
        
		/*
        $this->createForeignKey($this->_tableName, $this->_fkName, array(
             'local'         => 'domain_id',
             'foreign'       => 'id',
             'foreignTable'  => 'domains',
             'onDelete'      => 'RESTRICT',
             'onUpdate'      => 'CASCADE'
        ));
		*/
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN user_role SET DEFAULT 1');
	Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        // $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }
}

?>
