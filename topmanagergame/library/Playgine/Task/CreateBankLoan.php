<?php
/**
 * @author Radosław Szczepaniak
 */

class Playgine_Task_CreateBankLoan extends Playgine_Task_Abstract
{
    public function run()
    {
        $modelLoan = new Model_Loan();
        $modelLoan->createLoanForCompany($this->getCompany(), $this->getOptions());
        $this->setCost(-1 * $this->getOption('amount'));
    }

    public function getMessageParams()
    {
        $bankParams = Model_Param::get('bank');
        $bankParams = $bankParams[$this->getOption('bank_id')];
        return array(
            $bankParams['name'],
            $this->currency($this->getOption('amount')),
            $this->getOption('duration')
        );
    }
}