<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class GradesController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$params = $request->getParams();
		if (isset($_SESSION['group-id']) and $_SESSION['group-id'] != '') { $this->view->groupId = intval($_SESSION['group-id']); }
		if (isset($_SESSION['course-id']) and $_SESSION['course-id'] != '') { $this->view->courseId = intval($_SESSION['course-id']); }
		if (isset($_SESSION['unit-id']) and $_SESSION['unit-id'] != '') { $this->view->unitId = intval($_SESSION['unit-id']); }
		if (isset($params['group-id']) and $params['group-id'] != '') { $this->view->groupId = intval($request->getParam('group-id')); }
		if (isset($params['course-id']) and $params['course-id'] != '') { $this->view->courseId = intval($request->getParam('course-id')); }
		if (isset($params['unit-id']) and $params['unit-id'] != '') { $this->view->unitId = intval($request->getParam('unit-id')); }

		$this->view->groups = $this->makeRequest('/groups');
		if (isset($this->view->groupId))
		{
			$this->view->courses = $this->makeRequest('/group-courses', array('id' => $this->view->groupId));
			if (count($this->view->courses['outputJSON']) == 1)
			{
				$tmp = end($this->view->courses['outputJSON']);
				$this->view->courseId = $tmp['id'];
			}
		}
		if (isset($this->view->courseId))
		{
			$this->view->units = $this->makeRequest('/course-units', array('course_id' => $this->view->courseId));
			if (count($this->view->units['outputJSON']) == 1)
			{
				$tmp = end($this->view->units['outputJSON']);
				$this->view->unitId = $tmp['id'];
			}
		}

		if (isset($this->view->groupId) and isset($this->view->courseId) and isset($this->view->unitId))
		{
			$this->view->grades = $this->makeRequest('/group-grades', array('id' => $this->view->unitId));
		}

		if (isset($this->view->groupId)) { $_SESSION['group-id'] = $this->view->groupId; }
		if (isset($this->view->courseId)) { $_SESSION['course-id'] = $this->view->courseId; }
		if (isset($this->view->unitId)) { $_SESSION['unit-id'] = $this->view->unitId; }
	}

	public function editGradesAction()
	{
		$request = $this->getRequest();
		$grades = $this->makeRequest('/group-grades', array('id' => $request->getParam('unit-id')));
		$this->view->responses = array();
		foreach ($grades['outputJSON']['exams'] as $examId => $exam)
		{	
			foreach ($grades['outputJSON']['users'] as $userId => $user)
			{
				$gradeOld = $grades['outputJSON']['grades'][$userId][$examId];
				$gradeNew = $request->getParam('grade-' . $examId . '-' . $userId);
				if ($gradeOld == $gradeNew)
				{
					continue;
				}
				$postData = array
				(
					'user_id' => $userId,
					'exam_id' => $examId,
					'grade' => $gradeNew
				);
				$this->view->responses []= $this->makeRequest('/group-grades', array(), $postData);
			}
		}
	}

	public function editExamAction()
	{
		$request = $this->getRequest();
		$examId = $request->getParam('exam-id');
		$unitId = $request->getParam('unit-id');
		if (empty($_POST))
		{
			$this->view->record = $this->makeRequest('/group-exams', array('id' => $examId));
			$this->view->grades = $this->makeRequest('/group-grades', array('id' => $request->getParam('unit-id')));
		}
		else
		{
			$action = $request->getParam('submit-action');
			if ($action == 'edit')
			{
				$postData = array
				(
					'name' => $request->getParam('name'),
					'created_date' => $request->getParam('date')
				);
				$this->view->response = $this->makeRequest('/group-exams', array('id' => $examId), $postData);
			}
			else if ($action == 'delete')
			{
				$this->view->response = $this->makeRequest('/group-exams', array('id' => $examId), array(), 'DELETE');
			}
		}
	}

	public function createExamAction()
	{
		$request = $this->getRequest();
		if (empty($_POST))
		{
			$this->view->userId = $request->getParam('user-id');
			$this->view->grades = $this->makeRequest('/group-grades', array('id' => $request->getParam('unit-id')));
		}
		else
		{
			$unitId = $request->getParam('unit-id');
			$userId = $request->getParam('user-id');
			$grade = $request->getParam('grade');
			$name = $request->getParam('name');
			$createdDate = $request->getParam('date');

			$response = $this->makeRequest('/group-exams', array('course_unit_id' => $unitId, 'name' => $name));
			if (empty($exam['outputJSON']))
			{
				$postData = array
				(
					'name' => $name,
					'created_date' => $createdDate,
					'course_unit_id' => $unitId
				);
				$response = $this->makeRequest('/group-exams', array(), $postData);
				$examId = $response['outputJSON']['id'];
			}
			else
			{
				$examId = $response['outputJSON']['id'];
			}

			$postData = array
			(
				'user_id' => $userId,
				'exam_id' => $examId,
				'grade' => $grade
			);
			$this->view->response = $this->makeRequest('/group-grades', array(), $postData);
		}
	}
}
?>
