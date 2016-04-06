<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterCompanyEmployeeAddColumnFired extends Doctrine_Migration_Base
{
    private $_tableName = 'company_employee';

    private $_colName = 'fired';

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' SMALLINT NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
    }
}