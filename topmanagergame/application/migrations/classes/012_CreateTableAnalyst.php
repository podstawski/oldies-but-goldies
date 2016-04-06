<?php

class CreateTableAnalyst extends Doctrine_Migration_Base
{
    private $_tableName = 'analyst';
    private $_idxName = 'uidx_analyst_type_day';

    public function up()
    {
        $this->createTable(
            $this->_tableName, array(
                                    'id'               => array(
                                        'type'          => 'integer',
                                        'notnull'       => true,
                                        'primary'       => true,
                                        'autoincrement' => true,
                                    ),
                                    'type'             => array(
                                        'type'    => 'smallint',
                                        'notnull' => true,
                                    ),
                                    'day'              => array(
                                        'type'    => 'int',
                                        'notnull' => true
                                    ),
                                    'share_amount'     => array(
                                        'type'    => 'decimal(5, 2)',
                                        'notnull' => true,
                                    ),
                                    'offered_amount'   => array(
                                        'type'    => 'integer',
                                        'notnull' => true
                                    ),
                                    'sold_amount'      => array(
                                        'type'    => 'integer',
                                        'notnull' => true
                                    ),
                                    'companies_amount' => array(
                                        'type'    => 'integer',
                                        'notnull' => true
                                    ),
                                    'average_price'    => array(
                                        'type'    => 'decimal(22, 2)',
                                        'notnull' => true
                                    ),
                                    'prediction'       => array(
                                        'type'    => 'smallint',
                                        'notnull' => true
                                    )
                               ), array(
                                       'type' => 'InnoDB'
                                  )
        );
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute(
            'CREATE UNIQUE INDEX ' . $this->_idxName . ' ON ' . $this->_tableName . ' (type, day)'
        );
        Doctrine_Manager::connection()->execute(
            'ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN share_amount SET DEFAULT 0'
        );
        Doctrine_Manager::connection()->execute(
            'ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN offered_amount SET DEFAULT 0'
        );
        Doctrine_Manager::connection()->execute(
            'ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN sold_amount SET DEFAULT 0'
        );
        Doctrine_Manager::connection()->execute(
            'ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN companies_amount SET DEFAULT 0'
        );
        Doctrine_Manager::connection()->execute(
            'ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN average_price SET DEFAULT 0'
        );
        Doctrine_Manager::connection()->execute(
            'ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN prediction SET DEFAULT 0'
        );
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('DROP INDEX ' . $this->_idxName);
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}