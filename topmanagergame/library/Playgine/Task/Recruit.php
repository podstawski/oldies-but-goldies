<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Playgine_Task_Recruit extends Playgine_Task_Abstract
{
    /**
     * @var array
     */
    private $_employees;

    /**
     * @var string
     */
    private $_names;

    public function init()
    {
        $type = $this->getOption('type');
        if ($type == null) {
            throw new Playgine_Exception('no recruits type provided');
        }

        $employees = $this->getOption('employee');
        if (empty($employees)) {
            throw new Playgine_Exception('no recruits selected');
        }

        $modelCompanyEmployee = new Model_CompanyEmployee();
        $this->_employees = $modelCompanyEmployee->fetchAll(array(
            'company_id IS NULL',
            'type = ?' => $type,
            'id IN (?)' => array_keys($employees)
        ));

        if ($this->_employees->count() != count($employees)) {
            throw new Playgine_Exception('some recruits are no longer available');
        }

        $this->setCost($this->_employees->count() * Model_Param::get('recruitment_cost') * $this->getCompany()->getEmployeeRow($type)->getAvgSalary());
    }

    public function run()
    {
        $company = $this->getCompany();
        $today = $company->getToday();
        $names = array();
        foreach ($this->_employees as $employee) {
            $employee->company_id = $company->id;
            $employee->day = $today;
            $employee->save();

            $names[] = $employee->findParentRow('Model_EmployeeCv')->name;
        }
        $this->_names = implode(', ', $names);

        $employees = $company->getEmployeeRow($this->getOption('type'));
        $employees->amount += $this->_employees->count();
        $employees->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->_names
        );
    }
}