<?php
class AlterTablePayments extends Doctrine_Migration_Base
{
    private $_tableName = 'payment';
    private $_colName1 = 'custom_id';
    private $_colName2 = 'payer_id';
    private $_colName3 = 'custom_data';
    private $_colName4 = 'name';
    private $_colName5 = 'type';
    private $_fkName = 'fk_payment_users';

    public function up()
    {
        $this->addColumn($this->_tableName, $this->_colName1, 'character varying(32)', null, array('notnull' => false));
        $this->addColumn($this->_tableName, $this->_colName2, 'integer', null, array('notnull' => false));
        $this->addColumn($this->_tableName, $this->_colName3, 'text', null, array('notnull' => false));
        $this->createForeignKey(
            $this->_tableName,
            $this->_fkName,
            array(
                'local' => $this->_colName2,
                'foreign' => 'id',
                'foreignTable' => 'users',
                'onDelete' => 'CASCADE',
                )
            );
        Doctrine_Manager::connection()->execute('CREATE SEQUENCE payment_id_seq START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;');
        Doctrine_Manager::connection()->execute('ALTER SEQUENCE payment_id_seq OWNED BY payment.id;');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName5 . ' DROP NOT NULL');
        $this->removeColumn($this->_tableName, $this->_colName4);
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('DROP SEQUENCE payment_id_seq');
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->removeColumn($this->_tableName, $this->_colName1);
        $this->removeColumn($this->_tableName, $this->_colName2);
        $this->removeColumn($this->_tableName, $this->_colName3);
        $this->addColumn($this->_tableName, $this->_colName4, 'character varying(256)', null, array('notnull' => true));
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName5 . ' SET NOT NULL');
    }
}

