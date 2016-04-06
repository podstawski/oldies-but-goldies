<?php
/**
 * @author Radosław Benkel
 */
 
abstract class Playgine_Event_Abstract
{
	protected $_options;
    protected $_eventId;
    protected $_eventName;

    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
    }

    /**
     * @param  $key
     * @return mixed
     */
	public function getOption($key)
	{
		return isset($this->_options[$key]) ? $this->_options[$key] : null;
	}

	public function getOptions()
	{
		return $this->_options;
	}

    public function setTaskId($eventId)
	{
		$this->_eventId = $eventId;
		return $this;
	}

	public function getTaskId()
	{
		return $this->_eventId;
	}

    /**
     * Generate this task instance options based on params
     * @return void
     */
	public function init($min, $max)
	{
        if ($max !== 0) {
            $value = rand($min, $max);
        } else {
            $value = $min;
        }
        return array('param' => $value);
	}

    public function setTaskName($eventName)
    {
        $this->_eventName = $eventName;
    }

    public function getTaskName()
    {
        return $this->_eventName;
    }
}
