<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Playgine_Task_PayBankLoan extends Playgine_Task_PayCommitment
{
    /**
     * @var Model_LoanRow
     */
    private $_loan;

    public function run()
    {
        $this->_loan = $this->getCompany()->getLoan($this->getCommitment()->object_id);
        $this->_loan->interests += max($this->getCost() - $this->_loan->single_installment_amount, 0);
        $this->_loan->months_paid++;
        $this->_loan->save();
    }

    public function getMessageParams()
    {
        $bankParams = Model_Param::get('bank');
        $bankParams = $bankParams[$this->_loan->bank_id];
        return array(
            $this->currency($this->getCost()),
            $bankParams['name'],
        );
    }
}