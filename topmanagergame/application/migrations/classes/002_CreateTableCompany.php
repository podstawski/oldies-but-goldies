<?php

class CreateTableCompany extends Doctrine_Migration_Base
{
    private $_tableName = 'company';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'balance' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
                'unsigned' => true
            ),
            'created' => array(
                'type' => 'timestamp',
                'notnull' => true
            ),
            'description' => array(
                'type' => 'text',
                'notnull' => false
            ),
            'rounds_left' => array(
                'type' => 'smallint',
                'notnull' => true,
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_company_user', array(
            'local' => 'user_id',
            'foreign' => 'id',
            'foreignTable' => 'users',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER rounds_left SET DEFAULT 0');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, 'fk_company_user');
        $this->dropTable($this->_tableName);
    }
}