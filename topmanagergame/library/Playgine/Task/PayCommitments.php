<?php

class Playgine_Task_PayCommitments extends Playgine_Task_Abstract
{
    /**
     * @var Zend_Db_Table_Rowset
     */
    protected $_commitments;

    public function init()
    {
        $commitmentTypes = (array) $this->getOption('commitmentTypes');

        if (empty($commitmentTypes)) {
            throw new Playgine_Exception('please select at least one commitment');
        }

        if (($k = array_search(Model_Commitment::PAY_ALL_PENALTY, $commitmentTypes)) !== false) {
            unset($commitmentTypes[$k]);
            $commitmentTypes = array_merge($commitmentTypes, Model_Commitment::$penaltyTypes);
        }

        $cost = 0;
        foreach ($this->_commitments = $this->getCompany()->getCommitments(array_unique($commitmentTypes)) as $commitment) {
            $cost += $commitment->cost;
        }
        $this->setCost($cost);
    }

    public function run()
    {
        foreach ($this->_commitments as $commitment) {
            $task = Playgine_TaskFactory::factory((int) $commitment->type);
            $task->setCompany($this->getCompany())
                 ->setCommitment($commitment);
            $task->init();
            $task->beforeRun();
            $task->run();
            $task->afterRun();
        }

        $this->getCompany()->checkOldestCommitment();

        $this->setCost(0);
    }
}