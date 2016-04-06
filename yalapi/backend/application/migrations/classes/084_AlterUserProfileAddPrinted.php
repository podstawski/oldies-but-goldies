<?php

class AlterUserProfileAddPrinted extends Doctrine_Migration_Base
{
    private $_tableName = 'user_profile';
    private $_colName = 'printed';

    public function preUp()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' SMALLINT NOT NULL DEFAULT 0');
        Doctrine_Manager::connection()->execute("CREATE OR REPLACE FUNCTION user_profile_printed(Integer) Returns Integer
AS $$
    UPDATE user_profile SET printed = 1 WHERE user_id = $1;
    SELECT 1;
$$ LANGUAGE 'sql';");
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
        Doctrine_Manager::connection()->execute('DROP FUNCTION IF EXISTS user_profile_printed(Integer)');
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }

    public function postDown()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
    }
}
