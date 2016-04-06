<?php

class AddStatusColumnToCourses extends Doctrine_Migration_Base
{
    private $_tableName = 'courses';
    private $_columnName = 'status';

    public function up()
    {
       $this->addColumn($this->_tableName, $this->_columnName, 'integer', null, array('notnull' => true, 'default' => 1));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName);
    }

    public function postUp()
    {
        $this->postDown();
    }

    public function preUp()
    {
        $this->preDown();
    }

    public function postDown()
    {
        $this->_makeQuery("SELECT create_acl_view('".$this->_tableName."')");
    }

    public function preDown()
    {
        $this->_makeQuery("SELECT drop_acl_view('".$this->_tableName."')");
    }

    private function _makeQuery($q)
    {
        Doctrine_Manager::connection()->exec($q);
    }
}



