<?php

class AddColumnUserIdToAttempts extends Doctrine_Migration_Base
{
    private $_tableName = 'attempts';
    private $_columnName1 = 'user_id';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer');
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN user_id SET DEFAULT 0');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
