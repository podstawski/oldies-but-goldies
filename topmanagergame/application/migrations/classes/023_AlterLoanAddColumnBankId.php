<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterLoanAddColumnBankId extends Doctrine_Migration_Base
{
    private $_tableName = 'loan';
    private $_colName = 'bank_id';

    public function up()
    {
        Doctrine_Manager::connection()->execute('TRUNCATE TABLE '. $this->_tableName);

        $this->addColumn($this->_tableName, $this->_colName, 'smallint', null, array(
            'notnull' => true
        ));
    }

    public function down()
    {
        $this->removeColumn($this->_tableName, $this->_colName);
    }
}