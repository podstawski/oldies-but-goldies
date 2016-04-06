<?php

require_once __DIR__.'/../../library/Millionaire-quiz/application/controllers/GraController.php';

class GraController extends MillionaireGraController 
{

	public function init()
	{
		parent::init();

		$this->view->actionName = $this->getRequest()->getActionName();		
		if($this->view->actionName != 'old-browser' && $this->view->actionName != 'java-script-disabled') {
				$browser = $this->getBrowser();
				if($browser['name'] == 'Internet Explorer' && $browser['version'] < 8) {
					$this->_redirect('index/old-browser');
				}
		}

		if ($this->_request->getParam('action') === 'die') {
			try {
				throw new Exception('Just testing...');
			} catch (Exception $e) {
				echo $e->getTraceAsString();
			}
			die;
		}

		$this->dbAnswers = new Model_Answer;
		$this->dbAttempts = new Model_Attempt;
		$this->dbLifeBuoys = new Model_Lifebuoy;
		$this->dbQuestions = new Model_Question;
		$this->dbQuestionCategories = new Model_QuestionCategory;
		$this->dbTests = new Model_Test;
	}

}
