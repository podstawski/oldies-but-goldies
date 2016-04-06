<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class ProjectsController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$this->view->viewStatus = 1;
		$params = $request->getParams();
		if (isset($params['view-status']))
		{
			$this->view->viewStatus = $params['view-status'];
		}
		$request = $this->makeRequest('/projects', array('pager' => array('status' => $this->view->viewStatus, 'total_records' => 1)));
		$this->view->totalRecords = $request['outputJSON']['total_records'];
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->records = $this->makeRequest('/projects', array('pager' => array('status' => $this->view->viewStatus, 'offset' => $offset, 'limit' => $limit)));
	}

	public function viewAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->project = $this->makeRequest('/projects', array('id' => $id));
		$this->view->leaders = $this->makeRequest('/project-leaders', array('id' => $id));
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->project = $this->makeRequest('/projects', array('id' => $id));
			$this->view->leaders = $this->makeRequest('/project-leaders', array('id' => $id));
		}
		else
		{
			$request = $this->getRequest();
			$id = intval($request->getParam('id'));
			$postData = array
			(
				'name' => $request->getParam('name'),
				'code' => $request->getParam('code'),
				'description' => $request->getParam('description'),
				'start_date' => $request->getParam('start_date'),
				'end_date' => $request->getParam('end_date'),
				'status' => intval($request->getParam('status')),
				'leaders' => $request->getParam('leaders')
			);
			$this->view->response = $this->makeRequest('/projects', array('id' => $id), $postData);
		}
	}

	public function createAction()
	{
		$request = $this->getRequest();
		if (empty($_POST))
		{
			$this->view->viewStatus = $request->getParam('view-status');
			$this->view->leaders = $this->makeRequest('/project-leaders');
		}
		else
		{
			$postData = array
			(
				'name' => $request->getParam('name'),
				'code' => $request->getParam('code'),
				'description' => $request->getParam('description'),
				'start_date' => $request->getParam('start_date'),
				'end_date' => $request->getParam('end_date'),
				'status' => intval($request->getParam('status')),
				'leaders' => $request->getParam('leaders')
			);
			$this->view->response = $this->makeRequest('/projects', array(), $postData);
		}
	}

	public function deleteAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->response = $this->makeRequest('/projects', array('id' => $id), array(), 'DELETE');
	}
}
?>
