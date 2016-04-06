<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class PresenceController extends Light_Controller
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
			$this->view->presence = $this->makeRequest('/group-presence', array('id' => $this->view->unitId));
		}

		if (isset($this->view->groupId)) { $_SESSION['group-id'] = $this->view->groupId; }
		if (isset($this->view->courseId)) { $_SESSION['course-id'] = $this->view->courseId; }
		if (isset($this->view->unitId)) { $_SESSION['unit-id'] = $this->view->unitId; }
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$presence = $this->makeRequest('/group-presence', array('id' => $request->getParam('unit-id')));
		$this->view->responses = array();
		foreach ($presence['outputJSON']['lessons'] as $lessonId => $lesson)
		{
			$users = array
			(
				false => array(), //absent users
				true => array() //present users
			);
			foreach ($presence['outputJSON']['users'] as $userId => $user)
			{
				$present = $request->getParam('presence-' . $lessonId . '-' . $userId) != null;
				$present2 = @$presence['outputJSON']['presence'][$userId][$lessonId] == 1;
				if ($present == $present2)
				{
					continue;
				}
				array_push($users[$present], $userId);
			}
			foreach ($users as $present => $subUsers)
			{
				if (empty($subUsers))
				{
					continue;
				}
				$postData = array
				(
					'lesson_id' => $lessonId,
					'user_id' => join(',', $subUsers),
					'present' => $present
				);
				$this->view->responses []= $this->makeRequest('/group-presence', array(), $postData);
			}
		}
	}
}
?>
