<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class GroupsController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$request = $this->makeRequest('/groups', array('pager' => array('total_records' => 1)));
		$this->view->totalRecords = $request['outputJSON']['total_records'];
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->records = $this->makeRequest('/groups', array('pager' => array('offset' => $offset, 'limit' => $limit)));
	}

	public function viewAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->group = $this->makeRequest('/groups', array('id' => $id));
		$this->view->courses = $this->makeRequest('/courses', array('group_id' => $id));
		$this->view->users = $this->makeRequest('/groups', array('group_user_id' => $id));
	}

	public function importAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->record = $this->makeRequest('/groups', array('id' => $id));
		}
		else
		{
			$mode = $request->getParam('mode');
			$this->view->response = $this->makeRequest('/google-apps/sync-group', array('group_id' => $id, 'sync_mode' => $mode));
		}
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->record = $this->makeRequest('/groups', array('id' => $id));
			$this->view->recordUsers = $this->makeRequest('/groups', array('group_user_id' => $id));
		}
		else
		{
			$users = json_decode($request->getParam('users'), true);
			$users2 = array();
			foreach ($users as $user)
			{
				$users2[$user['id']] = 0;
			}
			$this->view->response = $this->makeRequest('/groups', array('id' => $id), array
			(
				'name' => $request->getParam('name'),
				'advance_level' => $request->getParam('advance-level'),
				'google_group_id' => $request->getParam('code'),
				'users' => json_encode($users2)
			), 'PUT');
		}
	}

	public function createAction()
	{
		if (empty($_POST))
		{
		}
		else
		{
			$request = $this->getRequest();
			$users = json_decode($request->getParam('users'), true);
			$users2 = array();
			foreach ($users as $user)
			{
				$users2[$user['id']] = 0;
			}
			$this->view->response = $this->makeRequest('/groups', array(), array
			(
				'name' => $request->getParam('name'),
				'advance_level' => $request->getParam('advance-level'),
				'google_group_id' => $request->getParam('code'),
				'users' => json_encode($users2)
			));
		}
	}

	public function deleteAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->response = $this->makeRequest('/groups', array('id' => $id), array(), 'DELETE');
	}
}
?>
