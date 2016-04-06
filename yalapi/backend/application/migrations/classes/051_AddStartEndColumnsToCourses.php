<?php

class AddStartEndColumnsToCourses extends Doctrine_Migration_Base
{
    private $_tableName = 'courses';
    private $_columnName1 = 'start_date';
    private $_columnName2 = 'end_date';

    public function up()
    {
       $this->addColumn($this->_tableName, $this->_columnName1, 'timestamp', null, array('notnull' => false));
       $this->addColumn($this->_tableName, $this->_columnName2, 'timestamp', null, array('notnull' => false));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
        $this->removeColumn($this->_tableName, $this->_columnName2);
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



