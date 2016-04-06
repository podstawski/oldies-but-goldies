<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Model_ExamsRow extends Zend_Db_Table_Row
{
	public function getQuestionCount($competenceId)
	{
		$modelQuestions = new Model_Questions();
		$select = $modelQuestions
			->select()
			->from('questions', array())
			->columns(array('question_count' => 'COUNT(questions.id)'))
			->where('questions.competence_id = ?', $competenceId)
			;
		$questionCount = $modelQuestions->fetchAll($select)->current()->question_count;
		return $questionCount;
	}

	public function getAllParticipants()
	{
		$model = new Model_ExamParticipants();
		$select = $model
			->select()
			->where('exam_id = ?', $this->id)
			;
		return $model->fetchAll($select);
	}

	public function getFinishedParticipants()
	{
		$model = new Model_ExamParticipants();
		$select = $model
			->select()
			->where('exam_id = ?', $this->id)
			->where('date_finished IS NOT NULL')
			;
		return $model->fetchAll($select);
	}

	public function getManagers()
	{
		$modelExamManagers = new Model_ExamManagers();
		$select = $modelExamManagers
			->select(true)
			->setIntegrityCheck(false)
			->join('users', 'users.id = exam_managers.user_id')
			->where('exam_managers.exam_id = ?', $this->id)
			;
		return $modelExamManagers->fetchAll($select);
	}

	public function getFirstManager()
	{
		$modelExamManagers = new Model_ExamManagers();
		$select = $modelExamManagers
			->select(true)
			->setIntegrityCheck(false)
			->join('users', 'users.id = exam_managers.user_id')
			->where('exam_managers.exam_id = ?', $this->id)
			->order('exam_managers.id ASC');
		return $modelExamManagers->fetchAll($select)->current();
	}

	public function addManager($managerId)
	{
		$modelExamManagers = new Model_ExamManagers();
		$manager = $modelExamManagers->fetchRow($modelExamManagers->select()->where('user_id = ?', $managerId)->where('exam_id = ?', $this->id));
		if ($manager !== null)
		{
			return;
		}
		$modelExamManagers->createAndSave
		(
			array
			(
				'user_id' => $managerId,
				'exam_id' => $this->id
			)
		);
	}

	public function removeManager($managerId)
	{
		$modelExamManagers = new Model_ExamManagers();
		$select = $modelExamManagers
			->select()
			->where('exam_managers.user_id = ?', $managerId)
			->where('exam_managers.exam_id = ?', $this->id)
			;
		foreach ($modelExamManagers->fetchAll($select) as $row)
		{
			$row->delete();
		}
	}

	public function getAnswerCount($competenceId, $participantId)
	{
		$modelAnswers = new Model_Answers();
		$select = $modelAnswers
			->select()
			->setIntegrityCheck(false)
			->from('answers', array())
			->columns(array('answer_count' => 'COUNT(answers.id)'))
			->join('questions', 'questions.id = answers.question_id', array())
			->where('questions.competence_id = ?', $competenceId)
			->where('answers.user_id = ?', $participantId)
			;
		$answerCount = $modelAnswers->fetchAll($select)->current()->answer_count;
		return $answerCount;
	}

	public function isDone($participantId)
	{
		$model = new Model_ExamParticipants();
		$select = $model
			->select()
			->where('exam_participants.user_id = ?', $participantId)
			->where('exam_participants.exam_id = ?', $this->id)
			;
		$row = $model->fetchRow($select);
		if ($row === null)
		{
			return false;
		}
		else
		{
			if (!empty($row->date_finished))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public function getAssociatedCompetencies()
	{
		$model = new Model_Competencies();
		$select = $model
				->select(true)
				->setIntegrityCheck(false)
				->join('exam_competencies', 'competencies.id = exam_competencies.competence_id', array())
				->where('exam_competencies.exam_id = ?', $this->id)
				->order('id ASC')
				//->group(array('competencies.id', 'exam_competencies.id'))
				;
		return $model->fetchAll($select);
	}

	public function getAssociatedGroups($managerId = null)
	{
		$model = new Model_Groups();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->distinct('groups.id')
			//użytkownicy powiązani z grupą poprzez dany egzamin
			->join('user_groups', 'user_groups.group_id = groups.id', array())
			->join('exam_participants', '(user_groups.user_id = exam_participants.user_id AND user_groups.group_id = exam_participants.group_id)', array())
			->where('groups.domain_id = ?', $this->domain_id)
			->where('exam_participants.exam_id = ?', $this->id)
			;
		if ($managerId !== null)
		{
			$select
				//managerzy powiązani z grupą poprzez dany egzamin
				->join('exam_managers', 'exam_managers.exam_id = exam_participants.exam_id', array())
				->where('exam_managers.user_id = ?', $managerId)
				;
		}

		return $model->fetchAll($select);
	}
}
