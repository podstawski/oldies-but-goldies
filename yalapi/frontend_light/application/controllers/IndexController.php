<?php
class IndexController extends Light_Controller
{
	public function init()
	{
		parent::init();
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$this->view->dashboardEvents = $this->makeRequest('/dashboard-events', array('day' => date('d'), 'month' => date('m'), 'year' => date('y')));
	}

	public function logoutAction()
	{
		foreach ($_SESSION as $key => $val)
		{
			unset($_SESSION[$key]);
		}
	}

}
?>
