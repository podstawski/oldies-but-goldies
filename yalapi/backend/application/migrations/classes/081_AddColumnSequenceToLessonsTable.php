<?php

class AddColumnSequenceToLessonsTable extends Doctrine_Migration_Base
{
    private $_tableName = 'lessons';
    private $_columName = 'sequence';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columName, 'int', '5', array('notnull' => true, 'default' => 0));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columName);
    }

    public function postDown()
    {
        $this->_makeQuery("SELECT create_acl_view('".$this->_tableName."')");
    }

    public function preUp()
    {
        $this->preDown();
    }

    public function postUp()
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



