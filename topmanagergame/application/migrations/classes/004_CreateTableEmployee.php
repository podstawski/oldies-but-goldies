<?php

class CreateTableEmployee extends Doctrine_Migration_Base
{
    private $_tableName = 'employee';

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
                'type' => 'integer',
                'notnull' => true,
            ),
            'fired' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'busy' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'skill_level' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_employee_company', array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER amount SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER fired SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER busy SET DEFAULT 0');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER skill_level SET DEFAULT 1');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, 'fk_employee_company');
        $this->dropTable($this->_tableName);
    }
}