<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Model_Exams extends Model_Abstract
{
    protected $_name = 'exams';
	protected $_rowClass = 'Model_ExamsRow';

	public function selectParticipant($participantId, $domainId)
	{
		return $this
			->select(true)
			->setIntegrityCheck(false)
			->join('exam_participants', 'exams.id = exam_id', array('date_finished', 'date_started', 'group_id'))
			->where('exam_participants.user_id = ?', $participantId)
			->where('exams.domain_id = ?', $domainId)
			;
	}

	public function selectManager($managerId, $domainId)
	{
		return $this
			->select(true)
			->setIntegrityCheck(false)
			->join('exam_managers', 'exams.id = exam_id', array())
			->where('exam_managers.user_id = ?', $managerId)
			->where('exams.domain_id = ?', $domainId)
			;
	}

	public function selectDomain($domainId)
	{
		return $this
			->select(true)
			->where('exams.domain_id = ?', $domainId)
			;
	}
}
