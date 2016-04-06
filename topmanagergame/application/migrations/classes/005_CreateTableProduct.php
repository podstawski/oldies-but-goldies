<?php

class CreateTableProduct extends Doctrine_Migration_Base
{
    private $_tableName = 'product';

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
            'employees' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_product_company', array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER technology SET DEFAULT 1');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER quality SET DEFAULT 1');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER employees SET DEFAULT 0');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}