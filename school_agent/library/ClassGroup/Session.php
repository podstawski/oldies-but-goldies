<?php
class ClassGroup_Session
{
	private static $sessionStopped = false;
	private static $oldSessionData = '';
	private static $oldSessionId = '';

	public static function isStopped() {
		return ClassGroup_Session::$sessionStopped;
	}

	public static function stop() {
		if (!ClassGroup_Session::$sessionStopped) {
			ClassGroup_Session::$sessionStopped = true;
			ClassGroup_Session::$oldSessionData = $_SESSION;
			ClassGroup_Session::$oldSessionId = session_id();
			session_write_close();
		}
	}

	public static function restore($regenerate = false) {
		if (ClassGroup_Session::$sessionStopped) {
			session_id(ClassGroup_Session::$oldSessionId);
			$oe = error_reporting();
			error_reporting(0);
			session_start();
			error_reporting($oe);
			$_SESSION = ClassGroup_Session::$oldSessionData;
			ClassGroup_Session::$sessionStopped = false;
		}
	}
}
