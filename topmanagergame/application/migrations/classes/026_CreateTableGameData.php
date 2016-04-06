<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableGameData extends Doctrine_Migration_Base
{
    private $_tableName = 'game_data';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'key' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
                'primary' => true,
            ),
            'value' => array(
                'type' => 'text',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB'
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}