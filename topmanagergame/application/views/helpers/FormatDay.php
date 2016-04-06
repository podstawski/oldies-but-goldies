<?php
/**
 * Class used to format number of days into game date (01/02/01)
 * @author Radosław Benkel 
 */

class Zend_View_Helper_FormatDay extends Zend_View_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_weekMonthYear;

    /**
     * @var string
     */
    protected $_format = Zend_Date::DATE_LONG;

    /**
     * @param null $gameDay
     * @return Zend_View_Helper_FormatDay
     */
    public function formatDay($gameDay = null, $format = null)
	{
	    if ($gameDay === null)
	        $gameDay = Model_Player::getCompany()->getToday();

        if ($format)
            $this->_format = $format;

		$this->_weekMonthYear = Model_Day::gameDayIntoGameDate($gameDay);

		return $this;
	}

    /**
     * @return string
     */
    public function asString($format = null)
	{
	    $date = new Zend_Date;
	    $date->setDay($this->_weekMonthYear['day']);
	    $date->setMonth($this->_weekMonthYear['month']);
	    $date->setYear($this->_weekMonthYear['year']);

	    if ($format === null)
	        $format = $this->_format;

		return $date->toString($format);
	}

    /**
     * @return array
     */
    public function toArray()
	{
		return $this->_weekMonthYear;
	}

    /**
     * @return string
     */
    public function __toString()
	{
		return $this->asString();
	}

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get' && array_key_exists($key = lcfirst(substr($name, 3)), $this->_weekMonthYear))
            return $this->_weekMonthYear[$key];
    }
}