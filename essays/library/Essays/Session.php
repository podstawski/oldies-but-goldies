<?php
class Essays_Session
{
	private static $sessionStopped = false;
	private static $oldSessionData = '';
	private static $oldSessionId = '';

	public static function isStopped() {
		return Essays_Session::$sessionStopped;
	}

	public static function stop() {
		if (!Essays_Session::$sessionStopped) {
			Essays_Session::$sessionStopped = true;
			Essays_Session::$oldSessionData = $_SESSION;
			Essays_Session::$oldSessionId = session_id();
			session_write_close();
		}
	}

	public static function restore($regenerate = false) {
		if (Essays_Session::$sessionStopped) {
			session_id(Essays_Session::$oldSessionId);
			$oe = error_reporting();
			error_reporting(0);
			session_start();
			error_reporting($oe);
			$_SESSION = Essays_Session::$oldSessionData;
			Essays_Session::$sessionStopped = false;
		}
	}
}

