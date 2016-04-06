<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterTableEmployeeCvAddColumnFace extends Doctrine_Migration_Base
{
    private $_tableName = 'employee_cv';
    private $_colName = 'face';

    public function up()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' SMALLINT');
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('UPDATE ' . $this->_tableName . ' SET ' . $this->_colName . ' = TRUNC(RANDOM() * 17)');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN ' . $this->_colName . ' SET NOT NULL');
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }
}