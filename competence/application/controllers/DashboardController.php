<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

require_once 'CompetenceController.php';

class DashboardController extends CompetenceController
{
	public function indexAction()
	{
		$modelExams = new Model_Exams();
		$select = $modelExams->selectParticipant($this->user->id, $this->user->domain_id);
		$this->view->exams = $modelExams->fetchAll($select);
		unset($select);

		if (($this->user->role == Model_Users::ROLE_TEACHER) or ($this->user->role == Model_Users::ROLE_ADMINISTRATOR))
		{
			$select = $modelExams->selectManager($this->user->id, $this->user->domain_id);
		}
		elseif ($this->user->role == Model_Users::ROLE_SUPER_ADMINISTRATOR)
		{
			$select = $modelExams->selectDomain($this->user->domain_id);
		}
		if (isset($select))
		{
			$this->view->managedExams = $modelExams->fetchAll($select);
		}
	}
}
?>
