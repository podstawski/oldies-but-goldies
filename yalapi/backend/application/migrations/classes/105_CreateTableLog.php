<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableLog extends Doctrine_Migration_Base
{
    private $_tableName = 'logs';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'url' => array(
                'type' => 'text',
                'notnull' => true,
            ),
            'type' => array(
                'type' => 'varchar(10)',
                'notnull' => false
            ),
            'data' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'username' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
            'ip' => array(
                'type' => 'varchar(15)',
                'notnull' => false,
            ),
            'date' => array(
                'type' => 'timestamp',
                'notnull' => false,
            )
        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}