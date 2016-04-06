<?php

require_once 'MillionaireController.php';

class MillionaireAdministratorController extends MillionaireController
{
	public function init()
	{
		parent::init();

		if (in_array($this->getRequest()->getActionName(), array('import-epu'))) return;

		$this->checkAdmin();
		$this->checkToken();
	}

	// funkcja która sprawdza czy dany user już jest w bazie danych,
	// jeżeli nie to go tworzy i nadaje rangę NAUCZYCIELA
	protected function checkAndAddTeacher($email)
	{
		if (isset($email)) {
			$email = trim($email);
			$dbUser = new Model_User;
			$dbDomain = new Model_Domain;
			$user = $dbUser->findByMail($email);
			if (isset($user->id)) {
				// znalazłem w bazie danych i zwracam ID
				return $user->id;
			} else {
				// sprawdzamy czy jest taka domena w bazie danych i ją dodajemy
				$domain = explode('@', $email);
				$domain = end($domain);
				$domainInDb = $dbDomain->getByName($domain);
				if (!isset($domainInDb->id)) {
					$dbDomain->insert(array(
						'domain_name' => $domain,
						'org_name' => $domain,
						'admin_email' => $this->user->email,
						'oauth_token' => ''
					));
				}
				$domainInDb = $dbDomain->getByName($domain);
				// niema takiego usera w bazie danych, więc tworzę go
				$dbUser->insert(array(
					'email' => $email,
					'domain_id' => $domainInDb->id,
					'user_role' => 3,
					'active' => 1,
					'name' => ''
				));
				$user = $dbUser->findByMail($email);
				return $user->id;
			}
		} else {
			return false;
		}
	}

	public function indexAction()
	{

	}

	public function autoryzacjaLinkaAction()
	{
		require_once 'Zend/Http/Client.php';

		// params
		$email = $this->_getParam("email");

		// constans
		$remoteAddr = 'http://platforma.eszkola-wielkopolska.pl';
		$ID = 'EPU';
		$hash = 'c68f39913f901f3ddf44c707357a7d70';
		$sig = md5($email . $hash);

		$httpClient = new Zend_Http_Client($remoteAddr . '/links/all/mail/' . $email . '/sig/' . $sig . '/id/' . $ID, array(
			// $httpClient = new Zend_Http_Client('http://localhost/' , array(
			'maxredirects' => 0,
			'timeout' => 30));

		$response = $httpClient->request();
		echo '<pre>';
		print_r($response->getBody());
		die();
		$this->view->debug = $httpClient->request();
	}

	public function importPytanAction()
	{
		$this->view->google_docs_link = $this->user->google_docs_link;

		$accessToken = $this->user->getAccessToken();
		$httpClient = $accessToken->getHttpClient($this->_oauthOptions);
		$client = new Zend_Gdata_Docs($httpClient);

		// Pobierz listę spreadsheetów i formularzy
		$spreadsheets = array();

		$spreadsheetService = new Zend_Gdata_Spreadsheets($httpClient);
		$feed = $spreadsheetService->getSpreadsheetFeed();
		foreach ($feed->entries as $entry) {
			$alternateLink = '';
			foreach ($entry->link as $link) {
				if ($link->getRel() === 'alternate') {
					$alternateLink = $link->getHref();
				}
			}
			$linkQuery = explode('?', $alternateLink);
			$queryParts = explode('&', $linkQuery[1]);
			$params = array();
			foreach ($queryParts as $param) {
				$item = explode('=', $param);
				if (isset($item[0]) && isset($item[1])) {
					$params[$item[0]] = $item[1];
					$spreadsheet_key = $params['key'];
				} else {
					$spreadsheet_key = '';
				}
			}
			$spreadsheets[] = array('title' => $entry->title, 'key' => $spreadsheet_key);
		}
		$arkusze = array();
		if ($this->user['google_docs_link'] != '') {
			$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
			$query->setSpreadsheetKey($this->user['google_docs_link']);
			$worksheets_feed = $spreadsheetService->getWorksheetFeed($query);
			foreach ($worksheets_feed->entries as $entry) {
				$arkusze[] = array(
					'title' => $entry->title->text,
					'id' => basename($entry->id)
				);
			}
			;
		}
		$dbCategories = new Model_Category;
		$this->view->main_categories = $dbCategories->findByParent(0);
		$this->view->spreadsheets = $spreadsheets;
		$this->view->worksheets = $arkusze;
		$this->view->google_docs_link = $this->user['google_docs_link'];
		$spreadsheetService = new Zend_Gdata_Spreadsheets($httpClient);
	}

	public function arkuszeAction()
	{
		if (isset($_POST['key'])) {
			$accessToken = unserialize(base64_decode($this->user->access_token));
			$httpClient = $accessToken->getHttpClient($this->_oauthOptions);
			$spreadsheetService = new Zend_Gdata_Spreadsheets($httpClient);
			$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
			$query->setSpreadsheetKey($_POST['key']);
			$feed = $spreadsheetService->getWorksheetFeed($query);
			$arkusze = array();
			foreach ($feed->entries as $entry) {
				$arkusze[] = array(
					'title' => $entry->title->text,
					'id' => basename($entry->id)
				);
			}
			;
			echo json_encode($arkusze);
		}
		die;
	}

	public function changeRankAction()
	{
		$dbUser = new Model_User;
		$id = $this->_request->getParam('id');
		$rank = $this->_request->getParam('rank');
		if (isset($rank) && isset($id)) {
			$data = array('user_role' => $rank);
			$where = $dbUser->getAdapter()->quoteInto('id = ?', $id);
			$dbUser->update($data, $where);
		}
		die;
	}

	public function deleteUserAction()
	{
		$dbUser = new Model_User;
		$delete = $this->_request->getParam('id');
		if (isset($delete) && $delete > 0) {
			$where = $dbUser->getAdapter()->quoteInto('id = ?', $delete);
			$dbUser->delete($where);
		}
		die;
	}

	public function importPytanPobierzPytaniaGoogleAction()
	{
		$accessToken = $this->user->getAccessToken();
		$httpClient = $accessToken->getHttpClient($this->_oauthOptions);
		$spreadsheetService = new Zend_Gdata_Spreadsheets($httpClient);
		if (isset($_POST["key"])) {
			if ($_POST["key"] != '') {
				$key = trim($_POST["key"]);
				$dbUser = new Model_User;
				$user = $dbUser->findByMail($this->ZendSession->OPENID['email']);
				if (isset($user->id)) {
					$where = $dbUser->getAdapter()->quoteInto('id = ?', $user->id);
					$data = array('google_docs_link' => $_POST["key"]);
					$dbUser->update($data, $where);
				}
			} else {
				$key = "0AlYEyBsCVO5EdGdMeGdhWHZsaG5PdW1aeWxJa1NEZXc";
			}
		} else {
			$key = "0AlYEyBsCVO5EdGdMeGdhWHZsaG5PdW1aeWxJa1NEZXc";
		}
		$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
		$query->setSpreadsheetKey($key);

		$spreadsheet = $spreadsheetService->getWorksheetFeed($query);

		$query = new Zend_Gdata_Spreadsheets_ListQuery();
		$query->setSpreadsheetKey($key);
		if (isset($_POST["worksheet"])) {
			$worksheet = $_POST["worksheet"];
		} else {
			$worksheet = 1;
		}
		$query->setWorksheetId($worksheet);
		$listFeed = $spreadsheetService->getListFeed($query);
		$file = array();
		if (isset($listFeed->entries[0])) {
			foreach ($listFeed->entries[0]->getCustom() as $column) {
				$file['legenda'][] = $column->getColumnName();
			}
		} else {
			echo json_encode(array('validation' => false));
			die;
		}
		foreach ($listFeed->entries as $entry) {
			$rowData = $entry->getCustom();
			$data = array();
			foreach ($rowData as $customEntry) {
				$key = array_search($customEntry->getColumnName(), $file['legenda']);
				$data[$key] = $customEntry->getText();
			}
			$file[] = $data;
		}
		// Tworzenie tablicy pomagajacej w ustaleniu kolejności komórek
		$domyslnaKolejnosc = array(
			"sygnat",
			"nazwa|mail|autor",
			"treśćpytania",
			"odpowiedź1|1|odpowiedź A",
			"odpowiedź2|2|odpowiedź B",
			"odpowiedź3|3|odpowiedź C",
			"odpowiedź4|4|odpowiedź D",
			"odpowiedź5|5|odpowiedźeopcjonalnie",
			"odpowiedź6|6|odpowiedźfopcjonalnie",
			"dobr|popraw",
			"podpowiedz|ratun|ekspert|expert",
			"rodzaj|typ|szko",
			"program|zakres|dzia",
			"trudno|poziom|stopie",
			"rysunek"
		);
		$kolejnosc = array();
		$legenda = $file['legenda'];
		unset($file['legenda']);
		foreach ($legenda as $key => $value) {
			$kolejnosc[$key] = NULL;
			foreach ($domyslnaKolejnosc as $pozycja => $etykiety) {
				$etykiety = explode('|', $etykiety);
				foreach ($etykiety as $etykieta) {
					if (strstr($value, $etykieta)) {
						if ($kolejnosc[$key] == NULL && !array_search($pozycja, $kolejnosc)) {
							$kolejnosc[$key] = $pozycja;
						}
					}
					if ($kolejnosc[$key] != NULL) break;
				}
			}
		}
		$fileSniff = array(
			'validation' => true,
			'kolejnosc' => $kolejnosc,
			'legenda' => $legenda,
			'file' => $file
		);
		if (isset($file[0]) && count($file[0]) < 8) {
			$fileSniff['validation'] = false;
		}
		echo json_encode($fileSniff);
		die;
	}

	public function importFileSniffAction()
	{
		if (isset($_POST['filename'])) {
			if (($handle = fopen('http://localhost' . $_POST['filename'], 'r')) !== FALSE) {
				$file = array();
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$file[] = $data;
				}
				$fileSniff = array(
					'validation' => true,
					'file' => $file
				);
				fclose($handle);
			} else {
				$fileSniff = array(
					'validation' => false
				);
			}

		} else {
			$fileSniff = array(
				'validation' => false
			);
		}
		echo json_encode($fileSniff);
		die;
	}

	public function importPytanPobierzPytaniaAction()
	{
		$questions = array();
		if (($handle = fopen($this->_getBaseUrl() . '/upload/pytania.csv', 'r')) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				if ($data[0] != '') {
					$created = date("Y-m-d H:i:s", strtotime($data[0]));
				} else {
					$created = date("Y-m-d H:i:s");
				}
				$questions[] = array(
					'created' => $created,
					'user_name' => $data[1],
					'question' => array(
						'content' => trim($data[2]),
						'school' => $data[9],
						'chapter' => $data[12],
						'level' => $data[14],
						'media' => $data[15]
					),
					'answer' => array(
						'1' => trim($data[3]),
						'2' => trim($data[4]),
						'3' => trim($data[5]),
						'4' => trim($data[6]),
						'5' => trim($data[10]),
						'6' => trim($data[11]),
						'correct' => $data[7]
					),
					'lifebuoy' => array(
						'expert' => $data[8],
					),
				);
			}
			fclose($handle);
		}
		$questions['count'] = count($questions);
		echo json_encode($questions);
		die;
	}

	public function importPytanZapiszDoBazyAction()
	{
		// baza danych
		$dbQuestions = new Model_Question;
		$dbCategories = new Model_Category;
		$dbQuestionCategories = new Model_QuestionCategory;
		$dbAnswers = new Model_Answer;
		$dbLifebuoys = new Model_Lifebuoy;

		// wczytywanie danych z pliku csv do tablicy
		$questions = array();
		$row = 0;
		$schools = array();
		
		if (isset($_POST['question'])) {
			$data = json_decode($_POST['question']);
			if ($data[1] == '') {
				$author = $this->user->email;
			} else {
				$author = $data[1];
			}
			for ($i = 0; $i < 16; $i++) {
				if (!isset($data[$i])) $data[$i] = '';
			}
			$question = array(
				// 'created' => date("Y-m-d H:i:s", strtotime($data[0]),
				'created' => date("Y-m-d H:i:s"),
				'user_name' => $author,
				'question' => array(
					'content' => trim($data[2]),
					'school' => $data[11],
					'chapter' => trim($data[12]),
					'level' => trim($data[13]),
					'media' => trim($data[14])
				),
				'answer' => array(
					'1' => trim($data[3]),
					'2' => trim($data[4]),
					'3' => trim($data[5]),
					'4' => trim($data[6]),
					'5' => trim($data[7]),
					'6' => trim($data[8]),
					'correct' => $data[9]
				),
				'lifebuoy' => array(
					'expert' => $data[10],
				)
			);

			$path = '.'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'questions'.DIRECTORY_SEPARATOR;
			if($question['question']['media']!=''&&file_exists($path.$question['question']['media'])) {				
				$image_source = $path.$question['question']['media'];
			}
			if (trim($question['question']['content'] != '')) {
				//PYTANIA
				$data = array();
				// sprawdź czy takie pytanie jest już w bazie, i jeżeli nie to zwróć jego id w zmiennej $questionInDb
				if (isset($_POST['document'])) {
					$documentKey = $_POST['document'];
				} else {
					$documentKey = '';
				}
				$unikalnyKlucz = $question['question']['content'];
				foreach ($question['answer'] as $key => $value) {
					if ($key != 'correct') {
						$unikalnyKlucz .= $value;
					}
				}
				$unikalnyKlucz = md5($unikalnyKlucz);
				if (!$questionInDb = $dbQuestions->findByQuestionContent($unikalnyKlucz)) {
					// Zapis do bazy
					if (!$author_id = $this->checkAndAddTeacher($question['user_name'])) {
						$author_id = 0; // w razie czego ustawiamy autora na 0
					}
					$author_id = (int)$author_id;

					$thumbnail='';
					if(isset($image_source)) {
						// sprawdź czy obrazek jest przygotowany czy trzeba wygenerować miniaturkę
						if (
							stristr($image_source, 'bmp') ||
							stristr($image_source, 'gif') ||
							stristr($image_source, 'jpg') ||
							stristr($image_source, 'jpeg') ||
							stristr($image_source, 'png')
						) {
							if ($thumbnail = $this->prepareImage($image_source, $unikalnyKlucz)) {
							}
						}

					}

					$data = array(
						'author_id' => $author_id,
						'question' => trim($question['question']['content']),
						'question_hash' => $unikalnyKlucz,
						'media' => '',
						'source' => $thumbnail,
						'status' => 0,
						'flag' => 0,
						'flag_data' => ''
					);
					$dbQuestions->insert($data);
					$questionInDb = $dbQuestions->findByQuestionContent($unikalnyKlucz);
					

					// KATEGORIE PYTAŃ
					// I - SZKOŁA
					$data = array();
					// czy do tego pytania przydzielono już kategorie? jeżeli tak to nie dodawaj drugi raz...
					//if($questionCategoriesInDb = $dbQuestionCategories->findByQuestionId($questionInDb->id)) {
					//} else {
					// przypisz rodzaj szkoły (niestety na sztywno)
					// sprawdz czy już jest taka kategoria, jak niema to utwórz
					$szkola = $dbCategories->findByParentTypeName(0, 2, 'Wszystkie szkoły');
					if (!isset($szkola->id)) {
						$dbCategories->insert(array(
							'parent_id' => 0,
							'name' => 'Wszystkie szkoły',
							'category_type_id' => 2
						));
						$szkola = $dbCategories->findByParentTypeName(0, 2, 'Wszystkie szkoły');
					}
					$data = array(
						array(
							'question_id' => $questionInDb->id,
							'category_id' => $szkola->id)
					);

					// II - POZIOM TRUDNOSCI
					$question['question']['level'] = trim($question['question']['level']);
					switch ($question['question']['level']) {
						case 'Łatwe':
						case 'łatwe':
							$data[] = array(
								'question_id' => $questionInDb->id,
								'category_id' => 1
							);
							break;
						case 'Średnie':
						case 'średnie':
							$data[] = array(
								'question_id' => $questionInDb->id,
								'category_id' => 2
							);
							break;
						case 'Trudne':
						case 'trudne':
							$data[] = array(
								'question_id' => $questionInDb->id,
								'category_id' => 3
							);
							break;
						default:
							$data[] = array(
								'question_id' => $questionInDb->id,
								'category_id' => 1
							);
							break;
					}

					// III - Rozdział
					// przyporządkuj pytanie od stosownego 'Zakresu programowego'
					$question['question']['chapter'] = trim($question['question']['chapter']);
					if (isset($question['question']['chapter']) && $question['question']['chapter'] != '') {
						// sprawdz czy już jest taka kategoria, jak niema to utwórz
						if(isset($_POST['main_category'])) {
							$main_category = $_POST['main_category'];
						} else {
							$main_category = 6;
						}
						if(isset($_POST['new_main_category']) && $_POST['new_main_category']!='') {
							$new_main_category = $dbCategories->findByParentTypeName(0, 3, $_POST['new_main_category']);
							if(!isset($new_main_category->id)) {
								$main_category = $dbCategories->insert(array(
									'parent_id' => 0,
									'name' => $_POST['new_main_category'],
									'category_type_id' => 3
								));
							} else {
								$main_category = $new_main_category->id;
							}
						}
						$program = $dbCategories->findByParentTypeName($main_category, 3, $question['question']['chapter']);
						if (!isset($program->id)) {
							$dbCategories->insert(array(
								'parent_id' => $main_category,
								'name' => $question['question']['chapter'],
								'category_type_id' => 3
							));
							$program = $dbCategories->findByParentTypeName($main_category, 3, $question['question']['chapter']);
						}
						$data[] = array(
							'question_id' => $questionInDb->id,
							'category_id' => $program->id
						);
					}

					// zapisz wszystkie kategorie pytania do bazy danych
					foreach ($data as $row) {
						if (isset($row['question_id']) && isset($row['category_id'])) {
							$dbQuestionCategories->insert($row);
						}
					}

					// ODPOWIEDZI
					$data = array();
					$answers = array();
					$a = 1;
					$question['answer']['correct'] = trim($question['answer']['correct']);
					$ABC = array('A', 'B', 'C', 'D', 'E', 'F');
					foreach ($question['answer'] as $key => $answer) {
						if ($answer != '' && $key != 'correct') {
							if (
								stristr($question['answer']['correct'], 'Odpowiedź ' . $a) ||
								stristr($question['answer']['correct'], 'Odpowiedź ' . $ABC[$a - 1])
							) {
								$correct = 1;
							} else {
								$correct = 0;
							}
							if (in_array($question['answer']['correct'], $ABC)) {
								if ($key == (array_search($question['answer']['correct'], $ABC) + 1)) {
									$correct = 1;
								} else {
									$correct = 0;
								}
							} else {
								if (strstr($question['answer']['correct'], $a) || $question['answer']['correct'] == $a) {
									$correct = 1;
								} else {

								}
							}
							$data[$key] = array(
								'question_id' => $questionInDb->id,
								'answer' => $answer,
								'is_correct' => $correct,
								'probability' => 0
							);
							$a++;
						}
					}
					// zapisz wszystkie kategorie pytania do bazy danych
					if ($questionAnswersInDb = $dbAnswers->findByQuestionId($questionInDb->id)) {
					} else {
						foreach ($data as $row) {
							$dbAnswers->insert($row);
							$answers[] = $row;
						}
					}

					// KOŁA RATUNKOWE
					$data = array();
					// czy do tego pytania są już zapisane koła ratunkowe
					if ($questionLifebuoysInDb = $dbLifebuoys->findByQuestionId($questionInDb->id)) {
					} else {
						// pytanie do experta
						if ($question['lifebuoy']['expert'] != '') {
							$data = array(
								'question_id' => $questionInDb->id,
								'lifebuoy' => $question['lifebuoy']['expert'],
								'lifebuoy_type' => 1
							);
							$dbLifebuoys->insert($data);
						}
					}
					// }
				}
				print_r($question);
			} else {
				echo 'Pytanie jest puste';
			}
		}
		die;
	}

	public function ajaxTruncateAction()
	{
		if (isset($_POST['truncate'])) {
			$sql = "TRUNCATE answers;";
			$this->db->query($sql);
			$sql = "TRUNCATE attempts;";
			$this->db->query($sql);
			$sql = "TRUNCATE tests;";
			$this->db->query($sql);
			$sql = "TRUNCATE lifebuoys;";
			$this->db->query($sql);
			$sql = "TRUNCATE questions;";
			$this->db->query($sql);
			$sql = "TRUNCATE question_categories;";
			$this->db->query($sql);
		}
		die;
	}

	public function myQuestionsAction()
	{
		$page = $this->_request->getParam('page');
		$this->view->page = $page;

		$dbQuestionCategories = new Model_QuestionCategory;
		$categories = array();

		$dbQuestions = new Model_Question;

		$questions = $dbQuestions->getByAuthorId($this->user->id, $page);
		foreach ($questions as $question) {
			$categories[$question->id] = $dbQuestionCategories->getCategories($question->id);
		}
		$this->view->questions = $questions;
		$this->view->categories = $categories;
	}

	public function allQuestionsAction()
	{
		$dbQuestionCategories = new Model_QuestionCategory;
		$dbQuestions = new Model_Question;
		$dbCategories = new Model_Category;
		$dbUsers = new Model_User;
		$page = $this->_request->getParam('page');
		if ($this->user->user_role === 3) {
			$this->_redirect('my-questions');
		}
		$categories = array();
		$params = $this->_parseSearchParams($this->getRequest()->getParams());
		$this->ZendSession->moderationSearchParams = $params;
		$questions = $dbQuestions->questionSearch($params);
		if ($questions) {
			foreach ($questions as $question) {
				$categories[$question->id] = $dbQuestionCategories->getCategories($question->id);
			}
		} else {
			$questions = array();
		}
		$users = $dbUsers->getAll();
		$usersById = array();
		foreach ($users as $user) {
			$usersById[$user->id] = $user->toArray();
		}
		$usersByMail = $dbUsers->getAllByMail();
		$levelsByName = $dbCategories->findByType(1, false);
		$schoolsByName = $dbCategories->findByType(2, true);
		$categoriesByName = $dbCategories->findByType(3, true);
		$this->view->page = $page;
		$this->view->questions = $questions;
		$this->view->categories = $categories;
		$this->view->categoriesByName = $categoriesByName;
		$this->view->schoolsByName = $schoolsByName;
		$this->view->levelsByName = $levelsByName;
		$this->view->users = $usersById;
		$this->view->usersByMail = $usersByMail;
		$this->view->params = $params;
		$this->view->actionName = $this->getRequest()->getActionName();
		// $this->view->debug = $this->ZendSession;
	}

	public function showQuestionAction()
	{
		$id = $this->_request->getParam('id');
		$this->view->actionName = $this->getRequest()->getActionName();
		$this->view->goBackLink = 'my-questions';
		$this->view->showLink = 'show-question';
		if (!isset($id)) $this->_redirect('/administrator/all-questions');
		$this->_forward('add-question');
	}

	public function _parseSearchParams($params)
	{

		// Domyślnie chcemy wyświetlać po 5 rekordów na stronę
		if (!isset($params['perPage'])) {
			$params['perPage'] = 20;
		}

		// Domyślnie chcemy wyświetlać tylko aktywne posty
		if (!isset($params['active'])) {
			$params['active'] = 1;
		}

		foreach ($params as $key => $value) {
			// Filtrujemy puste wartości
			if (is_null($value) or $value == '') {
				unset($params[$key]);
				continue;
			}

			switch ($key) {

				case 'module':
				case 'controller':
				case 'action':
				case 'submit':
					// Te dane nie będą nam potrzebne - usuwamy
					unset($params[$key]);
					continue;
					break;

			}
		}
		return $params;
	}

	public function checkQuestionAction()
	{
		$id = $this->_request->getParam('id');
		$this->view->actionName = $this->getRequest()->getActionName();
		$this->view->goBackLink = 'check-questions';
		$moderationSearchParams = $this->ZendSession->moderationSearchParams;
		$this->view->moderationSearchParams = $moderationSearchParams;
		// linki do poprzedniego pytania
		if (isset($moderationSearchParams)) {
			$dbQuestions = new Model_Question;
			$questionsList = $dbQuestions->questionSearchNoPagination($moderationSearchParams);
			$questionsIds = array();
			foreach ($questionsList as $key => $value) {
				$questionsIds[] = $value->id;
			}
			foreach ($questionsIds as $key => $value) {
				if ($value == $id && isset($questionsIds[$key + 1])) {
					$this->view->nextQuestionId = $questionsIds[$key + 1];
				}
				if ($value == $id && isset($questionsIds[$key - 1])) {
					$this->view->prevQuestionId = $questionsIds[$key - 1];
				}
			}
		}
		$this->view->showLink = 'check-question';
		if (!isset($id)) $this->_redirect('/administrator/check-questions');
		$this->_forward('add-question');
	}

	public function checkQuestionsAction()
	{
		$this->view->actionName = $this->getRequest()->getActionName();
		$this->view->showLink = 'check-question';
		$this->_forward('all-questions');
	}

	public function deleteEmptyCategoriesAction()
	{
		// 1. Kasujemy przypisania do kategorii odnoszące się do nieistniejących pytań
		$sql = "delete from question_categories where question_id not in (select id from questions)";
		$this->db->query($sql);
		// 2. Kasujemy kategorie do których nie przypisano żadnych pytań
		$sql = "delete from categories where id not in (select distinct category_id from question_categories,questions where questions.id=question_id) and parent_id > 0;";
		$this->db->query($sql);
		$dbQuestionCategories = new Model_QuestionCategory;
		$dbCategories = new Model_Category;
		$kategorie = $dbCategories->getCategoriesArray();
		// 3. Sprawdzamy czy w przypisaniu pytań do kategorii nie zakradły się jakieś błędy
		$allQuestionsCategories = $dbQuestionCategories->getAll();
		$question_category_QCid = array();
		// 3.1. pogrupuj wszystkie
		foreach ($allQuestionsCategories as $k => $v) {
			if (!isset($question_category_QCid[$v->question_id][$v->category_id])) {
				$question_category_QCid[$v->question_id][$v->category_id] = array($v->id);
			} else {
				$question_category_QCid[$v->question_id][$v->category_id][] = $v->id;
			}
		}
		// 3.2. przygotuj listę id zduplikowanych przypisań
		$ids2delete = array();
		foreach ($question_category_QCid as $question_id => $question) {
			foreach ($question as $category_id => $category) {
				if (count($category) > 1) {
					$arr = $category;
					unset($arr[0]);
					$ids2delete = array_merge($ids2delete, $arr);
				}
			}
		}
		// 3.3. usuń zduplikowane przypisania
		foreach ($ids2delete as $k => $v) {
			$where = $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $v);
			$dbQuestionCategories->delete($where);
		}
		$this->view->message = array(
			'message' => 'Usunięto wszystkie kategorie do których nie zostały przypisane pytania.',
			'class' => 'messageOkay'
		);
		$this->_forward('index');
	}

	public function addQuestionAction()
	{
		$dbCategory = new Model_Category;
		$dbQuestions = new Model_Question;
		$dbAnswers = new Model_Answer;
		$dbLifeBuoys = new Model_Lifebuoy;
		$dbQuestionCategories = new Model_QuestionCategory;
		$questionId = $this->_request->getParam('id');
		$page = $this->_request->getParam('page');
		if (isset($page)) $this->view->page = $page;
		$levels = $dbCategory->findByType(1);
		$schools = $dbCategory->findByType(2);
		$categories = $dbCategory->findByParentTypeName(6, 3);
		$status = $this->_request->getParam('status');
		if (isset($status) && isset($questionId) && $this->user->user_role >= 4) {
			$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $questionId);
			$question = $dbQuestions->findByQuestionId($questionId);
			if ($status != $question->status) {
				$flag_data = unserialize($question->flag_data);
				if (!isset($flag_data['status_history'])) $flag_data['status_history'] = array();
				switch ($status) {
					case 10:
						$flag_data['status_history'][] = array('user_id' => $this->user->id, 'time' => time(), 'status' => 10);
						$data = array('status' => 10, 'flag_data' => serialize($flag_data));
						$dbQuestions->update($data, $where);
						$this->view->komunikat = array('class' => 'komunikatOkay', 'text' => 'Pytanie zostało zatwierdzone');
						break;
					case 0:
						$flag_data['status_history'][] = array('user_id' => $this->user->id, 'time' => time(), 'status' => 0);
						$data = array('status' => 0, 'flag_data' => serialize($flag_data));
						$dbQuestions->update($data, $where);
						$this->view->komunikat = array('class' => 'komunikatOkay', 'text' => 'Pytanie zostało cofnięte');
						break;
				}
			}
		}
		$flag = $this->_request->getParam('flag');
		if (isset($flag) && isset($questionId) && $this->user->user_role >= 4) {
			$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $questionId);
			$question = $dbQuestions->findByQuestionId($questionId);
			if ($flag != $question->flag) {
				$flag_data = unserialize($question->flag_data);
				if (!isset($flag_data['status_history'])) $flag_data['status_history'] = array();
				$flag_data['status_history'][] = array('user_id' => $this->user->id, 'time' => time(), 'status' => 222);
				$data = array('flag' => 0, 'flag_data' => serialize($flag_data));
				$dbQuestions->update($data, $where);
				$this->view->komunikat = array('class' => 'komunikatOkay', 'text' => 'Flaga została zdjęta');
			}
		}
		// 1. Jeżeli przesłano dane z formularza zapisz je
		if (isset($_POST['question'])) {
			// sprawdź czy obrazek jest przygotowany czy trzeba wygenerować miniaturkę
			if (
				$_POST['media'] != '' &&
				!strstr($_POST['media'], 'youtube') &&
				(
					stristr($_POST['media'], 'bmp') ||
					stristr($_POST['media'], 'gif') ||
					stristr($_POST['media'], 'jpg') ||
					stristr($_POST['media'], 'jpeg') ||
					stristr($_POST['media'], 'png')
				)
			) {
				if ($thumbnail = $this->prepareImage($_POST['media'], $_POST['question_hash'])) {
					$_POST['media'] = '';
					$_POST['source'] = $thumbnail;
				}
			}
			// walidacja linka do youtube'a
			if (isset($_POST['media']) && $_POST['media'] != '' && strstr($_POST['media'], 'youtube')) {
				$media = parse_url($_POST['media']);
				parse_str($media['query'], $yt);
			}
			if (!isset($yt['v'])) {
				$_POST['media'] = '';
			}
			$dataQ = array(
				'question' => $_POST['question'],
				'question_hash' => $_POST['question_hash'],
				'created' => date("Y-m-d H:i:s"),
				'source' => $_POST['source'],
				'media' => $_POST['media']
			);
			// 1.1 Jeżeli przesłano w formularzu id pytania, wyedytuj już istniejące
			if (isset($_POST['id'])) {
				// 1.1.1 Zapisywanie treści pytania
				$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $_POST['id']);
				$dbQuestions->update($dataQ, $where);
				// 1.1.2 Usuwanie zbędnych odpowiedzi
				if (isset($questionId)) {
					$answers = $dbAnswers->getAll($_POST['id']);
					if (count($answers) > count($_POST['answer_id'])) {
						foreach ($answers as $key => $answer) {
							if (!in_array($answer->id, $_POST['answer_id'])) {
								$where = $dbAnswers->getAdapter()->quoteInto('id = ?', $answer->id);
								$dbAnswers->delete($where);
							}
						}
					}
				}
				// 1.1.3 Zapisywanie odpowiedzi
				foreach ($_POST['answer'] as $key => $answer) {
					// sprawdź czy poprawna
					if ($_POST['correct'] == $key + 1) {
						$correct = 1;
					} else {
						$correct = 0;
					}
					// istniejące odpowiedzi zapisz, nowe utwórz
					if (isset($_POST['answer_id'][$key])) {
						$where = $dbAnswers->getAdapter()->quoteInto('id = ?', $_POST['answer_id'][$key]);
						$dataA = array(
							'answer' => $answer,
							'is_correct' => $correct,
							'probability' => 0
						);
						$dbAnswers->update($dataA, $where);
					} else {
						$dataA = array(
							'answer' => $answer,
							'question_id' => $_POST['id'],
							'is_correct' => $correct,
							'probability' => 0
						);
						$dbAnswers->insert($dataA, $where);
					}
				}
				// 1.1.4 Zapisywanie podpowiedzi eksperta
				$dataL = array(
					'lifebuoy' => $_POST['expert']
				);
				if (isset($_POST['expert_id'])) {
					$where = $dbLifeBuoys->getAdapter()->quoteInto('id = ?', $_POST['expert_id']);
					$dbLifeBuoys->update($dataL, $where);
				}
				// 1.1.5 Zapisywanie kategorii (poziom trudności, rodzaj szkoły...)
				// 1.1.5.1 Poziom trudności
				$questionLevels = $dbQuestionCategories->getByQuestionAndType($_POST['id'], 1);
				if (count($questionLevels) == 1) {
					$data = array(
						'category_id' => $_POST['level']
					);
					$where = $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $questionLevels[0]->id);
					$dbQuestionCategories->update($data, $where);
				} else {
					// jeżeli przypisano więcej niż jedne, usuń wszystkie
					if (count($questionLevels) > 1) {
						foreach ($questionLevels as $qL) {
							$where = $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $qL->id);
							$dbQuestionCategories->delete($where);
						}
					}
					// wstawianie poziomu trudności
					$data = array(
						'question_id' => $_POST['id'],
						'category_id' => $_POST['level']
					);
					$dbQuestionCategories->insert($data);
					unset($data);
				}
				// 1.1.5.2 Rodzaj szkoły
				// - po pierwsze sprawdzamy co jest w bazie danych i kasujemy nie potrzebne
				$questionSchools = $dbQuestionCategories->getByQuestionAndType($_POST['id'], 2);
				$schoolIds = array();
				if (!isset($_POST['school']) || count($_POST['school']) == 0) {
					foreach ($schools as $school) {
						if ($school->name == 'Wszystkie szkoły') {
							$data = array(
								'question_id' => $_POST['id'],
								'category_id' => $school->id
							);
							$dbQuestionCategories->insert($data);
							unset($data);
						}
					}
				} else {
					foreach ($questionSchools as $qSch) {
						$schoolIds[] = $qSch->category_id;
						if (!in_array($qSch->category_id, $_POST['school'])) {
							$where = $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $qSch->id);
							$dbQuestionCategories->delete($where);
						}

					}
					foreach ($_POST['school'] as $qSch) {
						if (!in_array($qSch, $schoolIds)) {
							$data = array(
								'question_id' => $_POST['id'],
								'category_id' => $qSch
							);
							$dbQuestionCategories->insert($data);
							unset($data);
						}
					}
				}
				// 1.1.5.3 Kategorie
				// - po pierwsze sprawdzamy co jest w bazie danych i kasujemy nie potrzebne
				$questionCategories = $dbQuestionCategories->getByQuestionAndType($_POST['id'], 3);
				$categoryIds = array();
				foreach ($questionCategories as $qCat) {
					$categoryIds[] = $qCat->category_id;
					if (!in_array($qCat->category_id, $_POST['category'])) {
						$where = $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $qCat->id);
						$dbQuestionCategories->delete($where);
					}
				}
				foreach ($_POST['category'] as $qCat) {
					if (!in_array($qCat, $categoryIds)) {
						$data = array(
							'question_id' => $_POST['id'],
							'category_id' => $qCat
						);
						$dbQuestionCategories->insert($data);
						unset($data);
					}
				}
			} else {
				// Zapisz pytania
				$newQuestion = array(
					'author_id' => $this->user->id,
					'question' => $_POST['question'],
					'question_hash' => $_POST['question_hash'],
					'media' => '',
					'source' => $_POST['source'],
					'created' => date("Y-m-d H:i:s"),
					'status' => 0,
					'flag' => 0,
					'flag_data' => ''
				);
				$dbQuestions->insert($newQuestion);
				$question = $dbQuestions->findByHash($_POST['question_hash']);
				// Zapisz odpowiedzi
				foreach ($_POST['answer'] as $key => $answer) {
					// sprawdź czy poprawna
					if ($_POST['correct'] == $key + 1) {
						$correct = 1;
					} else {
						$correct = 0;
					}
					$dataA = array(
						'answer' => $answer,
						'question_id' => $question->id,
						'is_correct' => $correct,
						'probability' => 0
					);
					$dbAnswers->insert($dataA, $where);
					unset($dataA);
				}
				// Zapisz podpowiedź eksperta
				$dataL = array(
					'question_id' => $question->id,
					'lifebuoy' => $_POST['expert'],
					'lifebuoy_type' => 1
				);
				$dbLifeBuoys->insert($dataL);
				unset($dataL);
				// Zapisz poziom trudności
				$data = array(
					'question_id' => $question->id,
					'category_id' => $_POST['level']
				);
				$dbQuestionCategories->insert($data);
				unset($data);
				// Zapisz rodzaj szkoły
				if (!isset($_POST['school']) || count($_POST['school']) == 0) {
					foreach ($schools as $school) {
						if ($school->name == 'Wszystkie szkoły') {
							$data = array(
								'question_id' => $question->id,
								'category_id' => $school->id
							);
							$dbQuestionCategories->insert($data);
							unset($data);
						}
					}
				} else {
					foreach ($_POST['school'] as $qSch) {
						$data = array(
							'question_id' => $question->id,
							'category_id' => $qSch
						);
						$dbQuestionCategories->insert($data);
						unset($data);
					}
				}
				// Zapisz kategorie
				foreach ($_POST['category'] as $qCat) {
					$data = array(
						'question_id' => $question->id,
						'category_id' => $qCat
					);
					$dbQuestionCategories->insert($data);
					unset($data);
				}
				$this->_redirect('/administrator/add-question/id/' . $question->id);
			}
			$this->view->komunikat = array('class' => 'komunikatOkay', 'text' => 'Pytanie zostało zapisane');
		}
		// 2. Jeżeli ustawiono id w parametrze wczytaj posta i przekaż zmienne do widoku
		if (isset($questionId)) {
			$question = $dbQuestions->findByQuestionId($questionId);
			// Zablokuj formularz w razie potrzeby
			// 1. Zablokuj nauczycielom możliwość edycji nieswoich pytań
			if ($this->user->user_role === 3) {
				if ($question->author_id != $this->user->id) {
					$this->view->locked = true;
				} else {
					$this->view->locked = false;
				}
			} else {
				$this->view->locked = false;
			}
			// 2. Zablokuj edycję zatwierdzonych pytań, nawet moderatorom
			if (isset($question->status) && $question->status === 10) {
				$this->view->locked = true;
			}

			// sprawdź czy obrazek jest przygotowany czy trzeba wygenerować miniaturkę
			if (
				isset($question->media) &&
				$question->media != '' &&
				!strstr($question->media, 'youtube') &&
				(
					stristr($question->media, 'bmp') ||
					stristr($question->media, 'gif') ||
					stristr($question->media, 'jpg') ||
					stristr($question->media, 'jpeg') ||
					stristr($question->media, 'png')
				)
			) {
				if ($thumbnail = $this->prepareImage($question->media, $question->question_hash)) {
					$question->media = '';
					$question->source = $thumbnail;
					$data = array(
						'media' => '',
						'source' => $thumbnail
					);
					$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $question->id);
					$dbQuestions->update($data, $where);
				}
			}

			$this->view->question = $question;
			if (isset($question->id)) {
				$answers = $dbAnswers->getAll($question->id);
				$this->view->answers = $answers;
				$questionCategories = $dbQuestionCategories->findAllByQuestionId($question->id);
				$qC = array();
				foreach ($questionCategories as $key => $cat) {
					$qC[$cat->category_id] = $cat->toArray();
				}
				$this->view->questionCategories = $qC;
				$expert = $dbLifeBuoys->findByQuestionId($question->id, 1);
				$this->view->expert = $expert;
			}
		}
		// przekaż odczytane zapisane zmienne zmienne do widoku
		$this->view->levels = $levels;
		$this->view->schools = $schools;
		$this->view->categories = $categories;
		if (isset($questionId)) {
			$dbUsers = new Model_User;
			$users = $dbUsers->getAll();
			$usersById = array();
			foreach ($users as $user) {
				$usersById[$user->id] = $user->toArray();
			}
			$this->view->users = $usersById;
		}
		if (isset($question->question_hash)) {
			$this->view->hash = $question->question_hash;
		} else {
			$this->view->hash = md5($this->user->email . time() . rand(1, 999999));
		}
	}

	public function deleteImageAction()
	{
		$this->_isRenderAction = false;
		$hash = $this->_request->getParam('hash');
		if (isset($hash)) {
			unlink('uploads/' . $hash . '.jpg');
			$this->_responseContent = '';
		}
	}

	protected function prepareImage($imageSource = false, $imageTarget = false)
	{
		if ($imageSource) {
			if (!$imageTarget) $imageTarget = md5($imageSource);
			include('js/uploadify/resize-class.php');
			$resizeObj = new resize($imageSource);
			$resizeObj->resizeImage(560, 348, 'crop');
			$resizeObj->saveImage('uploads/' . $imageTarget . '.jpg', 90);
			$resizeObj->resizeImage(1024, 768);
			$resizeObj->saveImage('uploads/' . $imageTarget . '_big.jpg', 90);
			return 'uploads/' . $imageTarget . '.jpg';
		} else {
			return false;
		}
	}

	public function deleteQuestionAction()
	{
		$dbQuestions = new Model_Question;
		$dbAnswers = new Model_Answer;
		$dbQuestionCategories = new Model_QuestionCategory;
		if (isset($_POST['id'])) {
			$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $_POST['id']);
			$dbQuestions->delete($where);
			$where = $dbAnswers->getAdapter()->quoteInto('question_id = ?', $_POST['id']);
			$dbAnswers->delete($where);
			$where = $dbQuestionCategories->getAdapter()->quoteInto('question_id = ?', $_POST['id']);
			$dbQuestionCategories->delete($where);
		}
		die;
	}

	public function changeQuestionStatusAction()
	{
		$questionId = $this->_request->getParam('id');
		$dbQuestions = new Model_Question;
		$question = $dbQuestions->findByQuestionId($questionId);
		if ($question->status === 10) {
			$status = 0;
		} else {
			$status = 10;
		}
		if (isset($status) && isset($questionId) && $this->user->user_role >= 4) {
			$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $questionId);
			$question = $dbQuestions->findByQuestionId($questionId);
			$flag_data = unserialize($question->flag_data);
			if (!isset($flag_data['status_history'])) $flag_data['status_history'] = array();
			if ($status != $question->status) {
				switch ($status) {
					case 10:
						$flag_data['status_history'][] = array('user_id' => $this->user->id, 'time' => time(), 'status' => 10);
						$data = array('status' => 10, 'flag_data' => serialize($flag_data));
						$dbQuestions->update($data, $where);
						break;
					case 0:
						$flag_data['status_history'][] = array('user_id' => $this->user->id, 'time' => time(), 'status' => 0);
						$data = array('status' => 0, 'flag_data' => serialize($flag_data));
						$dbQuestions->update($data, $where);
						break;
				}
				$question = $dbQuestions->findByQuestionId($questionId);
			}
			echo json_encode($question->toArray());
		}
		die;
	}

	public function listaUserowAction()
	{
		$dbUsers = new Model_User;
		$dbTests = new Model_Test;
		$dbQuestions = new Model_Question;
		$dbAttempts = new Model_Attempt;
		$dbUserRoles = new Model_UserRole;
		$page = $this->_request->getParam('page');
		$params = $this->_parseSearchParams($this->getRequest()->getParams());
		$this->ZendSession->moderationSearchParams = $params;		
		$this->view->roles = $dbUserRoles->getAll();
		$this->view->params = $params;
		$this->view->allUsers = $dbUsers->userSearch($params);
		$usersIds = array();
		foreach($this->view->allUsers as $key=>$user) {
			$usersIds[] = $user->id;
		}	

		// Pytania użytkowników
		if(count($usersIds)>0) {	
			$questions = $dbQuestions->getByUsersIds($usersIds);
			$user_questions = array();
			if(count($questions)>0) {
				foreach($questions as $key=>$question) {
					if(!isset($user_questions[$question->author_id])) $user_questions[$question->author_id] = array();
					$user_questions[$question->author_id][] = $question->id;
				}	
			}		
			// Testy użytkowników
			$tests = $dbTests->getByUsersIds($usersIds);
			$user_tests = array();
			$testsPasses = array();
			if(count($tests)>0) {
				foreach($tests as $key=>$test) {
					if(!isset($user_tests[$test->author_id])) $user_tests[$test->author_id] = array();
					$user_tests[$test->author_id][] = $test->id;
					$testsPasses[] = $test->pass;
				}	
			}
			// Rozwiązane testy użytkowników
			if(count($testsPasses) > 0) {		
				$tests_attempts = $dbAttempts->getByTestsPassesComplete($testsPasses);
				$tests_attempts_ids = array();
				if(count($tests_attempts)>0) {
					foreach($tests_attempts as $key=>$attempt) {
						if(!isset($tests_attempts_ids[$attempt->author_id])) $tests_attempts_ids[$attempt->author_id] = array();
						$tests_attempts_ids[$attempt->author_id][] = $attempt->id;						
					}
				}				
				$this->view->tests_attempts = $tests_attempts_ids;
			}
			// Testy rozwiązane przez użytkowinków
			// $users_attempts = $dbAttempts->getByUsersIds($usersIds);
			$users_attempts_ids = array();
			/*
			if(count($users_attempts)>0) {
				foreach($users_attempts as $key=>$attempt) {
					if(!isset($users_attempts_ids[$attempt->user_id])) $users_attempts_ids[$attempt->user_id] = array();
					$users_attempts_ids[$attempt->user_id][] = $attempt->id;
				}	
			}
			*/
		}
		$this->view->user_questions = $user_questions;
		$this->view->user_tests = $user_tests;
		$this->view->users_attempts = $users_attempts_ids;
	}

	public function pokazUseraAction()
	{
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$dbUsers = new Model_User;

		$user_id = $this->_request->getParam('id');
		$page = $this->_request->getParam('page');
		$delete = $this->_request->getParam('delete');
		$status = $this->_request->getParam('status');

		$selectedUser = $dbUsers->findById($user_id);

		if(isset($_POST['tests']) && count($_POST['tests'])>0) {
			$where = $dbTests->getAdapter()->quoteInto('id IN(?)', $_POST['tests']);
			$dbTests->delete($where);
			$this->view->komunikat = array('class' => 'komunikatOkay', 'text' => $this->view->translate('Usunięto %s quizów.',count($_POST['tests'])));
		}		

		$this->view->showOnlyTableBody = false;
		if (isset($delete) && $delete > 0) {
			$where = $dbTests->getAdapter()->quoteInto('id = ?', $delete);
			$dbTests->delete($where);
			$this->view->showOnlyTableBody = true;
		}
		if (isset($status) && $status > 0) {
			$test = $dbTests->getById($status);
			if ($test->status == 1) {
				$data = array('status' => 0);
				print_r($test->status);
			} else {
				$data = array('status' => 1);
			}
			$where = $dbTests->getAdapter()->quoteInto('id = ?', $status);
			$dbTests->update($data, $where);
			$this->view->showOnlyTableBody = true;
		}

		$allTests = $dbTests->getTestList($user_id, $page);
		$attemptsCount = array();
		foreach ($allTests as $test) {
			$attempts = $dbAttempts->getByTestPass($test->pass);
			$attemptsCount[$test->pass] = count($attempts);
		}
		$this->view->allTests = $allTests;
		$this->view->selectedUser = $selectedUser;
		$this->view->user_id = $user_id;
		$this->view->attemptsCount = $attemptsCount;
		$this->view->page = $page;
	}

	public function convertToLowercaseAction() {
		$dbUsers = new Model_User;
		$users = $dbUsers->getAll();
		$users_emails = array();
		foreach($users as $key=>$value) {
			$users_emails[] = $value->email;
		}
		$converted = array();
		foreach($users_emails as $email) {
			if($email != strtolower($email)) {
				$u = $dbUsers->findByMail($email);
				if(in_array(strtolower($email),$users_emails)) {
					if($this->moveAccountData($u->id)) {
						$converted[] = $email;
					}
				} else {
					$data = array(
						'email' => strtolower($email)						
					);
					$where = $dbUsers->getAdapter()->quoteInto('id = ?', $u->id);
					$dbUsers->update($data, $where);
					$converted[] = $email;
				}
			}
		}
		echo '<pre>';
		print_r($converted);
		die;
	}

	public function moveAccountData($user_id)		
	{
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$dbUsers = new Model_User;
		$dbQuestions = new Model_Question;
		$selectedUser = $dbUsers->findById($user_id);
		$secondAccount = $dbUsers->findByMail(strtolower($selectedUser->email));
		if(isset($secondAccount->id)) {
			$tests = $dbTests->getByUserId($selectedUser->id);
			if(count($tests)>0) {
				foreach($tests as $t) {
					$data = array("author_id"=>$secondAccount->id);
					$where = $dbTests->getAdapter()->quoteInto('id = ?', $t->id);
					$dbTests->update($data, $where);
				}
			}
			$questions = $dbQuestions->getByUserId($selectedUser->id);
			if(count($questions)>0) {
				foreach($questions as $q) {
					$data = array("author_id"=>$secondAccount->id);
					$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $q->id);
					$dbQuestions->update($data, $where);
				}
			}
			$attempts = $dbAttempts->getByUserId($selectedUser->id);
			if(count($attempts)>0) {
				foreach($attempts as $a) {
					$data = array("user_id"=>$secondAccount->id);
					$where = $dbAttempts->getAdapter()->quoteInto('id = ?', $a->id);
					$dbAttempts->update($data, $where);
				}
			}
			$where = $dbUsers->getAdapter()->quoteInto('id = ?', $user_id);
			$dbUsers->delete($where);
			return true;
		} else {
			return false;
		}
	}

	public function moveAccountDataAction()		
	{
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$dbUsers = new Model_User;
		$dbQuestions = new Model_Question;
		$user_id = $this->_request->getParam('id');
		$selectedUser = $dbUsers->findById($user_id);
		$secondAccount = $dbUsers->findByMail(strtolower($selectedUser->email));
		if(isset($secondAccount->id)) {
			echo $this->view->translate('Znaleziono nowe konto.')."<br\n>";
			$tests = $dbTests->getByUserId($selectedUser->id);
			if(count($tests)>0) {
				foreach($tests as $t) {
					$data = array("author_id"=>$secondAccount->id);
					$where = $dbTests->getAdapter()->quoteInto('id = ?', $t->id);
					$dbTests->update($data, $where);
				}
			}
			$questions = $dbQuestions->getByUserId($selectedUser->id);
			if(count($questions)>0) {
				foreach($questions as $q) {
					$data = array("author_id"=>$secondAccount->id);
					$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $q->id);
					$dbQuestions->update($data, $where);
				}
			}
			$attempts = $dbAttempts->getByUserId($selectedUser->id);
			if(count($attempts)>0) {
				foreach($attempts as $a) {
					$data = array("user_id"=>$secondAccount->id);
					$where = $dbAttempts->getAdapter()->quoteInto('id = ?', $a->id);
					$dbAttempts->update($data, $where);
				}
			}
			echo $this->view->translate('Przeniesiono ').count($tests).$this->view->translate(' testów oraz %s pytań na konto ',count($questions)).'<a href="'.$this->view->baseUrl('/administrator/pokaz-usera/id/'.$secondAccount->id).'"'.$secondAccount->email."</a><br\n>";
		} else {
			echo $this->view->translate('Nie znaleziono drugiego konta.')."<br\n>";
		}
		die;
	}

	public function pokazTestAction()
	{
		session_write_close();
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$testPass = $this->_request->getParam('id');
		$dbUser = new Model_User;
		$user = $dbUser->findByMail($this->ZendSession->OPENID['email']);
		if ($test = $dbTests->getByPass($testPass)) {
			if ($user->user_role > 3) {
				$this->view->selectedUser = $dbUser->findById($test->author_id);
				$this->attemptsCleaner($test->id);
				$allPlayers = $dbAttempts->getAllHighScores($test->pass)->toArray();
				$test->categories = unserialize($test->categories);
				$kategorie = array();
				$levels = array();
				$dbCategory = new Model_Category;
				foreach ($test->categories as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $v) {
							$kategoria = $dbCategory->findById($v);
							$levels[] = $kategoria->name;
						}
					} else {
						$kategoria = $dbCategory->findById($value);
						if ($key === 1) {
							$szkola = $kategoria->name;
						} else {
							$kategorie[] = $kategoria->name;
						}
					}
				}
				$allPlayersSorted = array();
				$allPlayersById = array();
				foreach($allPlayers as $key=>$player) {
					$player['lifebuoys'] = unserialize($player['lifebuoys']);
					$allPlayers[$key]['lifebuoys'] = $player['lifebuoys'];
					$allPlayers[$key]['points'] = $player['points'];
					$extraPoints = 15;
					if(is_array($player['lifebuoys']) && count($player['lifebuoys'])>0) {
						foreach($player['lifebuoys'] as $lifebuoy) {
							$extraPoints = $extraPoints-5;
						}
					}
					$allPlayers[$key]['points_brutto'] = $player['points']+$extraPoints;
					$allPlayersById[$player['id']] = $allPlayers[$key];
					$answers_time = unserialize($player['answers_time']);					
					if(count($answers_time)==10) {
						$time_left = $test->time*60 - ($answers_time[9]['server'] - $player['server_started']);
					} else {
						$time_left = $test->time*60 - ($player['server_finished'] - $player['server_started']);
					}					
					if($time_left < 0 ) {
						$time_left = 0;
					}
					if($time_left > $test->time*60) {
						$time_left = $test->time*60;
					}
					$allPlayersSorted[] = array(
						'points' => $allPlayers[$key]['points_brutto'],
						'points_netto' => $allPlayers[$key]['points'],
						'time_left' => $time_left,
						'time_started' => $player['time_started'],
						'time_finished' => $player['time_finished'],
						'server_started' => $player['server_started'],
						'server_finished' => $player['server_finished'],
						'id' => $player['id']
					);
				}

				function sortujWyniki($a, $b)
				{
					if ($a == $b) {
						return 0;
					}
					return ($a > $b) ? -1 : 1;
				}

				usort($allPlayersSorted,"sortujWyniki");

				if($this->user->id<10) {
					// echo '<pre>'; print_r($allPlayersSorted); die;
				}

				$this->view->test = $test;
				$this->view->levels = $levels;
				$this->view->school = $szkola;
				$this->view->kategorie = $kategorie;
				$this->view->allPlayers = $allPlayersById;
				$this->view->allPlayersSorted = $allPlayersSorted;
			} else {
				$this->_redirect('/administrator/');
			}
		} else {
			$this->_redirect('/administrator/');
		}
	}

	public function importEpuAction()
	{
		$googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');

		if (!$googleapps['json_link']) die("No googleapps.json_link config.\n");

		$token = '/links/all';
		$pos = strpos($googleapps['json_link'], $token);
		$link = substr($googleapps['json_link'], 0, $pos + strlen($token) - 3);

		$email = $googleapps['admin_mail'] ? : 'admin@game.pl';
		$id = $googleapps['id'] ? : 'game';

		$link .= 'router/mail/' . $email . '/sig/' . GN_User::getSig($email, $googleapps['json_hash']) . '/dest/epu/id/' . $id . '/path/';
		$request = json_decode(file_get_contents($link . 'synchronize,g'));

		if ($request->success == '1') {
			$hash = $request->hash;
			$file = $link . 'base64:' . base64_encode('download-package/' . $hash);
			$bezpiecznik = 600;
			sleep(10);
			while (true) {
				$result = file_get_contents($file);

				if (strlen($result) < 1000) {
					$res = json_decode($result);
					// print_r($res);
					echo date('d-m-Y H:i:s ... ') . $res->log . "\n";
					flush();
					ob_flush();
					// sleep(10);
					$bezpiecznik -= 10;
					if ($bezpiecznik <= 0) break;
					continue;
				}

				$zip = new Zend_Filter_Compress_Zip();
				$tmp = sys_get_temp_dir() . '/' . time() . rand();
				mkdir($tmp);
				$file = $tmp . '/.epu.zip';

				file_put_contents($file, $result);
				$zip->setTarget($tmp);
				// $zip->decompress($file);
				unlink($file);

				$zips = array();
				$dh = opendir($tmp);
				while (($file = readdir($dh)) !== false) {
					if ($file[0] == '.') continue;
					$zips[] = $tmp . '/' . $file;
				}
				closedir($dh);

				foreach ($zips AS $zipfile) {
					$zip->decompress($zipfile);
					// unlink($zipfile);
					echo "Rozpakowano $zipfile\n";
				}

				echo json_encode($zips);

				break;
			}
		}
		else {
			die($request->log . "\n");
		}

		die();
	}

	const CSV_DELIMITER = ';';

	/**
	 * @var array
	 */
	private $_paramsTables = array(
		'tutorial',
	);

	/**
	 * @var Zend_Db_Table
	 */
	private $_paramsModel;

	/**
	 * @var array
	 */
	private $_paramsColumns;

	/**
	 * @var array
	 */
	private $_paramsData;

	/**
	 * @var array
	 */
	private $_paramsComments;

	public function exportParamsAction()
	{
		$this->view->paramsTables = $this->_paramsTables;

		if (($tableName = $this->_request->getParam('table', null)) !== null) {
			if (!in_array($tableName, $this->_paramsTables)) {
				throw new Exception('Invalid params table name');
			}
			$modelClass = 'Model_' . implode('', array_map('ucfirst', explode('_', $tableName)));
			if (!class_exists($modelClass) && !class_exists($modelClass = 'Millionaire_' . $modelClass)) {
				throw new Exception('Could not find model for table "' . $tableName . '"');
			}
			$this->_paramsModel = new $modelClass();
			$data = $this->_paramsModel->fetchAll()->toArray();
			$columns = $this->_paramsModel->info('cols');
			array_unshift($data, array($tableName), array(''), $columns);
			$fileName = $tableName . '.csv';
			$filePath = APPLICATION_PATH . '/cache/' . $fileName;
			$file = fopen($filePath, 'w');
			foreach ($data as $line) {
				fputcsv($file, $line, self::CSV_DELIMITER);
			}
			fclose($file);
			$content = file_get_contents($filePath);
			@unlink($filePath);
			header("Content-Type: text/csv");
			header("Content-Disposition: inline; filename=$fileName");
			die($content);
		}
	}

	public function importParamsAction()
	{
		$adapter = new Zend_File_Transfer_Adapter_Http();
		if ($adapter->isUploaded()) {
			$adapter->addValidator('Extension', true, 'csv');
			$fileinfo = $adapter->getFileInfo();
			$file = array_shift($fileinfo);

			if ($adapter->isValid($file['name']) && $adapter->receive($file['name'])) {
				$filename = $adapter->getDestination() . '/' . $file['name'];
				$this->_readParams($filename);
				$this->_importParams();
			}
		}
		$this->_redirectBack();
	}

	/**
	 * Read params file.
	 * First line must be table name,
	 * Second line are comments [optional],
	 * Third line must be column names,
	 * the rest are actual params.
	 *
	 * @param string $filename
	 *
	 * @return void
	 */
	private function _readParams($filename)
	{
		if (!is_readable($filename) || ($handle = fopen($filename, 'r')) === false) {
			throw new Exception('Reading params file failed');
		}
		$counter = 0;
		$params = array();
		// SIM read each line in params file
		while (($line = fgetcsv($handle, null, ';')) !== false) {
			$params[$counter++] = $line;
		}
		$tableName = array_shift(array_shift($params));
		if (!in_array($tableName, $this->_paramsTables)) {
			throw new Exception('Table "' . $tableName . '" is not allowed for editing');
		}
		$modelClass = 'Model_' . implode('', array_map('ucfirst', explode('_', $tableName)));
		if (!class_exists($modelClass)) {
			throw new Exception('Class "' . $modelClass . '" does not exists');
		}
		$this->_paramsModel = new $modelClass();
		if (!($this->_paramsModel instanceof Zend_Db_Table_Abstract)) {
			throw new Exception(
				'Model "' . get_class($this->_paramsModel) . '" is not an instance of Zend_Db_Table_Abstract class');
		}
		// SIM second line - comments
		$comments = array_shift($params);
		// SIM third line is column definition
		$columns = array_shift($params);
		if (empty($params)) {
			throw new Exception('Params data is empty');
		}
		foreach ($columns as $k => $col) {
			// SIM remove empty columns
			if (empty($col)) {
				unset($columns[$k]);
				// SIM also remove corresponding columns in params
				$params = array_map(
					function($val) use ($k)
					{
						unset($val[$k]);
						return $val;
					}, $params
				);
			}
		}
		if (count($columns) != count(current($params))) {
			throw new Exception('Params columns count and columns count are not equal');
		}
		reset($params);

		$this->_paramsComments = $comments;
		$this->_paramsColumns = $columns;
		$this->_paramsData = $params;
	}

	/**
	 * Import params into DB using model.
	 */
	private function _importParams()
	{
		$tableName = $this->_paramsModel->info('name');

		//RB not possible to import table 'log'
		if ($tableName == 'log') {
			return;
		}

		$db = $this->_paramsModel->getDefaultAdapter();
		$db->beginTransaction();
		$counter = 0;

		$tablePrimaryKey = array_shift($this->_paramsModel->info('primary'));
		try {
			foreach ($this->_paramsData as $values) {
				// SIM combine data for single row
				$data = array_combine($this->_paramsColumns, $values);
				// SIM get primary key value
				if (array_key_exists($tablePrimaryKey, $data)) {
					$id = $data[$tablePrimaryKey];
				} else {
					reset($data);
					$id = current($data);
				}
				foreach ($data as $k => $d) {
					if (empty($d) && $d !== 0) {
						$data[$k] = null;
					}
				}
				;
				// SIM if param exists, update it; insert new row otherwise
				if (!$id || ($param = $this->_paramsModel->find($id)->current()) == null) {
					$param = $this->_paramsModel->createRow();
				}
				$param->setFromArray($data);
				$param->save();
				$counter++;
			}
			$db->commit();
			$this->_flash('params have been succesfuly imported into table %s', $tableName);
		}
		catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		if ($counter != $this->_paramsModel->fetchAll()->count()) {
			$this->_flash('notice that not all records have been updated');
		}
	}

	public function statystykiAction() {
		$dbTests = new Model_Test;
		$testy = $dbTests->getAll();	
		$testy_tabela = array();
		foreach($testy as $t) {
			$rok = date('Y',strtotime($t->created));
			$miesiac = date('m',strtotime($t->created));
			if(!isset($testy_tabela[$rok][$miesiac])) $testy_tabela[$rok][$miesiac] = array();
			$testy_tabela[$rok][$miesiac][] = $t->id;
		}
		$this->view->testy = $testy;
		$this->view->testy_tabela = $testy_tabela;
		$dbAttempts = new Model_Attempt;
		$attempts = $dbAttempts->getAll();	
		$attempts_tabela = array();
		foreach($attempts as $a) {
			$rok = date('Y',strtotime($a->created));
			$miesiac = date('m',strtotime($a->created));
			if(!isset($attempts_tabela[$rok][$miesiac])) $attempts_tabela[$rok][$miesiac] = array();
			$attempts_tabela[$rok][$miesiac][] = $t->id;
		}
		$this->view->attempts = $attempts;
		$this->view->attempts_tabela = $attempts_tabela;
	}

	public function categoriesAction()
	{
		$dbCategory = new Model_Category;
		$categories = $dbCategory->findByType(3,true);
		$this->view->categories = array();
		foreach($categories as $c) {
			if($c->parent_id == 0) {
				$this->view->categories[$c->id]['name'] = $c->name;
			} else {
				$this->view->categories[$c->parent_id]['subcategories'][$c->id] = $c->name;
			}
		}
	}

	public function categoryActivateAction()
	{
		$category_id = $this->_request->getParam('id');
		$dbCategory = new Model_Category;
		$dbQuestion = new Model_Question;
		$dbQuestionCategories = new Model_QuestionCategory;
		$category = $dbCategory->getById($category_id);
		if(isset($category->id)) {
			$questions_ids = array();
			if($category->parent_id == 0) {
				$child_categories = $dbCategory->findByParent($category->id);
				foreach($child_categories as $category) {
					$questions = $dbQuestionCategories->getByCategoryId($category->id);
					foreach($questions as $q) {
						$questions_ids[] = $q->question_id;
					}
				}				
			} else {
				$questions = $dbQuestionCategories->getByCategoryId($category_id);
				foreach($questions as $q) {
					$questions_ids[] = $q->question_id;
				}
			}
			if(count($questions_ids)>0) {
				$this->view->count = count($questions_ids);
				$data = array(
					'status' => 10
				);
				$where = $dbQuestion->getAdapter()->quoteInto('id IN(?)', $questions_ids);
				$dbQuestion->update($data,$where);
			}
		}
	}

	public function fixAction() {
		$mCategory = new Model_Category;
		$category = $mCategory->getById(6);
		echo '<pre>';
		if(!isset($category->id)) {
			$mCategory->insert(array(
				'id' => 6,
				'parent_id' => 0,
				'name' => "Przedsiębiorczość",
				'category_type_id' => 3,
				'user_id' => 0,
				'status' => 0
			));
		} else {
			$mCategory->update(array(
				'parent_id' => 0,
				'name' => "Przedsiębiorczość",
				'category_type_id' => 3,
				'user_id' => 0,
				'status' => 0
			),$mCategory->getAdapter()->quoteInto('id IN(?)', 6));
			print_r($category->toArray());
		}
		die;
	}

}
