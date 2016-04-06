<?php

class CreateTableStandards extends Doctrine_Migration_Base
{
    private $_tableName = 'standards';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'character varying(255)',
                'notnull' => true,
            )
        ));

    }


    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
