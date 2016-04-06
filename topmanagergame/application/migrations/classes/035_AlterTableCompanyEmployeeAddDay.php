<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterTableCompanyEmployeeAddDay extends Doctrine_Migration_Base
{
    private $_tableName = 'company_employee';
    private $_colName = 'day';

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' INT NOT NULL DEFAULT 1');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }
}