<?php

class AlterTrainingCentersAddDescAndPhoneCols extends Doctrine_Migration_Base
{
    private $_tableName = 'training_centers';
    private $_colName1  = 'description';
    private $_colName2  = 'phone_number';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName1, 'text');
        $this->addColumn($this->_tableName, $this->_colName2, 'varchar(256)');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName2);
        $this->removeColumn($this->_tableName, $this->_colName1);
    }

    public function preUp()
    {
        $this->preDown();
    }

    public function postUp()
    {
        $this->postDown();
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
