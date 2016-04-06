<?php
class GN_Session
{
	private static $sessionStopped = false;
	private static $oldSessionData = '';
	private static $oldSessionId = '';

	public static function isStopped() {
		return GN_Session::$sessionStopped;
	}

	public static function stop() {
		if (!GN_Session::$sessionStopped) {
			GN_Session::$sessionStopped = true;
			GN_Session::$oldSessionData = $_SESSION;
			GN_Session::$oldSessionId = session_id();
			session_write_close();
		}
	}

	public static function restore($regenerate = false) {
		if (GN_Session::$sessionStopped) {
			session_id(GN_Session::$oldSessionId);
			$oe = error_reporting();
			error_reporting(0);
			session_start();
			error_reporting($oe);
			$_SESSION = GN_Session::$oldSessionData;
			GN_Session::$sessionStopped = false;
		}
	}

	public static function detachBrowser($url = null) {
		if (!empty($url)) {
			header('Location: ' . $url);
		}
		usleep(2000);
		echo '<script type="text/javascript">';
		echo 'function reload() {';
		if (!empty($url)) {
			echo 'window.location.href="' . $url . '";';
		}
		echo '}';
		echo 'setTimeout(reload,300);';
		echo '</script>';

		//wydaje mi się, że to zamiast tego można dać Content-Length: 0
		for ($i = 0; $i < 4096; $i++) {
			echo ' ' . PHP_EOL;
		}
		flush();
		ob_end_flush();
		ini_set('max_execution_time', 0);
	}

}
