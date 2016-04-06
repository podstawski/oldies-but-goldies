<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_TaxRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @return float
     */
    public function getRevenue()
    {
        return floatval($this->income - $this->costs);
    }
}