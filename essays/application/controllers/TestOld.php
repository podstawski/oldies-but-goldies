<?php
require_once 'AbstractController.php';

class TestController extends AbstractController {
	public function indexAction() {
		$this->_redirectExit('index', 'dashboard');
	}

	private function isValidDate($d) {
		return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
	}

	private function isValidTime($t) {
		return preg_match('/^\d{2}:\d{2}$/', $t);
	}

	private function isValidEmail($m) {
		return preg_match('/^\S+@\S+\.\S+$/', $m);
	}

	//get link to embeddable google document based on normal edit link
	private function getEmbedLink($link) {
		if (strpos($link, '?') === false) {
			$link .= '?';
		} else {
			$link .= '&';
		}
		$link .= 'rm=embedded';
		return $link;
	}

	//append domain to mail if necessary
	private function constructMail($mail, $domain) {
		if (strpos($mail, '@') === false) {
			$mail .= '@' . $domain;
		}
		return $mail;
	}

	private function constructId($mail) {
		if (strpos($mail, '@') !== false) {
			return current(explode('@', $mail));
		} else {
			return $mail;
		}
	}

	protected function retrieveFolks($groupID, $detailLevel = 0) {
		$folks = array();
		if ($this->user->getDomain()->isSpecial() or (!$this->user->getDomain()->marketplace and empty($this->user->getDomain()->oauth_token))) {
			return $folks;
		}
		$client = new GN_GClient($this->user, GN_GClient::MODE_DOMAIN);
		if ($groupID !== null) {
			if ($detailLevel >= 2) {
				$users = $client->retrieveAllUsers();
			}
			$members = $client->retrieveAllMembers($groupID);
			foreach ($members as $member) {
				$properties = array();
				foreach ($member->property as $property) {
					$properties[$property->name] = $property->value;
				}
				$type = $properties['memberType'];
				$mail = $this->constructMail($properties['memberId'], $this->user->getDomain()->domain_name);
				$id = $this->constructId($mail);

				if (strtolower($type) == 'group') {
					continue;
				}

				$folk = array(
					'e-mail' => $mail,
					'id' => $id,
				);

				//n requestów pobierających imię i nazwisko użytkownika
				if ($detailLevel >= 2) {
					/*$user = $client->retrieveUser($id);*/
					$name = null;
					foreach ($users as $user) {
						if ($user->login->username == $id) {
							$name = $user->name->givenName . ' ' . $user->name->familyName;
							break;
						}
					}
					$folk['name'] = $name;
				}

				$folks []= $folk;
			}

			//1 request pobierający listę ownerów danej grupy
			if ($detailLevel >= 1) {
				$owners = array();
				foreach ($client->retrieveGroupOwners($groupID) as $owner) {
					$properties = array();
					foreach ($owner->property as $property) {
						$properties[$property->name] = $property->value;
					}
					$mail = $this->constructMail($properties['email'], $this->user->getDomain()->domain_name);
					$owners []= $mail;
				}
				foreach ($folks as $key => $folk) {
					if (in_array($folk['e-mail'], $owners)) {
						$folk['owner'] = true;
					} else {
						$folk['owner'] = false;
					}
					$folks[$key] = $folk;
				}
			}
		} else {
			$users = $client->retrieveAllUsers();
			foreach ($users as $user) {
				$mail = $this->constructMail($user->login->username, $this->user->getDomain()->domain_name);
				$id = $this->constructId($mail);
				$folk = array(
					'e-mail' => $mail,
					'id' => $id,
					'name' => $user->name->givenName . ' ' . $user->name->familyName,
				);

				$folks []= $folk;
			}
		}
		return $folks;
	}

	private function increaseTrialCount($user) {
		$trial = $this->getInvokeArg('bootstrap')->getOption('trial');

		if (!$trial['enabled']) {
			return;
		}

		//jest z gmaila - zwiększ userowi
		if ($user->getDomain()->isSpecial()) {
			$user->trial_count ++;
			$user->save();
		//nie jest z gmaila - zwiększ domence
		} else {
			$domain = $user->getDomain();
			$domain->trial_count ++;
			$domain->save();
		}
	}


	public function createAction() {
		if (!$this->checkTrial($this->user)) {
			$this->_redirectExit('index', 'payment');
		}

		$isWizard = $this->_hasParam('wizard');
		$isCreated = true;

		$modelTests = new Model_Tests();
		$test = $modelTests->createRow();
		$test->user_id = $this->user->id;
		$test->document_title = $this->view->translate('misc_default_test_title', date($this->view->translate('misc_date_format'), self::getNow()));
		$test->document_id = null;
		$test->time_zone = GN_Tools::getUserTimezoneOffset();
		$test->mail_sent = 0;
		$test->save();

		$this->_redirectExit('details', 'test', array('test-id' => $test->id, 'wizard' => $this->_hasParam('wizard') ? 1 : 0, 'created' => true));
	}

	public function editContentsAction() {
		$this->_helper->layout()->disableLayout();
		$testID = $this->_getParam('test-id');
		try {
			if (empty($testID)) {
				throw new Exception($this->view->translate('test_details_no_test_specified_error'));
			}
			$modelTests = new Model_Tests();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				throw new Exception($this->view->translate('test_details_wrong_test_error'));
			}
			if (!$this->checkTestACL($test)) {
				throw new Exception($this->view->translate('test_details_illegal_test_error'));
			}

			if (!empty($test->document_link)) {
				header('Location: ' . $test->document_link);
				exit;
			}

			GN_Debug::debug('create-test: start');

			//utwórz informacje w bazie danych
			$documentTitle = $test->document_title;
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			//zacznij requesty do google
			$ep = new GN_Pararell();
			$client = new GN_GClient($this->user);
			$meta = array();
			$meta['document-title'] = $documentTitle;
			$meta['main-folder-title'] = $this->view->translate('misc_main_google_folder');



			//pobierz główny folder
			$ep->request($client, 'create-document (creating folder: ' . $meta['main-folder-title'] . ')', function($c) use (&$meta) {
				$client = $c['client'];
				$mainFolder = $client->getFolderByTitle($meta['main-folder-title'], false);
				if ($mainFolder) {
					$mainFolderUri = $mainFolder->content->src;
					if (strpos($mainFolderUri, '?') !== false) {
						$mainFolderUri = substr($mainFolderUri, 0, strpos($mainFolderUri, '?'));
					}
					$meta['main-folder'] = $mainFolder;
					$meta['main-folder-uri'] = $mainFolderUri;
				}
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			//utwórz podfolder
			$ep->request($client, 'create-document (creating folder: ' . $meta['document-title'] . ')', function($c) use (&$meta) {
				$client = $c['client'];
				$folder = $client->createFolder($meta['document-title']);
				$folderUri = $folder->content->src;
				if (strpos($folderUri, '?') !== false) {
					$folderUri = substr($folderUri, 0, strpos($folderUri, '?'));
				}
				$meta['folder'] = $folder;
				$meta['folder-uri'] = $folderUri;
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			//utwórz dokument
			$ep->request($client, 'create-document (creating document: ' . $meta['document-title'] . ')', function($c) use (&$meta) {
				$client = $c['client'];
				$documentTitle = $meta['document-title'];
				$documentID = $client->createDocument($documentTitle);
				$meta['document-id'] = $documentID;
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			if (!$ep->work()) {
				throw new Exception($this->view->translate('test_details_create_folder_error'));
			}
			$ep->reset();



			//utwórz główny folder jeśli nie istniał
			if (empty($meta['main-folder-uri'])) {
				$ep->request($client, 'create-document (creating folder: ' . $meta['main-folder-title'] . ')', function($c) use (&$meta) {
					$client = $c['client'];
					$mainFolder = $client->createFolder($meta['main-folder-title']);
					$mainFolderUri = $mainFolder->content->src;
					if (strpos($mainFolderUri, '?') !== false) {
						$mainFolderUri = substr($mainFolderUri, 0, strpos($mainFolderUri, '?'));
					}
					$meta['main-folder'] = $mainFolder;
					$meta['main-folder-uri'] = $mainFolderUri;
				}, null, function($c) {
					GN_Debug::debug($c['exceptions']);
				});
			}

			//pobierz dokument
			$ep->request($client, 'create-document (retrieving document: ' . $meta['document-title'] . ', ' . $meta['document-id'] . ')', function($c) use (&$meta) {
				$client = $c['client'];
				$document = $client->getDocumentEntry($meta['document-id']);
				$meta['document'] = $document;
			});

			if (!$ep->work()) {
				throw new Exception($this->view->translate('test_details_create_folder_error'));
			}
			$ep->reset();



			GN_Debug::debug('create-document (working with database)');
			$document = $meta['document'];
			$test->folder_uri = $meta['folder-uri'];
			$test->document_id = $meta['document-id'];
			$test->document_link = $client->getDocumentLink($document);
			$test->save();



			//dodaj dokument do podfolderu
			$ep->request($client, 'create-document (adding ' . $meta['document-title'] . ' document to ' . $meta['document-title'] . ' folder)', function($c) use (&$meta) {
				$client = $c['client'];
				$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
				$docsClient->insertEntry($meta['document'], $meta['folder-uri']);
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			//dodaj podfolder do folderu
			$ep->request($client, 'create-document (adding ' . $meta['document-title'] . ' folder to root folder)', function($c) use (&$meta) {
				$client = $c['client'];
				$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
				$docsClient->insertEntry($meta['folder'], $meta['main-folder-uri']);
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			//usuń dokument z roota
			$ep->request($client, 'create-document (removing ' . $meta['document-title'] . ' folder from root folder)', function($c) use (&$meta) {
				$client = $c['client'];
				$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
				$uri = Zend_Gdata_Docs::DOCUMENTS_FOLDER_FEED_URI . '/folder%3Aroot/' . $meta['document-id'];
				$requestData = $docsClient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
				$docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			//usuń podfolder z roota
			$ep->request($client, 'create-test (removing ' . $meta['document-title'] . ' folder from root folder)', function($c) use (&$meta) {
				$client = $c['client'];
				$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
				//usuń folder z root folderu
				$tmp = explode('/', $meta['folder-uri']);
				$uri = Zend_Gdata_Docs::DOCUMENTS_FOLDER_FEED_URI . '/folder%3Aroot/' . end($tmp);
				$requestData = $docsClient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
				$docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);
			}, null, function($c) {
				GN_Debug::debug($c['exceptions']);
			});

			if (!$ep->work()) {
				throw new Exception($this->view->translate('test_details_move_folder_error'));
			}
			$ep->reset();

			$db->commit();
			GN_Debug::debug('create-test: end with success');
			header('Location: ' . $test->document_link);
			exit;
		} catch (Exception $e) {
			GN_Debug::debug('create-test: end with errors');
			$this->addError($e->getMessage());
			if (!empty($test)) {
				$this->_redirectExit('details', 'test', array('test-id' => $test->id));
			} else {
				$this->_redirectExit('index', 'dashboard');
			}
		}
	}



	public function deleteAction() {
		$testID = $this->_getParam('test-id');
		try {
			GN_Debug::debug('delete-test: start');
			if (empty($testID)) {
				throw new Exception($this->view->translate('delete_test_no_test_specified_error'));
			}
			$modelTests = new Model_Tests();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				throw new Exception($this->view->translate('delete_test_wrong_test_error'));
			}
			if (!$this->checkTestACL($test)) {
				throw new Exception($this->view->translate('delete_test_illegal_test_error'));
			}

			//usuń dokumenty
			if ($this->_hasParam('delete-documents')) {
				$client = new GN_GClient($this->user);
				$docsClient = new Zend_Gdata_Docs($client->getHttpClient());

				$participants = $test->getAssociatedParticipants();
				$meta = array();
				$ep = new GN_Pararell();
				foreach ($participants as $participant) {
					$meta[$participant->id] = array();
				}

				//pobierz dokumenty uczestników
				foreach ($participants as $participant) {
					$ep->request($client, 'delete-test (fetching document of ' . $participant->participant_email . ')', function($c) use (&$meta, $test, $participant) {
						$client = $c['client'];
						$documentID = $participant->document_id;
						if (!empty($documentID)) {
							$document = $client->getDocumentEntry($documentID);
							$meta[$participant->id]['document'] = $document;
						}
					});
				}
				//pobierz główny dokument
				$ep->request($client, 'delete-test (fetching main document)', function($c) use (&$meta, $test) {
					$client = $c['client'];
					$documentID = $test->document_id;
					if (!empty($documentID)) {
						$document = $client->getDocumentEntry($documentID);
						$meta['document'] = $document;
					}
				});
				if (!$ep->work()) {
					throw new Exception($this->view->translate('delete_test_document_fetch_error'));
				}
				$ep->reset();

				//usuń dokumenty uczestników
				foreach ($participants as $participant) {
					$ep->request($client, 'delete-test (removing document of ' . $participant->participant_email . ')', function($c) use (&$meta, $participant) {
						$document = $meta[$participant->id]['document'];
						if (!empty($document))  {
							$document->delete();
						}
					});
				}
				//usuń główny dokument
				$ep->request($client, 'delete-test (removing main document)', function($c) use (&$meta) {
					//usuń główny dokument
					if (isset($meta['document'])) {
						$document = $meta['document'];
						if (!empty($document)) {
							$document->delete();
						}
					}
				});
				//usuń folder
				$ep->request($client, 'delete-test (removing test folder)', function($c) use (&$meta, $test) {
					$client = $c['client'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					if (!empty($test->folder_uri)) {
						$uri = $test->folder_uri;
						//zamień adres - api v2 używa do usuwania folderów adresu api _dokumentów_ a nie folderów (api v3)
						$uri = Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI  . '/' . end(explode('/', $uri));
						$requestData = $docsClient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
						$docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);
					}
				});
				if (!$ep->work()) {
					throw new Exception($this->view->translate('delete_test_document_removal_error'));
				}
				$ep->reset();

			}
			$test->delete();

			$this->addSuccess($this->view->translate('delete_test_success'));
			GN_Debug::debug('delete-test: end with success');
		} catch (Exception $e) {
			$this->addError($e->getMessage());
			GN_Debug::debug('delete-test: end with errors');
			$this->_redirectExit('index', 'dashboard');
		}
		$this->_redirectExit('index', 'dashboard');
	}

	public function dispatchAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$modelTests = new Model_Tests();
		$role = Zend_Controller_Front::getInstance()->getPlugin('GN_Plugin_Acl')->getRoleName();
		$time = microtime(true);

		switch ($role) {
			case Model_Users::ROLE_TEACHER:
				$select = $modelTests->selectManager($this->user->id);
				break;
			case Model_Users::ROLE_ADMINISTRATOR:
			case Model_Users::ROLE_SUPER_ADMINISTRATOR:
				$select = $modelTests->selectDomain($this->user->id);
				break;
			case Model_Users::ROLE_CLI:
				$select = $modelTests->select(true);
				break;
			default:
				die($this->view->translate('misc_bad_role_error'));
		}

		foreach ($modelTests->fetchAll($select) as $test) {
			$now = self::getNow();

			if (!$test->scheduled_date_opening) {
				$start = null;
			} else {
				$start = $this->view->getHelper('misc')->convertDateTime($test->scheduled_date_opening);
			}
			if (!$test->scheduled_date_closing) {
				$end = null;
			} else {
				$end = $this->view->getHelper('misc')->convertDateTime($test->scheduled_date_closing);
			}
			if (!empty($test->error)) {
				#$this->addInfo($this->view->translate('dispatch_test_has_errors', $test->id));
				continue;
			}
			switch ($test->status) {
				case Model_Tests::STATUS_UNOPENED:
					if ($start === null) {
						#$this->addInfo($this->view->translate('dispatch_test_opening_not_defined_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
					} elseif ($now < $start) {
						#$this->addInfo($this->view->translate('dispatch_test_opening_too_early_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
					} elseif ($now >= $start) {
						$this->addInfo($this->view->translate('dispatch_test_opening_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
						$this->doOpenTest($test->id);
					}
					break;
				case Model_Tests::STATUS_OPENED:
					if ($end === null) {
						#$this->addInfo($this->view->translate('dispatch_test_closing_not_defined_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
					} elseif ($now < $end) {
						#$this->addInfo($this->view->translate('dispatch_test_closing_too_early_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
					} else {
						$this->addInfo($this->view->translate('dispatch_test_closing_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
						$this->doCloseTest($test->id);
					}
					break;
				case Model_Tests::STATUS_FINISHED:
					#$this->addInfo($this->view->translate('dispatch_test_finished_info', $test->id, $test->document_title, date($this->view->translate('misc_date_format_second'), $this->view->getHelper('misc')->convertDateTime($test->date_created))));
					break;
			}
			@ob_flush();
			@flush();
		}

		$time2 = microtime(true);
		$this->addInfo($this->view->translate('dispatch_test_timing_info', $time2 - $time));
		die;
	}



	private function doOpenTest($testID) {
		GN_Session::stop();
		try {
			GN_Debug::debug('open-test: start');
			$modelTests = new Model_Tests();
			$modelUsers = new Model_Users();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				throw new Exception($this->view->translate('open_test_wrong_test_error'));
			}
			if ($test->status != Model_Tests::STATUS_UNOPENED) {
				throw new Exception($this->view->translate('open_test_already_opened_error', $test->document_title));
			}
			if (!$this->checkTestACL($test)) {
				throw new Exception($this->view->translate('open_test_illegal_test_error'));
			}

			$user = $modelUsers->find($test->user_id)->current();
			$client = new GN_GClient($user);
			$docsClient = new Zend_Gdata_Docs($client->getHttpClient());

			if (!$this->checkTrial($user)) {
				if (isset($_SERVER['REMOTE_ADDR'])) {
					$this->_redirectExit('index', 'payment');
				} else {
					throw new Exception($this->view->translate('open_test_trial_expired_error'));
				}
			}

			//dla każdej osoby bioracej udzial w tescie:
			$participants = $test->getAssociatedParticipants();
			$ep = new GN_Pararell();
			$meta = array();
			$_this = $this;
			foreach ($participants as $participant) {
				$meta[$participant->id] = array();
			}

			if (empty($meta)) {
				throw new Exception($this->view->translate('open_test_no_participants_error'));
			}

			//utwórz kopię dokumentu
			foreach ($participants as $participant) {
				if (!empty($participant->document_id)) {
					$documentCopyTitle = sprintf('%s (%s)', $test->document_title, $participant->participant_email);
					$meta[$participant->id]['document-copy-title'] = $documentCopyTitle;
					$shouldWork = false;
				} else {
					$shouldWork = true;
					$ep->request($client, 'open-test (duplicating document for ' . $participant->participant_email . ')', function($c) use (&$meta, $test, $participant) {
						$client = $c['client'];
						$documentCopyTitle = sprintf('%s (%s)', $test->document_title, $participant->participant_email);
						$meta[$participant->id]['document-copy-title'] = $documentCopyTitle;
						$documentCopy = new Zend_Gdata_Docs_DocumentListEntry();
						$documentCopy->setCategory(array(new Zend_Gdata_App_Extension_Category("http://schemas.google.com/docs/2007#document", "http://schemas.google.com/g/2005#kind")));
						$documentCopy->setTitle(new Zend_Gdata_App_Extension_Title($documentCopyTitle), null);
						$documentCopy->setId(new Zend_Gdata_App_Extension_Id($test->document_id));
						$client->insertEntry($documentCopy, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI, 'Zend_Gdata_Docs_DocumentListEntry');
					}, null, function($c) use (&$test) {
						foreach ($c['exceptions'] as $e) {
							$user = $test->getUser();
							if (method_exists($e, 'getResponse')) {
								if (strpos(strtolower($e->getResponse()->getBody()), 'token revoked') !== false) {
									if ($user->token != null) {
										$user->token = null;
										$user->save();
									} else {
										$d = $user->getDomain();
										$d->oauth_token = null;
										$d->save();
									}
								}
							}
						}
					});
				}
			}
			if ($shouldWork) {
				if (!$ep->work()) {
					throw new Exception($this->view->translate('open_test_document_copy_error'));
				}
			}
			$ep->reset();

			//wyciągnij info o dokumencie
			foreach ($participants as $participant) {
				$ep->request($client, 'open-test (fetching document for ' . $participant->participant_email . ')', function($c) use (&$meta, $test, $participant, &$_this) {
					$client = $c['client'];
					$query = new Zend_Gdata_Docs_Query();
					$query->setTitle($meta[$participant->id]['document-copy-title']);
					$query->setTitleExact('true');
					$documentCopy = $client->getEntry($query, 'Zend_Gdata_docs_DocumentListEntry');
					$documentCopyID = $client::getDocumentID($documentCopy);
					$meta[$participant->id]['document-copy'] = $documentCopy;
					$meta[$participant->id]['document-copy-id'] = $documentCopyID;

					$participant->document_id = $documentCopyID;
					$participant->document_link = $client->getDocumentLink($documentCopy);
					$participant->share_flags = Model_Participants::FLAG_READ | Model_Participants::FLAG_WRITE;
					$participant->save();
				});
			}
			if (!$ep->work()) {
				throw new Exception($this->view->translate('open_test_document_fetch_error'));
			}
			$ep->reset();
			//header('Content-Type: text/plain; charset=utf-8');

			foreach ($participants as $participant) {
				//nadaj uprawnienia człowiekowi do odczytu i do zapisu
				if ($participant->participant_email != $user->email) {
					$ep->request($client, 'open-test (changing permission for ' . $participant->participant_email . ')', function($c) use ($meta, $participant) {
						$client = $c['client'];
						$documentCopy = $meta[$participant->id]['document-copy'];
						$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
						$docsClient->updateAcl($documentCopy, $participant->participant_email, 'writer');
					}, null,
					function($c) use (&$_this) {
						throw new Exception($_this->view->translate('open_test_document_permission_change_error'));
					});
				}
				//dopisz do nowego folderu
				$ep->request($client, 'open-test (adding document for ' . $participant->participant_email . ' to test folder)', function($c) use (&$meta, $test, $participant) {
					$client = $c['client'];
					$documentCopy = $meta[$participant->id]['document-copy'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					$docsClient->insertEntry($documentCopy, $test->folder_uri);
				});
				//usuń z root folderu
				$ep->request($client, 'open-test (removing document for ' . $participant->participant_email . ' from root folder)', function($c) use (&$meta, $participant) {
					$client = $c['client'];
					$documentCopy = $meta[$participant->id]['document-copy'];
					$documentCopyID = $meta[$participant->id]['document-copy-id'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					$uri = Zend_Gdata_Docs::DOCUMENTS_FOLDER_FEED_URI . '/folder%3Aroot/' . $documentCopyID;
					$requestData = $docsClient->prepareRequest('DELETE', $uri, array('If-Match' => '*'));
					$docsClient->performHttpRequest($requestData['method'], $requestData['url'], $requestData['headers'], '', $requestData['contentType'], null);
				});
			}
			if (!$ep->work()) {
				throw new Exception($this->view->translate('open_test_move_folder_error'));
			}
			$ep->reset();

			//"anyone with link can edit"
			foreach ($participants as $participant) {
				$domain = end(explode('@', $participant->participant_email));
				if (Essays_Misc::isSpecialDomain($domain)) {
					continue;
				}
				$modelDomains = new Model_Domains();
				$select = $modelDomains
					->select(true)
					->where('domain_name = ?', $domain);
				$domainRow = $modelDomains->fetchRow($select);
				if ($domainRow !== null) {
					if (!empty($domainRow->oauth_token) or $domainRow->marketplace) {
						continue;
					}
				}

				$ep->request($client, 'open-test (changing share permission for writers)', function($c) use ($meta, $participant) {
					$client = $c['client'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					$headers = array(
						'GData-Version' => '3.0',
						'X-Upload-Content-Length' => '0',
						'If-Match' => '*',
					);
					$xml = '<?xml version="1.0" encoding="UTF-8"?' . '>' .
						'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:docs="http://schemas.google.com/docs/2007">' .
						'<docs:writersCanInvite value="false" />' .
						'<docs:writersCanShare value="false" />' .
						'</entry>';
					$uri = "https://docs.google.com/feeds/default/private/full/" . $meta[$participant->id]['document-copy-id'];
					$docsClient->performHttpRequest('PUT', $uri, $headers, $xml, 'application/atom+xml');
				}, null,
				function($c) use (&$_this) {
					throw new Exception($_this->view->translate('open_test_document_permission_change_error'));
				});

				$ep->request($client, 'open-test (changing permission for ' . $participant->participant_email . ')', function($c) use ($meta, $participant) {
					$client = $c['client'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					$headers = array(
						'GData-Version' => '3.0',
						'X-Upload-Content-Length' => '0',
					);
					$xml = '<?xml version="1.0" encoding="UTF-8"?' . '>' .
						'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl="http://schemas.google.com/acl/2007">' .
						'<category term="http://schemas.google.com/acl/2007#accessRule" scheme="http://schemas.google.com/g/2005#kind"/>' .
						'<gAcl:withKey key="batman"><gAcl:role value="writer"/></gAcl:withKey>' .
						'<gAcl:scope type="default"/>' .
						'</entry>';
					$uri = "https://docs.google.com/feeds/default/private/full/" . $meta[$participant->id]['document-copy-id'] . '/acl';
					$docsClient->performHttpRequest('POST', $uri, $headers, $xml, 'application/atom+xml');
				}, null,
				function($c) use (&$_this) {
					throw new Exception($_this->view->translate('open_test_document_permission_change_error'));
				});
			}
			if (!$ep->work()) {
				throw new Exception($this->view->translate('open_test_move_folder_error'));
			}
			$ep->reset();

			$test->date_opened = date('Y-m-d H:i:s', self::getNow());
			$test->status = Model_Tests::STATUS_OPENED;
			$test->save();

			$this->increaseTrialCount($user);

			if (!$test->mail_sent) {
				$oldUser = $this->user;
				$this->user = $test->getUser();
				$this->initObserver()->observe('openTest', true, array( 'testId' => $test->id));
				$this->user = $oldUser;
				$test->mail_sent = 0;
				$test->save();
			}

			GN_Debug::debug('open-test: end with success');
			$this->addSuccess($this->view->translate('open_test_success', $test->document_title));
			return true;
		} catch (Exception $e) {
			if (!empty($test)) {
				if (!$test->mail_sent) {
					$oldUser = $this->user;
					$this->user = $test->getUser();
					$this->initObserver()->observe('openTest', false, array( 'testId' => $test->id, 'error' => $e->getMessage()));
					$this->user = $oldUser;
					$test->mail_sent = 1;
					$test->save();
				}
				$test->error = $e->getMessage();
				$test->save();
			}
			GN_Debug::debug('open-test: end with errors');
			$this->addError($e->getMessage());
			return false;
		}
	}

	public function openAction() {
		$testID = $this->_getParam('test-id');
		if (empty($testID)) {
			$this->addError($this->view->translate('open_test_no_test_specified_error'));
			$this->_redirectExit('index', 'dashboard');
		}
		if ($this->doOpenTest($testID)) {
		}
		$this->_redirectExit('details', 'test', array('test-id' => $testID));
	}









	private function doCloseTest($testID) {
		GN_Session::stop();
		try {
			GN_Debug::debug('close-test: start');
			$modelTests = new Model_Tests();
			$modelUsers = new Model_Users();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				throw new Exception($this->view->translate('close_test_wrong_test_error'));
			}
			if ($test->status != Model_Tests::STATUS_OPENED) {
				throw new Exception($this->view->translate('close_test_not_opened_error', $test->document_title));
			}
			if (!$this->checkTestACL($test)) {
				throw new Exception($this->view->translate('close_test_illegal_test_error'));
			}

			$user = $modelUsers->find($test->user_id)->current();
			$client = new GN_GClient($user);
			$docsClient = new Zend_Gdata_Docs($client->getHttpClient());

			//nadaj uprawnienia człowiekowi tylko do odczytu
			$participants = $test->getAssociatedParticipants();
			$ep = new GN_Pararell();
			$meta = array();
			foreach ($participants as $participant) {
				$meta[$participant->id] = array();
			}

			foreach ($participants as $participant) {
				$ep->request($client, 'close-test (fetching document for ' . $participant->participant_email . ')', function($c) use (&$meta, $participant) {
					$client = $c['client'];
					$documentCopy = $client->getDocumentEntry($participant->document_id);
					$meta[$participant->id]['document-copy'] = $documentCopy;
					$meta[$participant->id]['document-copy-id'] = $participant->document_id;
				}, null, function($c) use (&$test) {
					foreach ($c['exceptions'] as $e) {
						$user = $test->getUser();
						if (method_exists($e, 'getResponse')) {
							if (strpos(strtolower($e->getResponse()->getBody()), 'token revoked') !== false) {
								if ($user->token != null) {
									$user->token = null;
									$user->save();
								} else {
									$d = $user->getDomain();
									$d->oauth_token = null;
									$d->save();
								}
							}
						}
					}
				});
			}
			if (!$ep->work()) {
				throw new Exception($this->view->translate('close_test_document_fetch_error'));
			}
			$ep->reset();

			foreach ($participants as $participant) {
				if (empty($meta[$participant->id]['document-copy'])) {
					continue;
				}
				$ep->request($client, 'close-test (changing permissions of document for ' . $participant->participant_email . ')', function($c) use (&$meta, $participant, $user) {
					$client = $c['client'];
					$documentCopy = $meta[$participant->id]['document-copy'];

					$publishedRaw = $documentCopy->published->text;
					$updatedRaw = $documentCopy->updated->text;

					//skonwertowanie strtotime spowoduje automatyczna konwersje z UTC na strefe serwera. probably.
					$publishedUnix = strtotime($documentCopy->published->text);
					$updatedUnix = strtotime($documentCopy->updated->text);

					try {
						$xml = $client->getLastResponse()->getBody();
						$xml = preg_replace('/<(\/)?[a-z]*:/', '<\1', $xml); //usuń namespace, inaczej simplexml nie przeczyta
						$xml = simplexml_load_string($xml);
						$author = (string)$xml->entry[0]->lastModifiedBy->email;
					} catch (Exception $e) {
						$author = null;
					}

					if ($author == $participant->participant_email) {
						if ($updatedUnix - $publishedUnix > 50) {
							$participant->date_modified = date('Y-m-d H:i:s', $updatedUnix);
						}
					}
					if ($participant->participant_email != $user->email) {
						$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
						$docsClient->updateAcl($documentCopy, $participant->participant_email, 'reader', 'commenter');
					}

					$participant->share_flags = Model_Participants::FLAG_READ;
					$participant->save();
				});
			}
			if (!$ep->work()) {
				throw new Exception($this->view->translate('close_test_document_permission_change_error'));
			}
			$ep->reset();

			//"anyone with link can comment"
			foreach ($participants as $participant) {
				$domain = end(explode('@', $participant->participant_email));
				if (Essays_Misc::isSpecialDomain($domain)) {
					continue;
				}
				$modelDomains = new Model_Domains();
				$select = $modelDomains
					->select(true)
					->where('domain_name = ?', $domain);
				$domainRow = $modelDomains->fetchRow($select);
				if ($domainRow !== null) {
					if (!empty($domainRow->oauth_token) or $domainRow->marketplace) {
						continue;
					}
				}

				if (empty($meta[$participant->id]['document-copy'])) {
					continue;
				}
				$ep->request($client, 'close-test (changing permissions of document for ' . $participant->participant_email . ')', function($c) use (&$meta, $participant, $user) {
					$client = $c['client'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					$headers = array(
						'GData-Version' => '3.0',
						'X-Upload-Content-Length' => '0',
					);
					$xml = '';
					$uri = "https://docs.google.com/feeds/default/private/full/" . $meta[$participant->id]['document-copy-id'] . '/acl/default';
					$docsClient->performHttpRequest('DELETE', $uri, $headers, $xml, 'application/atom+xml');
				}, null,
				function($c) use (&$_this) {
					throw new Exception($_this->view->translate('open_test_document_permission_change_error'));
				});

				$ep->request($client, 'close-test (changing permissions of document for ' . $participant->participant_email . ')', function($c) use (&$meta, $participant, $user) {
					$client = $c['client'];
					$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
					$headers = array(
						'GData-Version' => '3.0',
						'X-Upload-Content-Length' => '0',
					);
					$xml = '<?xml version="1.0" encoding="UTF-8"?' . '>' .
						'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl="http://schemas.google.com/acl/2007">' .
						'<category term="http://schemas.google.com/acl/2007#accessRule" scheme="http://schemas.google.com/g/2005#kind"/>' .
						'<gAcl:withKey key="batman"><gAcl:role value="reader"/><gAcl:additionalRole value="commenter"/></gAcl:withKey>' .
						'<gAcl:scope type="default"/>' .
						'</entry>';
					$uri = "https://docs.google.com/feeds/default/private/full/" . $meta[$participant->id]['document-copy-id'] . '/acl';
					$docsClient->performHttpRequest('POST', $uri, $headers, $xml, 'application/atom+xml');
				}, null,
				function($c) use (&$_this) {
					throw new Exception($_this->view->translate('open_test_document_permission_change_error'));
				});
			}
			if (!$ep->work()) {
				throw new Exception($this->view->translate('close_test_document_permission_change_error'));
			}
			$ep->reset();

			$test->date_closed = date('Y-m-d H:i:s', self::getNow());
			$test->status = Model_Tests::STATUS_FINISHED;
			$test->save();

			if (!$test->mail_sent) {
				$oldUser = $this->user;
				$this->user = $test->getUser();
				$this->initObserver()->observe('closeTest', true, array( 'testId' => $test->id));
				$this->user = $oldUser;
				$test->mail_sent = 0;
				$test->save();
			}

			GN_Debug::debug('close-test: end with success');
			$this->addSuccess($this->view->translate('close_test_success', $test->document_title));
			return true;
		} catch (Exception $e) {
			if (!empty($test)) {
				if (!$test->mail_sent) {
					$oldUser = $this->user;
					$this->user = $test->getUser();
					$this->initObserver()->observe('closeTest', false, array( 'testId' => $test->id, 'error' => $e->getMessage()));
					$this->user = $oldUser;
					$test->mail_sent = 1;
					$test->save();
				}
				$test->error = $e->getMessage();
				$test->save();
			}
			GN_Debug::debug('close-test: end with errors');
			$this->addError($e->getMessage());
			return false;
		}
	}

	public function closeAction() {
		$testID = $this->_getParam('test-id');
		if (empty($testID)) {
			$this->addError($this->view->translate('close_test_no_test_specified_error'));
			$this->_redirectExit('index', 'dashboard');
		}
		if ($this->doCloseTest($testID)) {
		}
		$this->_redirectExit('details', 'test', array('test-id' => $testID));
	}

	public function ajaxToggleStarAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();

		try {
			$testID = $this->_getParam('test-id');
			if (empty($testID)) {
				throw new Exception($this->view->translate('test_ajax_toggle_star_no_test_specified_error'));
			}
			$modelTests = new Model_Tests();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				throw new Exception($this->view->translate('test_ajax_toggle_star_wrong_test_error'));
			}
			$test->doStar($this->user->id, !$test->isStarred($this->user->id));
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
		}

		echo json_encode($json);
		die();
	}




	public function ajaxListAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();

		$modelTests = new Model_Tests();

		switch ($this->user->role) {
			case Model_Users::ROLE_TEACHER:
				$select = $modelTests->selectManager($this->user->id);
				break;
			case Model_Users::ROLE_ADMINISTRATOR:
			case Model_Users::ROLE_SUPER_ADMINISTRATOR:
				$select = $modelTests->selectDomain($this->user->domain_id);
				break;
			case Model_Users::ROLE_CLI:
				$select = $modelTests->select(true);
				break;
			default:
				die($this->view->translate('misc_bad_role_error'));
		}

		$select->order(array('status DESC', 'id DESC'));
		if ($this->_getParam('status')) {
			$select->where('status = ?', $this->_getParam('status'));
		}

		if ($this->_hasParam('starred')) {
			$select->setIntegrityCheck(false);
			$select->join('stars', 'test_id = tests.id', array('star'));
			$select->where('star = ?', intval($this->_getParam('starred')));
		}

		if ($this->_getParam('search-title')) {
			$select->where('STRPOS(LOWER(document_title), LOWER(?)) > 0', $this->_getParam('search-title'));
		}

		if ($this->_getParam('search-group')) {
			$select->where('group_name = ?', $this->_getParam('search-group'));
		}

		if ($this->_getParam('search-status-id')) {
			$select->where('status = ?', intval($this->_getParam('search-status-id')));
		}

		foreach ($modelTests->fetchAll($select) as $test) {
			$row = array();
			$row['id'] = $test->id;
			$row['document-title'] = $test->document_title;
			$row['status'] = $test->status;
			$json []= $row;
		}

		echo json_encode($json);
		die();
	}

	public function ajaxEditDatesAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();

		try {
			$testID = $this->_getParam('test-id');
			if (empty($testID)) {
				throw new Exception($this->view->translate('edit_test_no_test_specified_error'));
			}
			$modelTests = new Model_Tests();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				throw new Exception($this->view->translate('edit_test_wrong_test_error'));
			}
			if (!$this->checkTestACL($test)) {
				throw new Exception($this->view->translate('edit_test_illegal_test_error'));
			}
			if ($this->_hasParam('datetime-opening')) {
				@list($date, $time) = explode(' ', $this->_getParam('datetime-opening'));
				if (!$this->isValidDate($date)) {
					throw new Exception($this->view->translate('test_details_invalid_date_specified_error'));
				}
				if (!$this->isValidTime($time)) {
					throw new Exception($this->view->translate('test_details_invalid_time_specified_error'));
				}
				$test->scheduled_date_opening = "$date $time";
			} else {
				$test->scheduled_date_opening = null;
			}

			if ($this->_hasParam('datetime-closing')) {
				@list($date, $time) = explode(' ', $this->_getParam('datetime-closing'));
				if (!$this->isValidDate($date)) {
					throw new Exception($this->view->translate('test_details_invalid_date_specified_error'));
				}
				if (!$this->isValidTime($time)) {
					throw new Exception($this->view->translate('test_details_invalid_time_specified_error'));
				}
				$test->scheduled_date_closing = "$date $time";
			} else {
				$test->scheduled_date_closing = null;
			}

			if (!empty($test->scheduled_date_opening) and !empty($test->scheduled_date_closing)) {
				if (strtotime($test->scheduled_date_opening) >= strtotime($test->scheduled_date_closing)) {
					throw new Exception($this->view->translate('test_details_later_opening_than_closing_error'));
				}
			}

			$test->mail_sent = 0;
			$test->save();
			//$this->addSuccess($this->view->translate('edit_test_success'));
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
		}

		echo json_encode($json);
		die();
	}


	public function participantAddAction() {
		$testID = $this->_getParam('test-id');
		if (empty($testID)) {
			$this->addError($this->view->translate('test_participation_edit_no_test_specified_error'));
		} else {
			$modelTests = new Model_Tests();
			$modelParticipants = new Model_Participants();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				$this->addError($this->view->translate('test_participation_edit_wrong_test_error'));
			} else {
				$email = $this->_getParam('e-mail');
				$emails = preg_split('/[\s;,]+/', $email);
				foreach ($emails as $email) {
					$email = trim($email);
					if (strpos($email, ':') !== false) {
						list($key, $email) = explode(':', $email);
						if ($key == 'group') {
							$test->group_name = $email;
							$test->save();
						}
						continue;
					}
					if (empty($email)) {
						//$this->addError($this->view->translate('test_participation_edit_no_mail_specified_error'));
						continue;
					}
					if (strpos($email, '@') === false) {
						$email .= '@' . $this->user->getDomain()->domain_name;
					}
					if (!$this->isValidEmail($email)) {
						/*trigger_error($email);
						$this->addError($this->view->translate('test_participation_edit_invalid_mail_specified_error'));*/
						continue;
					} elseif ($modelParticipants->fetchRow($modelParticipants->select()->where('test_id = ?', $test->id)->where('participant_email = ?', $email)) !== null) {
						//$this->addError($this->view->translate('test_participation_edit_duplicate_mail_specified_error'));
						continue;
					} else {
						//wrzuć e-mail do bazy
						$participantRow = $modelParticipants->createRow();
						$participantData = array(
							'test_id' => $test->id,
							'participant_email' => $email,
							'participant_group_id' => null,
							'document_id' => null,
							'document_link' => null,
							'share_flags' => 0,
						);
						if (!empty($folk['name'])) {
							$participantData['participant_name'] = $folk['name'];
						}
						$participantRow->setFromArray($participantData);
						$participantRow->save();
						$this->addSuccess($this->view->translate('test_participation_add_success'));
					}
				}
			}
			$test->mail_sent = 0;
			$test->save();
			$this->_redirectExit('details', 'test', array('test-id' => $this->_getParam('test-id')));
		}
		$this->_redirectExit('index', 'dashboard');
	}

	public function participantRemoveAction() {
		$testID = $this->_getParam('test-id');
		if (empty($testID)) {
			$this->addError($this->view->translate('test_participation_edit_no_test_specified_error'));
		} else {
			$modelTests = new Model_Tests();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				$this->addError($this->view->translate('test_participation_edit_wrong_test_error'));
			} else {
				$email = $this->_getParam('e-mail');
				if (empty($email)) {
					$this->addError($this->view->translate('test_participation_edit_no_mail_specified_error'));
				} else {
					//usuń e-mail z bazy
					$found = false;
					$participants = $test->getAssociatedParticipants();
					foreach ($participants as $participant) {
						if ($participant->participant_email == $email) {
							$participant->delete();
							$found = true;
						}
					}
					if (!$found) {
						$this->addError($this->view->translate('test_participation_edit_wrong_mail_error'));
					} else {
						$this->addSuccess($this->view->translate('test_participation_removal_success'));
					}
				}
			}
			$test->mail_sent = 0;
			$test->save();
			$this->_redirectExit('details', 'test', array('test-id' => $this->_getParam('test-id')));
		}
		$this->_redirectExit('index', 'dashboard');
	}


	private function checkTestACL($test) {
		$role = Zend_Controller_Front::getInstance()->getPlugin('GN_Plugin_Acl')->getRoleName();
		switch ($role) {
			case Model_Users::ROLE_CLI:
				return true;
			case Model_Users::ROLE_TEACHER:
				return $this->user->id == $test->user_id;
			case Model_Users::ROLE_ADMINISTRATOR:
			case Model_Users::ROLE_SUPER_ADMINISTRATOR:
				$modelUsers = new Model_Users();
				$user = $modelUsers->find($test->user_id)->current();
				return $this->user->domain_id == $user->domain_id;
		}
		return false;
	}


	public function detailsAction() {
		$this->view->isWizard = ($this->_hasParam('wizard') and intval($this->_getParam('wizard')) and (!isset($_COOKIE['wizard-closed']) or !$_COOKIE['wizard-closed']));
		$testID = $this->_getParam('test-id');
		if (empty($testID)) {
			$this->addError($this->view->translate('test_details_no_test_specified_error'));
		} else {
			$modelTests = new Model_Tests();
			$modelUsers = new Model_Users();
			$test = $modelTests->find($testID)->current();
			if (!$test) {
				$this->addError($this->view->translate('test_details_wrong_test_error'));
			} elseif (!$this->checkTestACL($test)) {
				$this->addError($this->view->translate('test_details_illegal_test_error'));
			} else {
				$user = $modelUsers->find($test->user_id)->current();

				$participants = $test->getAssociatedParticipants();
				$this->view->test = $test;
				$this->view->participants = $participants;

				$this->view->justCreated = $this->_hasParam('created') and intval($this->_hasParam('created'));

				/*if ((!empty($test->document_id)) and (empty($test->document_link) or empty($test->document_embed_link))) {
					$client = new GN_GClient($user);
					try {
						$document = $client->getDocumentEntry($test->document_id);
						if (!empty($document)) {
							$test->document_link = $client->getDocumentLink($document);
							$test->document_embed_link = $this->getEmbedLink($test->document_link);
							$test->save();
						}
					} catch (Exception $e) {
						$this->addError($this->view->translate('test_details_document_retrieval_error'));
					}
				}

				foreach ($participants as $participant) {
					if ((!empty($participant->document_id)) and (empty($participant->document_link) or empty($participant->document_embed_link))) {
						$client = new GN_GClient($user);
						try {
							$document = $client->getDocumentEntry($participant->document_id);
							if (!empty($document)) {
								$participant->document_link = $client->getDocumentLink($document);
								$participant->document_embed_link = $this->getEmbedLink($participant->document_link);
								$participant->save();
							}
						} catch (Exception $e) {
							$this->addError($this->view->translate('test_details_participant_document_retrieval_error'));
						}
					}
				}*/

				return;
			}
		}
		$this->_redirectExit('index', 'dashboard');
	}



	public function ajaxCheckTitleAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();

		try {
			$model = new Model_Tests();
			$testID = $this->_getParam('test-id');
			if (empty($testID)) {
				throw new Exception($this->view->translate('test_check_title_no_test_specified_error'));
			}
			$test = $model->find($testID)->current();
			$documentID = $test->document_id;
			try {
				$client = new GN_GClient($this->user);
				$docsClient = new Zend_Gdata_Docs($client->getHttpClient());
				$document = $client->getDocumentEntry($documentID);
				if (!empty($document) and !empty($document->title)) {
					$documentTitle = $document->title->text;
				} else {
					$documentTitle = $test->document_title;
				}

				$json['title'] = $documentTitle;
				$test->document_title = $documentTitle;
				$test->save();
			} catch (Exception $e) {
				throw new Exception($this->view->translate('test_check_title_wrong_document_specified_error'));
			}
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
		}

		echo json_encode($json);
		die();
	}


	public function ajaxCheckParticipantModDateAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();

		$modelParticipants = new Model_Participants();
		$participantID = $this->_getParam('participant-id');
		try {
			if (empty($participantID)) {
				throw new Exception($this->view->translate('test_check_participant_mod_date_no_participant_specified_error'));
			}

			$participant = $modelParticipants->find($participantID)->current();
			if (empty($participant)) {
				throw new Exception($this->view->translate('test_check_participant_mod_date_wrong_participant_specified_error'));
			}

			$modelTests = new Model_Tests();
			$test = $modelTests->find($participant->test_id)->current();

			$json['date_modified'] = $this->view->translate('test_details_participant_no_modified_text');
			if (!empty($participant->date_modified)) {
				$date = $this->view->getHelper('misc')->convertDateTime($participant->date_modified);
				$json['date_modified'] = date($this->view->translate('misc_date_format_second'), $date);
			}
			if ($test->status != Model_Tests::STATUS_UNOPENED) {
				$user = $this->user;
				$client = new GN_GClient($user);
				try {
					$document = $client->getDocumentEntry($participant->document_id);
				} catch (Exception $e) {
					$document = null;
				}

				if (empty($document)) {
					throw new Exception($this->view->translate('test_check_participant_mod_date_no_document_error'));
				}

				$publishedRaw = $document->published->text;
				$updatedRaw = $document->updated->text;

				//skonwertowanie strtotime spowoduje automatyczna konwersje z UTC na strefe serwera. probably.
				$publishedUnix = strtotime($document->published->text);
				$updatedUnix = strtotime($document->updated->text);

				try {
					$xml = $client->getHttpClient()->getLastResponse()->getBody();
					$xml = preg_replace('/<(\/)?[a-z]*:/', '<\1', $xml); //usuń namespace, inaczej simplexml nie przeczyta
					$xml = simplexml_load_string($xml);
					$author = (string)$xml->entry[0]->lastModifiedBy->email;
				} catch (Exception $e) {
					$author = null;
				}

				#if ($author == $participant->participant_email) {
					if ($updatedUnix - $publishedUnix > 10) {
						$participant->date_modified = date('Y-m-d H:i:s', $updatedUnix);
						$participant->save();
						$json['date_published_raw'] = $publishedRaw;
						$json['date_updated_raw'] = $updatedRaw;
						$json['d0'] = date('Y-m-d H:i:s', $updatedUnix);
						$date = GN_Tools::switchTimeZone($participant->date_modified, GN_Tools::TZ_SERVER_TO_USER);
						$json['d1'] = $date;
						$date = $this->view->getHelper('misc')->convertDateTime($date);
						$json['d2'] = $date;
						$json['date_modified'] = date($this->view->translate('misc_date_format_second'), $date);
					}
				#}
			}

		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
		}

		echo json_encode($json);
		die();
	}




	public function ajaxRetrieveFolksAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();
		$json['folks'] = array();
		$filter = strtolower($this->_getParam('filter'));
		$cacheManager = Zend_Registry::get('cache');

		$detailLevel = intval($this->_getParam('detail-level'));

		//dodaj wszystkich userów z domeny lub grupy
		$groupID = $this->_getParam('group-id');
		$cacheKey = 'users_' . str_replace(str_split('+='), '_', base64_encode($this->user->getDomain()->domain_name) . '_' . base64_encode($groupID)) . '_' . $detailLevel;
		if (empty($groupID)) {
			$folks = $cacheManager->load($cacheKey);
			if ($folks === false) {
				$folks = $this->retrieveFolks(null, $detailLevel);
				$cacheManager->save($folks, $cacheKey);
			}
			$json['folks'] = $folks;
			//$json['message'] = $this->view->translate('test_details_no_group_specified_error');
		} else {
			$json['id'] = $groupID;
			$json['folks'] = $this->retrieveFolks($groupID, $detailLevel);
		}

		//dodaj wszystkich userów ever w testach stworzonych przez zalogowanego usera
		if ($this->_hasParam('search-for-users')) {
			$modelTests = new Model_Tests();
			$select = $modelTests->select(true);
			$select->where('user_id = ?', $this->user->id);
			$tests = $modelTests->fetchAll($select);
			foreach ($tests as $test) {
				/*if ($test->id == $this->_getParam('test-id')) {
					continue;
				}*/
				$participants = $test->getAssociatedParticipants();
				foreach ($participants as $participant) {
					$json['folks'] []= array('e-mail' => $participant->participant_email);
				}
			}
		}

		if (!empty($filter)) {
			$json['folks'] = array_filter($json['folks'], function($folk) use ($filter) { return preg_match('/(^|\W)' . $filter . '.*@/i', strtolower($folk['e-mail'])); });
		}
		//odfiltruj unikalnych użytkowników
		$tmp = array();
		foreach ($json['folks'] as $folk) {
			$ok = true;
			for ($j = 0; $j < count($tmp); $j ++) {
				if ($tmp[$j]['e-mail'] == $folk['e-mail']) {
					$ok = false;
					break;
				}
			}
			if ($ok) {
				$tmp []= $folk;
			}
		}
		$json['folks'] = $tmp;



		//dodaj nazwy testów
		if ($this->_hasParam('search-for-tests')) {
			$json['tests'] = array();
			$modelTests = new Model_tests();
			$select = $modelTests->select(true);
			$select->where('user_id = ?', $this->user->id);
			$tests = $modelTests->fetchAll($select);
			foreach ($tests as $test) {
				$participants = $test->getAssociatedParticipants();
				$subfolks = array();
				foreach ($participants as $participant) {
					$subfolks []= array('e-mail' => $participant->participant_email);
				}
				$test = array(
					'id' => $test->id,
					'name' => $test->document_title,
					'folks' => $subfolks
				);
				if (empty($filter) or preg_match('/(^|\W)' . $filter . '/i', strtolower($test['name']))) {
					$json['tests'] []= $test;
				}
			}
		}

		//dodaj nazwy grup
		if ($this->_hasParam('search-for-groups')) {
			$json['groups'] = array();
			if (!$this->user->getDomain()->isSpecial() and ($this->user->getDomain()->marketplace or !empty($this->user->getDomain()->oauth_token))) {
				try {
					$cacheKey = 'groups_' . str_replace(str_split('+='), '_', base64_encode($this->user->getDomain()->domain_name));
					$groups = $cacheManager->load($cacheKey);
					if ($groups === false) {
						$googleGroups = array();
						$clientDomain = new GN_GClient($this->user, GN_GClient::MODE_DOMAIN);
						$googleGroups = $clientDomain->retrieveAllGroups();
						$groups = array();
						foreach ($googleGroups->getEntry() as $group) {
							$email = $group->property[0]->value;
							if (strpos($email, '@') !== false) {
								$id = current(explode('@', $email));
							} else {
								$id = $email;
								$email .= '@' . $clientDomain->getDomain();
							}
							$name = $group->property[1]->value;
							$group = array(
								'id' => $id,
								'e-mail' => $email,
								'name' => $name,
							);
							$groups []= $group;
						}
						$cacheManager->save($groups, $cacheKey);
					}

					foreach ($groups as $group) {
						if (empty($filter) or preg_match('/(^|\W)' . $filter . '/i', strtolower($group['name'])) or preg_match('/(^|\W)' . $filter . '.*@/i', strtolower($group['e-mail']))) {
							$cacheKey = 'groups_' . str_replace(str_split('+='), '_', base64_encode($this->user->getDomain()->domain_name)) . '_' . $group['id'];
							$subfolks = $cacheManager->load($cacheKey);
							if ($subfolks === false) {
								$subfolks = $this->retrieveFolks($group['id'], 0);
								$cacheManager->save($subfolks, $cacheKey);
							}
							$group['folks'] = $subfolks;
							$json['groups'] []= $group;
						}
					}
				} catch (Exception $e) {
				}
			}
		}

		echo json_encode($json);
		die();
	}


	public function ajaxScoreAction() {
		GN_Session::stop();
		$this->_helper->layout()->disableLayout();
		header('Content-Type: application/json; charset=utf-8');
		$json = array();

		$participationID = $this->_getParam('participation-id');
		try {
			if (empty($participationID)) {
				throw new Exception($this->view->translate('score_test_no_participation_specified_error'));
			}
			$model = new Model_Participants();
			$participation = $model->find($participationID)->current();
			if (!$participation) {
				throw new Exception($this->view->translate('score_test_wrong_participation_error'));
			}
			if (!$this->checkTestACL($participation->getTest())) {
				throw new Exception($this->view->translate('score_test_illegal_test_error'));
			}

			$score = $this->_getParam('score-value');
			#if (empty($score)) {
			#	throw new Exception($this->view->translate('score_test_no_score_specified_error'));
			#}
			$participation->score = $score;
			$participation->save();
		} catch (Exception $e) {
			$json['message'] = $e->getMessage();
		}

		echo json_encode($json);
		die();
	}


}
