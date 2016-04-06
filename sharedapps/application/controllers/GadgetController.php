<?php
require_once 'AbstractController.php';

class GadgetController extends AbstractController {
	public function authAction() {
		header('Content-Type: application/json');
		
		$json = array();
		$json['popup'] = $this->view->absoluteUrl(array('controller' => 'auth', 'action' => 'open-id'), null, true);

		$modelUsers = new Model_Users();
		$user = $modelUsers->getByIdentity($_GET['opensocial_owner_id']);
		//GN_Debug::debug('social id: ' . $_GET['opensocial_owner_id']);
		//GN_Debug::debug(print_r($user, true));
		if (empty($user)) {
			$json['userExists'] = false;
		} else {
			$json['userExists'] = true;
			$json['userEmail'] = $user->email;
			$json['messageID'] = $_GET['message-id'];
			$json['fromAddress'] = $_GET['from-address'];
		}

		echo json_encode($json);
		die;
	}

}
