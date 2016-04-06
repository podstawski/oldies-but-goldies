<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Model_QuestionsRow extends Zend_Db_Table_Row
{
	public function getAnswer($examId, $participantId)
	{
		$model = new Model_Answers();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->where('question_id = ?', $this->id)
			->where('exam_id = ?', $examId)
			->where('user_id = ?', $participantId)
			;
		$answer = $model->fetchRow($select);
		return $answer;
	}

	public function getAnswerValue($examId, $participantId)
	{
		$answer = $this->getAnswer($examId, $participantId);
		if ($answer === null)
		{
			return $this->default_value;
		}
		return $answer->answer_value;
	}
}

