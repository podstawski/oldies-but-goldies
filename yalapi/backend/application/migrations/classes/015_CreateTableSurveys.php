<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableSurveys extends Doctrine_Migration_Base
{
    private $_tableName = 'surveys';
    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'user_id' => array(
                'type' => 'integer'
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'text',
                'notnull' => true,
            ),
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
