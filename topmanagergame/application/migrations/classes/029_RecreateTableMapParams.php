<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class RecreateTableMapParams extends Doctrine_Migration_Base
{
    private $_tableName = 'map_params';

    public function up()
    {
        $this->dropTable($this->_tableName);

        $this->createTable('map_params', array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'type' => array(
                'type' => 'integer',
                'notnull' => true,
                'unique' => true
            ),
            'bname' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'bhint' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'burl' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            )
        ), array(
            'type' => 'InnoDB',
            'charset' => 'utf8',
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);

        $this->createTable('map_params', array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'type' => array(
                'type' => 'varchar(64)',
                'notnull' => true,
                'unique' => true
            ),
            'title' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'url' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            )
        ), array(
            'type' => 'InnoDB',
            'charset' => 'utf8',
        ));
    }
}