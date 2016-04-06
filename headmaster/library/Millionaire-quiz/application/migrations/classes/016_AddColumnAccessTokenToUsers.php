<?php

class AddColumnAccessTokenToUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'users';
    private $_columnName1 = 'access_token';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'text');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
