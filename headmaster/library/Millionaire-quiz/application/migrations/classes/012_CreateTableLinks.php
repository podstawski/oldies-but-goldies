<?php

class CreateTableLinks extends Doctrine_Migration_Base
{
    private $_tableName = 'links';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'text_id' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'url' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'text' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'title' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'target' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'icon' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'active' => array(
                'type' => 'smallint',
                'notnull' => false,
            ),
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
