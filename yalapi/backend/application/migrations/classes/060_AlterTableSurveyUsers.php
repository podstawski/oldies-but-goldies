<?php

class AlterTableSurveyUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_users';

    public function up()
    {
        $this->addColumn($this->_tableName, "sent", "timestamp");
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, 'sent');
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



