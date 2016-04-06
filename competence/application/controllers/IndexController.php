<?php
require_once 'CompetenceController.php';

class IndexController extends CompetenceController {
	public function indexAction() {
		if ($this->user) {
			$this->_redirectExit('index', 'dashboard');
		}
	}

	public function studentInfoAction() {
	}

	public function aboutProgramAction() {
	}
}
?>
