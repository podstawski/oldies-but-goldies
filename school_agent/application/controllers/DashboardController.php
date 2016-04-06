<?php
require_once 'AbstractController.php';

class DashboardController extends AbstractController
{
    public function ajaxRememberSpreadsheetAction()
    {
        header('Content-Type: application/json');

        if (!$this->_hasParam('spreadsheet-id')) {
            $spreadsheetId = null;
        } else {
            $spreadsheetId = $this->_getParam('spreadsheet-id');
        }

        $domain = $this->user->getDomain();
        $domain->last_spreadsheet = $spreadsheetId;
        $domain->save();

        header('HTTP/1.1 200 OK');
        echo json_encode(true);
        die();
    }

    public function resetTokenAction()
    {
        $domain = $this->user->getDomain();
        $domain->oauth_token = null;
        $domain->save();
        $this->_redirectExit('logout', 'auth');
    }

    public function googleErrorAction()
    {
    }

    public function indexAction()
    {
        $this->view->processID = ClassGroup_Process::generateProcessId();

		$gclient = null;
		if ($this->user->admin) {
			$domain = $this->user->getDomain();
			if (!empty($domain->oauth_token)) {
				$gclient = new GN_GClient($this->user);
			}
		}
		if (empty($gclient)) {
			$this->_redirectExit('provisioning-api', 'index');
		}
        $gapps = new ClassGroup_Gapps($gclient);
        try {
            $folder = $gclient->getFolderByTitle($this->view->translate('spreadsheet_folder_name'));
            if ($folder !== false) {
                $folderUri = $folder->content->src;
                $this->view->spreadsheetList = $gapps->getSpreadsheetList($folderUri);
            } else {
                $this->view->spreadsheetList = null;
            }
        } catch (Exception $e) {
            $this->_redirectExit('google-error');
        }

        $this->view->activeAction = $domain->getActiveAction();

        $modelAction = new Model_Action();
        $this->view->actions = $modelAction->fetchAll($modelAction->selectByDomainId($this->user->domain_id)->order('date_created DESC'));


	$client = new GN_GClient($this->user);
    }
}
