<?php

class CreateTableSaleReport extends Doctrine_Migration_Base
{
    private $_tableName = 'sale_report';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'warehouse_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'offered_price' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'offered_amount' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'sold_amount' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'day' => array(
                'type' => 'integer',
                'notnull' => true
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_sale_report_warehouse', array(
            'local' => 'warehouse_id',
            'foreign' => 'id',
            'foreignTable' => 'warehouse',
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, 'fk_sale_report_warehouse');
        $this->dropTable($this->_tableName);
    }
}