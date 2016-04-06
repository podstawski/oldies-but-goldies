<?php
/**
 * @author Radosław Szczepaniak
 */

abstract class Playgine_Task_PayCommitment extends Playgine_Task_Abstract
{
    /**
     * @var Model_CommitmentRow
     */
    protected $_commitment;

    final public function setCommitment(Model_CommitmentRow $commitment)
    {
        $this->_commitment = $commitment;
        return $this;
    }

    final public function getCommitment()
    {
        return $this->_commitment;
    }

    final public function init()
    {
        if (!($this->_commitment instanceof Model_CommitmentRow)) {
            throw new Playgine_Exception('Commitment must be an instance of Model_CommitmentRow');
        }

        $this->setCost(max($this->getCommitment()->cost, 0));
    }

    public function run() {}

    final public function afterRun()
    {
        if ($this->getCost()) {
            $this->getCompany()->addBalanceInfo(
                $this->getTaskId(),
                $this->getCost(),
                $this->getMessage()
            );
        }

        $commitment = clone $this->getCommitment();
        $commitment->delete();
    }

    public function getMessageParams()
    {
        $currency = Zend_Registry::get('Zend_Currency');
        return array(
            $currency->toCurrency($this->getCost())
        );
    }
}