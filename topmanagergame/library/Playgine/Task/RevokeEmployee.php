<?php

class Playgine_Task_RevokeEmployee extends Playgine_Task_Abstract
{
    /**
     * @var Model_ProductRow
     */
    protected $product;

    /**
     * @var Model_EmployeeRow
     */
    protected $employee;

    public function init()
    {
        $id = intval($this->getOption('id'));
        if (!$id) {
            throw new InvalidArgumentException('Missing product ID');
        }
        $this->product = $this->getCompany()->getProduct($id);
        if ($this->product == null) {
            throw new Playgine_Exception('Could not find product');
        }
        $this->employee = $this->getCompany()->getEmployeeRow();
    }

    public function beforeRun()
    {
        if (!($this->product->employees > 0)) {
            throw new Playgine_Exception('No employees are assign to this product');
        }
    }

    public function run()
    {
        $this->product->employees = max(0, $this->product->employees - 1);
        $this->product->save();

        $this->employee->busy = max(0, $this->employee->busy - 1);
        $this->employee->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->translate('ProductType:' . $this->product->type)
        );
    }
}