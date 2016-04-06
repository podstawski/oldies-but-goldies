<?php

class CreateTableWarehouse extends Doctrine_Migration_Base
{
    private $_tableName = 'warehouse';

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
                'type' => 'smallint',
                'notnull' => true,
            ),
            'technology' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'quality' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'amount' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'parts_cost' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'price' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'status' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_warehouse_company', array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER status SET DEFAULT 0');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, 'fk_warehouse_company');
        $this->dropTable($this->_tableName);
    }
}