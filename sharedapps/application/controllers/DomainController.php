<?php
require_once 'AbstractController.php';

class DomainController extends AbstractController {

	public function changeAction() {
		if (!$this->_hasParam('domain-id')) {
			$this->addError($this->view->translate('change_domain_no_domain_specified_error'));
		} else {
			$domainId = intval($this->_getParam('domain-id'));
			$modelDomains = new Model_Domains();
			$domain = $modelDomains->find($domainId)->current();
			if (empty($domain)) {
				$this->addError($this->view->translate('change_domain_wrong_domain_error'));
			} else {
				$_SESSION['domain-id'] = $domain->id;
				$_SESSION['domain-name'] = $domain->domain_name;
				$this->user->domain_id = $domain->id;
				$this->addSuccess($this->view->translate('change_domain_success'));
				$this->domains = $modelDomains->fetchAll($modelDomains->select(true));
			}
		}
		$this->_redirectExit('index', 'labels');
	}

}
