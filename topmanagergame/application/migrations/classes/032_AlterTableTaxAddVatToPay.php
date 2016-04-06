<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterTableTaxAddVatToPay extends Doctrine_Migration_Base
{
    private $_tableName = 'tax';
    private $_colNames = array(
        'advance' => 'income_tax',
        'overpaid_vat_tax' => 'vat_to_pay'
    );

    public function up()
    {
        foreach ($this->_colNames as $old => $new) {
            Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' RENAME COLUMN ' . $old . ' TO ' . $new);
        }
    }

    public function down()
    {
        foreach ($this->_colNames as $old => $new) {
            Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' RENAME COLUMN ' . $new . ' TO ' . $old);
        }
    }
}