<?php

class CreateTableDay extends Doctrine_Migration_Base
{
    private $_tableName = 'day';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'day' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true
            )
        ), array(
            'type' => 'InnoDB'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('INSERT INTO ' . $this->_tableName . ' VALUES (1)');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}