<?php

class CreateTableApps extends Doctrine_Migration_Base
{
    private $_tableName = 'apps';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'domain' => array(
                'type' => 'varchar(256)',
                'notnull' => true
            ),
            'token' => array(
                'type' => 'text',
                'notnull' => true,
            )
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_table(\'' . $this->_tableName . '\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_table(\'' . $this->_tableName . '\')');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
