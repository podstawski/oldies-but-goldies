<?php
require_once 'AbstractController.php';

class DashboardController extends AbstractController {

	public function ajaxSettingsAction() {
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');

		if (!isset($_SESSION['settings'])) {
			$_SESSION['settings'] = array();
		}
		foreach ($this->_getAllParams() as $key => $value) {
			$_SESSION['settings'][$key] = $value;
		}

		echo json_encode($_SESSION['settings']);
		die();
	}

	public function indexAction() {
		$modelTests = new Model_Tests();
		$role = Zend_Controller_Front::getInstance()->getPlugin('GN_Plugin_Acl')->getRoleName();
		$this->view->isWizard = (!isset($_COOKIE['wizard-closed']) or !$_COOKIE['wizard-closed']);

		if (isset($_SERVER['REMOTE_ADDR'])) {
			$paginators = array ();

			switch ($role) {
				case Model_Users::ROLE_TEACHER:
					$select = $modelTests->selectManager($this->user->id);
					break;
				case Model_Users::ROLE_ADMINISTRATOR:
				case Model_Users::ROLE_SUPER_ADMINISTRATOR:
					$select = $modelTests->selectManager($this->user->id);
					#$select = $modelTests->selectDomain($this->user->domain_id);
					break;
				case Model_Users::ROLE_CLI:
					$select = $modelTests->select(true);
					break;
				default:
					die($this->view->translate('misc_bad_role_error'));
			}

			$select->order(array('status DESC', 'id DESC'));
			$this->view->sections = true;

			if ($this->_hasParam('starred')) {
				$this->view->sections = false;
				$select->setIntegrityCheck(false);
				$select->joinLeft('stars', 'test_id = tests.id', array('star'));
				if (intval($this->_getParam('starred')) > 0) {
					$select->where('star = 1');
				} else {
					$select->where('star = 0 or star is null');
				}
			}

			if ($this->_hasParam('search-title')) {
				$this->view->sections = false;
				$this->view->searchTitle = $this->_getParam('search-title');
				if ($this->_getParam('search-title') != '') {
					$select->where('STRPOS(LOWER(document_title), LOWER(?)) > 0', $this->_getParam('search-title'));
				}
			}

			if ($this->_hasParam('search-group')) {
				$this->view->sections = false;
				$this->view->searchGroup = $this->_getParam('search-group');
				if ($this->_getParam('search-group') != '') {
					$select->where('group_name = ?', $this->_getParam('search-group'));
				}
			}

			if ($this->_hasParam('search-status-id')) {
				$this->view->sections = false;
				$this->view->searchStatus = $this->_getParam('search-status-id');
				if ($this->_getParam('search-status-id') != '') {
					$select->where('status = ?', intval($this->_getParam('search-status-id')));
				}
			}

			if ($this->view->sections) {
				$baseSelect = $select;
				foreach (array(Model_Tests::STATUS_UNOPENED, Model_Tests::STATUS_OPENED, Model_Tests::STATUS_FINISHED) as $status) {
					$select = clone($baseSelect);
					$select->where('status = ?', $status);

					$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
					$paginator->setCurrentPageNumber($this->_getParam('pageID-' . $status, 1));
					$paginators[$status] = $paginator;
				}
				$this->view->paginators = $paginators;
			} else {
				$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
				$paginator->setCurrentPageNumber($this->_getParam('pageID', 1));
				$this->view->paginator = $paginator;
			}

		} else {
			$modelUsers = new Model_Users();
			//skonstruuj tabelke z egzaminami
			$table = array();
			foreach ($modelTests->fetchAll() as $test) {
				$user = $modelUsers->find($test->user_id)->current();
				$row = array();
				$row['id'] = $test->id;
				$row['title'] = $test->document_title;
				switch ($test->status) {
					case Model_Tests::STATUS_UNOPENED:
						$row['status'] = 'unopened';
						break;
					case Model_Tests::STATUS_OPENED:
						$row['status'] = 'opened';
						break;
					case Model_Tests::STATUS_FINISHED:
						$row['status'] = 'closed';
						break;
					default:
						$row['status'] = 'unknown';
				}
				$row['creator'] = $user->email;
				$row['domain'] = $user->getDomain()->domain_name;
				$table []= $row;
			}
			if (!empty($table)) {
				//dodaj naglowek tabelce
				array_unshift($table, array_combine(array_keys($table[0]), array_keys($table[0])));
				//pobierz maksymalne dlugosci dla kazdej kolumny
				$keys = array_keys($table[0]);
				$max = array();
				foreach ($keys as $key) {
					$max[$key] = 0;
					foreach ($table as $i => $row) {
						$max[$key] = max($max[$key], strlen($row[$key]));
					}
				}
				//wyprintuj tabelke
				foreach ($table as $i => $row) {
					//wyrownaj kolumny spacjami
					foreach ($keys as $key) {
						$row[$key] = str_pad($row[$key], $max[$key]);
					}
					echo join('  |  ', $row) . PHP_EOL;
				}
			}
			die();
		}
	}

	public function trialExpiredAction() {
	}
}
?>
