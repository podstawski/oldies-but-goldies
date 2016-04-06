<?php

class AddColumnsToTraningCenters extends Doctrine_Migration_Base
{
    private $_tableName = 'training_centers';

    public function up()
    {
        $this->addColumn($this->_tableName, 'manager', 'varchar', 256, array('notnull' => false));
        $this->addColumn($this->_tableName, 'url', 'varchar', 256, array('notnull' => false));
        $this->addColumn($this->_tableName, 'rating', 'integer', null, array('notnull' => true, 'default' => 5));
        $this->addColumn($this->_tableName, 'room_amount', 'integer', null, array('notnull' => false, 'default' => 0));
        $this->addColumn($this->_tableName, 'seats_amount', 'integer', null, array('notnull' => false, 'default' => 0));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, 'manager');
        $this->removeColumn($this->_tableName, 'url');
        $this->removeColumn($this->_tableName, 'rating');
        $this->removeColumn($this->_tableName, 'room_amount');
        $this->removeColumn($this->_tableName, 'seats_amount');
    }
}
