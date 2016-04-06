<?php

class AlterProjectsAddStatus extends Doctrine_Migration_Base
{
    private $_tableName  = 'projects';
    private $_columnName = 'status';

    public function up()
    {
        Doctrine_Manager::connection()->execute(sprintf('ALTER TABLE %s ADD COLUMN %s SMALLINT NOT NULL DEFAULT 0', $this->_tableName, $this->_columnName));
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute(sprintf('ALTER TABLE %s DROP COLUMN %s', $this->_tableName, $this->_columnName));
    }
}



