<?php
require_once 'AbstractController.php';

class ExportController extends AbstractController
{
    public function indexAction()
    {
        $this->_redirectExit('index', 'dashboard');
    }

    private function getWorksheetId($worksheetEntry)
    {
        return current(array_reverse(explode('/', $worksheetEntry->id->text)));
    }

    private function finishError($message = false)
    {
        $progressID = $this->_getParam('progress-id');
        ClassGroup_Progress::finish($progressID, false);
        if (!empty($message)) {
            $this->addError($message);
        }
    }

    private function finishSuccess($message = false)
    {
        $progressID = $this->_getParam('progress-id');
        ClassGroup_Progress::finish($progressID, true);
        if (!empty($message)) {
            $this->addSuccess($message);
        }
    }
    

    public function exportAction()
    {
        header('Content-type: text/html; charset=utf-8');
        $this->_helper->layout->disableLayout();

        if ((!$this->_hasParam('process-id') or !ClassGroup_Process::confirmProcessId($this->_getParam('process-id')))) {
            die($this->view->translate('misc_wrong_process'));
        }
		$progressID = $this->_getParam('progress-id');
		


		$this->user->getDomain()->export_count ++;
		$this->user->getDomain()->save();


		
		ClassGroup_Process::discardProcessId();
		ClassGroup_Session::stop();
		ClassGroup_Progress::detachBrowser();
		ClassGroup_Progress::start($progressID, 0, 1);

		
		
		try {
			$this->addDebug('export started');

			if (!$this->_hasParam('spreadsheet-title')) {
				$this->finishError($this->view->translate('export_no_spreadsheet_title_specified_error'));
				return;
			}

			//zainicjuj stuff
			$spreadsheetTitle = $this->_getParam('spreadsheet-title');


			$gclient = new GN_GClient($this->user);
			$domainName = $gclient->getDomain();
			$gapps = new ClassGroup_Gapps($gclient);

			//pobierz info o grupach i uzytkownikach
			$this->addDebug('retrieving all google groups');
			$groups = $gapps->getGroups();
			$this->addDebug('retrieving all google users');
			$users  = $gapps->getUsers();
			ClassGroup_Progress::start($progressID, 2, 2
				+ count($groups) // pobieranie danych o grupach
				+ count($users)  // pobieranie danych o userach
				+ count($groups) // wpisywanie danych o grupie
				+ 1              // wpisywanie danych o userach bez grupy
			);

			$this->addDebug('retrieving details of google groups');
			foreach ($groups as $key => $group) {
				$googleGroup = $gapps->getGroup($group['e-mail']);
				if (!empty($googleGroup)) {
					$group = array_merge($group, $googleGroup);
				} else {
					$group['members'] = array();
					$group['owners'] = array();
				}
				$groups[$key] = $group;
				ClassGroup_Progress::step($progressID);
			}

			$this->addDebug('retrieving details of google users');
			$users = array_combine(array_map(function($user) { return $user['e-mail']; }, $users), $users);
			foreach ($users as $key => $user) {
				$googleUser = $gapps->getUser($user['e-mail']);
				if (!empty($googleUser)) {
					$user = array_merge($user, $googleUser);
				} else {
					$user['member-of'] = array();
					$user['owner-of'] = array();
				}
				$users[$key] = $user;
				ClassGroup_Progress::step($progressID);
			}
			
			

			if (empty($groups) and empty($users)) {
				$this->finishError($this->view->translate('export_empty_domain_error'));
				return;
			}

			//dodaj nieistniejących userów
			$this->addDebug('analyzing users basing on groups');
			foreach ($groups as $group) {
				foreach (array_unique(array_merge($group['owners'], $group['members'])) as $member) {
					if (isset($users[$member])) {
						continue;
					}
					$user = null;
					list ($userName, $domainName) = explode('@', $member);
					try {
						$user = $gapps->getUser($member);
					} catch (Exception $e) {
					}
					if (empty($user)) {
						$user = array();
						$user['first-name'] = '';
						$user['last-name'] = '';
					}
					$user['password'] = '';
					$user['e-mail'] = $member;
					$user['owner-of'] = array();
					$user['member-of'] = array();
					$user['added-artificially'] = true;
					$users[$member] = $user;
				}
			}

			$this->addDebug('merging information');
			//zobacz kto jest czego ownerem
			foreach ($groups as $group) {
				foreach ($group['members'] as $member) {
					if (!in_array($group['e-mail'], $users[$member]['member-of'])) {
						$users[$member]['member-of'] []= $group['e-mail'];
					}
				}
				foreach ($group['owners'] as $owner) {
					if (!in_array($group['e-mail'], $users[$owner]['owner-of'])) {
						$users[$owner]['owner-of'] []= $group['e-mail'];
					}
				}
			}

			$worksheetHeader = array(
				'first-name' => $this->view->translate('spreadsheet_header_first_name'),
				'last-name' => $this->view->translate('spreadsheet_header_last_name'),
				'e-mail' => $this->view->translate('spreadsheet_header_email'),
				'password' => $this->view->translate('spreadsheet_header_password'),
				'owner-of' => $this->view->translate('spreadsheet_header_ownership'),
			);

            require_once 'PHPExcel/PHPExcel.php';
            $PHPExcel = new PHPExcel;
            $filenameBase = APPLICATION_PATH . '/cache/' . md5(time());
			$filenameXLSX = $filenameBase . '.xlsx';

			/**
			 * @var PHPExcel_Worksheet $worksheet
			 */
			foreach ($groups as $group) {
				$this->addDebug('creating worksheet for ' . $group['e-mail']);
                $groupMembers = array_unique(array_merge($group['owners'], $group['members']));

                $worksheet = $PHPExcel->createSheet();
                $worksheet->setTitle($group['e-mail']);
                $col = 0;
                foreach ($worksheetHeader as $k => $header) {
                    $row = 1;
                    $worksheet->setCellValueByColumnAndRow($col, $row, $header, true);
                    $worksheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $row++;
                    foreach ($groupMembers as $member) {
                        $val = isset($users[$member][$k]) ? $users[$member][$k] : '';
                        if (is_array($val))
                            $val = implode(', ', $val);
                        $worksheet->setCellValueByColumnAndRow($col, $row, $val);
                        $row++;
                    }
                    $col++;
                }

				ClassGroup_Progress::step($progressID);
			}

			
			
			$this->addDebug('creating worksheet for all');
			$worksheetHeader = array(
				'first-name' => $this->view->translate('spreadsheet_header_first_name'),
				'last-name' => $this->view->translate('spreadsheet_header_last_name'),
				'e-mail' => $this->view->translate('spreadsheet_header_email'),
				'password' => $this->view->translate('spreadsheet_header_password'),
				'owner-of' => $this->view->translate('spreadsheet_header_ownership'),
				'member-of' => $this->view->translate('spreadsheet_header_membership'),
			);

            $worksheet = $PHPExcel->getSheet(0);
            $worksheet->setTitle($this->view->translate('export_group_all'));
            $col = 0;
            foreach ($worksheetHeader as $k => $header) {
                $row = 1;
                $worksheet->setCellValueByColumnAndRow($col, $row, $header, true);
                $worksheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $row++;
                foreach ($users as $user) {
                    if (!empty($user['added-artificially']))
                        continue;

                    $val = isset($user[$k]) ? $user[$k] : '';
                    if (is_array($val))
                        $val = implode(', ', $val);
                    $worksheet->setCellValueByColumnAndRow($col, $row, $val);
                    $row++;
                }
                $col++;
            }

			ClassGroup_Progress::step($progressID);

			$this->addDebug('saving xls');
			

//            file_put_contents('/home/simer/dump.txt', print_r($users, 1) . PHP_EOL . print_r($groups, 1));

            $PHPExcelWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $PHPExcelWriter->save($filenameXLSX);

//            $PHPExcelWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
//            $PHPExcelWriter->save('/home/simer/dump.xls');

			try {
				//utwórz folder
				$folderTitle = $this->view->translate('spreadsheet_folder_name');
				$folder = $gclient->getFolderByTitle($folderTitle, true);
				$folderUri = $folder->content->src;
			} catch (Exception $e) {
				$this->finishError($this->view->translate('export_folder_creation_error', $folderTitle));
				return;
			}

			$tmpTitle = md5(time());
			$httpClient = $gapps->getGClient()->getHttpClient();
			$docs = new Zend_Gdata_Docs($httpClient);
			try {
				$this->addDebug('uploading xls');
				$doc = $docs->uploadFile($filenameXLSX, $tmpTitle, 'application/vnd.ms-excel');
				@unlink($filenameXLSX);
			} catch (Exception $e) {
			}

			$docs = new Zend_Gdata_Docs($httpClient);
			try {
				$id = $gclient->getSpreadsheetId($tmpTitle);
				$doc = $docs->getSpreadsheet($id);
				$doc->setTitle(new Zend_Gdata_App_Extension_Title($spreadsheetTitle));
				$doc->save();
			} catch (Exception $e) {
				$this->finishError($this->view->translate('export_document_creation_error'));
				return;
			}

			try {
				//dopisz do nowego folderu
				$spreadsheetId = $gclient->getDocumentID($doc);
				$gclient->insertEntry($doc, $folderUri);

				//usuń z root folderu
				$uri = Zend_Gdata_Docs::DOCUMENTS_FOLDER_FEED_URI . '/folder%3Aroot/' . $spreadsheetId;
				$requestData = $gclient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
				$gclient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);
			} catch (Exception $e) {
				$this->finishError($this->view->translate('export_folder_move_error', $folderTitle));
				return;
			}

			$this->addDebug('export done!');

			$this->finishSuccess(
				$this->view->translate('export_finished_success_prefix') .
				'<a target="_blank" href="' . $doc->getLink('alternate')->href . '">' .
				$this->view->translate('export_finished_success_link') .
				'</a>' .
				$this->view->translate('export_finished_success_suffix')
			);
		} catch (Exception $e) {
			$this->addCrashReport($e);
			$this->finishError($e->getMessage());
		}
    }
}

?>
