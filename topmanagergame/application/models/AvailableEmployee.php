<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_AvailableEmployee extends Zend_Db_Table_Abstract
{
    const GENERATE_AMOUNT = 10;

    protected $_name = 'available_employee';

    protected $_referenceMap = array(
        'EmployeeCv' => array(
            'columns' => 'employee_cv_id',
            'refTableClass' => 'Model_EmployeeCv',
            'refColumns' => 'id',
            'onDelete' => 'CASCADE',
        )
    );

    /**
     * @return Model_AvailableEmployee
     */
    public function generateForCompany(Model_CompanyRow $company)
    {
        $this->delete('1 = 1');

        $modelEmployeeCv = new Model_EmployeeCv();

        for ($i = 0; $i < self::GENERATE_AMOUNT; $i++) {
            $cv = $modelEmployeeCv->generateCV();
            $employee = $this->createRow();
            $employee->employee_cv_id = $cv->id;
            $employee->save();
        }

        return $this;
    }
}