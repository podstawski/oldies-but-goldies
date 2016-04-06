<?php

class AlterLessonRemoveColumns extends Doctrine_Migration_Base
{
    private $_tableName = 'lessons';
    private $_columnName1 = 'rec_type';
    private $_columnName2 = 'event_pid';
    private $_columnName3 = 'event_length';

    public function up()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
        $this->removeColumn($this->_tableName, $this->_columnName2);
        $this->removeColumn($this->_tableName, $this->_columnName3);
    }

    public function down()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'varchar', 256, array('notnull' => true, 'default' => 'day'));
        $this->addColumn($this->_tableName, $this->_columnName2, 'integer', null, array('notnull' => true, 'default' => '1'));
        $this->addColumn($this->_tableName, $this->_columnName3, 'integer', null, array('notnull' => true, 'default' => '1'));
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



