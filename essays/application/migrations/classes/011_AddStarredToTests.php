<?php
class AddStarredToTests extends Doctrine_Migration_Base
{
    private $_tableName = 'tests';
    private $_colName = 'starred';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName, 'smallint default 0', null, array('notnull' => false));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
    }
}

