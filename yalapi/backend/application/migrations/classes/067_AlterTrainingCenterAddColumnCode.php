<?php

class AlterTrainingCenterAddColumnCode extends Doctrine_Migration_Base
{
    private $_tableName = 'training_centers';
    private $_columnName = 'code';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName, 'varchar', 256, array('notnull' => true, 'default' => 'DTC'));
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



