<?php

class Playgine_Task_TrainEmployee extends Playgine_Task_Abstract
{
    /**
     * @var Model_EmployeeRow
     */
    protected $_employees;

    public function init()
    {
        $type = $this->getOption('type');
        if ($type == null) {
            throw new Playgine_Exception('no recruits type provided');
        }

        $this->_employees = $this->getCompany()->getEmployeeRow($type);

        if (!$this->_employees->getCanTrain()) {
            throw new Playgine_Exception('You cannot further train your employees');
        }

        $this->setCost($this->_employees->getTrainingCost());
    }

    public function run()
    {
        $this->_employees->skill_level++;
        $this->_employees->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->_employees->skill_level
        );
    }
}