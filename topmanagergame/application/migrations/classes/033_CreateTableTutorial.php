<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableTutorial extends Doctrine_Migration_Base
{
    private $_tableName = 'tutorial';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'url' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'video' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB',
            'charset' => 'utf8',
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}