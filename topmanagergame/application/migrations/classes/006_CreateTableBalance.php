<?php

class CreateTableBalance extends Doctrine_Migration_Base
{
    private $_tableName = 'balance';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'company_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'type' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'day' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'amount' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
                'unsigned' => true,
            ),
            'current_balance' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
                'unsigned' => true,
            ),
            'text' => array(
                'type' => 'text',
                'notnull' => false,
            ),
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_balance_company', array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, 'fk_balance_company');
        $this->dropTable($this->_tableName);
    }
}