<?php
require_once 'CompetenceController.php';

class DomainsController extends CompetenceController
{
	public function indexAction()
	{
		$modelDomains = new Model_Domains();
		$select = $modelDomains
			->select(true)
			;


		$this->view->name = null;
		if ($this->_hasParam('name'))
		{
			$this->view->name = trim($this->_getParam('name'));
		}
		if (!empty($this->view->name))
		{
			foreach (explode(' ', $this->view->name) as $word)
			{
				$select->where('STRPOS(lower(domain_name), lower(?)) > 0', $word);
			}
		}

		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($this->_getParam('pageID', 1));
		$this->view->paginator = $paginator;
	}

	public function deleteAction()
	{
		if (!$this->_hasParam('domain-id'))
		{
			$this->addError($this->view->translate('No domain ID specified'));
			return;
		}
		$domainId = intval($this->_getParam('domain-id'));
		$modelDomains = new Model_Domains();
		$domain = $modelDomains->find($domainId)->current();
		if (empty($domain))
		{
			$this->addError($this->view->translate('No domain with ID %d', $domainId));
			return;
		}
		$modelDomains->delete(array('id = ?' => $domainId));
		$this->addSuccess($this->view->translate('Domain deleted successfully'));
	}

	public function changeAction()
	{
		if (!$this->_hasParam('domain-id'))
		{
			$this->addError($this->view->translate('No domain ID specified'));
			return;
		}
		$domainId = intval($this->_getParam('domain-id'));
		$modelDomains = new Model_Domains();
		$domain = $modelDomains->find($domainId)->current();
		if (empty($domain))
		{
			$this->addError($this->view->translate('No domain with ID %d', $domainId));
			return;
		}
		$_SESSION['domain-id'] = $domain->id;
		$_SESSION['domain-name'] = $domain->domain_name;
		$this->user->domain_id = $domain->id;
		$this->addSuccess($this->view->translate('Domain changed successfully'));
	}

	public function settingsAction()
	{
		$domain = $this->user->getDomain();
		if ($this->_hasParam('course-standard-threshold'))
		{
			$domain->data['opening-text-teacher'] = $this->_getParam('opening-text-teacher');
			$domain->data['opening-text-student'] = $this->_getParam('opening-text-student');
			$domain->data['course-standard-threshold'] = intval($this->_getParam('course-standard-threshold'));
			$domain->save();
			$this->addSuccess($this->view->translate('Domain settings changed successfully'));
		}
		$this->view->domain = $domain;
	}
}
?>
