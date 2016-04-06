<?php
class GN_Session
{
	private static $sessionStopped = false;
	private static $oldSessionData = '';
	private static $oldSessionId = '';

	public static function isStopped() {
		return self::$sessionStopped;
	}

	public static function stop() {
		if (!self::$sessionStopped) {
			self::$sessionStopped = true;
			self::$oldSessionData = $_SESSION;
			self::$oldSessionId = session_id();
			session_write_close();
		}
	}

	public static function restore($regenerate = false) {
		if (self::$sessionStopped) {
			session_id(self::$oldSessionId);
			#ini_set('session.use_only_cookies', false);
			#ini_set('session.use_cookies', false);
			#ini_set('session.use_trans_sid', false);
			#ini_set('session.cache_limiter', null);
			$oe = error_reporting();
			error_reporting(0);
			session_start();
			error_reporting($oe);
			#$_SESSION = self::$oldSessionData;
			self::$sessionStopped = false;
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
