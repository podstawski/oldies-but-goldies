<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_CommitmentRow extends Zend_Db_Table_Row_Abstract
{
    /**
     * @return bool
     */
    public function isPenalty()
    {
        return in_array($this->type, Model_Commitment::$penaltyTypes);
    }

    /**
     * @return float
     */
    public function calculateInterest()
    {
        if ($this->type == Model_Commitment::BANK_LOAN) {
            $params = Model_Param::get('bank');
            $params = $params[$this->object_id];
            $interest = $this->cost * $params['interest_percentage'] / 100;
        } else {
            $params = Model_Param::get('commitment.penalty.interest_percentage');
            $interest = $this->cost * rand($params['min'], $params['max']) / 100;
        }
        return $interest;
    }
}
