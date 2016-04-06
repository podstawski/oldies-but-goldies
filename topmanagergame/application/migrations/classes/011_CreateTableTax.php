<?php

class CreateTableTax extends Doctrine_Migration_Base
{
	private $_tableName = 'tax';
	private $_idxName = 'idx_tax_company_month_year';
	private $_fkName = 'fk_tax_company';

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
            'month' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'year' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'costs' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'income' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'advance' => array(
                'type' => 'decimal(22, 2)',
            ),
            'overpaid_vat_tax' => array(
                'type' => 'decimal(22, 2)',
            )
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
        Doctrine_Manager::connection()->execute('CREATE UNIQUE INDEX ' . $this->_idxName . ' ON ' . $this->_tableName . ' (company_id, month, year)');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('DROP INDEX ' . $this->_idxName);
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
		$this->dropTable($this->_tableName);
    }
}