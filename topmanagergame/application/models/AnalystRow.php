<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_AnalystRow extends Zend_Db_Table_Row_Abstract
{
    public function getPrediction()
    {
        return ($this->prediction > 0 ? '+' : '') . $this->prediction . '%';
    }

    public function getShareAmount()
    {
        return round($this->share_amount) . '%';
    }
}
