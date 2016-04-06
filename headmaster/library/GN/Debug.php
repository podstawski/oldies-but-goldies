<?php
class GN_Debug
{
	public static function debug($what, $msg = null) {
		$controller = Zend_Controller_Front::getInstance();
		$opt = $controller->getParam('bootstrap')->getOption('debug');

		$path = $opt['path'];
		if (empty($path)) {
			return;
		}

		$line = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);
		$line .= ' ' . $what;
		if (!empty($msg)) {
			$line .= ': ' . $msg;
		}
		$line .= PHP_EOL;
		file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
	}

	public static function trace() {
		try {
			throw new Exception();
		} catch (Exception $e) {
			foreach (explode("\n", $e->getTraceAsString()) as $line) {
				self::debug($line);
			}
		}
	}

}

