<?php
require_once 'AbstractController.php';

class ContactsController extends AbstractController {
	public function listAction() {
		$this->_redirectExit('index', 'contacts');
	}

	public function indexAction() {
		$modelHistory = new Model_History();
		$modelUserContactGroups = new Model_UserContactGroups();
		$this->view->history = $modelHistory->getByUserIDWithLimit($this->user->id, 'contact');
		$this->view->unconfirmedUserContactGroups = $modelUserContactGroups->getAllUnconfirmedByUserID($this->user->id);
	}

	public function unconfirmAction() {
		$modelUserContactGroups = new Model_UserContactGroups();
		$hash = $this->_getParam('hash');
		$userContactGroup = $modelUserContactGroups->getByID(intval($this->_getParam('groupid')));
		if (empty($userContactGroup)) {
			$this->addError($this->view->translate('common_error_wrong_contact_group_specified'));
			$this->_redirectExit('index', 'contacts');
		} else {
			if ($hash == $userContactGroup->agree_hash) {
				GN_SessionCache::delete('contacts' . $this->user->id);
				CRM_History::observe($this->user, 'contacts-unconfirm', array(
					'contact-group-id' => $userContactGroup->getContactGroup()->id,
					'user-contact-group-id' => $userContactGroup->id,
					'user-contact-group-name' => $userContactGroup->getName(),
					'user-mp' => $userContactGroup->getUser()->getDomain()->marketplace,
					'user-domain' => $userContactGroup->getUser()->getDomain()->domain_name,
					'user-name' => $userContactGroup->getUser()->name,
					'user-id' => $userContactGroup->user_id,
					'user-email' => $userContactGroup->getUser()->email,
				));
				$userContactGroup->agree_time = null;
				$userContactGroup->save();
			} else {
				$this->addError($this->view->translate('common_error_wrong_hash_specified'));
			}
		}
		$this->_redirectExit('edit', 'contacts', array('id' => $userContactGroup->getContactGroup()->id));
	}

	public function confirmAction() {
		$modelUserContactGroups = new Model_UserContactGroups();
		$hash = $this->_getParam('hash');
		$userContactGroup = $modelUserContactGroups->getByID(intval($this->_getParam('groupid')));
		if (empty($userContactGroup)) {
			$this->addError($this->view->translate('common_error_wrong_contact_group_specified'));
			$this->_redirectExit('index', 'contacts');
		} else {
			if ($hash == $userContactGroup->agree_hash) {
				GN_SessionCache::delete('contacts' . $this->user->id);
				$userContactGroup->agree_time = 'NOW()';
				$userContactGroup->save();
				CRM_History::observe($this->user, 'contacts-confirm', array(
					'contact-group-id' => $userContactGroup->getContactGroup()->id,
					'user-contact-group-id' => $userContactGroup->id,
					'user-contact-group-name' => $userContactGroup->getName(),
					'user-mp' => $userContactGroup->getUser()->getDomain()->marketplace,
					'user-domain' => $userContactGroup->getUser()->getDomain()->domain_name,
					'user-name' => $userContactGroup->getUser()->name,
					'user-id' => $userContactGroup->user_id,
					'user-email' => $userContactGroup->getUser()->email,
				));
			} else {
				$this->addError($this->view->translate('common_error_wrong_hash_specified'));
			}
		}
		$this->_redirectExit('edit', 'contacts', array('id' => $userContactGroup->getContactGroup()->id));
	}

	public function shareAction() {
		GN_SessionCache::delete('contacts' . $this->user->id);
		#GN_Session::stop();

		$modelContactGroups = new Model_ContactGroups();
		$modelUserContactGroups = new Model_UserContactGroups();

		try {
			if (!$this->_hasParam('google-id')) {
				throw new Exception($this->view->translate('common_error_no_contact_group_specified'));
			}
			$id = $this->_getParam('google-id');
			$cclient = CRM_Core::getContactsClient($this->user);
			$group = $cclient->getGroup($id);

			$contactGroup = $modelContactGroups->getByNameAndUserID($group->name, $this->user->id);
			if ($contactGroup !== null) {
				throw new Exception($this->view->translate('common_error_contact_group_already_exists'));
			}

			$contactGroup = $modelContactGroups->createRow();
			$contactGroup->user_id = $this->user->id;
			$contactGroup->group_id = $id;
			$contactGroup->name = $group->name;
			$contactGroup->save();

			$userContactGroup = $modelUserContactGroups->createRow();
			$userContactGroup->contact_group_id = $contactGroup->id;
			$userContactGroup->user_id = $this->user->id;
			$userContactGroup->local_name = null;
			$userContactGroup->save();

			CRM_History::observe($this->user, 'contacts-share', array(
				'contact-group-id' => $userContactGroup->getContactGroup()->id,
				'contact-group-name' => $userContactGroup->getContactGroup()->name,
				'user-contact-group-id' => $userContactGroup->id,
				'user-contact-group-name' => $userContactGroup->getName(),
				'owner-mp' => $this->user->getDomain()->marketplace,
				'owner-name' => $this->user->name,
				'owner-id' => $this->user->id,
				'owner-email' => $this->user->email,
			));
		} catch (Exception $e) {
			GN_Session::restore();
			$this->addError($e->getMessage());
		}
		if ($this->_hasParam('redirect')) {
			$this->_redirectUrlExit(base64_decode($this->_getParam('redirect')));
		}
		$this->_redirectExit('index', 'contacts');
	}

	public function deleteAction() {
		GN_SessionCache::delete('contacts' . $this->user->id);
		#GN_Session::stop();

		try {
			$modelContactGroups = new Model_ContactGroups();
			$contactGroup = $modelContactGroups->getByID($this->_getParam('id'));
			if (empty($contactGroup)) {
				throw new Exception($this->view->translate('common_error_wrong_contact_group_specified'));
			}
			if ($contactGroup->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			$emails = array();
			foreach ($contactGroup->getUserContactGroups() as $userContactGroup) {
				$emails []= $userContactGroup->getUser()->email;
			}

			CRM_History::observe($this->user, 'contacts-unshare', array(
				'contact-group-id' => $contactGroup->id,
				'contact-group-name' => $contactGroup->name,
				'owner-mp' => $this->user->getDomain()->marketplace,
				'owner-name' => $this->user->name,
				'owner-id' => $this->user->id,
				'owner-email' => $this->user->email,
				'user-emails' => join(',', $emails),
			));

			$contactGroup->delete();
		} catch (Exception $e) {
			GN_Session::restore();
			$this->addError($e->getMessage());
		}
		if ($this->_hasParam('redirect')) {
			$this->_redirectUrlExit(base64_decode($this->_getParam('redirect')));
		}
		$this->_redirectExit('index', 'contacts');
	}

	public function editAction() {
		try {
			$modelContactGroups = new Model_ContactGroups();
			$contactGroup = $modelContactGroups->getByID($this->_getParam('id'));
			if (empty($contactGroup)) {
				if (!$this->_hasParam('google-id')) {
					$this->_redirectExit('index', 'contacts');
				}
				$googleContactGroup = $this->_getParam('google-id');
				$contactGroup = $modelContactGroups->getByGoogleID($googleContactGroup);
				if (!empty($contactGroup)) {
					$params = $this->_getAllParams();
					$params['id'] = $contactGroup->id;
					$this->_redirectExit('edit', 'contacts', $params);
				}
				if (empty($googleContactGroup)) {
					throw new Exception($this->view->translate('common_error_wrong_contact_group_specified'));
				}
			}
			if (!empty($contactGroup)) {
				$this->view->hasRightToManage = $contactGroup->getOwnerUserContactGroup()->user_id == $this->user->id;
				$this->view->userContactGroup = $contactGroup->getUserContactGroupByUserID($this->user->id);
				$this->view->contactGroup = $contactGroup;
			}
			if (!empty($googleContactGroup)) {
				$this->view->googleContactGroupID = $googleContactGroup;
				$this->view->googleContactGroupName = base64_decode($this->_getParam('google-name'));
			}
		} catch (Exception $e) {
			$this->addError($e->getMessage());
			$this->_redirectExit('index', 'contacts');
		}
	}

	public function ajaxMemberAddAction() {
		GN_Session::stop();
		header('Content-Type: application/json');
		$json = array();

		try {
			$modelContactGroups = new Model_ContactGroups();
			$contactGroup = $modelContactGroups->getByID($this->_getParam('contact-group-id'));
			if (empty($contactGroup)) {
				throw new Exception($this->view->translate('common_error_wrong_contact_group_specified'));
			}
			if ($contactGroup->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			$modelUsers = new Model_Users();
			foreach (preg_split('/[\s,;:]+/', $this->_getParam('email')) as $email) {
				$email = CRM_Core::parseEmail($email);
				if (!$email) {
					throw new Exception($this->view->translate('common_error_wrong_mail_specified'));
				}
				$user = CRM_Misc::findCreateUser($email);

				$modelUserContactGroups = new Model_UserContactGroups();
				$userContactGroup = $modelUserContactGroups->getByContactGroupIDAndUserID($contactGroup->id, $user->id);
				if (!$userContactGroup) {
					$userContactGroup = $modelUserContactGroups->createRow();
					$userContactGroup->contact_group_id = $contactGroup->id;
					$userContactGroup->user_id = $user->id;
					$userContactGroup->local_name = null;//$contactGroup->name;
					$userContactGroup->agree_hash = md5(microtime(true) . mt_rand() . '-dynks2');
					$userContactGroup->save();
				}

				CRM_History::observe($this->user, 'contacts-member-add', array(
					'contact-group-id' => $userContactGroup->getContactGroup()->id,
					'user-contact-group-id' => $userContactGroup->id,
					'user-contact-group-name' => $userContactGroup->getName(),
					'owner-mp' => $this->user->getDomain()->marketplace,
					'owner-name' => $this->user->name,
					'owner-id' => $this->user->id,
					'owner-email' => $this->user->email,
					'user-mp' => $userContactGroup->getUser()->getDomain()->marketplace,
					'user-domain' => $userContactGroup->getUser()->getDomain()->domain_name,
					'user-name' => $userContactGroup->getUser()->name,
					'user-id' => $userContactGroup->user_id,
					'user-email' => $userContactGroup->getUser()->email,
					'agree-hash' => $userContactGroup->agree_hash,
				));

				CRM_Core::addAutocompleteEmail($this->user, $userContactGroup->getUser()->email);
			}
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
			$json['trace'] = explode("\n", $e->getTraceAsString());
		}
		echo json_encode($json);
		die;
	}

	public function ajaxMemberDeleteAction() {
		GN_Session::stop();
		header('Content-Type: application/json');
		$json = array();

		try {
			$modelUserContactGroups = new Model_UserContactGroups();
			$userContactGroup = $modelUserContactGroups->getByID($this->_getParam('id'));
			if (empty($userContactGroup)) {
				throw new Exception($this->view->translate('common_error_wrong_contact_group_specified'));
			}
			if ($userContactGroup->getContactGroup()->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			CRM_History::observe($this->user, 'contacts-member-remove', array(
				'contact-group-id' => $userContactGroup->getContactGroup()->id,
				'user-contact-group-id' => $userContactGroup->id,
				'user-contact-group-name' => $userContactGroup->getName(),
				'owner-mp' => $this->user->getDomain()->marketplace,
				'owner-name' => $this->user->name,
				'owner-id' => $this->user->id,
				'owner-email' => $this->user->email,
				'user-mp' => $userContactGroup->getUser()->getDomain()->marketplace,
				'user-domain' => $userContactGroup->getUser()->getDomain()->domain_name,
				'user-name' => $userContactGroup->getUser()->name,
				'user-id' => $userContactGroup->user_id,
				'user-email' => $userContactGroup->getUser()->email,
				'agree-hash' => $userContactGroup->agree_hash,
			));

			$userContactGroup->delete();
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
			$json['trace'] = explode("\n", $e->getTraceAsString());
		}
		echo json_encode($json);
		die;
	}

}
