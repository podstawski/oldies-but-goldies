<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

require_once 'CompetenceController.php';

class ExamsController extends CompetenceController {
	public function indexAction() {
		$modelExams = new Model_Exams();
		if ($this->user->role == Model_Users::ROLE_TEACHER) {
			$select = $modelExams->selectManager($this->user->id, $this->user->domain_id);
		} else {
			$select = $modelExams->selectDomain($this->user->domain_id);
		}
		$select->setIntegrityCheck(false);
		$select->joinLeft('users', 'exams.user_id = users.id', array('users.email as user_email'));

		$this->view->name = null;
		if ($this->_hasParam('name')) {
			$this->view->name = trim($this->_getParam('name'));
		}
		if (!empty($this->view->name)) {
			foreach (explode(' ', $this->view->name) as $word) {
				$select->where('STRPOS(lower(name), lower(?)) > 0', $word);
			}
		}

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
		$paginator->setCurrentPageNumber($this->_getParam('pageID', 1));
		$this->view->paginator = $paginator;
		
		
	}

	public function editAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
		} else {
			//pobierz badanie z bazy
			$modelExams = new Model_Exams();
			$exam = $modelExams->find($this->_getParam('exam-id'))->current();
			if ($exam === null) {
				$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			} else {
				if (($this->user->role == Model_Users::ROLE_TEACHER) and ($this->user->id != $exam->getFirstManager()->user_id)) {
					$this->addError($this->view->translate('Trying to perform invalid request'));
				} else {
					$table = new Model_Standards();
					$select = $table
						->select(true)
						->distinct('standards.id')
						->join('competence_standards', 'competence_standards.standard_id = standards.id', array())
						->join('exam_competencies', 'exam_competencies.competence_id = competence_standards.competence_id', array())
						->where('exam_competencies.exam_id = ?', $exam->id)
						;
					$this->view->standards = $table->fetchAll($select);

					if ($this->_hasParam('description') and $this->_hasParam('name') and $this->_hasParam('standard')) {
						$exam->description = $this->_getParam('description');
						$exam->name = $this->_getParam('name');
						$exam->standard_id = intval($this->_getParam('standard'));
						$exam->save();
						$this->addSuccess($this->view->translate('Exam updated successfully'));
						$this->_redirectExit('index', 'exams');
					}
					$this->view->exam = $exam;
					return;
				}
			}
		}
		$this->_redirectExit('index', 'exams');
	}

	private function getWorksheetId($worksheetEntry) {
		return current(array_reverse(explode('/', $worksheetEntry->id->text)));
	}

	public function exportAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
		} else {
			//pobierz badanie z bazy
			$modelExams = new Model_Exams();
			$exam = $modelExams->find($this->_getParam('exam-id'))->current();
			if ($exam === null) {
				$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			} else {
				if (!$this->_hasParam('spreadsheet-title')) {
					$this->addError($this->view->translate('No spreadsheet title specified'));
				} else {
					$client = new GN_GClient($this->user);
					$client->getHttpClient()->setToken($this->user->getAccessToken(true));
					$httpClient = $client->getHttpClient();
					$spreadsheetTitle = $this->_getParam('spreadsheet-title');
					try {
						$spreadsheetId = $client->getSpreadsheetId($spreadsheetTitle, true);
					} catch (Exception $e) {
						$this->addError($this->view->translate('exam_export_google_drive_error'));
						$this->_redirectExit('details', 'exams', array('exam-id' => $exam->id));
					}
					$docsClient = new Zend_Gdata_Docs($httpClient);
					$spreadsheetClient = new Zend_Gdata_Spreadsheets($httpClient);
					$worksheetTitle = $this->view->translate('Exam results');
					$spreadsheet = $client->getSpreadsheetEntry($spreadsheetId);

					$modelUsers = new Model_Users();
					$modelGroups = new Model_Groups();
					$modelStandards = new Model_Standards();
					$standard = $modelStandards->find($exam->standard_id)->current();

					$header = array (
						'name' => $this->view->translate('Student'),
						'group' => $this->view->translate('Group'),
					);
					foreach ($exam->getAssociatedCompetencies() as $i => $competence) {
						$header['competence-' . $i] = $competence->name;
					}
					//$header['standard'] = $this->view->translate('Standard (%s)', $standard->name);

					$rows = array();
					foreach ($exam->getAllParticipants() as $participant) {
						$user = $modelUsers->find($participant->user_id)->current();
						$group = $modelGroups->find($participant->group_id)->current();
						$row = array ();
						$row['name'] = $user->name;
						$row['group'] = $group->name;
						foreach ($exam->getAssociatedCompetencies() as $i => $competence) {
							$sum = 0;
							$count = 0;
							foreach ($competence->getAssociatedQuestions() as $question) {
								$value = $question->getAnswerValue($exam->id, $user->id);
								if ($participant->date_finished == 0) {
									$value = 0;
								}
								$sum += $value;
								$count ++;
							}
							$mean = $sum / max(1, $count);
							$row['competence-' . $i] = sprintf('=%.4f', $mean);
						}
						//$row['standard'] = sprintf('=%.4f', $competence->getStandardValue($exam->standard_id));
						$rows []= $row;
					}

					$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
					$query->setSpreadsheetKey($spreadsheetId);
					$feed = $spreadsheetClient->getWorksheetFeed($query);
					$worksheetsToDelete = array();
					foreach ($feed as $entry) {
						if (($entry->title == 'Sheet 1') or ($entry->title == $worksheetTitle)) {
							$worksheetsToDelete []= $this->getWorksheetId($entry);
						}
					}

					try {
						$worksheetId = GN_Gapps::createWorksheet($httpClient, $spreadsheetId, $worksheetTitle, count($rows) + 1, count($header));
					} catch (Exception $e) {
						$this->addError($this->view->translate('An error occured while creating worksheet'));
						$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
					}

					//wpisz klucze nagłówka
					try {
						$x = 1;
						foreach ($header as $key => $d) {
							$spreadsheetClient->updateCell(1, $x, $key, $spreadsheetId, $worksheetId);
							$x ++;
						}
					} catch (Exception $e) {
						$this->addError($this->view->translate('An error occured while setting up header'));
						$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
					}

					//wpisz wiersze
					foreach ($rows as $row) {
						try {
							$spreadsheetClient->insertRow($row, $spreadsheetId, $worksheetId);
						} catch (Exception $e) {
							$this->addError($this->view->translate('An error occured while adding row for student "%s"', $user->name));
							$this->addError(print_r(array('message' => $e->getMessage(), 'y' => $y, 'x' => $x, 'd' => $d), true));
							$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
						}
					}

					//wpisz faktyczny nagłówek. dzięki temu złożoność = O(m+2n), m = liczba wierszy, n = liczba kolumn
					//alternatywa: O(mn) w wypadku vanilla updateCell lub błędy przy próbie
					try {
						$x = 1;
						foreach ($header as $key => $d) {
							$spreadsheetClient->updateCell(1, $x, $d, $spreadsheetId, $worksheetId);
							$x ++;
						}
					} catch (Exception $e) {
						$this->addError($this->view->translate('An error occured while setting up header'));
						$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
					}

					//usuń zbędne worksheety
					//połącz się na nowo, żeby się mogły ułożyć nowe query
					try {
						$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
						$query->setSpreadsheetKey($spreadsheetId);
						$feed = $spreadsheetClient->getWorksheetFeed($query);
						foreach ($feed as $entry) {
							$worksheetId = $this->getWorksheetId($entry);
							$remove = false;
							foreach ($worksheetsToDelete as $worksheetId2) {
								if ($worksheetId == $worksheetId2) {
									$remove = true;
									break;
								}
							}
							if ($remove) {
								$entry->delete();
							}
						}
					} catch (Exception $e) {
						$this->addError($this->view->translate('Error while removing redundant worksheets'));
						$errors = true;
					}

					$link = $spreadsheet->getLink('alternate')->href;

					$this->addSuccess(
						$this->view->translate('exam_export_success_prefix') .
						'<a href="' . $link . '">' .
						$this->view->translate('exam_export_success_link') .
						'</a>' .
						$this->view->translate('exam_export_success_suffix')
					);
				}
			}
			$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
		}
		$this->_redirectExit('index', 'exams');
	}

	public function detailsAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}

		//pobierz badanie z bazy
		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}

		$this->view->finishedUsers = array();
		$this->view->availableUsers = array();

		$modelUsers = new Model_Users();
		$groups = $exam->getAssociatedGroups();
		foreach ($groups as $group) {
			$select = $modelUsers
				->select(true)
				->setIntegrityCheck(false)
				//->distinct('users.id')
				->join('exam_participants', 'exam_participants.user_id = users.id', array('date_finished'))
				->where('exam_participants.exam_id = ?', $exam->id)
				->where('exam_participants.group_id = ?', $group->id)
				->join('user_groups', '(user_groups.user_id = users.id AND user_groups.group_id = exam_participants.group_id)', array('owner'))
				;
			$availableUsers = $modelUsers->fetchAll($select);

			$finishedUsers = array();
			foreach ($availableUsers as $user) {
				$finishedUsers []= $user;
			}
			$finishedUsers = array_filter($finishedUsers, function($user) { return !empty($user->date_finished); });

			$this->view->finishedUsers[$group->id] = $finishedUsers;
			$this->view->availableUsers[$group->id] = $availableUsers;
		}
		$this->view->exam = $exam;
	}

	public function resendAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}
		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}
		$this->view->exam = $exam;

		if ($this->_hasParam('group')) {
			//potwórz relacje między badaniem a użytkownikami.
			$mailAddresses = array
			(
				'groups' => array(),
				'managers' => array(),
				'participants' => array()
			);

			$modelExamParticipants = new Model_ExamParticipants();
			$modelExamManagers = new Model_ExamManagers();
			$modelGroups = new Model_Groups();

			//zawsze dodaj do managerów tego, kto tworzy
			/*if ($modelExamManagers->fetchAll($modelExamManagers->select(true)->where('user_id = ?', $this->user->id)->where('exam_id = ?', $exam->id))->count() == 0) {
				$modelExamManagers->insert(array('user_id' => $this->user->id, 'exam_id' => $exam->id));
				$mailAddresses['managers'] []= $user->email;
			}*/

			//dla każdej grupy
			foreach ($this->_getParam('group') as $groupId) {
				$group = $modelGroups->fetchRow($modelGroups->select()->where('id = ?', $groupId));
				foreach ($group->getAssociatedUsers() as $user) {
					if ($user->role == Model_Users::ROLE_STUDENT) {
						if ($modelExamParticipants->fetchAll($modelExamParticipants->select(true)->where('user_id = ?', $user->id)->where('exam_id = ?', $exam->id)->where('group_id = ?', $group->id))->count() == 0) {
							$modelExamParticipants->insert(array('user_id' => $user->id, 'exam_id' => $exam->id, 'group_id' => $group->id));
							$mailAddresses['participants'] []= $user->email;
						}
					} else {
						//nie dodawaj tego, kto tworzy dwukrotnie do managerów
						if ($this->user->id == $user->id) {
							continue;
						}
						if ($modelExamManagers->fetchAll($modelExamManagers->select(true)->where('user_id = ?', $user->id)->where('exam_id = ?', $exam->id))->count() == 0) {
							$modelExamManagers->insert(array('user_id' => $user->id, 'exam_id' => $exam->id));
							$mailAddresses['managers'] []= $user->email;
						}
					}
				}
				$mailAddresses['groups'] []= $group->email;
			}
			if ($this->observer && $this->_getParam('send-mail')) {
				$data = array
				(
					'mailList' => $mailAddresses,
					'examId' => $exam->id,
					'examName' => $exam->name,
				);
				$this->observer->observe('resendExam', true, $data);
			}

			/*if (empty($mailAddresses['participants'])) {
				$this->addSuccess($this->view->translate('Exam wasn\'t sent due to lack of users that meet the criteria'));
			} else {
				$this->addSuccess($this->view->translate('Exam resent successfully (%d users affected)', count($mailAddresses['participants'])));
			}*/

			if (empty($mailAddresses['participants']) and empty($mailAddresses['managers'])) {
				$this->addError($this->view->translate('No users satisfy the criteria'));
			} else {
				if (!empty($mailAddresses['managers'])) {
					$this->addSuccess($this->view->translate('Exam sent successfully to %d teachers', count($mailAddresses['managers'])));
				}
				if (!empty($mailAddresses['participants'])) {
					$this->addSuccess($this->view->translate('Exam sent successfully to %d students', count($mailAddresses['participants'])));
				}
			}
			$this->_redirectExit('details', 'exams', array('exam-id' => $exam->id));
		} else {
			$model = new Model_Groups();
			$this->view->groups = $model->fetchAll($model->select()->where('domain_id = ?', $this->user->domain_id));
		}
	}

	public function createAction() {
		if ($this->_hasParam('group') and $this->_hasParam('project') and $this->_hasParam('name') and $this->_hasParam('standard')) {
			//pobierz kompetencje.
			$competencies = array();
			$modelProjects = new Model_Projects();
			$project = $modelProjects->fetchRow(array('id = ?' => $this->_getParam('project')));
			if ($project === null) {
				$this->addError($this->view->translate('No project with ID %d', $this->_getParam('project')));
				return;
			}
			foreach ($project->getCompetencies() as $competence) {
				$competencies []= $competence;
			}

			$db = Zend_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			//zapisz egzamin.
			$examData = array
			(
				'name' => $this->_getParam('name'),
				'description' => $this->_getParam('description'),
				'project_id' => $project->id,
				'standard_id' => $this->_getParam('standard'),
				'domain_id' => $this->user->domain_id,
				'user_id' => $this->user->id
			);
			if (empty($examData['name'])) {
				$this->addError($this->view->translate('Empty exam name!'));
				return;
			}
			$modelExams = new Model_Exams();
			$exam = $modelExams->createAndSave($examData);

			//potwórz relacje między badaniem a kompetencjami.
			$modelExamCompetencies = new Model_ExamCompetencies();
			foreach ($competencies as $competence) {
				$modelExamCompetencies->insert(array('competence_id' => $competence->id, 'exam_id' => $exam->id));
			}

			//potwórz relacje między badaniem a użytkownikami.
			$mailAddresses = array
			(
				'groups' => array(),
				'managers' => array(),
				'participants' => array()
			);

			$modelExamParticipants = new Model_ExamParticipants();
			$modelExamManagers = new Model_ExamManagers();
			$modelGroups = new Model_Groups();

			//zawsze dodaj do managerów tego, kto tworzy
			$modelExamManagers->insert(array('user_id' => $this->user->id, 'exam_id' => $exam->id));

			//dla każdej grupy
			foreach ($this->_getParam('group') as $groupId) {
				$group = $modelGroups->fetchRow($modelGroups->select()->where('id = ?', $groupId));
				foreach ($group->getAssociatedUsers() as $user) {
					if ($user->role == Model_Users::ROLE_STUDENT) {
						$modelExamParticipants->insert(array('user_id' => $user->id, 'exam_id' => $exam->id, 'group_id' => $group->id));
						$mailAddresses['participants'] []= $user->email;
					} else {
						//nie dodawaj tego, kto tworzy dwukrotnie do managerów
						if ($this->user->id == $user->id) {
							continue;
						}
						$modelExamManagers->insert(array('user_id' => $user->id, 'exam_id' => $exam->id));
						$mailAddresses['managers'] []= $user->email;
					}
				}
				$mailAddresses['groups'] []= $group->email;
			}
			if ($this->observer && $this->_getParam('send-mail')) {
				$data = array
				(
					'mailList' => $mailAddresses['groups'],
					'examId' => $exam->id,
					'examName' => $exam->name,
				);
				$this->observer->observe('createExam', true, $data);
			}

			$db->commit();
			$this->addSuccess($this->view->translate('Exam created successfully'));
		} else {
			$model = new Model_Groups();
			$this->view->groups = $model->fetchAll($model->select()->where('domain_id = ?', $this->user->domain_id));
			$model = new Model_Projects();
			$this->view->projects = $model->fetchAll($model->select()->order('date DESC'));

			$this->view->standards = array();
			foreach ($this->view->projects as $project) {
				$this->view->standards[$project->id] = $project->getStandards();
			}
		}
	}

	public function deleteAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
		} else {
			$modelExams = new Model_Exams();
			$exam = $modelExams->find($this->_getParam('exam-id'))->current();
			if ($exam === null) {
				$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			} else {
				if (($this->user->role == Model_Users::ROLE_TEACHER) and ($this->user->id != $exam->getFirstManager()->user_id)) {
					$this->addError($this->view->translate('Trying to perform invalid request'));
				} else {
					$this->view->exam = $exam;
					$modelExams->delete(array('id = ?' => $this->_getParam('exam-id')));
					$this->addSuccess($this->view->translate('Exam deleted successfully'));
				}
			}
		}
		$this->_redirectExit('index', 'exams');
	}

	public function closeAction() {
		//updatujemy czas zakończenia badania
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
		} else {
			$modelExams = new Model_Exams();
			$select = $modelExams
				->select()
				->where('id = ?', $this->_getParam('exam-id'))
				->where('date_closed IS NULL')
				;
			$exam = $modelExams->fetchRow($select);
			if ($exam === null) {
				$this->addError($this->view->translate('No opened exam with ID %d', $this->_getParam('exam-id')));
			} else {
				if (($this->user->role == Model_Users::ROLE_TEACHER) and ($this->user->id != $exam->getFirstManager()->user_id)) {
					$this->addError($this->view->translate('Trying to perform invalid request'));
				} else {
					$exam->date_closed = date('Y-m-d H:i:s');
					$exam->save();
					$this->view->exam = $exam;
					$this->addSuccess($this->view->translate('Exam closed successfully'));
				}
			}
		}
		$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
	}

	public function reopenAction() {
		//updatujemy czas zakończenia badania
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
		} else {
			$modelExams = new Model_Exams();
			$select = $modelExams
				->select()
				->where('id = ?', $this->_getParam('exam-id'))
				->where('date_closed IS NOT NULL')
				;
			$exam = $modelExams->fetchRow($select);
			if ($exam === null) {
				$this->addError($this->view->translate('No closed exam with ID %d', $this->_getParam('exam-id')));
			} else {
				if (($this->user->role == Model_Users::ROLE_TEACHER) and ($this->user->id != $exam->getFirstManager()->user_id)) {
					$this->addError($this->view->translate('Trying to perform invalid request'));
				} else {
					$exam->date_closed = new Zend_Db_Expr('NULL');
					$exam->save();
					$this->view->exam = $exam;
					$this->addSuccess($this->view->translate('Exam reopened successfully'));
				}
			}
		}
		$this->_redirectExit('details', 'exams', array('exam-id' => $this->_getParam('exam-id')));
	}



	public function resultsAction() {
		$modelExams = new Model_Exams();
		$modelUsers = new Model_Users();
		$modelGroups = new Model_Groups();

		//pobierz egzamin
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}
		$this->view->exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($this->view->exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}
		$this->view->competencies = $this->view->exam->getAssociatedCompetencies();


		//pobierz listę wszystkich grup którymi zarządza dany user
		if ($this->user->role == Model_Users::ROLE_TEACHER) {
			$this->view->groups = $this->view->exam->getAssociatedGroups($this->user->id);
		} elseif (($this->user->role == Model_Users::ROLE_ADMINISTRATOR) or ($this->user->role == Model_Users::ROLE_SUPER_ADMINISTRATOR)) {
			$this->view->groups = $this->view->exam->getAssociatedGroups();
		}
		//pobierz grupę
		if ($this->user->role == Model_Users::ROLE_STUDENT) {
			$select = $modelUsers
				->select(true)
				->setIntegrityCheck(false)
				//->distinct('users.id')
				->join('exam_participants', 'exam_participants.user_id = users.id', array('date_finished'))
				->where('exam_participants.exam_id = ?', $this->view->exam->id)
				->where('exam_participants.user_id = ?', $this->user->id)
				;
		} else {
			if ($this->_hasParam('group-id')) {
				$this->view->group = $modelGroups->find($this->_getParam('group-id'))->current();
				if ($this->view->group === null) {
					$this->addError($this->view->translate('No group with ID %d', $this->_getParam('group-id')));
					return;
				}
				$select = $modelUsers
					->select(true)
					->setIntegrityCheck(false)
					//->distinct('users.id')
					->join('exam_participants', 'exam_participants.user_id = users.id', array('date_finished'))
					->where('exam_participants.exam_id = ?', $this->view->exam->id)
					->where('exam_participants.group_id = ?', $this->view->group->id)
					->join('user_groups', '(user_groups.user_id = users.id AND user_groups.group_id = exam_participants.group_id)', array('owner'))
					;
			}
			//pobierz po prostu wszystkich uczestników
			else {
				$select = $modelUsers
					->select(true)
					->setIntegrityCheck(false)
					//->distinct('users.id')
					->join('exam_participants', 'exam_participants.user_id = users.id', array('date_finished'))
					->where('exam_participants.exam_id = ?', $this->view->exam->id)
					;
			}
		}
		$select->order('substr(users.name, strpos(users.name, \' \') + 1)');
		$this->view->availableUsers = $modelUsers->fetchAll($select);

		$this->view->finishedUsers = array();
		foreach ($this->view->availableUsers as $user) {
			$this->view->finishedUsers []= $user;
		}
		$this->view->finishedUsers = array_filter($this->view->finishedUsers, function($user) { return !empty($user->date_finished); });

		$this->populateMeanAnswers();
	}



	public function reportAction() {
		$modelExams = new Model_Exams();
		$modelUsers = new Model_Users();
		$modelGroups = new Model_Groups();

		//pobierz egzamin
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}
		$this->view->exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($this->view->exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}
		$this->view->competencies = $this->view->exam->getAssociatedCompetencies();

		//pobierz listę wszystkich grup którymi zarządza dany user
		if ($this->user->role == Model_Users::ROLE_TEACHER) {
			$this->view->groups = $this->view->exam->getAssociatedGroups($this->user->id);
		} elseif ($this->user->role == Model_Users::ROLE_ADMINISTRATOR) {
			$this->view->groups = $this->view->exam->getAssociatedGroups();
		}
		//pobierz grupę
		if ($this->user->role == Model_Users::ROLE_STUDENT) {
			$select = $modelUsers
				->select(true)
				->setIntegrityCheck(false)
				->distinct('users.id')
				->join('exam_participants', 'exam_participants.user_id = users.id', array('date_started', 'date_finished'))
				->where('exam_participants.exam_id = ?', $this->view->exam->id)
				->where('exam_participants.user_id = ?', $this->user->id)
				;
			$this->view->availableUsers = $modelUsers->fetchAll($select);
		} else {
			if ($this->_hasParam('group-id')) {
				$this->view->group = $modelGroups->find($this->_getParam('group-id'))->current();
				if ($this->view->group === null) {
					$this->addError($this->view->translate('No group with ID %d', $this->_getParam('group-id')));
					return;
				}
				$select = $modelUsers
					->select(true)
					->setIntegrityCheck(false)
					->distinct('users.id')
					->join('exam_participants', 'exam_participants.user_id = users.id', array('date_started', 'date_finished'))
					->where('exam_participants.exam_id = ?', $this->view->exam->id)
					->where('exam_participants.group_id = ?', $this->view->group->id)
					->join('user_groups', '(user_groups.user_id = users.id AND user_groups.group_id = exam_participants.group_id)', array('owner'))
					;
				$this->view->availableUsers = $modelUsers->fetchAll($select);
			}
			//pobierz po prostu wszystkich uczestników
			else {
				$select = $modelUsers
					->select(true)
					->setIntegrityCheck(false)
					->distinct('users.id')
					->join('exam_participants', 'exam_participants.user_id = users.id', array('date_started', 'date_finished'))
					->where('exam_participants.exam_id = ?', $this->view->exam->id)
					;
				$this->view->availableUsers = $modelUsers->fetchAll($select);
			}
		}

		// pobierz żądanych użytkowników
		$this->view->selectedUsers = array();
		if ($this->_hasParam('user-id')) {
			foreach (explode(',', $this->_getParam('user-id')) as $userId) {
				$found = false;
				foreach ($this->view->availableUsers as $user) {
					if ($user->id == $userId) {
						$found = true;
						break;
					}
				}
				if ($found) {
					$this->view->selectedUsers []= $user;
				}
			}
		} else {
			foreach ($this->view->availableUsers as $user) {
				$this->view->selectedUsers []= $user;
			}
		}

		$this->view->selectedUsers = array_filter($this->view->selectedUsers, function($user) { return $user->date_finished !== null; });


		$this->_helper->layout()->disableLayout();
		$this->populateMeanAnswers();
		
		//die('<pre>'.print_r($this,1));
	}



	private function populateMeanAnswers() {
		$users = $this->view->availableUsers;
		if (!empty($this->view->selectedUsers)) {
			$users = $this->view->selectedUsers;
		}
		$this->view->answerUserMean = array();
		$this->view->answerStandard = array();
		$this->view->answerGroupMean = array();

		foreach ($this->view->competencies as $competence) {
			$this->view->answerUserMean[$competence->id] = array();
			$groupSum = 0;
			$groupCount = 0;
			foreach ($users as $user) {
				$userSum = 0;
				$userCount = 0;
				foreach ($competence->getAssociatedQuestions() as $question) {
					$value = $question->getAnswerValue($this->view->exam->id, $user->id);
					$userSum += $value;
					$userCount ++;
				}
				$groupSum += $userSum;
				$groupCount += $userCount;
				$userMean = $userSum / max(1, $userCount);
				$this->view->answerUserMean[$competence->id][$user->id] = round($userMean);
			}
			$groupMean = $groupSum / max(1, $groupCount);
			$this->view->answerGroupMean[$competence->id] = round($groupMean);
			$this->view->answerStandard[$competence->id] = $competence->getStandardValue($this->view->exam->standard_id);
		}
	}


	public function listSharesAction() {
		$this->_helper->layout()->disableLayout();
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			return;
		}

		//pobierz badanie z bazy
		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			return;
		}

		$this->view->exam = $exam;
	}


	public function addShareAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			$this->_redirectExit('index', 'exams');
			return;
		}

		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			$this->_redirectExit('index', 'exams');
			return;
		}

		if (!$this->_hasParam('user-email')) {
			$this->addError($this->view->translate('No user e-mail specified'));
			$this->_redirectExit('index', 'exams');
			return;
		}
		$modelUsers = new Model_Users();
		$user = $modelUsers->fetchRow(array('email = ?' => $this->_getParam('user-email')));
		if ($user === null) {
			$this->addError($this->view->translate('No user with e-mail %s', $this->_getParam('user-email')));
			$this->_redirectExit('index', 'exams');
			return;
		}
		$userId = $user->id;
		$exam->addManager($userId);
		if ($this->observer) {
			$data = array
			(
				'examId' => $exam->id,
				'examName' => $exam->name,
				'userName' => $user->name,
				'userMail' => $user->email,
			);
			$this->observer->observe('shareExam', true, $data);
		}
		$this->addSuccess($this->view->translate('Exam shared successfully'));
		$this->_redirectExit('index', 'exams');
	}


	public function removeShareAction() {
		if (!$this->_hasParam('exam-id')) {
			$this->addError($this->view->translate('No exam ID specified'));
			$this->_redirectExit('index', 'exams');
			return;
		}

		$modelExams = new Model_Exams();
		$exam = $modelExams->find($this->_getParam('exam-id'))->current();
		if ($exam === null) {
			$this->addError($this->view->translate('No exam with ID %d', $this->_getParam('exam-id')));
			$this->_redirectExit('index', 'exams');
			return;
		}
		$this->view->exam = $exam;

		if (!$this->_hasParam('user-id')) {
			$this->addError($this->view->translate('No user ID specified'));
			$this->_redirectExit('index', 'exams');
			return;
		}
		$userId = $this->_getParam('user-id');
		if ($userId == $this->user->id) {
			$this->addError($this->view->translate('Cannot unshare with oneself'));
			$this->_redirectExit('index', 'exams');
			return;
		}
		if ($userId == $exam->getFirstManager()->id) {
			$this->addError($this->view->translate('Cannot unshare with first manager'));
			$this->_redirectExit('index', 'exams');
			return;
		}

		try {
			$exam->removeManager($userId);
		} catch (Exception $e) {
			$this->addError($this->view->translate('No manager with ID %d', $userId));
			$this->_redirectExit('index', 'exams');
			return;
		}
		if ($this->observer) {
			$modelUsers = new Model_Users();
			$user = $modelUsers->find($userId)->current();
			$data = array
			(
				'examId' => $exam->id,
				'examName' => $exam->name,
				'userName' => $user->name,
				'userMail' => $user->email,
			);
			$this->observer->observe('unshareExam', true, $data);
		}
		$this->addSuccess($this->view->translate('Exam unshared successfully'));
		$this->_redirectExit('index', 'exams');
	}
}
?>
