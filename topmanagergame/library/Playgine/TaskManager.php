<?php
/**
 * @author Radosław Benkel
 */
 
class Playgine_TaskManager
{
    const TASK_RUN   = 'run';
    const TASK_QUEUE = 'queue';

    /**
     * @var \Model_Queue
     */
    protected $_modelQueue;

    public function __construct()
    {
        $this->_modelQueue = new Model_Queue();
    }

    /**
     * @param Playgine_Task_Abstract $task
     * @return string
     */
    public function runTask(Playgine_Task_Abstract $task)
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        try {
            $db->beginTransaction();
            $message = $this->_runTask($task);
            $db->commit();
            return $message;
        } catch (Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }

    public function _runTask(Playgine_Task_Abstract $task)
    {
        $this->_validateTask($task);
        $task->init();
        if ($this->_canRunTask($task)) {
            $task->beforeRun();
            $this->_validateCostIfNeeded($task);
            $task->run();
            $task->afterRun();
            $message = $task->getMessage(self::TASK_RUN);
        } else {
            $task->beforeQueue();
            $this->_validateCostIfNeeded($task);
            $message = $task->getMessage(self::TASK_QUEUE);
            $this->_modelQueue->add($task, $message);
        }
        $this->_storeBalanceInfo($task, $message);
        return $message;
    }

    /**
     * @param Playgine_Task_Abstract $task
     * @return bool
     */
    private function _canRunTask(Playgine_Task_Abstract $task)
    {
        return $task->getInoreCheck() || $task->getDay() == null || ($task->getDay() == $task->getCompany()->getToday());
    }

    /**
     * @throws Playgine_Exception
     * @param Playgine_Task_Abstract $task
     */
    private function _validateTask(Playgine_Task_Abstract $task)
    {
        if ($task->getCompany() === null) {
            throw new Playgine_Exception('Company row must be set');
        }
    }

    /**
     * @throws Playgine_NotEnoughMoneyException
     * @param Playgine_Task_Abstract $task
     * @return void
     */
    private function _validateCostIfNeeded(Playgine_Task_Abstract $task)
    {
        if ($task->hasCost() && $task->getCompany()->balance < $task->getCost()) {
            throw new Playgine_NotEnoughMoneyException('You cant afford it');
        }
    }

    /**
     * @param Playgine_Task_Abstract $task
     * @param string $message
     * @return void
     */
    private function _storeBalanceInfo(Playgine_Task_Abstract $task, $message)
    {
        if ($task->getStoreMessage()) {
            $task->getCompany()->addBalanceInfo($task->getTaskId(), $task->getCost(), $message);
        }
    }
}
