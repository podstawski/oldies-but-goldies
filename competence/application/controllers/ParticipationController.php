<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

require_once 'CompetenceController.php';

class ParticipationController extends CompetenceController
{
	public function questionAction()
	{
		if (!$this->_hasParam('exam-id'))
		{
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}

		//pobierz badanie z bazy
		$modelExams = new Model_Exams();
		$exam = $modelExams->fetchRow
		(
			$modelExams
				->selectParticipant($this->user->id, $this->user->domain_id)
				->where('exam_participants.exam_id = ?', $this->_getParam('exam-id'))
		);
		if ($exam === null)
		{
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}
		elseif (!empty($exam->date_closed))
		{
			$this->addError($this->view->translate('Exam was closed, cannot answer'));
			return;
		}


		//ustal aktywną kompetencję
		$activeCompetence = false;
		foreach ($exam->getAssociatedCompetencies() as $competence)
		{
			if ($activeCompetence === false or ($this->_hasParam('competence-id') and ($this->_getParam('competence-id') == $competence->id)))
			{
				$activeCompetence = $competence;
			}
		}
		if ($activeCompetence === false)
		{
			$this->addError($this->view->translate('Exam has no associated competencies'));
			return;
		}

		$this->view->exam = $exam;
		$this->view->activeCompetence = $activeCompetence;
		$this->view->skills = $this->view->activeCompetence->getSkills();

		//updatujemy czas rozpoczęcia badania
		if (empty($exam->date_started))
		{
			$modelExamParticipants = new Model_ExamParticipants();
			$row = $modelExamParticipants->fetchRow($modelExamParticipants
				->select(true)
				->where('exam_id = ?', $this->_getParam('exam-id'))
				->where('user_id = ?', $this->user->id));
			$row->date_started = date('Y-m-d H:i:s');
			$row->save();
		}
	}

	public function finishAction()
	{
		//updatujemy czas zakończenia badania
		if (!$this->_hasParam('exam-id'))
		{
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}
		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null)
		{
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}
		$this->view->exam = $exam;
		$modelExamParticipants = new Model_ExamParticipants();
		$count = $modelExamParticipants->update(array('date_finished' => 'NOW()'), array('exam_id = ?' => $this->_getParam('exam-id'), 'user_id = ?' => $this->user->id));
		if ($count == 0)
		{
			$this->addError($this->view->translate('User %d doesn\'t participate in exam %d', $this->user->id, $this->_getParam('exam-id')));
			return;
		}
		if ($this->observer)
		{
			$mailAddresses = array();
			foreach ($exam->getManagers() as $manager)
			{
				$mailAddresses []= $manager->email;
			}
			$data = array
			(
				'managerList' => $mailAddresses,
				'examId' => $exam->id,
				'examName' => $exam->name,
			);
			$this->observer->observe('finishExam', true, $data);
		}
		$this->addSuccess($this->view->translate('Exam finished successfully'));
	}

	public function answerAction()
	{
		$this->_helper->layout->setLayout('ajax');

		if (!$this->_hasParam('question-id'))
		{
			$this->addError($this->view->translate('No question ID specified'));
			return;
		}
		if (!$this->_hasParam('exam-id'))
		{
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}
		if (!$this->_hasParam('answer-value'))
		{
			$this->addError($this->view->translate('No answer value specified'));
			return;
		}

		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null)
		{
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}
		elseif (!empty($exam->date_closed))
		{
			$this->addError($this->view->translate('Exam was closed, cannot answer'));
			return;
		}

		$modelAnswers = new Model_Answers();
		$answerData = array
		(
			'user_id' => $this->user->id,
			'exam_id' => intval($this->_getParam('exam-id')),
			'question_id' => intval($this->_getParam('question-id')),
			'answer_value' => intval($this->_getParam('answer-value')),
			'answer_time' => date('Y-m-d H:i:s')
		);
        $answer = $modelAnswers->fetchRow(array
		(
			'user_id = ?' => $this->user->id,
			'exam_id = ?' => $this->_getParam('exam-id'),
			'question_id = ?' => $this->_getParam('question-id'))
		);
		if ($answer == null)
		{
			$answer = $modelAnswers->createRow();
		}
		$answer->setFromArray($answerData);
		$answer->save();

		$this->addSuccess($this->view->translate('Answer stored successfully'));
	}

}
?>
