<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class CourseScheduleController extends Light_Controller
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
			$getData = array
			(
				'pager' => array
				(
					'group_id' => $this->view->groupId,
					'course_unit_id' => $this->view->unitId
				)
			);
			$this->view->lessons = $this->makeRequest('/course-schedule', $getData);
		}

		if (isset($this->view->groupId)) { $_SESSION['group-id'] = $this->view->groupId; }
		if (isset($this->view->courseId)) { $_SESSION['course-id'] = $this->view->courseId; }
		if (isset($this->view->unitId)) { $_SESSION['unit-id'] = $this->view->unitId; }
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$params = $request->getParams();
		if (isset($params['group-id']) and $params['group-id'] != '')
		{
			$this->view->groupId = intval($request->getParam('group-id'));
		}
		if (isset($params['course-id']) and $params['course-id'] != '')
		{
			$this->view->courseId = intval($request->getParam('course-id'));
		}
		if (isset($params['unit-id']) and $params['unit-id'] != '')
		{
			$this->view->unitId = intval($request->getParam('unit-id'));
		}

		if (isset($params['id']))
		{
			$id = intval($request->getParam('id'));
		}

		if (empty($_POST))
		{
			if (isset($id))
			{
				$this->view->record = $this->makeRequest('/course-schedule', array('id' => $id));
			}
		}
		else
		{
			if (isset($id))
			{
				$postData = array
				(
					'subject' => $request->getParam('subject'),
					'schedule' => $request->getParam('schedule'),
				);
				$this->view->response = $this->makeRequest('/course-schedule', array('id' => $id), $postData);
			}
			else
			{
				$postData = array
				(
					'subject' => $request->getParam('subject'),
					'schedule' => $request->getParam('schedule'),
					'lesson_id' => $request->getParam('lesson-id'),
				);
				$this->view->response = $this->makeRequest('/course-schedule', array(), $postData);
			}
		}
	}
}
?>
