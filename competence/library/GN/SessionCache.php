<?php
class GN_SessionCache {
	const NS = 'chrup-cache';

	public static function isFresh($key) {
		$stopped = GN_Session::isStopped();
		if ($stopped) {
			GN_Session::restore();
		}

		$ret = false;
		if (isset($_SESSION[self::NS][$key])) {
			if ($_SESSION[self::NS][$key]['alive'] === false) {
				$ret = true;
			} elseif (time() < $_SESSION[self::NS][$key]['expires']) {
				$ret = true;
			} else {
			}
		}

		if ($stopped) {
			GN_Session::stop();
		}
		return $ret;
	}



	public static function delete($key) {
		$stopped = GN_Session::isStopped();
		if ($stopped) {
			GN_Session::restore();
		}

		if (!isset($_SESSION[self::NS][$key])) {
			$ret = false;
		} else {
			unset($_SESSION[self::NS][$key]);
			$ret = true;
		}

		if ($stopped) {
			GN_Session::stop();
		}
		return $ret;
	}



	public static function get($key) {
		$stopped = GN_Session::isStopped();
		if ($stopped) {
			GN_Session::restore();
		}

		if (!isset($_SESSION[self::NS][$key])) {
			$ret = null;
		} else {
			$ret = unserialize($_SESSION[self::NS][$key]['data']);
		}

		if ($stopped) {
			GN_Session::stop();
		}
		return $ret;
	}



	//if alive === false, cache will die only after session ends or key is manually removed
	public static function set($key, $data, $alive = 300) {
		$stopped = GN_Session::isStopped();
		if ($stopped) {
			GN_Session::restore();
		}

		if (!isset($_SESSION[self::NS])) {
			$_SESSION[self::NS] = array();
		}
		$_SESSION[self::NS][$key] = array(
			'data' => serialize($data),
			'alive' => $alive,
			'expires' => time() + $alive,
		);

		if ($stopped) {
			GN_Session::stop();
		}
	}
}

