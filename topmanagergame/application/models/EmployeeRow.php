<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_EmployeeRow extends Zend_Db_Table_Row_Abstract
{
    protected $_tableClass = 'Model_Employee';

    protected $_salaryDetails;

    /**
     * @return float
     */
    public function getSalaryCost()
    {
        return floatval($this->amount * $this->getAvgSalary());
    }

    /**
     * @return float
     */
    public function getAvgSalary()
    {
        $salaryDetails = $this->getSalaryDetails();
        return floatval($salaryDetails['totalEmployerCost'] - $salaryDetails['socialInsuranceTotal']);
    }

    /**
     * @return float
     */
    public function getSocialInsuranceCost()
    {
        return floatval($this->amount * $this->getAvgSocialInsurance());
    }

    /**
     * @return float
     */
    public function getAvgSocialInsurance()
    {
        $salaryDetails = $this->getSalaryDetails();
        return floatval($salaryDetails['socialInsuranceTotal']);
    }

    /**
     * @return array
     */
    public function getSalaryDetails()
    {
        $salary = Model_Param::get('employee.' . $this->type . '.salary.' . $this->skill_level);

        $taxParams = Model_Param::get('tax');

        $retiringEmployee = round($salary * 0.0976, 2);
        $retiringEmployer = $retiringEmployee;
        $rentEmployee = round($salary * 0.015, 2);
        $rentEmployer = round($salary * 0.045, 2);
        $sicknessEmployee = round($salary * 0.0245, 2);
        $sicknessEmployer = round($salary * 0.0167, 2);
        $income = round($salary - $retiringEmployee - $rentEmployee - $sicknessEmployee, 2); //RB polish = 'przychód'
        $costs = floatval($taxParams['costs']);
        $profit = round($income - $costs);

        $tax = round($profit * 0.18, 2) - $taxParams['deduction']['month'];
        $tax = round(max($tax, 0), 2);

        $medicalInsurance = round($income * 0.09, 2);

        $medicalInsuranceTaxRelief = round($income * 0.0775, 2);
        $medicalInsuranceTaxRelief = ($medicalInsuranceTaxRelief < $tax) ? $medicalInsuranceTaxRelief : $tax;

        $medicalInsuranceToDepartment = min($medicalInsurance, $tax);

        $medicalInsuranceEmployee = round(max($medicalInsuranceToDepartment - $medicalInsuranceTaxRelief, 0), 2);

        $taxToDepartment = round(max($tax - $medicalInsuranceTaxRelief, 0), 2);

        $salaryToPay = round($salary - $retiringEmployee - $rentEmployee - $sicknessEmployee - $medicalInsuranceTaxRelief - $medicalInsuranceEmployee - $taxToDepartment, 2);

        $socialInsuranceEmployee = round($retiringEmployee + $rentEmployee + $sicknessEmployee, 2);
        $socialInsuranceEmployer = round($retiringEmployer + $rentEmployer + $sicknessEmployer, 2);
        $socialInsuranceTotal = round($socialInsuranceEmployee + $socialInsuranceEmployer, 2);

        $workFund = round($salary * 0.0245, 2);
        $employeeGuarantedBenefitsFund = round($salary * 0.001, 2);

        $totalEmployerCost = $salary + $socialInsuranceEmployer + $workFund + $employeeGuarantedBenefitsFund;

        return get_defined_vars();
    }

    /**
     * @return int
     */
    public function getNotBusy()
    {
        return intval($this->getMaxAmount() - $this->busy);
    }

    /**
     * @return float
     */
    public function getTrainingCost()
    {
        return floatval(Model_Param::get('employee.' . $this->type . '.training.' . ($this->skill_level + 1)));
    }

    /**
     * @return int
     */
    public function getEfficiency()
    {
        return intval(Model_Param::get('employee.' . $this->type . '.efficiency.' . $this->skill_level));
    }

    /**
     * @return bool
     */
    public function getCanTrain()
    {
        return $this->skill_level < Model_Employee::MAX_SKILL_LEVEL;
    }

    /**
     * @return int
     */
    public function getMaxAmount()
    {
        if ($this->type == Model_CompanyEmployee::TYPE_MANAGER) {
            return $this->amount;
        }

        $managers = $this->findParentRow('Model_Company')->getManagers();
        return min($this->amount, $managers->amount * $managers->getEfficiency());
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getEmployees()
    {
        $modelCompanyEmployee = new Model_CompanyEmployee;
        return $modelCompanyEmployee->fetchAll(array(
            'company_id = ?' => $this->company_id,
            'type = ?' => $this->type
        ));
    }
}
