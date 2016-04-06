<?php

class Playgine_Task_HireEmployee extends Playgine_Task_Abstract
{
    protected $employee;
    
    public function init()
    {
        $this->employee = $this->getCompany()->getEmployeeRow();
    }
    
    public function run()
    {
        $this->employee->amount++;
        $this->employee->save();
    }
}