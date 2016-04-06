<?php

class CreateContent extends Doctrine_Migration_Base
{
    private $_tableName = 'content';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'title' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'youtube' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'category_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'data' => array(
                'type' => 'text'
            )
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
