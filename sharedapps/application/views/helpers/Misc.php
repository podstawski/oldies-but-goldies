<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Zend_View_Helper_Misc extends Zend_View_Helper_Abstract {
	public function misc() {
		die();
	}

	private static function intvalize(array $input) {
		return array_map(function($a) { return intval($a); }, $input);
	}

	public static function convertDateTime($date) {
		if (strpos($date, ' ') !== false) {
			list ($datePart, $timePart) = explode(' ', $date);
		} else {
			$datePart = $date;
		}
		if (isset($datePart)) {
			list ($day, $month, $year) = Zend_View_Helper_Misc::intvalize(explode('-', $datePart));
			if (strlen($day) == 4) {
				$tmp = $day;
				$day = $year;
				$year = $tmp;
			}
		} else {
			list ($day, $month, $year) = Zend_View_Helper_Misc::intvalize(explode('-', date('d-m-Y')));
		}
		if (isset($timePart)) {
			list ($hour, $minute, $second) = Zend_View_Helper_Misc::intvalize(explode(':', $timePart));
		} else {
			$hour = 0;
			$minute = 0;
			$second = 0;
		}

		$unixTime = mktime($hour, $minute, $second, $month, $day, $year);
		return $unixTime;
	}

	public function replaceTokens($subject, $replacements) {
		foreach ($replacements as $from => $to) {
			$subject = str_replace('{' . $from . '}', $to);
		}
		return $subject;
	}

}
