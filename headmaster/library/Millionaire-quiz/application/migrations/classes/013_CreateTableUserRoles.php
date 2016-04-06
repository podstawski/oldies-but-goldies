<?php

class CreateTableUserRoles extends Doctrine_Migration_Base
{
    private $_tableName = 'user_roles';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'character varying(256)',
                'notnull' => true,
            )
        ));
    }

    public function postUp()
    {
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
