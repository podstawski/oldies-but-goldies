<?php

class Playgine_Task_CalculateCommitments extends Playgine_Task_Abstract
{
    /**
     * @var Model_Commitment
     */
    protected $_modelCommitment;

    /**
     * @var Model_Tax
     */
    protected $_modelTax;

    /**
     * @var Model_Balance
     */
    protected $_modelBalance;

    /**
     * @var bool
     */
    protected $_storeMessage = false;

    /**
     * @var Model_TaxRow
     */
    protected $_taxRow;

    /**
     * @var array
     */
    protected $_previousMonthParams;

    public function init()
    {
        $this->_modelCommitment = new Model_Commitment;
        $this->_modelTax = new Model_Tax;
        $this->_modelBalance = new Model_Balance;
        $this->_previousMonthParams = Model_Day::getPreviousMonthParams($this->getDay());
    }

    public function run()
    {
        $this->_calculateCostsAndIncome();
        $this->_calculateIncomeTax();
        $this->_calculateVatTax();
        $this->_calculatePayments();
        $this->_calculateSocialInsurance();
        $this->_calculateFixedCost();
        $this->_calculateLoanInstallment();
    }

    private function _insertCommitment($type, $cost, $delay, $objectID = null)
    {
        if ($cost > 0)
            $this->_modelCommitment->insert(array(
                'company_id' => $this->getCompany()->id,
                'type'       => $type,
                'cost'       => $cost,
                'day'        => $this->getCompany()->getToday() + $delay,
                'object_id'  => $objectID
            ));
    }

    private function _calculateCostsAndIncome()
    {
        list ($month, $year, $from, $to) = $this->_previousMonthParams;

        $tasks_netto = array(
            Playgine_TaskFactory::getTaskTypeByName('NpcBuy'),
            Playgine_TaskFactory::getTaskTypeByName('Production'),
            Playgine_TaskFactory::getTaskTypeByName('UpgradeTechnology'),
            Playgine_TaskFactory::getTaskTypeByName('UpgradeQuality'),
            Playgine_TaskFactory::getTaskTypeByName('Recruit'),
            Playgine_TaskFactory::getTaskTypeByName('TrainEmployee'),
            Playgine_TaskFactory::getTaskTypeByName('PayFixedCosts'),
        );

        $tasks_brutto = array(
            Playgine_TaskFactory::getTaskTypeByName('PayEmployeePayment'),
            Playgine_TaskFactory::getTaskTypeByName('PaySocialInsurance'),
        );

        list ($costs_netto, $income_netto)   = $this->_modelBalance->fetchAllCostsAndIncome($this->getCompany()->id, $from, $to, $tasks_netto);
        list ($costs_brutto, $income_brutto) = $this->_modelBalance->fetchAllCostsAndIncome($this->getCompany()->id, $from, $to, $tasks_brutto);

        $taxVat = 1 + Model_Param::get('tax.vat');

        $this->_taxRow = $this->_modelTax->setForCompanyYearMonth(
            $this->getCompany()->id,
            $costs_netto / $taxVat + $costs_brutto,
            $income_netto / $taxVat + $income_brutto,
            $year,
            $month
        );
    }

    private function _calculateIncomeTax()
    {
        $thisYear = $this->_modelTax->fetchForCompanyYearSum($this->getCompany()->id, $this->_taxRow->year);
        if ($thisYear == null)
            return;

        $tax = max($thisYear->getRevenue() * Model_Param::get('tax.straight') - $thisYear->income_tax, 0);

        //RB in december we pay twice amount
        if ($this->_taxRow->month == 12)
            $tax *= 2;

        $this->_taxRow->income_tax = $tax;
        $this->_taxRow->save();

        $this->_insertCommitment(Model_Commitment::INCOME_TAX, $tax, 20);
    }

    private function _calculateVatTax()
    {
        list ($month, $year, $from, $to) = $this->_previousMonthParams;
        $tasks = array(
            Playgine_TaskFactory::getTaskTypeByName('NpcBuy'),
            Playgine_TaskFactory::getTaskTypeByName('Production'),
            Playgine_TaskFactory::getTaskTypeByName('UpgradeTechnology'),
            Playgine_TaskFactory::getTaskTypeByName('UpgradeQuality'),
            Playgine_TaskFactory::getTaskTypeByName('Recruit'),
            Playgine_TaskFactory::getTaskTypeByName('TrainEmployee'),
        );

        list ($costs, $income) = $this->_modelBalance->fetchAllCostsAndIncome($this->getCompany()->id, $from, $to, $tasks);

        $vatTax = 1 + Model_Param::get('tax.vat');
        $vatLastMonth = min($this->_taxRow->vat_to_pay, 0);
        $vatCosts = $costs - $costs / $vatTax;
        $vatIncome = $income - $income / $vatTax;
        $vatToPay = floatval($vatIncome - $vatCosts - $vatLastMonth);

        $this->_taxRow->vat_to_pay = $vatToPay;
        $this->_taxRow->save();

        $this->_insertCommitment(Model_Commitment::VAT_TAX, $vatToPay, 25);
    }

    private function _calculatePayments()
    {
        $today = $this->getCompany()->getToday();
        list ($month, $year, $from, $to) = $this->_previousMonthParams;
        $salaryCost = 0;
        foreach (Model_CompanyEmployee::$types as $type) {
            $employeeRow = $this->getCompany()->getEmployeeRow($type);
            $avgSalary = $employeeRow->getAvgSalary();
            foreach ($employeeRow->getEmployees() as $employee) {
                $gameDate = Model_Day::gameDayIntoGameDate($employee->day);
                if ($gameDate['month'] == $month && $gameDate['year'] == $year)
                    $salaryCost += $avgSalary * ($today - $employee->day + 1) / ($to - $from + 1);
                else
                    $salaryCost += $avgSalary;
            }
//            $salaryCost += $this->getCompany()->getEmployeeRow($type)->getSalaryCost();
        }
        $this->_insertCommitment(Model_Commitment::EMPLOYEE_PAYMENT, $salaryCost, 10);
    }

    private function _calculateSocialInsurance()
    {
        $today = $this->getCompany()->getToday();
        list ($month, $year, $from, $to) = $this->_previousMonthParams;
        $socialInsuranceCost = 0;
        foreach (Model_CompanyEmployee::$types as $type) {
            $employeeRow = $this->getCompany()->getEmployeeRow($type);
            $avgSocialInsurance = $employeeRow->getAvgSocialInsurance();
            foreach ($employeeRow->getEmployees() as $employee) {
                $gameDate = Model_Day::gameDayIntoGameDate($employee->day);
                if ($gameDate['month'] == $month && $gameDate['year'] == $year)
                    $socialInsuranceCost += $avgSocialInsurance * ($today - $employee->day + 1) / ($to - $from + 1);
                else
                    $socialInsuranceCost += $avgSocialInsurance;
            }
//            $socialInsuranceCost += $this->getCompany()->getEmployeeRow($type)->getSocialInsuranceCost();
        }
        $this->_insertCommitment(Model_Commitment::SOCIAL_INSURANCE, $socialInsuranceCost, 15);
    }

    private function _calculateFixedCost()
    {
        $amount = 0;
        foreach (Model_CompanyEmployee::$types as $type) {
            $amount += $this->getCompany()->getEmployeeRow($type)->amount;
        }
        $accountancy = Model_Param::get('accountancy');
        $fixedCost = $accountancy['cost'] + $accountancy['per_employee'] * $amount;
        $this->_insertCommitment(Model_Commitment::FIXED_COST, $fixedCost, 10);
    }

    private function _calculateLoanInstallment()
    {
        $stmt = $this->_modelCommitment->getAdapter()->prepare('SELECT COUNT(id)
            FROM commitment
            WHERE type = ?
            AND company_id = ?
            AND object_id = ?'
        );
        foreach ($this->getCompany()->getLoanRows() as $loan) {
            $stmt->execute(array(
                Model_Commitment::BANK_LOAN,
                $loan->company_id,
                $loan->id,
            ));
            if ($stmt->fetchColumn() < $loan->months_amount)
                $this->_insertCommitment(Model_Commitment::BANK_LOAN, $loan->single_installment_amount, 10, $loan->id);
        }
    }
}