<?php

class AlterRoomAlterColumnDescription extends Doctrine_Migration_Base
{
    private $_tableName  = 'rooms';
    private $_columnName = 'description';

    public function up()
    {
         Doctrine_Manager::connection()->execute(sprintf('ALTER TABLE %s ALTER COLUMN %s DROP NOT NULL', $this->_tableName, $this->_columnName));
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute(sprintf("UPDATE %s SET %s = 'some'", $this->_tableName, $this->_columnName));
        Doctrine_Manager::connection()->execute(sprintf("ALTER TABLE %s ALTER COLUMN %s SET NOT NULL", $this->_tableName, $this->_columnName));
    }
}



