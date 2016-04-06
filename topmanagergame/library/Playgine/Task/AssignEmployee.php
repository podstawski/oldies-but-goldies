<?php

class Playgine_Task_AssignEmployee extends Playgine_Task_Abstract
{
    /**
     * @var Model_ProductRow
     */
    protected $product;

    /**
     * @var Model_EmployeeRow
     */
    protected $workers;

    public function init()
    {
        $id = intval($this->getOption('id'));
        if (!$id) {
            throw new InvalidArgumentException('Invalid product ID');
        }

        $this->product = $this->getCompany()->getProduct($id);
        if ($this->product == null) {
            throw new Playgine_Exception('Could not find product');
        }

        $this->workers = $this->product->getWorkers();
    }

    public function run()
    {
        $amount = intval($this->getOption('amount'));

        $this->product->employees = $amount;
        $this->product->output = min($this->product->output, $this->product->getMaxOutput());
        $this->product->save();
        
        $amount = $this->product->getTable()->getAdapter()->query('SELECT SUM(employees) FROM product WHERE company_id = ?', array(
            $this->getCompany()->id
        ))->fetchColumn();

        if ($amount > $this->workers->getMaxAmount()) {
            throw new Playgine_Exception('Cannot assign that many employees');
        }

        $this->workers->busy = $amount;
        $this->workers->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->getOption('amount'),
            $this->translate('ProductType:' . $this->product->type)
        );
    }
}