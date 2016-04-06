<?php
class GN_SessionCache {
	public static function isFresh($key) {
		$stop = false;
		if (GN_Session::isStopped()) {
			GN_Session::restore();
			$stop = true;
		}

		echo 'check1';
		$ret = false;
		if (isset($_SESSION['chrup-cache'][$key])) {
			echo 'check2';
			if (time() < $_SESSION['chrup-cache'][$key]['expires']) {
				echo 'check3';
				$ret = true;
			}
		}

		if ($stop) {
			GN_Session::stop();
		}
		return $ret;
	}



	public static function get($key) {
		$stop = false;
		if (GN_Session::isStopped()) {
			GN_Session::restore();
			$stop = true;
		}

		echo 'get';
		if (!isset($_SESSION['chrup-cache'][$key])) {
			return null;
		}
		$ret = unserialize($_SESSION['chrup-cache'][$key]['data']);

		if ($stop) {
			GN_Session::stop();
		}
		return $ret;
	}



	public static function set($key, $data, $alive) {
		$stop = false;
		if (GN_Session::isStopped()) {
			GN_Session::restore();
			$stop = true;
		}

		echo 'set';
		if (!isset($_SESSION['chrup-cache'])) {
			$_SESSION['chrup-cache'] = array();
		}
		$_SESSION['chrup-cache'][$key] = array(
			'data' => serialize($data),
			'expires' => time() + $alive,
		);

		if ($stop) {
			GN_Session::stop();
		}
	}
}

