<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableGameServer extends Doctrine_Migration_Base
{
    private $_tableName = 'game_server';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'url' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            ),
            'token' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'admin_email' => array(
                'type' => 'character varying(256)',
                'notnull' => false,
            ),
            'active' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'create_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'settings' => array(
                'type' => 'text'
            ),
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN active SET DEFAULT 1');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN create_date SET DEFAULT NOW()');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}