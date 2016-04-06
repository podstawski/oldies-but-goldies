<?php
class GN_Debug
{
	private static $path = null;
	private static $enabled = false;
	private static $lastTime = null;

	public static function init() {
		if (class_exists('Zend_Controller_Front')) {
			$controller = Zend_Controller_Front::getInstance();
			$bootstrap = $controller->getParam('bootstrap');
			if ($bootstrap) {
				$opt = $bootstrap->getOption('debug');
				self::$path = $opt['path'];
				self::$enabled = $opt['enabled'];
			}
		}
		self::$lastTime = microtime(true);
	}

	public static function debug($what, $ident = 0) {
		if (!self::$enabled) {
			return;
		}

		if (is_array($what)) {
			foreach (explode("\n", str_replace("\r", '', print_r($what, true))) as $line) {
				self::debug($line, $ident);
			}
			return;
		} elseif (is_object($what)) {
			ob_start();
			var_dump($what);
			foreach (explode("\n", str_replace("\r", '', ob_get_contents())) as $line) {
				self::debug($line, $ident);
			}
			ob_end_clean();
			return;
		}

		$lastTime = self::$lastTime;
		$time = microtime(true);
		#self::$lastTime = $time;

		$line = date('Y-m-d H:i:s');
		$line .= ':+' . sprintf('%.05f', microtime(true) - $lastTime);
		$line .= ' ';
		$line .= str_repeat('  ', $ident);
		$line .= $what;

		if (empty(self::$path)) {
			error_log($line, 0);
		} elseif (self::$path == 'stdout') {
			echo $line . PHP_EOL;
		} else {
			file_put_contents(self::$path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
		}
	}

	public static function traceMethod() {
		$backtrace = debug_backtrace();
		$backtrace = $backtrace[1];
		self::debug($backtrace['file'] . '(' . $backtrace['line'] . '): '  .
			(isset($backtrace['class']) ? $backtrace['class'] . '->' : '') .
			$backtrace['function'] . '(' . join(', ', array_map(function($x) {
				if (is_object($x)) {
					return get_class($x);
				}
				return (string) $x;
			}, $backtrace['args'])) . ')');
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

GN_Debug::init();
