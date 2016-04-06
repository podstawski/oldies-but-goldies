<?php
require_once 'CompetenceController.php';

class ImportController extends CompetenceController
{
	public function ajaxIsActiveAction() {
		$id = $this->_getParam('import-op-id');
		$json = array();
		$json['import-op-id'] = $id;

		$modelImportOps = new Model_ImportOperations();
		$importOp = $modelImportOps->find($id)->current();
		if ($importOp === null) {
			$json['active'] = -1;
		} else {
			$json['active'] = empty($importOp->ended) ? 1 : 0;
		}

		$this->view->layout()->disableLayout();
		header('Content-Type: application/json');
		echo json_encode($json);
		die();
	}

	public function groupsAction()
	{
		$client = new GN_GClient($this->user);

		if (!empty($_POST)) {
			$modelImportOps = new Model_ImportOperations();
			$importOp = $modelImportOps->createRow();
			$importOp->type = Model_ImportOperations::TYPE_GROUP_IMPORT;
			$importOp->started = date('Y-m-d H:i:s');
			$importOp->save();

			//odłącz przeglądarkę
			$urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('url');
			$url = $urlHelper->url(array('controller' => 'import', 'action' => 'ajax-is-active', 'import-op-id' => $importOp->id), null, true);
			if (getenv('APPLICATION_ENV') != 'development') {
				header('Location: ' . $url);
			}
			usleep(2000);
			echo '<script type="text/javascript">';
			echo 'function reload() {';
			echo 'window.location.href="' . $url . '";';
			echo '}';
			if (getenv('APPLICATION_ENV') != 'development') {
				echo 'setTimeout(reload,300);';
			}
			echo '</script>';
			for ($i = 0; $i < 4096; $i++) {
				echo ' ' . PHP_EOL;
			}
			flush();
			ob_end_flush();
			ini_set('max_execution_time', 0);
			session_write_close();

			$this->detachBrowser();
            $this->view->groupsImported = 0;
            $this->view->usersImported = 0;
            $request = $this->getRequest();
            $googleGroups = $client->retrieveAllGroups();
            $dbGroups = new Model_Groups();
            $dbUsers = new Model_Users();
            $dbUserGroups = new Model_UserGroups();

            $groupsToImport = array();

            foreach ($googleGroups as $group) {
                $groupId = $group->property[0]->value;
                if ($request->getParam(base64_encode($groupId)) !== null) {
                    $groupsToImport [] = $groupId;
                }
            }

            for ($i = 0; $i < count($groupsToImport); $i++) {
                ++$this->view->groupsImported;
                $groupId = $groupsToImport[$i];

                unset($groupName);
                foreach ($googleGroups as $group) {
                    if ($groupId == $group->property[0]->value) {
                        $groupName = $group->property[1]->value;
                        $groupDescription = $group->property[4]->value;
                        break;
                    }
                }
                //jeśli nie ma na liście wszystkich grup - pomiń ją
                if (!isset($groupName)) {
                    $this->addError($this->view->translate('Subgroup "%s" found, but it is inaccessible', $groupId));
                    continue;
                }

                $dbGroupData = array
                (
                    'email' => $groupId,
                    'name' => $groupName,
                    'description' => $groupDescription
                );
                //wkładamy grupę do bazy, pamiętamy jej id z AR...
                $dbGroup = $dbGroups->fetchRow(array('email = ?' => $groupId));
                if (!$dbGroup) {
                    $dbGroup = $dbGroups->createRow();
                }
                $dbGroup->setFromArray($dbGroupData);
                $dbGroup->domain_id = $this->user->domain_id;
                $dbGroup->save();

                $usersToImport = array();

                //patrzymy, jakich userów chcemy z tej bazy...
                $googleGroupMembers = $client->retrieveAllMembers($groupId);
                foreach ($googleGroupMembers as $member) {
                    $memberType = $member->property[0]->value;
                    $memberId = $member->property[1]->value;
                    if ($memberId == '*') {
                        $this->addInfo('Warning: importing wildcard groups (*) is not supported.');
                        continue;
                    }
                    if (strtolower($memberType) == 'group') {
                        if (!in_array($memberId, $groupsToImport)) {
                            array_push($groupsToImport, $memberId);
                        }
                    } else if (strtolower($memberType) == 'user') {
                        if (!in_array($memberId, $usersToImport)) {
                            array_push($usersToImport, $memberId);
                        }
                    }
                }

                //usuwamy userów z bazy
                $dbUserGroups->delete($dbUserGroups->getAdapter()->quoteInto('group_id = ?', $dbGroup->id));

                //wkładamy userów do bazy
                foreach ($usersToImport as $userId) {
                    $userLogin = current(explode('@', $userId));
                    $user = $client->retrieveUser($userLogin);
                    if (!$user) {
                        $nick = $client->retrieveNickName($userLogin);
                        $userLogin = $nick->login->username;
                        $user = $client->retrieveUser($userLogin);
                    }
                    if (!$user) {
                        $this->addError($this->view->translate('Couldn\'t import user "%s" (?)', $userLogin));
                        continue;
                    }
                    ++$this->view->usersImported;
                    $isOwner = $client->isOwner($userId, $groupId);

                    $userName = $user->getName()->getGivenName() . ' ' . $user->getName()->getFamilyName();

                    $dbUser = $dbUsers->fetchRow(array('email = ?' => $userId));
                    if ($dbUser == null) {
                        $dbUser = $dbUsers->createRow();
                        $dbUser->setFromArray(array(
                            'email' => $userId,
                            'name' => $userName,
                            'role' => $isOwner ? Model_Users::ROLE_TEACHER : Model_Users::ROLE_STUDENT,
                            'domain_id' => $this->user->domain_id
                        ));
                        $dbUser->save();
                    } else {
                        if ($isOwner and ($dbUser->role == Model_Users::ROLE_STUDENT)) {
                            $dbUser->role = Model_Users::ROLE_TEACHER;
                            $dbUser->save();
                        }
                    }

                    //teraz dodaj relację grupa-user
                    $dbUserGroups->insert(array('user_id' => $dbUser->id, 'group_id' => $dbGroup->id, 'owner' => $isOwner ? 'true' : 'false'));
                }
            }

			$importOp->ended = date('Y-m-d H:i:s');
			$importOp->save();
        }
        else {
            try {
                $this->view->groups = $client->retrieveAllGroups();
            }
            catch (Exception $e) {
                $this->addError($this->view->translate('An error occured while fetching group list. Are you domain administrator?'));
                return;
            }
        }
    }


    /**
     * @param string $haystack
     * @param string $needle
     * @return string
     */
    protected function strstr($haystack, $needle)
    {
        if (!is_array($needle)) {
            return strstr($haystack, $needle);
        }

        foreach ($needle AS $n) {
            if ($s = $this->strstr($haystack, $n)) {
                return $s;
            }
        }
    }

    /**
     * @param string $str
     * @return string
     */
    protected function strtolower($str)
    {
        return mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
    }

    /**
     * @param array $headers
     * @return array
     */
    protected function obtainHeaderIds($headers)
    {
        $result = array();
        foreach ($headers AS $id => $title) {
            $title = $this->strtolower($title);

            if ($this->strstr($title, array('pyta', 'quest'))
                && !isset($result['question'])
            ) $result['question'] = $id;

            if ($this->strstr($title, array('kompet', 'competen'))
                && !isset($result['competence'])
            ) $result['competence'] = $id;

            if ($this->strstr($title, array('anal'))
                && !isset($result['competence_description'])
            ) $result['competence_description'] = $id;

            if ($this->strstr($title, array('min'))
                && !isset($result['min'])
            ) $result['min'] = $id;

            if ($this->strstr($title, array('max'))
                && !isset($result['max'])
            ) $result['max'] = $id;

            if ($this->strstr($title, array('odpow', 'skil'))
                && !isset($result['skill'])
            ) $result['skill'] = $id;

            if ($this->strstr($title, array('stand'))
                && !isset($result['standard'])
            ) $result['standard'] = $id;

            if ($this->strstr($title, array('opis', 'descript'))
                && !isset($result['description'])
            ) $result['description'] = $id;

            if ($this->strstr($title, array('suwa', 'default', 'start'))
                && !isset($result['default_value'])
            ) $result['default_value'] = $id;

            if ($this->strstr($title, array('url', 'link'))
                && !isset($result['url'])
            ) $result['url'] = $id;

        }

        return $result;
    }


    private function returnError($txt)
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->addError($this->view->translate($txt));
            return;
        }
        die ($txt);
    }


    public function competenciesAction()
    {
        if ($this->_hasParam('ajax')) {
            $this->view->layout()->disableLayout();
            $this->view->ajax = true;
        }

        $modelUsers = new Model_Users;
        $modelDomains = new Model_Domains;
        $modelProjects = new Model_Projects;
        $modelCompetencies = new Model_Competencies;
        $modelQuestions = new Model_Questions;
        $modelSkills = new Model_Skills;
        $modelStandards = new Model_Standards;
        $modelCompetenceDescriptions = new Model_CompetenceDescriptions;

        if ($this->user) {
            $client = new GN_GClient($this->user);
			$client->getHttpClient()->setToken($this->user->getAccessToken());
        } elseif ($this->_hasParam('domain_name')) {
            $name = $this->_getParam('domain_name');
            if (strpos($name, '@') !== false) {
                $user = $modelUsers->fetchUser($name);
                if ($user === null) return $this->returnError('Invalid user name');
                $client = new GN_GClient($user);
				$client->getHttpClient()->setToken($user->getAccessToken());
            }
            else {
                $domain = $modelDomains->fetchDomain($name);
                if ($domain === null) return $this->returnError('Invalid domain name');
                $user = $modelUsers->fetchRow(array(
                    'domain_id = ?' => $domain->id,
                    'role = ?' => $modelUsers::ROLE_SUPER_ADMINISTRATOR
                ));
                if ($user === null) return $this->returnError('Could not find domains super administrator');
                $client = new GN_GClient($user);
				$client->getHttpClient()->setToken($user->getAccessToken());
            }
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $this->addError($this->view->translate('No token nor domain specified'));
                return;
            } else {
                echo 'No token nor domain specified' . PHP_EOL;
                echo 'List of available domains:' . PHP_EOL;
                foreach ($modelDomains->fetchAll() as $domain) {
                    echo "\t- " . $domain->domain_name . PHP_EOL;
                }
                die();
            }
        }

        try {
            //pobierz tytuł spreadsheetu
            $spreadsheetFeed = $client->getSpreadsheetsList();
        }
        catch (Exception $e) {
            return $this->returnError('Invalid token!');
        }

        /**
         * @var Zend_Gdata_Spreadsheets_SpreadsheetEntry $spreadsheet
         */

        //pobierz ID spreadsheetów
        if (!$this->_hasParam('sp_id')) {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $this->view->spreadsheetFeed = $spreadsheetFeed;
                return;
            } else {
                echo $this->view->translate('Spreadsheet list:' . PHP_EOL);
                foreach ($spreadsheetFeed->getEntry() as $spreadsheet) {
                    printf("\t- %s\t[%s]" . PHP_EOL, $client->getDocumentID($spreadsheet), (string)$spreadsheet->getTitle());
                }
                die();
            }
        }
        $spreadsheetId = $this->_getParam('sp_id');

        foreach ($spreadsheetFeed->getEntry() as $spreadsheet) {
            if ($client->getDocumentID($spreadsheet) == $spreadsheetId) {
                $spreadsheetTitle = (string)$spreadsheet->getTitle();
            }
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

		try {
			//utwórz nowy projekt
			$project = $modelProjects->find_one_by_file($spreadsheetId);
			if ($project === null) {
				$projectData = array
				(
					'file' => $spreadsheetId,
					'name' => $spreadsheetTitle,
					'domain_id' => $client->getUser()->getDomain()->id
				);
				if ($this->user) {
					$projectData['user_id'] = $this->user->id;
				}
				$project = $modelProjects->createAndSave($projectData);
			}

			$worksheets = $client->getWorksheetsListData($spreadsheetId);
			$worksheets = $this->obtainHeaderIds($worksheets);

			if (!isset($worksheets['competence'])) {
				$db->rollBack();
				return $this->returnError('No competence worksheet!');

			}

			foreach ($worksheets AS $competenceName => $worksheetId) {
				$data = $client->getWorksheetData($spreadsheetId, $worksheetId);

				if (empty($data)) {
					unset($worksheets[$competenceName]);
					continue;
				}

				$header = $data[0];
				foreach ($header AS $k => $v) $header[$k] = $k;

				$header = $this->obtainHeaderIds($header);

				if ($competenceName == 'standard' && isset($competence)) {
					foreach (array_keys($data[0]) AS $k) {
						if (in_array($k, array_keys($competence))) $header[$competence[$k]] = $k;
					}
				}

				$worksheets[$competenceName] = array();
				foreach ($data AS $rek) {
					$r = array();
					foreach ($header AS $field => $key) $r[$field] = $rek[$key];

					$worksheets[$competenceName][] = $r;
				}

				if ($competenceName == 'competence') {
					$competence = array();
					foreach ($worksheets[$competenceName] AS $rek) {
						$competence[$this->strtolower(str_replace(' ', '', $rek['competence']))] = 'competence:' . $rek['competence'];
					}
				}

			}

			$md5skill_id = md5($spreadsheetId . serialize($worksheets['skill']));

			if (is_array($worksheets['competence']) && is_array($worksheets['question'])) {
				foreach ($worksheets['competence'] AS $i => $competence) {
					$worksheets['competence'][$i]['md5skill_id'] = $md5skill_id;
					foreach ($worksheets['question'] AS $j => $question) {
						if ($question['competence'] == $competence['competence']) {
							unset($question['competence']);
							unset($worksheets['question'][$j]);
							if (!isset($worksheets['competence'][$i]['questions']))
								$worksheets['competence'][$i]['questions'] = array();
							$worksheets['competence'][$i]['questions'][] = $question;
						}
					}
					$worksheets['competence'][$i]['md5id'] = md5($md5skill_id . serialize($worksheets['competence'][$i]['questions']));
				}
			}

			if (count($worksheets['question'])) {
				foreach ($worksheets['question'] AS $question) {
					echo 'Unknown competence: ' . $question['competence'] . '(' . $question['question'] . ')' . $eol;
				}
			}

			if (is_array($worksheets['skill'])) {
				foreach (array_keys($worksheets['skill']) AS $i) {
					$worksheets['skill'][$i]['md5group'] = $md5skill_id;
					$worksheets['skill'][$i]['name'] = $worksheets['skill'][$i]['skill'];
					unset($worksheets['skill'][$i]['skill']);
				}
			}

			if (is_array($worksheets['competence'])) {
				$competence_ids = array();
				foreach ($worksheets['competence'] AS $c) {
					$competence = $modelCompetencies->find_one_by_md5id($c['md5id']);
					$c['name'] = $c['competence'];
					unset($c['competence']);
					$questions = $c['questions'];
					unset($c['questions']);
					$new_competence = false;
					if (!$competence) {
						$competence = $modelCompetencies->createAndSave($c);
						$new_competence = true;
					} else {
						$competence->setFromArray($c);
						$competence->save();
					}
					$competence_id = $competence->id;
					$competence_name_table['competence:' . $competence->name] = $competence_id;
					$competence_ids[] = $competence_id;

					if ($new_competence) {
						$question_ids = array();
						foreach ($questions AS $question) {
							$question['competence_id'] = $competence_id;
							$modelQuestions->createAndSave($question);
						}
					}

				}

				foreach ($competence_ids AS $competence_id) {
					$project->add_competence($competence_id);
				}

				$project->delete_competencies_except($competence_ids);
			}

			if (is_array($worksheets['skill'])) {
				$skills = $modelSkills->find_by_md5group($md5skill_id);
				if (count($skills) != count($worksheets['skill'])) {
					$modelSkills->delete("md5group='$md5skill_id'");
					foreach ($worksheets['skill'] AS $skill) {
						$modelSkills->createAndSave($skill);
					}
				}

			}

			if (is_array($worksheets['standard'])) {
				foreach ($worksheets['standard'] AS $s) {
					$standard = $modelStandards->find_one_by_name($s['standard']);
					if (!$standard) $standard = $modelStandards->createAndSave(array('name' => $s['standard']));
					if (!$standard) continue;
					foreach ($s AS $key => $value) {
						if ($competence_id = $competence_name_table[$key]) {
							$competence_standard = $standard->add_competence($competence_id, $value);
						}
					}
				}
			}

			if (is_array($worksheets['competence_description'])) {
				foreach ($worksheets['competence_description'] AS $i => $competence_description) {
					$key = 'competence:' . $competence_description['competence'];
					$worksheets['competence_description'][$i]['competence_id'] = $competence_description['competence_id'] = $competence_name_table[$key];
					unset($worksheets['competence_description'][$i]['competence']);
					if (!$competence_description['competence_id'])
						continue;
					$obj = $modelCompetenceDescriptions->find_on_min_max_competence($competence_description['min'], $competence_description['max'], $competence_description['competence_id']);
					if (!$obj)
						$modelCompetenceDescriptions->delete_descriptions_for_competence($competence_description['competence_id']);
				}

				foreach ($worksheets['competence_description'] AS $competence_description) {
					if (!$competence_description['competence_id'])
						continue;

					$description = $modelCompetenceDescriptions->find_on_min_max_competence($competence_description['min'], $competence_description['max'], $competence_description['competence_id']);
					if (!$description) {
						$modelCompetenceDescriptions->createAndSave($competence_description);
					} elseif ($description->description != $competence_description['description']) {
						$description->description = $competence_description['description'];
						$description->save();
					}

				}

			}

			$db->commit();
			$this->addSuccess($this->view->translate('Import successful'));
			if (!isset($_SERVER['REMOTE_ADDR'])) {
				die ('Import successful');
			}
		} catch (Exception $e) {
			$db->rollback();
			return $this->returnError($this->view->translate('Error: %s', $e->getMessage()));
		}
	}
}
