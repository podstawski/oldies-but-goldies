<?php

class CreateTableLoan extends Doctrine_Migration_Base
{
    private $_tableName = 'loan';
    private $_fkName = 'fk_loan_company';

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
            'amount' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'rate' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'first_day' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'months_amount' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'months_paid' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'single_installment_amount' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'interests' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN months_paid SET DEFAULT 0');
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN interests SET DEFAULT 0');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }
}