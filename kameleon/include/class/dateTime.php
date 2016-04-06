<?php
/*
 * Created on 2005-03-01
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

//require_once ('kronoclass/class.kronos.php');

class DT {
	
	var $startTime;
	var $date;

	//Time definition
	var $sec = 1;
	var $minute = 60;
	var $hour = 3600;
	var $day = 86400;
	
	function DT ()
	{
		$this->sec = 1;
		$this->minute = 60;
		$this->hour = 3600;
		$this->day = 86400;
	}
	
	function getDays ( $howManydays = 1 )
	{
		return $howManydays * $this->day;
	}
	
	function getLicense ( $YYYY_mm_dd )
	{
		$YYYY = substr($YYYY_mm_dd, 0,4);
		$mm = substr($YYYY_mm_dd, 5,2);
		$dd = substr($YYYY_mm_dd, 8,2);
		
		return mktime(0,0,0, $mm, $dd, $YYYY);
	}
	
	function getPLDate( $timestamp = '', $format = 'd-m-Y H:i' )
	{
		if (empty($timestamp)) $timestamp = time();
		return date($format, $timestamp);
	}

	function getPLTime( $timestamp )
	{
		return substr($this->getPLDate($timestamp), 11);
	}
//Class end	
}
?>
