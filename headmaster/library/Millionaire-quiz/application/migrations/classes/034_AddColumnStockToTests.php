<?php

class AddColumnStockToTests extends Doctrine_Migration_Base
{
    private $_tableName = 'tests';
    private $_columnName1 = 'stock';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
