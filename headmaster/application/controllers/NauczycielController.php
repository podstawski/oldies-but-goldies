<?php

require_once __DIR__.'/../../library/Millionaire-quiz/application/controllers/NauczycielController.php';


class NauczycielController extends MillionaireNauczycielController
{
	public function init()
	{
		parent::init();

		$this->view->actionName = $this->getRequest()->getActionName();		
		if($this->view->actionName != 'old-browser' && $this->view->actionName != 'java-script-disabled') {
				$browser = $this->getBrowser();
				if($browser['name'] == 'Internet Explorer' && $browser['version'] < 8) {
					$this->_redirect('index/old-browser');
				}
		}

		$this->checkTeacher();
		$this->checkToken();
		if ($this->user) {
		    $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
            if (isset($googleapps['json_link']) && isset($googleapps['json_hash'])) {
                $this->observer = new GN_Observer(
                    $googleapps['json_link'],
                    $googleapps['json_hash'],
                    $this->user->email,
                    Zend_Registry::get('Zend_Locale')->getLanguage(),
	                    'headmaster'
				);
            }
		}
	}

	public function getCustomCategoriesParentCategoryId() {
		$dbCategory = new Model_Category;
		$root_categories = $dbCategory->findByParentTypeName(0, 3);
		$CUSTOM_CATEGORIES_PARENT_CATEGORY_ID = 0;
		$CUSTOM_CATEGORIES_PARENT_CATEGORY_NAME = $this->user->email;
		if(count($root_categories)>1) {
			foreach($root_categories as $cat) {
				if($cat->name == $CUSTOM_CATEGORIES_PARENT_CATEGORY_NAME) {
					$CUSTOM_CATEGORIES_PARENT_CATEGORY_ID = $cat->id;
				}
			}
		}
		if($CUSTOM_CATEGORIES_PARENT_CATEGORY_ID == 0) {
			$CUSTOM_CATEGORIES_PARENT_CATEGORY_ID = $dbCategory->insert(array(
				'parent_id' => 0,
				'name' => $CUSTOM_CATEGORIES_PARENT_CATEGORY_NAME,
				'category_type_id' => 3
			));
		}
		return $CUSTOM_CATEGORIES_PARENT_CATEGORY_ID;
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
		$root_categories = $dbCategory->findByParentTypeName(0, 3);
		$CUSTOM_CATEGORIES_PARENT_CATEGORY_ID = $this->getCustomCategoriesParentCategoryId();
		$this->view->own_categories = $dbCategory->findByParentTypeName($CUSTOM_CATEGORIES_PARENT_CATEGORY_ID, 3);
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
		// 0. Zapisz uwagi do pytania (oflaguj)
		if (isset($_POST['comment']) && isset($_POST['id'])) {
			$question = $dbQuestions->findByQuestionId($questionId);
			$flag_data = unserialize($question->flag_data);
			$comment = array(
				'user_id' => $this->user->id,
				'comment' => $_POST['comment'],
				'time' => time()
			);
			$flag_data = unserialize($question->flag_data);
			if (!isset($flag_data['comments'])) {
				$flag_data['comments'] = array();
			}
			$flag_data['comments'][] = $comment;
			$dataQ = array(
				'flag' => 10,
				'flag_data' => serialize($flag_data)
			);
			$where = $dbQuestions->getAdapter()->quoteInto('id = ?', $_POST['id']);
			$dbQuestions->update($dataQ, $where);
			$this->view->komunikat = array('class' => 'komunikatOkay', 'text' => 'Uwagi zostały zapisane');
		}
		// 1. Jeżeli przesłano dane z formularza zapisz je
		if (isset($_POST['question'])) {
			// zaktualizuj listę wlasnych kategorii
			foreach ($_POST['own_category_name'] as $key=>$own_category_name) {					
				if ($own_category_name == '' && isset($_POST['own_category_id'][$key]) && $_POST['own_category_id'][$key]>0 ) {
					$own_category_name = $this->view->translate('Moja kategoria #%s',($key+1));
					$_POST['own_category_name'][$key] = $own_category_name;
				}
				if ($own_category_name != '' && isset($_POST['own_category_id'][$key]) && $_POST['own_category_id'][$key] == 0) {
					$category_id = $dbCategory->insert(array(
						'name' => $own_category_name,
						'parent_id' => $CUSTOM_CATEGORIES_PARENT_CATEGORY_ID,
						'category_type_id' => 3,
					));
					$_POST['own_category_id'][$key] = $category_id;
					foreach($_POST['category'] as $k=>$c) {
						if($c == 0) {
							$_POST['category'][$k] = $category_id;
						}
					}
				} elseif ($own_category_name != '' && isset($_POST['own_category_id'][$key]) && $_POST['own_category_id'][$key]>0 ) {
					$dbCategory->update(array(
						'name' => $own_category_name
					),$dbCategory->getAdapter()->quoteInto('id = ?', $_POST['own_category_id'][$key]));
				}
			}
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
						$dbAnswers->update(array(
							'answer' => $answer,
							'is_correct' => $correct,
							'probability' => 0
						), $dbAnswers->getAdapter()->quoteInto('id = ?', $_POST['answer_id'][$key]));
					} else {
						$dbAnswers->insert(array(
							'answer' => $answer,
							'question_id' => $_POST['id'],
							'is_correct' => $correct,
							'probability' => 0
						));
					}
				}
				// 1.1.4 Zapisywanie podpowiedzi eksperta
				if (isset($_POST['expert_id'])) {
					$dbLifeBuoys->update(array(
						'lifebuoy' => $_POST['expert'],
						'question_id' => $_POST['id']
					), $dbLifeBuoys->getAdapter()->quoteInto('id = ?', $_POST['expert_id']));
				} else {
					$dbLifeBuoys->insert(array(
						'lifebuoy' => $_POST['expert'],
						'question_id' => $_POST['id'],
						'lifebuoy_type' => 1
					));
				}
				// 1.1.5 Zapisywanie kategorii (poziom trudności, rodzaj szkoły...)
				// 1.1.5.1 Poziom trudności
				$questionLevels = $dbQuestionCategories->getByQuestionAndType($_POST['id'], 1);
				if (count($questionLevels) == 1) {
					$dbQuestionCategories->update(array(
						'category_id' => $_POST['level']
					), $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $questionLevels[0]->id));
				} else {
					// jeżeli przypisano więcej niż jedne, usuń wszystkie
					if (count($questionLevels) > 1) {
						foreach ($questionLevels as $qL) {
							$dbQuestionCategories->delete($dbQuestionCategories->getAdapter()->quoteInto('id = ?', $qL->id));
						}
					}
					// wstawianie poziomu trudności
					$dbQuestionCategories->insert(array(
						'question_id' => $_POST['id'],
						'category_id' => $_POST['level']
					));
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
						$dbQuestionCategories->insert(array(
							'question_id' => $_POST['id'],
							'category_id' => $qCat
						));
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
				$question_id = $dbQuestions->insert($newQuestion);
				$question = $dbQuestions->getById($question_id);

				if(isset($question_id)) {
					// Zapisz odpowiedzi
					foreach ($_POST['answer'] as $key => $answer) {
						// sprawdź czy poprawna
						if ($_POST['correct'] == $key + 1) {
							$correct = 1;
						} else {
							$correct = 0;
						}
						$dbAnswers->insert(array(
							'answer' => $answer,
							'question_id' => $question_id,
							'is_correct' => $correct,
							'probability' => 0
						));
					}
					// Zapisz podpowiedź eksperta
					$dbLifeBuoys->insert(array(
						'question_id' => $question_id,
						'lifebuoy' => $_POST['expert'],
						'lifebuoy_type' => 1
					));
					// Zapisz poziom trudności
					$dbQuestionCategories->insert(array(
						'question_id' => $question_id,
						'category_id' => $_POST['level']
					));
					// Zapisz rodzaj szkoły
					if (!isset($_POST['school']) || count($_POST['school']) == 0) {
						foreach ($schools as $school) {
							if ($school->name == 'Wszystkie szkoły') {
								$dbQuestionCategories->insert(array(
									'question_id' => $question_id,
									'category_id' => $school->id
								));
							}
						}
					} else {
						foreach ($_POST['school'] as $qSch) {
							$dbQuestionCategories->insert(array(
								'question_id' => $question_id,
								'category_id' => $qSch
							));
						}
					}
					// Zapisz kategorie
					// echo '<pre>'; print_r($_POST); die;
					foreach ($_POST['category'] as $qCat) {
						$dbQuestionCategories->insert(array(
							'question_id' => $question_id,
							'category_id' => $qCat
						));
					}
				}
				$this->_redirect('/nauczyciel/add-question/id/' . $question_id);
			}
			$this->view->own_categories = $dbCategory->findByParentTypeName($CUSTOM_CATEGORIES_PARENT_CATEGORY_ID, 3);
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
			if ($question->status === 10) {
				$this->view->locked = true;
			}
			$this->view->question = $question;
			if (isset($question->id)) {
				$answers = $dbAnswers->getAll($question->id);
				$this->view->answers = $answers;
				$questionCategories = $dbQuestionCategories->findAllByQuestionId($question->id);
				$qC = array();
				foreach ($questionCategories as $key => $cat) {
					if($cat->category_id > 0) {
						$qC[$cat->category_id] = $cat->toArray();
					} else {
						$where = $dbQuestionCategories->getAdapter()->quoteInto('id = ?', $cat->id);
						$dbQuestionCategories->delete($where);								
					}
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

	public function nowyTestAction()
	{
		$dbCategory = new Model_Category;
		$dbQuestionCategories = new Model_QuestionCategory;

		$this->view->chapters = $dbCategory->findByParentTypeName(6, 3);
		$this->view->szkoly = $dbCategory->findByType(2);
		$this->view->categoriesCount = $dbQuestionCategories->countQuestions(10,null,$this->user->id);	
		$this->view->pass = $this->passGen();
		$this->view->time = 15;

		$CUSTOM_CATEGORIES_PARENT_CATEGORY_ID = $this->getCustomCategoriesParentCategoryId();
		$this->view->own_categories = $dbCategory->findByParentTypeName($CUSTOM_CATEGORIES_PARENT_CATEGORY_ID, 3);
		
	}

	public function debugTestAction()
	{
		$dbTests = new Model_Test;
		$testPass = $this->_request->getParam('id');
		$test = $dbTests->getByPass($testPass);
		$t = $test->toArray();		
		$t['questions'] = unserialize($t['questions']);
		sort($t['questions']);
		echo '<pre>';
		print_r($t);
		die;
	}
	
}
