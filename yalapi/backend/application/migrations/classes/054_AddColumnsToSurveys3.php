<?php

class AddColumnsToSurveys3 extends Doctrine_Migration_Base
{
    private $_tableName = 'surveys';

    public function up()
    {
        $this->addColumn($this->_tableName, "library", "smallint");
        Doctrine_Manager::connection()->exec("DROP TABLE surveys_library CASCADE;");
        Doctrine_Manager::connection()->exec("ALTER table surveys ALTER COLUMN description DROP NOT NULL;");
    }

    public function down()
    {
        $this->createTable("surveys_library", array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'survey_id' => array(
                'type' => 'integer'
            ),
            'user_id' => array(
                'type' => 'integer'
            ),
            'readonly' => array(
                'type' => 'smallint'
            )
        ));
        $this->removeColumn($this->_tableName, 'library');
    }

    public function postDown()
    {
        $this->_makeQuery("SELECT create_acl_view('".$this->_tableName."')");
        Doctrine_Manager::connection()->execute('ALTER TABLE "surveys_library" ADD CONSTRAINT "surveys_fk" FOREIGN KEY ("survey_id") REFERENCES "public"."surveys"("id")  ON DELETE CASCADE');
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



