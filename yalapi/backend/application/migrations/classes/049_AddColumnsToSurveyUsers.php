<?php

class AddColumnsToSurveyUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'survey_users';

    public function up()
    {
       $this->addColumn($this->_tableName, "deadline", "date");
       $this->addColumn("surveys", "created_date", "timestamp");

       $this->removeColumn("surveys", 'deadline');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, 'deadline');
        $this->removeColumn('surveys', 'created_date');
        $this->addColumn('surveys', 'deadline', 'date');
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("ALTER TABLE surveys ALTER COLUMN created_date SET DEFAULT NOW()");
        $this->postDown();
    }

    public function preUp()
    {
        $this->preDown();
    }

    public function postDown()
    {
        $this->_makeQuery("SELECT create_acl_view('".$this->_tableName."')");
        $this->_makeQuery("SELECT create_acl_view('surveys')");
    }

    public function preDown()
    {
        $this->_makeQuery("SELECT drop_acl_view('".$this->_tableName."')");
        $this->_makeQuery("SELECT drop_acl_view('surveys')");
    }

    private function _makeQuery($q)
    {
        Doctrine_Manager::connection()->exec($q);
    }
}



