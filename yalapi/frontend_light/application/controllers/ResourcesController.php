<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class ResourcesController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$this->view->pageNumber = max(intval($request->getParam('page')), 1);
		$request = $this->makeRequest('/resource-types', array('pager' => array('total_records' => 1))); 
		$this->view->totalRecords = intval($request['outputJSON']['total_records']);
		$this->view->recordsPerPage = 15;
		$offset = $this->view->recordsPerPage * ($this->view->pageNumber - 1);
		$limit = $this->view->recordsPerPage;
		$this->view->records = $this->makeRequest('/resource-types', array('pager' => array('offset' => $offset, 'limit' => $limit)));
	}

	public function createAction()
	{
		if (empty($_POST))
		{
		}
		else
		{
			$request = $this->getRequest();
			$postData = array
			(
				'name' => $request->getParam('name')
			);
			$this->view->response = $this->makeRequest('/resource-types', array(), $postData);
		}
	}

	public function editAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		if (empty($_POST))
		{
			$this->view->record = $this->makeRequest('/resource-types', array('id' => $id));
		}
		else
		{
			$postData = array
			(
				'name' => $request->getParam('name')
			);
			$this->view->response = $this->makeRequest('/resource-types', array('id' => $id), $postData);
		}
	}

	public function deleteAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('id'));
		$this->view->response = $this->makeRequest('/resource-types', array('id' => $id), array(), 'DELETE');
	}
}
?>
