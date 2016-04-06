<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTablePayments extends Doctrine_Migration_Base
{
    private $_tableName = 'payments';

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
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'type' => array(
                'type' => 'smallint',
                'notnull' => false,
            ),
            'date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'transaction_id' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
            'amount' => array(
                'type' => 'decimal(12,2)',
                'notnull' => false,
            ),
            'status' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
            'payer_email' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
            'data' => array(
                'type' => 'text',
                'notnull' => false
            ),
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN date SET DEFAULT NOW()');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}