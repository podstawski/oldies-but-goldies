<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_Day extends Zend_Db_Table_Abstract
{
    protected $_name = 'day';
    protected $_primary = 'day';

    /**
     * @var int
     */
    protected static $_today = null;

    /**
     * @var Model_Day
     */
	protected static $_instance = null;

    /**
     * @return Model_Day
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return void
     */
    public static function reset()
    {
        self::$_instance = self::$_today = null;
    }

    /**
     * @static
     * @return int
     */
    public static function getToday()
    {
        if (null === self::$_today) {
            self::$_today = self::getInstance()->fetchRow()->day;
        }
        return self::$_today;
    }

	public static function addDays($days)
	{
		self::getInstance()->update(array(
			'day' => new Zend_Db_Expr('day + ' . intval($days))
		), '1 = 1');
	}

    public static function gameDateIntoGameDay($day, $month, $year)
    {
        $datetime1 = new DateTime(sprintf('%02d.%02d.%04d', $day, $month, $year));
        $datetime2 = new DateTime(Model_Param::get('general.game_start_date'));

        if ($datetime1 < $datetime2) {
            return 1;
        }

        $gameday = $datetime1->diff($datetime2)->format('%a') + 1;
        return $gameday;
    }

    public static function gameDayIntoGameDate($gameDay = null)
    {
        if ($gameDay === null)
            $gameDay = self::getToday();

		$datetime = new DateTime(Model_Param::get('general.game_start_date'));
		$interval = new DateInterval('P' . ($gameDay - 1) . 'D');
		$datetime->add($interval);

		$day   = $datetime->format('j');
		$month = $datetime->format('n');
		$year  = $datetime->format('Y');

        list ($dayName, $monthName) = explode(' ', iconv('ISO-8859-2', 'UTF-8', strftime('%A %B', mktime(0, 0, 1, $month, $day, $year))));

		return compact('day', 'month', 'year', 'dayName', 'monthName');
    }

    public static function getCurrentMonthParams($gameDay = null)
    {
        if ($gameDay === null) {
            $gameDay = self::getToday();
        }
        $gameDate = self::gameDayIntoGameDate($gameDay);

        $year  = $gameDate['year'];
        $month = $gameDate['month'];

        $lastDayOfMonth = self::getLastDayOfMonth($month, $year);

        $from = self::gameDateIntoGameDay(1, $month, $year);
        $to   = self::gameDateIntoGameDay($lastDayOfMonth, $month, $year);

        return array($month, $year, $from, $to);
    }

    public static function getPreviousMonthParams($gameDay = null)
    {
        if ($gameDay === null) {
            $gameDay = self::getToday();
        }
        $gameDate = self::gameDayIntoGameDate($gameDay);

        $year  = $gameDate['year'];
        $month = $gameDate['month'];

        if ($month == 1) {
            $year -= 1;
            $month = 12;
        } else {
            $month -= 1;
        }

        $lastDayOfMonth = self::getLastDayOfMonth($month, $year);

        $from = self::gameDateIntoGameDay(1, $month, $year);
        $to   = self::gameDateIntoGameDay($lastDayOfMonth, $month, $year);

        return array($month, $year, $from, $to);
    }

    /**
     * @static
     * @param $month
     * @param $year
     * @return string
     */
    public static function getLastDayOfMonth($month, $year = null)
    {
        if ($year == null) {
            $gameDate = self::gameDayIntoGameDate($month);

            $year  = $gameDate['year'];
            $month = $gameDate['month'];
        }
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    /**
     * @static
     * @param $gameDay
     * @return string
     */
    public static function getLastDayOfMonthFromGameDay($gameDay)
    {
        $gameDate = self::gameDayIntoGameDate($gameDay);
        return self::getLastDayOfMonth($gameDate['month'], $gameDate['year']);
    }
}