<?php

class CreateTableDomains extends Doctrine_Migration_Base
{
    private $_tableName = 'domains';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'domain_name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'org_name' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'oauth_token' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'admin_email' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'active' => array(
                'type' => 'smallint',
                'notnull' => true,
            )
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
        // Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `active` ');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');        
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
