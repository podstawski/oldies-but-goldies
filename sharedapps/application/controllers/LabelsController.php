<?php
require_once 'AbstractController.php';

class LabelsController extends AbstractController {
	public function listAction() {
		$this->_redirectExit('index', 'labels');
	}

	public function indexAction() {
		$modelHistory = new Model_History();
		$modelUserLabels = new Model_UserLabels();
		$this->view->history = $modelHistory->getByUserIDWithLimit($this->user->id, 'label');
		$this->view->unconfirmedUserLabels = $modelUserLabels->getAllUnconfirmedByUserID($this->user->id);
	}

	public function unconfirmAction() {
		$modelUserLabels = new Model_UserLabels();
		$hash = $this->_getParam('hash');
		$userLabel = $modelUserLabels->getByID(intval($this->_getParam('label')));
		if (empty($userLabel)) {
			$this->addError($this->view->translate('common_error_wrong_label_specified'));
			$this->_redirectExit('index', 'labels');
		} else {
			if ($hash == $userLabel->agree_hash) {
				GN_SessionCache::delete('folders' . $this->user->id);
				CRM_History::observe($this->user, 'labels-unconfirm', array(
					'label-id' => $userLabel->getLabel()->id,
					'user-label-id' => $userLabel->id,
					'user-label-name' => CRM_Core::imapDec($userLabel->getName()),
					'user-mp' => $userLabel->getUser()->getDomain()->marketplace,
					'user-domain' => $userLabel->getUser()->getDomain()->domain_name,
					'user-name' => $userLabel->getUser()->name,
					'user-id' => $userLabel->user_id,
					'user-email' => $userLabel->getUser()->email,
				));
				$userLabel->agree_time = null;
				$userLabel->save();
			} else {
				$this->addError($this->view->translate('common_error_wrong_hash_specified'));
			}
		}
		$this->_redirectExit('edit', 'labels', array('id' => $userLabel->getLabel()->id));
	}

	public function confirmAction() {
		$modelUserLabels = new Model_UserLabels();

		$hash = $this->_getParam('hash');
		$userLabel = $modelUserLabels->getByID(intval($this->_getParam('label')));
		if (empty($userLabel)) {
			$this->addError($this->view->translate('common_error_wrong_label_specified'));
			$this->_redirectExit('index', 'labels');
		} else {
			if ($hash == $userLabel->agree_hash) {
				GN_SessionCache::delete('folders' . $this->user->id);
				$userLabel->agree_time = 'NOW()';
				$userLabel->save();
				CRM_History::observe($this->user, 'labels-confirm', array(
					'label-id' => $userLabel->getLabel()->id,
					'user-label-id' => $userLabel->id,
					'user-label-name' => CRM_Core::imapDec($userLabel->getName()),
					'user-mp' => $userLabel->getUser()->getDomain()->marketplace,
					'user-domain' => $userLabel->getUser()->getDomain()->domain_name,
					'user-name' => $userLabel->getUser()->name,
					'user-id' => $userLabel->user_id,
					'user-email' => $userLabel->getUser()->email,
				));
			} else {
				$this->addError($this->view->translate('common_error_wrong_hash_specified'));
			}
		}
		$this->_redirectExit('edit', 'labels', array('id' => $userLabel->getLabel()->id));
	}

	public function shareAction() {
		GN_SessionCache::delete('folders' . $this->user->id);
		#GN_Session::stop();

		$modelLabels = new Model_Labels();
		$modelUserLabels = new Model_UserLabels();
		$imap = CRM_Core::getIMAP($this->user)->getIMAP();

		try {
			if (!$this->_hasParam('google-id')) {
				throw new Exception($this->view->translate('common_error_no_folder_specified'));
			}
			$folder = base64_decode($this->_getParam('google-id'));

			$label = $modelLabels->getByNameAndUserID($folder, $this->user->id);
			if ($label !== null) {
				throw new Exception('common_error_label_aready_exists');
			}

			$label = $modelLabels->createRow();
			$label->user_id = $this->user->id;
			$label->name = $folder;
			$label->save();

			$userLabel = $modelUserLabels->createRow();
			$userLabel->label_id = $label->id;
			$userLabel->user_id = $this->user->id;
			$userLabel->local_name = null;
			$userLabel->save();

			CRM_History::observe($this->user, 'labels-share', array(
				'label-id' => $userLabel->getLabel()->id,
				'label-name' => CRM_Core::imapDec($userLabel->getLabel()->name),
				'user-label-id' => $userLabel->id,
				'user-label-name' => CRM_Core::imapDec($userLabel->getName()),
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
		$this->_redirectExit('index', 'labels');
	}



	public function editAction() {
		try {
			$modelLabels = new Model_Labels();
			$label = $modelLabels->getByID($this->_getParam('id'));
			if (empty($label)) {
				if (!$this->_hasParam('google-id')) {
					$this->_redirectExit('index', 'labels');
				}
				$googleLabel = base64_decode($this->_getParam('google-id'));
				$label = $modelLabels->getByGoogleID($googleLabel);
				if (!empty($label)) {
					$params = $this->_getAllParams();
					$params['id'] = $label->id;
					$this->_redirectExit('edit', 'labels', $params);
				}
				if (empty($googleLabel)) {
					throw new Exception($this->view->translate('common_error_wrong_label_specified'));
				}
			}
			if (!empty($label)) {
				$this->view->hasRightToManage = $label->getOwnerUserLabel()->user_id == $this->user->id;
				$this->view->userLabel = $label->getUserLabelByUserID($this->user->id);
				$this->view->label = $label;
			}
			if (!empty($googleLabel)) {
				$this->view->googleLabel = $googleLabel;
			}
		} catch (Exception $e) {
			$this->addError($e->getMessage());
			$this->_redirectExit('index', 'labels');
		}
	}



	public function deleteAction() {
		GN_SessionCache::delete('folders' . $this->user->id);
		#GN_Session::stop();

		try {
			$modelLabels = new Model_Labels();
			$label = $modelLabels->getByID($this->_getParam('id'));
			if (empty($label)) {
				throw new Exception($this->view->translate('common_error_wrong_label_specified'));
			}
			if ($label->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			$emails = array();
			foreach ($label->getUserLabels() as $userLabel) {
				$emails []= $userLabel->getUser()->email;
			}

			CRM_History::observe($this->user, 'labels-unshare', array(
				'label-id' => $label->id,
				'label-name' => CRM_Core::imapDec($label->name),
				'owner-mp' => $this->user->getDomain()->marketplace,
				'owner-name' => $this->user->name,
				'owner-id' => $this->user->id,
				'owner-email' => $this->user->email,
				'user-emails' => join(',', $emails),
			));

			$label->delete();
			if ($this->_hasParam('google-id')) {
				$this->_redirectExit('edit', 'labels', array('google-id' => $this->_getParam('google-id')));
			}
		} catch (Exception $e) {
			GN_Session::restore();
			$this->addError($e->getMessage());
		}
		if ($this->_hasParam('redirect')) {
			$this->_redirectUrlExit(base64_decode($this->_getParam('redirect')));
		}
		$this->_redirectExit('index', 'labels');
	}



	public function ajaxRenameAction() {
		GN_SessionCache::delete('folders' . $this->user->id);
		#GN_Session::stop();
		header('Content-Type: application/json');
		$json = array();

		try {
			$modelUserLabels = new Model_UserLabels();
			$userLabel = $modelUserLabels->getByID($this->_getParam('user-label-id'));
			if (empty($userLabel)) {
				throw new Exception($this->view->translate('common_error_wrong_label_specified'));
			}
			if ($userLabel->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			$folder = $this->_getParam('folder');
			if (empty($folder)) {
				throw new Exception($this->view->translate('common_error_no_folder_specified'));
			}

			/*$imap = CRM_Core::getIMAP($this->user);
			CRM_Core::renamePath($imap, $userLabel->local_name, $folder);*/
			foreach ($modelUserLabels->getByUserID($this->user->id) as $otherUserLabel) {
				if ($otherUserLabel->id != $userLabel->id and $otherUserLabel->getName() == $folder) {
					throw new Exception($this->view->translate('common_error_label_already_used'));
				}
			}
			$userLabel->local_name = $folder;
			$userLabel->save();
			$label = $userLabel->getLabel();
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
			$json['trace'] = explode("\n", $e->getTraceAsString());
		}
		echo json_encode($json);
		die;
	}



	public function ajaxChangeDateAction() {
		GN_Session::stop();
		header('Content-Type: application/json');
		$json = array();

		try {
			$modelUserLabels = new Model_UserLabels();
			$userLabel = $modelUserLabels->getByID($this->_getParam('user-label-id'));
			if (empty($userLabel)) {
				throw new Exception($this->view->translate('common_error_wrong_label_specified'));
			}
			if ($userLabel->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}


			$date = $this->_getParam('date');
			if (empty($date)) {
				throw new Exception($this->view->translate('common_error_no_date_specified'));
			}

			$json['time'] = strtotime($date);

			$userLabel->start = date('Y-m-d', strtotime($date));
			$userLabel->save();
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
			$json['trace'] = explode("\n", $e->getTraceAsString());
		}
		echo json_encode($json);
		die;
	}



	public function ajaxMemberAddAction() {
		GN_Session::stop();
		header('Content-Type: application/json');
		$json = array();

		try {
			$modelLabels = new Model_Labels();
			$label = $modelLabels->getByID($this->_getParam('label-id'));
			if (empty($label)) {
				throw new Exception($this->view->translate('common_error_wrong_label_specified'));
			}
			if ($label->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			foreach (preg_split('/[\s,;:]+/', $this->_getParam('email')) as $email) {
				$email = CRM_Core::parseEmail($email);
				if (!$email) {
					throw new Exception($this->view->translate('common_error_wrong_mail_specified'));
				}
				$user = CRM_Misc::findCreateUser($email);

				$modelUserLabels = new Model_UserLabels();
				$userLabel = $modelUserLabels->getByLabelIDAndUserID($label->id, $user->id);
				if (!$userLabel) {
					$userLabel = $modelUserLabels->createRow();
					$userLabel->label_id = $label->id;
					$userLabel->user_id = $user->id;
					$userLabel->local_name = null;//$label->name;
					$userLabel->agree_hash = md5(microtime(true) . mt_rand() . '-dynks');
					$userLabel->save();
				}

				CRM_History::observe($this->user, 'labels-member-add', array(
					'label-id' => $userLabel->getLabel()->id,
					'user-label-id' => $userLabel->id,
					'user-label-name' => CRM_Core::imapDec($userLabel->getName()),
					'owner-mp' => $this->user->getDomain()->marketplace,
					'owner-name' => $this->user->name,
					'owner-id' => $this->user->id,
					'owner-email' => $this->user->email,
					'user-mp' => $userLabel->getUser()->getDomain()->marketplace,
					'user-domain' => $userLabel->getUser()->getDomain()->domain_name,
					'user-name' => $userLabel->getUser()->name,
					'user-id' => $userLabel->user_id,
					'user-email' => $userLabel->getUser()->email,
					'agree-hash' => $userLabel->agree_hash,
				));

				CRM_Core::addAutocompleteEmail($this->user, $userLabel->getUser()->email);
			}
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
			$json['trace'] = explode("\n", $e->getTraceAsString());
		}
		echo json_encode($json);
		die;
	}

	public function ajaxMemberDeleteAction() {
		GN_SessionCache::delete('labels' . $this->user->id);
		GN_Session::stop();
		header('Content-Type: application/json');
		$json = array();

		try {
			$modelUserLabels = new Model_UserLabels();
			$userLabel = $modelUserLabels->getByID($this->_getParam('id'));
			if (empty($userLabel)) {
				throw new Exception($this->view->translate('common_error_wrong_label_specified'));
			}
			if ($userLabel->getLabel()->user_id != $this->user->id) {
				throw new Exception($this->view->translate('common_error_illegal_action'));
			}

			CRM_History::observe($this->user, 'labels-member-remove', array(
				'label-id' => $userLabel->getLabel()->id,
				'user-label-id' => $userLabel->id,
				'user-label-name' => CRM_Core::imapDec($userLabel->getName()),
				'owner-mp' => $this->user->getDomain()->marketplace,
				'owner-name' => $this->user->name,
				'owner-id' => $this->user->id,
				'owner-email' => $this->user->email,
				'user-mp' => $userLabel->getUser()->getDomain()->marketplace,
				'user-domain' => $userLabel->getUser()->getDomain()->domain_name,
				'user-name' => $userLabel->getUser()->name,
				'user-id' => $userLabel->user_id,
				'user-email' => $userLabel->getUser()->email,
				'agree-hash' => $userLabel->agree_hash,
			));

			$userLabel->delete();
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
			$json['trace'] = explode("\n", $e->getTraceAsString());
		}
		echo json_encode($json);
		die;
	}
}
