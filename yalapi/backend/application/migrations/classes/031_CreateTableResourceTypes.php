<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableResourceTypes extends Doctrine_Migration_Base
{
    private $_tableName = 'resource_types';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'name' => array(
                'type' => 'varchar(256)'
            )
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
