<?php

class Playgine_Task_FireEmployee extends Playgine_Task_Abstract
{
    /**
     * @var Zend_Db_Table_Rowset
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
            'company_id = ?' => $this->getCompany()->id,
            'type = ?' => $type,
            'id IN (?)' => array_keys($employees),
            'fired = 0',
        ));

        if ($this->_employees->count() != count($employees)) {
            throw new Playgine_Exception('this operation is not allowed');
        }
    }

    public function run()
    {
        $names = array();
        foreach ($this->_employees as $employee) {
            $employee->fired = 1;
            $employee->save();

            $names[] = $employee->findParentRow('Model_EmployeeCv')->name;
        }
        $this->_names = implode(', ', $names);

        $type = $this->getOption('type');
        $employees = $this->getCompany()->getEmployeeRow($type);
        $employees->fired = $this->_employees->getTable()->getAdapter()->fetchOne('SELECT COUNT(id) FROM company_employee WHERE company_id = ? AND type = ? AND fired = 1', array(
            $this->getCompany()->id,
            $type,
        ));
        $employees->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->_names
        );
    }
}