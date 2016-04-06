<?php

class AddColumnContentIdToLinks extends Doctrine_Migration_Base
{
    private $_tableName = 'links';
    private $_columnName1 = 'content_id';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_columnName1, 'integer');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_columnName1);
    }
}
