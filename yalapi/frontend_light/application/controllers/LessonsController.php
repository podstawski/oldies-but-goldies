<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class LessonsController extends Light_Controller
{
	public function init()
	{
		parent::init();
	}

	public function indexAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('course-id'));
		$this->view->course = $this->makeRequest('/courses', array('id' => $id));
		$this->view->units = $this->makeRequest('/course-units', array('course_id' => $id));
		$this->view->lessons = $this->makeRequest('/lessons', array('course_id' => $id));
	}

	public function createAction()
	{
		$request = $this->getRequest();
		$id = intval($request->getParam('course-id'));
		if (empty($_POST))
		{
			$this->view->course = $this->makeRequest('/courses', array('id' => $id));
			$this->view->units = $this->makeRequest('/course-units', array('course_id' => $id));
			$this->view->lessons = $this->makeRequest('/lessons', array('course_id' => $id));
		}
		else
		{
			$request = $this->getRequest();
			$postData = array
			(
			);
			print_r($postData);
			//$this->view->response = $this->makeRequest('/courses', array(), $postData);
		}
	}

}
?>

