<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterTableCompanyAddColumnStatus extends Doctrine_Migration_Base
{
    private $_tableName = 'company';
    private $_colName = 'status';

    public function up()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN ' . $this->_colName . ' INTEGER NOT NULL DEFAULT 0');
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' DROP COLUMN ' . $this->_colName);
    }
}