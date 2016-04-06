<?php
require_once 'AbstractController.php';

class UsersController extends AbstractController {

	public function indexAction() {
		$modelUsers = new Model_Users();
		$select = $modelUsers
			->select(true)
			->setIntegrityCheck(false)
			->join('domains', 'domains.id = users.domain_id', array('domain_name'))
			;

		$this->view->name = null;
		if ($this->_hasParam('name')) {
			$this->view->name = trim($this->_getParam('name'));
		}
		if (!empty($this->view->name)) {
			foreach (explode(' ', $this->view->name) as $word) {
				$select->where('STRPOS(lower(email), lower(?)) > 0', $word);
			}
		}

		$this->view->role = null;
		if ($this->_hasParam('role')) {
			$this->view->role = $this->_getParam('role');
		}
		if (!in_array($this->view->role, Model_Users::getAllRoles())) {
			$this->view->role = null;
		}
		if (!empty($this->view->role)) {
			$select->where('role = ?', $this->view->role);
		}

		if ($this->user->role == Model_Users::ROLE_SUPER_ADMINISTRATOR) {
			$modelDomains = new Model_Domains();
			$this->view->domains = $modelDomains->fetchAll($modelDomains->select()->order('domain_name ASC'));
			$this->view->domain = null;
			if ($this->_hasParam('domain')) {
				$this->view->domain = $this->_getParam('domain');
			}
			if (!empty($this->view->domain)) {
				$select->where('domain_id = ?', $this->view->domain);
			}
		} else {
			$select->where('domain_id = ?', $this->user->domain_id);
		}

		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($this->_getParam('pageID', 1));
		$this->view->paginator = $paginator;
	}

	public function changeRoleAction() {
		if (!$this->_hasParam('user-id')) {
			$this->addError($this->view->translate('user_change_role_no_user_specified_error'));
			$this->_redirectExit('index', 'users');
		}
		if (!$this->_hasParam('role-id')) {
			$this->addError($this->view->translate('user_change_role_no_role_specified_error'));
			$this->_redirectExit('index', 'users');
		}

		$newRoleID = $this->_getParam('role-id');
		if (!in_array($newRoleID, Model_Users::getAllRoles())) {
			$this->addError($this->view->translate('user_change_role_wrong_role_error'));
			$this->_redirectExit('index', 'users');
		}

		$userID = $this->_getParam('user-id');
		$modelUsers = new Model_Users();
		$user = $modelUsers->find($userID)->current();
		if ($user === null) {
			$this->addError($this->view->translate('user_change_role_wrong_user_error'));
			$this->_redirectExit('index', 'users');
		}

		$oldRoleID = $user->role;
		$myRoleID = $this->user->role;
		if ((Model_Users::compareRoles($myRoleID, $newRoleID) < 0) or (Model_Users::compareRoles($oldRoleID, $myRoleID) > 0)) {
			$this->addError($this->view->translate('user_change_role_illegal_role_error'));
			$this->_redirectExit('index', 'users');
		}

		$modelUsers->update(array('role' => $newRoleID), array('id = ?' => $userID));
		$this->addSuccess($this->view->translate('user_change_role_success'));
		$this->_redirectExit('index', 'users');
	}


	public function emailsAction()
	{
		$action=$this->_getParam('observerAction')?:'reminder';

		$trial = $this->getInvokeArg('bootstrap')->getOption('trial');
		$paypal = $this->getInvokeArg('bootstrap')->getOption('paypal');

		$users=new Model_Users();

		$ludziki = Model_Users::getAll($this->_getParam('emailSearch'));

		foreach($ludziki AS $ludzik)
		{
			$this->observer = null;
			$this->user = $ludzik;
			$this->initObserver();

			$ludzik=$ludzik->toArray();

			if ($ludzik['domain_token'] || $ludzik['marketplace'])
			{
				$ludzik['expire']=$ludzik['domain_expire'];
				$ludzik['trial_count']=$ludzik['domain_trial_count'];
			}

			$days=round((strtotime($ludzik['expire'])-time())/(3600*24))+0;
			$trials_left=$trial['max_count']-$ludzik['trial_count'];
			if ($trials_left<0) $trials_left=0;

			$info=Model_Tests::getCountForUser($ludzik['id']);
			$tests=implode(':',$info);
			$info['days']=$days;
			$info['trials_left']=$trials_left;
			$info['name']=$ludzik['name'];
			$info['amount_mail']=$paypal['options'][2]['amount'];
			$info['amount_domain']=$paypal['options'][1]['amount'];
			$info['currency']=$paypal['currency'];
			$info['expired']=($days<=0)+0;

			if (!$ludzik['language']) $ludzik['language']='en';

			$wynik=$this->observer->observe($action,($days>0)+0, $info, $ludzik['language']);

			if (is_object($wynik)) $wynik=$wynik->mail;
			echo "$action-".$ludzik['language'].": ".$ludzik['email'].", days=$days, trials_left=$trials_left, tests=$tests .... $wynik\n";
			flush();
			@ob_flush();

		}
		die();
	}
}
