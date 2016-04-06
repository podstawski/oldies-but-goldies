<?php

class AddColumnServerStartedToAttempts extends Doctrine_Migration_Base
{
    private $_tableName = 'attempts';
    private $_columnName1 = 'server_started';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
