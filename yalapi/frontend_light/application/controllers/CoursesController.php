<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class CoursesController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$request = $this->makeRequest('/courses', array('pager' => array('total_records' => 1)));
		$this->view->totalRecords = intval($request['outputJSON']['total_records']);
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->records = $this->makeRequest('/courses', array('pager' => array('offset' => $offset, 'limit' => $limit)));
	}

	public function viewAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->record = $this->makeRequest('/courses', array('id' => $id));
		$this->view->units = $this->makeRequest('/course-units', array('course_id' => $id));
		$this->view->trainingCenter = $this->makeRequest('/training-centers', array('id' => $this->view->record['outputJSON']['training_center_id']));
		$this->view->project = $this->makeRequest('/projects', array('id' => $this->view->record['outputJSON']['project_id']));
		$this->view->group = $this->makeRequest('/groups', array('id' => $this->view->record['outputJSON']['group_id']));
	}

	public function reportsAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->record = $this->makeRequest('/courses', array('id' => $id));
		}
		else
		{
			$reportId = intval($request->getParam('type'));
			$reportFormat = $request->getParam('format');
			$course = $this->makeRequest('/courses', array('id' => $id));
			$groupId = $course['outputJSON']['group_id'];
			$projectId = $course['outputJSON']['project_id'];
			$getData = array
			(
				'id' => $reportId,
				'course_id' => $id,
				'group_id' => $groupId,
				'project_id' => $projectId,
				'report_format' => $reportFormat
			);
			/*echo '<pre>';
			print_r($getData);
			print_r($course);
			echo '</pre>';*/
			$this->view->report = $this->makeRequest('/reports', $getData);
		}
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->course = $this->makeRequest('/courses', array('id' => $id));
		$this->view->units = $this->makeRequest('/course-units', array('course_id' => $id));
		if (empty($_POST))
		{
			$this->view->trainingCenters = $this->makeRequest('/training-centers');
			$this->view->projects = $this->makeRequest('/projects');
			$this->view->groups = $this->makeRequest('/groups');
			$this->view->trainers = $this->makeRequest('/users', array('role_id' => 5));
		}
		else
		{
			$unitsData = array();
			foreach (json_decode($request->getParam('unit-data'), true) as $unit)
			{
				$unitData = array
				(
					'name' => $unit['name'],
					'hour_amount' => intval($unit['hour-amount']),
					'user_id' => $unit['trainer'],
				);
				if (isset($unit['unit-id']))
				{
					$unitData['id'] = intval($unit['unit-id']);
				}
				$unitsData []= $unitData;
			}
			$postData = array
			(
				'name' => $request->getParam('name'),
				'code' => $request->getParam('code'),
				'color' => $request->getParam('color'),
				'training_center_id' => intval($request->getParam('training-center-id')),
				'group_id' => intval($request->getParam('group-id')),
				'price' => intval($request->getParam('price')),
				'level' => intval($request->getParam('advance-level')),
				'project_id' => intval($request->getParam('project-id')),
				'status' => intval($request->getParam('status')),
				'show_on_www' => $request->getParam('show-on-www') !== null,
				'description' => $request->getParam('description'),
				'course_units' => json_encode($unitsData)
			);
			$this->view->response = $this->makeRequest('/courses', array('id' => $id), $postData);
		}
	}

	public function createAction()
	{
		$request = $this->getRequest();
		if (empty($_POST))
		{
			$this->view->trainingCenters = $this->makeRequest('/training-centers');
			$this->view->projects = $this->makeRequest('/projects');
			$this->view->groups = $this->makeRequest('/groups');
			$this->view->trainers = $this->makeRequest('/users', array('role_id' => 5));
		}
		else
		{
			$unitsData = array();
			foreach (json_decode($request->getParam('unit-data'), true) as $unit)
			{
				$unitData = array
				(
					'name' => $unit['unit-name'],
					'hour_amount' => intval($unit['unit-hours']),
					'user_id' => $unit['unit-trainer'],
				);
				$unitsData []= $unitData;
			}
			$postData = array
			(
				'name' => $request->getParam('name'),
				'code' => $request->getParam('code'),
				'color' => $request->getParam('color'),
				'training_center_id' => intval($request->getParam('training-center-id')),
				'group_id' => intval($request->getParam('group-id')),
				'price' => intval($request->getParam('price')),
				'level' => intval($request->getParam('advance-level')),
				'project_id' => intval($request->getParam('project-id')),
				'status' => intval($request->getParam('status')),
				'show_on_www' => $request->getParam('show-on-www') !== null,
				'description' => $request->getParam('description'),
				'course_units' => json_encode($unitsData)
			);
			$this->view->response = $this->makeRequest('/courses', array(), $postData);
		}
	}

	public function deleteAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->response = $this->makeRequest('/courses', array('id' => $id), array(), 'DELETE');
	}
}
?>
