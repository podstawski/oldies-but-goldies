<?php

class AlterGroupsAddGoogleGroupId extends Doctrine_Migration_Base
{
    private $_tableName = 'groups';
    private $_colName   = 'google_group_id';

    public function preUp()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' VARCHAR(256)');
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }

    public function postDown()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
    }
}
