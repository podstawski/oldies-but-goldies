<?php
/**
 * @author Radosław Benkel
 */
 
abstract class Playgine_Task_Abstract
{
    /**
     * @var int
     */
    protected $_taskId;

    /**
     * @var string
     */
    protected $_taskName;

    /**
     * @var Model_CompanyRow
     */
    protected $_company;

    /**
     * @var array
     */
	protected $_options;

	/**
     * @var bool
     */
    protected $_ignore = false;

    /**
     * @var float
     */
    protected $_cost;

    /**
     * @var int
     */
    protected $_day;

    /**
     * @var bool
     */
    protected $_storeMessage = true;

	/**
	 * @param Model_CompanyRow $company
	 * @return Playgine_Task_Abstract
	 */
	public function setCompany(Model_CompanyRow $company)
	{
		$this->_company = $company;
		return $this;
	}

	/**
	 * @return Model_CompanyRow
	 */
	public function getCompany()
	{
		return $this->_company;
	}

    /**
     * @param array $options
     * @return Playgine_Task_Abstract
     */
	public function setOptions(array $options)
	{
		$this->_options = $options;
		return $this;
	}

    /**
     * @param  string $key
     * @param  mixed $value
     * @return Playgine_Task_Abstract
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
        return $this;
    }

    /**
     * @param  $key
     * @return mixed
     */
	public function getOption($key)
	{
		return isset($this->_options[$key]) ? $this->_options[$key] : null;
	}

    /**
     * @return array
     */
	public function getOptions()
	{
		return $this->_options;
	}

    /**
     * @param  int $taskId
     * @return Playgine_Task_Abstract
     */
	public function setTaskId($taskId)
	{
		$this->_taskId = $taskId;
        if (($taskName = Playgine_TaskFactory::getTaskNameByType($taskId)) != $this->getTaskName()) {
            $this->_taskName = $taskName;
        }
		return $this;
	}

    /**
     * @return int
     */
	public function getTaskId()
	{
		return $this->_taskId;
	}

    public function getTaskName()
    {
        return $this->_taskName;
    }

    public function setTaskName($taskName)
    {
        $this->_taskName = $taskName;
        if (($taskId = Playgine_TaskFactory::getTaskTypeByName($taskName)) != $this->getTaskId()) {
            $this->_taskId = $taskId;
        }
        return $this;
    }

    /**
     * @param bool $check
     * @return Playgine_Task_Abstract
     */
    public function setIgnoreCheck($check)
    {
        $this->_ignore = $check === true ? true : false;
        return $this;
    }

    /**
     * @return bool
     */
    public function getInoreCheck()
    {
        return $this->_ignore;
    }

    /**
     * @param  int $cost
     * @return Playgine_Task_Abstract
     */
    public function setCost($cost)
    {
        $this->_cost = $cost;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCost()
    {
        return (is_null($this->_cost)) ? false : true;
    }

    /**
     * @return int if its negative, it means that running that tasks ADDs profits
     */
    public function getCost()
    {
        return $this->_cost ? : 0;
    }

    /**
     * @param $day
     * @return Playgine_Task_Abstract
     */
    public function setDay($day)
    {
        $this->_day = $day;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDay()
    {
        return $this->_day;
    }

    /**
     * @param $flag
     * @return Playgine_Task_Abstract
     */
    public function setStoreMessage($flag)
    {
        $this->_storeMessage = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getStoreMessage()
    {
        return $this->_storeMessage;
    }

	/**
	 * Stores logic of executing a task - you shouldn't run this directly - it doesn't contain validation!
	 * @return void
	 */
	abstract public function run();

	/**
	 * Place for storing validation of params, cost must be set here!
     * It is called directly before run.
	 * @return void
	 */
	public function beforeRun() {}

	/**
	 * Logic to be done before queuing. Note that it is called
     * when task needs to be confirmed by all company owners.
	 * @return void
	 */
	public function beforeQueue() {}

    /**
     * Logic to be done after running task or when not all company owners confirmed.
     * @return void
     */
    public function afterRun() {}

    /**
     * Task initialization.
     * Unlike beforeRun, beforeQueue and afterRun, init is always called.
     * @return void
     */
    public function init() {}

    public function getMessage($type = Playgine_TaskManager::TASK_RUN)
    {
        $class = explode('_', get_called_class());
        return $this->translate(
            array_pop($class) . ':' . $type,
            $this->getMessageParams($type)
        );
    }

    public function getMessageParams($type)
    {
        return array();
    }

    protected function translate($messageid, array $params = null)
    {
        if (Zend_Registry::isRegistered('Zend_Translate')) {
            return vsprintf(Zend_Registry::get('Zend_Translate')->translate($messageid), $params);
        }
        return $messageid;
    }

    protected function currency($number)
    {
        if (Zend_Registry::isRegistered('Zend_Currency')) {
            return Zend_Registry::get('Zend_Currency')->toCurrency($number);
        }

        return $number;
    }

    public function reset()
    {
        $this->_company = $this->_cost
                        = $this->_day
                        = $this->_options
                        = null;
                        
        $this->_ignore = false;
    }
}
