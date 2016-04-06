<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_CompanyEmployee extends Zend_Db_Table_Abstract
{
    const MIN_AVAILABLE = 500;

    const TYPE_WORKER  = 0;
    const TYPE_MANAGER = 1;

    public static $types = array(self::TYPE_WORKER, self::TYPE_MANAGER);

    protected $_name = 'company_employee';

    protected $_referenceMap = array(
        'EmployeeCv' => array(
            'columns' => 'employee_cv_id',
            'refTableClass' => 'Model_EmployeeCv',
            'refColumns' => 'id',
            'onDelete' => 'RESTRICT',
        ),
        'Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id',
            'onDelete' => 'CASCADE',
        )
    );

    /**
     * @param int $amount
     * @return Model_CompanyEmployee
     */
    public function generateManager($amount = 1)
    {
        return $this->generate(self::TYPE_MANAGER, $amount);
    }

    /**
     * @param int $amount
     * @return Model_CompanyEmployee
     */
    public function generateWorker($amount = 1)
    {
        return $this->generate(self::TYPE_WORKER, $amount);
    }

    /**
     * @param int $type
     * @param int $amount
     * @return Model_CompanyEmployee
     */
    public function generate($type, $amount)
    {
        $modelEmployeeCv = new Model_EmployeeCv();

        for ($i = 0; $i < $amount; $i++) {
            $cv = $modelEmployeeCv->generateCV();
            $employee = $this->createRow();
            $employee->type = $type;
            $employee->employee_cv_id = $cv->id;
            $employee->save();
        }

        return $this;
    }
}