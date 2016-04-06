<?php

class CreateTableCommitment extends Doctrine_Migration_Base
{
    private $_tableName = 'commitment';

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
            'cost' => array(
                'type' => 'decimal(22, 2)',
                'notnull' => true,
            ),
            'day' => array(
                'type' => 'integer',
                'notnull' => true,
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName, 'fk_commitment_company', array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, 'fk_commitment_company');
        $this->dropTable($this->_tableName);
    }
}