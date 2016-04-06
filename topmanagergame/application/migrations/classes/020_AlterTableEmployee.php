<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterTableEmployee extends Doctrine_Migration_Base
{
    private $_tableName1 = 'employee';
    private $_tableName2 = 'product';

    private $_colName1 = 'type';
    private $_colName2 = 'output';

    public function up()
    {
        Doctrine_Manager::connection()->exec('TRUNCATE TABLE ' . $this->_tableName1);
        Doctrine_Manager::connection()->exec('TRUNCATE TABLE ' . $this->_tableName2);

        $this->addColumn($this->_tableName1, $this->_colName1, 'smallint', null, array(
            'notnull' => true,
        ));

        $this->addColumn($this->_tableName2, $this->_colName2, 'integer', null, array(
            'notnull' => true,
            'default' => 0
        ));
    }

    public function postUp()
    {
//        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName2 . ' ALTER COLUMN ' . $this->_colName2 . ' SET DEFAULT 0');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName2, $this->_colName2);
        $this->removeColumn($this->_tableName1, $this->_colName1);
    }
}