<?php
require_once 'CompetenceController.php';

class UsersController extends CompetenceController
{
	public function ajaxListAction()
	{
		$modelUsers = new Model_Users();
		$select = $modelUsers
			->select(true)
			->where('domain_id = ?', $this->user->domain_id)
			;

		$this->_helper->layout->disableLayout();
		if ($this->_hasParam('name'))
		{
			$term = $this->_getParam('name');
			foreach (explode(' ', $term) as $word)
			{
				$select->where('STRPOS(lower(name), lower(?)) > 0', $word);
			}
		}
		if ($this->_hasParam('email'))
		{
			$term = $this->_getParam('email');
			foreach (explode(' ', $term) as $word)
			{
				$select->where('STRPOS(lower(email), lower(?)) = 1', $word);
			}
		}
		if ($this->_hasParam('role'))
		{
			$role = $this->_getParam('role');
			if (!is_array($role))
			{
				$role = array($role);
			}
			$select->where('ROLE IN (' . join(',', $role) . ')');
		}
		if ($this->_hasParam('limit'))
		{
			$select->limit(intval($this->_getParam('limit')));
		}

		$response = $modelUsers->fetchAll($select);

		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		echo json_encode(array('users' => $response->toArray()));
		die();
	}

	public function indexAction()
	{
		$modelUsers = new Model_Users();
		$select = $modelUsers->selectDomain($this->user->domain_id);
		$this->view->name = null;
		if ($this->_hasParam('name'))
		{
			$this->view->name = trim($this->_getParam('name'));
		}
		if (!empty($this->view->name))
		{
			foreach (explode(' ', $this->view->name) as $word)
			{
				$select->where('STRPOS(lower(name), lower(?)) > 0', $word);
			}
		}

		$modelGroups = new Model_Groups();
		$select2 = $modelGroups->selectDomain($this->user->domain_id);
		$this->view->groups = $modelGroups->fetchAll($select2);
		$this->view->group = null;
		if ($this->_hasParam('role'))
		{
			foreach ($this->view->groups as $group)
			{
				if ($group->id == $this->_getParam('group'))
				{
					$this->view->group = $group->id;
				}
			}
		}
		if (!empty($this->view->group))
		{
			$select
				->join('user_groups', 'user_groups.user_id = users.id', array())
				->group('users.id')
				->group('user_groups.user_id')
				->where('group_id = ?', $this->view->group);
		}


		$this->view->role = null;
		if ($this->_hasParam('role'))
		{
			$this->view->role = intval($this->_getParam('role'));
		}
		if (!in_array($this->view->role, array_keys(Model_Users::$roles)))
		{
			$this->view->role = null;
		}
		if (!empty($this->view->role))
		{
			$select->where('role = ?', $this->view->role);
		}

		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($this->_getParam('pageID', 1));
		$this->view->paginator = $paginator;
	}

	public function deleteAction() {
		if (($this->user->role != Model_Users::ROLE_ADMINISTRATOR) and ($this->user->role != Model_Users::ROLE_SUPER_ADMINISTRATOR)) {
			$this->addError($this->view->translate('users_delete_insufficient_privileges_error'));
		} else {
			if (!$this->_hasParam('user-id')) {
				$this->addError($this->view->translate('users_delete_no_user_id_specified_error'));
			} else {
				$userId = $this->_getParam('user-id');
				$modelUsers = new Model_Users();
				$user = $modelUsers->find($userId)->current();
				if ($user === null) {
					$this->addError($this->view->translate('users_delete_wrong_user_specified_error'));
				} elseif ($userId == $this->user->id) {
					$this->addError($this->view->translate('users_delete_cannot_delete_oneself_error'));
				} else {
					$user->delete();
					$this->addSuccess($this->view->translate('users_delete_success'));
				}
			}
		}
		$this->_redirectExit('index', 'users');
	}

	public function changeRoleAction()
	{
		if (!$this->_hasParam('user-id'))
		{
			$this->addError($this->view->translate('No user ID specified'));
			return;
		}
		if (!$this->_hasParam('role-id'))
		{
			$this->addError($this->view->translate('No role ID specified'));
			return;
		}
		$userId = $this->_getParam('user-id');
		$newRoleId = intval($this->_getParam('role-id'));
		if (!isset(Model_Users::$roles[$newRoleId]))
		{
			$this->addError($this->view->translate('No role with ID %d', $newRoleId));
			return;
		}
		$modelUsers = new Model_Users();
		$user = $modelUsers->find($userId)->current();
		if ($user === null)
		{
			$this->addError($this->view->translate('No user with ID %d', $userId));
			return;
		}
		if (($newRoleId > $this->user->role) or ($user->role > $this->user->role))
		{
			$this->addError($this->view->translate('Trying to perform invalid request'));
			return;
		}
		$oldRoleId = $user->role;
		$modelUsers->update(array('role' => $newRoleId), array('id = ?' => $userId));
		if ($this->observer)
		{
			$data = array
			(
				'oldRole' => Model_Users::$roles[$oldRoleId],
				'newRole' => Model_Users::$roles[$newRoleId],
				'userName' => $user->name,
				'userMail' => $user->email,
			);
			$this->observer->observe('changeRole', true, $data);
		}
		$this->addSuccess($this->view->translate('Role changed successfully.'));
	}
}
?>
