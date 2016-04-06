<?php

class AclObjId extends Doctrine_Migration_Base
{
    private $_tableName = 'acl';

    public function up()
    {
        $this->addColumn($this->_tableName, "object_id", "integer",null, array('default'=>0));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, 'object_id');
    }

}



