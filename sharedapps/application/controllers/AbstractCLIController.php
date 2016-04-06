<?php
require_once 'AbstractController.php';
class AbstractCLIController extends AbstractController {
	protected $flush = true;

	protected $lastIndent = 0;

	protected function getLastDebugIndent() {
		return $this->lastIndent;
	}
	protected function setLastDebugIndent($indent) {
		$this->lastIndent = $indent;
	}

	protected function debugArray($d, $level = 0) {
		$x = print_r($d, true);
		foreach (explode("\n", $x) as $line) {
			if (!empty($line)) {
				$this->debug($line, $level);
			}
		}
	}

	protected function debug($txt = '', $level = 0) {
		if ($level==0) echo "\n";
		$this->lastIndent = $level;
		for ($i = 0; $i < $level; $i ++) {
			echo '  ';
		}
		echo $txt;
		echo ' (' . date('c') . ')';
		echo "\n";
		if ($this->flush) {
			flush();
			ob_flush();
		}
	}

	protected function microtime_float() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
