<?php

class AlterCoursesAddShowOnWww extends Doctrine_Migration_Base
{
    private $_tableName = 'courses';
    private $_colName   = 'show_on_www';

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' SMALLINT NOT NULL DEFAULT 1');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("SELECT create_acl_table('$this->_tableName')");
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute("SELECT drop_acl_table('$this->_tableName')");
    }
}
