<?php

require_once 'MillionaireController.php';

class MillionaireNauczycielController extends MillionaireController
{
	/**
	 * @var bool
	 */
	protected $_isRenderAction = true;

	/**
	* @var GN_Observer
	*/
	protected $observer;

	public function init()
	{
		parent::init();
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

	public function postDispatch()
	{
		if (!$this->_isRenderAction) {
			$this->_response->setHttpResponseCode(200);
			$this->_response->setBody(json_encode($this->_responseContent));
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
		}
		parent::postDispatch();
	}

	public function indexAction()
	{
		session_write_close();
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;

		$page = $this->_request->getParam('page');
		$delete = $this->_request->getParam('delete');
		$status = $this->_request->getParam('status');
		if (isset($delete) && $delete > 0) {
			$where = $dbTests->getAdapter()->quoteInto('id = ?', $delete);
			$dbTests->delete($where);
		}
		if (isset($status) && $status > 0) {
			$test = $dbTests->getById($status);
			if ($test->status == 1) {
				$data = array('status' => 0);
			} else {
				$data = array('status' => 1);
			}
			$where = $dbTests->getAdapter()->quoteInto('id = ?', $status);
			$dbTests->update($data, $where);
		}
		$allTests = $dbTests->getTestList($this->user->id, $page);
		$attemptsCount = array();
		foreach ($allTests as $test) {
			$attempts = $dbAttempts->getByTestPass($test->pass);
			$attemptsCount[$test->pass] = count($attempts);
		}
		$this->view->allTests = $allTests;
		$this->view->attemptsCount = $attemptsCount;
		$this->view->page = $page;
	}

	public function nowyTestAction()
	{
		$dbCategories = new Model_Category;
		$dbQuestionCategories = new Model_QuestionCategory;

		$szkoly = $dbCategories->findByType(2);
		$chapters = $dbCategories->findByParentTypeName(6,3);

		$this->view->chapters = $chapters;
		$this->view->szkoly = $szkoly;
		$this->view->categoriesCount = $dbQuestionCategories->countQuestions(10,null,$this->user->id);
		$this->view->pass = $this->passGen();
		$this->view->time = 15;
	}

	public function testAction() {
		$dbQuestionCategories = new Model_QuestionCategory;
		$this->view->categoriesCount = $dbQuestionCategories->countQuestions(10,null,$this->user->id);
		echo '<pre>';
		print_r($this->view->categoriesCount);
		die;
	}

	public function jsonCategoriesCommonPartAction()
	{
		$_POST['category'][0] = explode(',', $_POST['category'][0]);
		if (isset($_POST['category'][0]) && isset($_POST['category'][1]) && $_POST['category'][0]!='' && $_POST['category'][1]!='') {
			if(isset($_POST['user_id']) && $_POST['user_id'] != 'false') {
				$user_id = $_POST['user_id'];				
			} else {
				$user_id = false;
			}
			if(isset($_POST['status']) && $_POST['status'] != 'false') {
				$status = $_POST['status'];
			} else {
				$status = false;
			}
			$dbQuestionCategory = new Model_QuestionCategory;			
			if (is_array($_POST['category'][0])) {
				$questions = array(
					'questions' => array(),
					'categories' => array()
				);
				foreach ($_POST['category'][0] as $poziomTrudnosci) {
					$q = $dbQuestionCategory->categoriesCommonPart(array($poziomTrudnosci, $_POST['category'][1]), $user_id, $status);
					$questions['questions'] = array_merge($questions['questions'], $q['questions']);
					foreach ($q['categories'] as $cat_id => $cat_count) {
						if (isset($questions['categories'][$cat_id])) {
							$questions['categories'][$cat_id] = $questions['categories'][$cat_id] + $cat_count;
						} else {
							$questions['categories'][$cat_id] = $cat_count;
						}
					}
				}
			} else {
				$questions = $dbQuestionCategory->categoriesCommonPart($_POST['category'], $user_id, $status);
			}
			echo json_encode(array('count' => count($questions['questions']), 'questions' => $questions['questions'], 'categories' => $questions['categories']));
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
		if (!isset($id)) $this->_redirect('/nauczyciel/all-questions');
		$this->_forward('add-question');
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
		if (!isset($id)) $this->_redirect('/nauczyciel/check-questions');
		$this->_forward('add-question');
	}

	public function checkQuestionsAction()
	{
		$this->view->actionName = $this->getRequest()->getActionName();
		$this->view->showLink = 'check-question';
		$this->_forward('all-questions');
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
				if($this->app['expert_mode'] == 1 && isset($_POST['expert'])) {
					// 1.1.4 Zapisywanie podpowiedzi eksperta
					$dataL = array(
						'lifebuoy' => $_POST['expert']
					);
					if (isset($_POST['expert_id'])) {
						$where = $dbLifeBuoys->getAdapter()->quoteInto('id = ?', $_POST['expert_id']);
						$dbLifeBuoys->update($dataL, $where);
					}
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
				if($this->app['expert_mode'] == 1 && isset($_POST['expert'])) {
				// Zapisz podpowiedź eksperta
					$dataL = array(
						'question_id' => $question->id,
						'lifebuoy' => $_POST['expert'],
						'lifebuoy_type' => 1
					);
					$dbLifeBuoys->insert($dataL);
					unset($dataL);
				}
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
				$this->_redirect('/nauczyciel/add-question/id/' . $question->id);
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
					$qC[$cat->category_id] = $cat->toArray();
				}
				$this->view->questionCategories = $qC;
				if($this->app['expert_mode'] == 1) {
					$expert = $dbLifeBuoys->findByQuestionId($question->id, 1);
					$this->view->expert = $expert;
				}
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
			$resizeObj->saveImage('uploads/' . $imageTarget . '.jpg', 80);
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

	public function statystykiAction()
	{
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$dbAnswer = new Model_Answer;
		$dbQuestion = new Model_Question;
		$testPass = $this->_request->getParam('id');
        $page = $this->_request->getParam('page');
		$dbUser = new Model_User;
		$user = $dbUser->findByMail($this->ZendSession->OPENID['email']);
		if ($test = $dbTests->getByPass($testPass)) {
			if ($test->author_id == $user->id || $user->user_role > 3) {
				$this->attemptsCleaner($test->id);
				$allPlayers = $dbAttempts->getHighScores($test->pass,$page);
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
				$this->view->test = $test;
				$this->view->levels = $levels;
				$this->view->school = $szkola;
				$this->view->kategorie = $kategorie;
				$this->view->allPlayers = $allPlayers;
				$wrongAnswers = array();
				foreach ($allPlayers as $attempt) {
					$answers = unserialize($attempt->answers);
					foreach ($answers as $key => $answer) {
						if ($answer['correct'] == 0) {
							$answer_in_db = $dbAnswer->getById($answer['id']);
							if (isset($answer_in_db->id)) {
								$question_in_db = $dbQuestion->findByQuestionId($answer_in_db->question_id);
								$wrongAnswers[$question_in_db->id]['question'] = $question_in_db->question;
								$wrongAnswers[$question_in_db->id]['answers'] = $dbAnswer->getAll($question_in_db->id);
								$wrongAnswers[$question_in_db->id]['answers'] = $wrongAnswers[$question_in_db->id]['answers']->toArray();
								if (!isset($wrongAnswers[$question_in_db->id]['count'])) $wrongAnswers[$question_in_db->id]['count'] = 0;
								if (!isset($wrongAnswers[$question_in_db->id]['players'])) $wrongAnswers[$question_in_db->id]['players'] = array();
								$wrongAnswers[$question_in_db->id]['count']++;
								$wrongAnswers[$question_in_db->id]['players'][] = $attempt->nick;
								$wrongAnswers[$question_in_db->id]['players'] = array_unique($wrongAnswers[$question_in_db->id]['players']);
							}
						}
					}
				}
				$this->view->wrongAnswers = $wrongAnswers;
			} else {
				$this->_redirect('/nauczyciel/');
			}
		} else {
			$this->_redirect('/nauczyciel/');
		}
	}

	public function pokazProbeAction() {
		$id = $this->_request->getParam('id');
		$dbAttempts = new Model_Attempt;
		$attempt = $dbAttempts->getById($id);
		$attempt = $attempt->toArray();
		$attempt['questions'] = unserialize($attempt['questions']);
		$attempt['answers'] = unserialize($attempt['answers']);
		$attempt['answers_time'] = unserialize($attempt['answers_time']);
		echo '<pre>';
		print_r($attempt);
		die;	
	}

	public function korygujCzasAction()		
	{
		$id = $this->_request->getParam('id');
		$time_left = $this->_request->getParam('time_left');
		$server_finished = $this->_request->getParam('server_finished');
		if(isset($id)&&isset($time_left)&&isset($server_finished)){
			$dbAttempts = new Model_Attempt;
			$where = $dbAttempts->getAdapter()->quoteInto('id = ?', $id);
			$data = array(
				'time_left' => $time_left,
				'server_finished' => $server_finished
			);
			$dbAttempts->update($data, $where);
		}
		die;
	}

	public function debugujTestAction()		
	{
		session_write_close();
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$testPass = $this->_request->getParam('id');
		$dbUser = new Model_User;
		$user = $dbUser->findByMail($this->ZendSession->OPENID['email']);
		if ($test = $dbTests->getByPass($testPass)) {
			if ($test->author_id == $user->id || $user->user_role > 3) {
				$this->attemptsCleaner($test->id);
				$allPlayers = $dbAttempts->getAllHighScores($test->pass)->toArray();
				foreach ($allPlayers as $k => $v) {
					echo '<pre>';
					$v['questions'] = unserialize($v['questions']);
					$v['answers'] = unserialize($v['answers']);
					$v['answers_time'] = unserialize($v['answers_time']);
					print_r($v);
					echo '</pre><hr/>';
				}
			}
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
			if ($test->author_id == $user->id || $user->user_role > 3) {
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
				$this->_redirect('/nauczyciel/');
			}
		} else {
			$this->_redirect('/nauczyciel/');
		}
	}

	public function nowyTestZapiszAction()
	{
		if (isset($_POST['level']) &&
			isset($_POST['school']) &&
			isset($_POST['questions']) &&
			isset($_POST['end']) &&
			isset($_POST['chapter']) &&
			isset($_POST['mode']) &&
			isset($_POST['time']) &&
			isset($_POST['pass']) &&
			isset($_POST['name'])
		) {
			if (isset($_POST['no_time_limit'])) {
				$time = 0;
			} else {
				$time = $_POST['time'];
			}
			$categories = array_merge(array($_POST['level'], $_POST['school']), $_POST['chapter']);
			$questions = $this->getTestQuestions($_POST['level'], $_POST['school'], $_POST['chapter']);
			
			$data = array(
				'pass' => $_POST['pass'],
				'name' => $_POST['name'],
				'author_id' => $this->user->id,
				'categories' => serialize(array_merge(array($_POST['level'], $_POST['school']), $_POST['chapter'])),
				'questions' => serialize($questions),
				'mode_questions' => $_POST['questions'],
				'mode_end' => $_POST['end'],
				'mode_players' => $_POST['mode'],
				'status' => 1,
				'time' => $time,
				'groups' => 0
			);
			if (isset($_POST['groups'])) {
				$data['groups'] = $_POST['groups'];
			}
			$dbTests = new Model_Test;
			$dbTests->insert($data);
		}
		$this->_redirect('nauczyciel');
	}

	public function getNewPassAction()
	{
		echo json_encode(array(
			'pass' => $this->passGen()
		));
		die;
	}

	public function passGen($lenght = 4)
	{

		$dbTests = new Model_Test;
		$pass = false;
		while (!$dbTests->isUnique($pass)) {
			$validCharacters = "ABCDEFGHIJKLMNOPRSTUYWZ"; // przy długości 4 daje 456976 kombinacji
			$validCharNumber = strlen($validCharacters);
			$pass = '';
			for ($i = 0; $i < $lenght; $i++) {
				$index = mt_rand(0, $validCharNumber - 1);
				$randomCharacter = $validCharacters[$index];
				$pass = $pass . $randomCharacter;
			}
		}
		return $pass;
	}

	protected function losowanieAction()
	{
		echo '<pre>';
		$questions = $this->getTestQuestions(array(1, 2, 3), 7, array(16));
		print_r($questions);
		die;
	}

	protected function getTestQuestions($levels = false, $school = false, $categories = false, $user_id = false, $status = false)
	{
		// baza danych
		$dbCategories = new Model_Category;
		$dbQuestions = new Model_Question;
		$dbQuestionCategories = new Model_QuestionCategory;
		$dbAnswers = new Model_Answer;
		$dbLifebuoys = new Model_Lifebuoy;

		$questions = array();
		$QUESTIONS = array();
		$QUESTIONS_COUNT = 10;
		if($this->app['expert_mode'] == 0) {
			$QUESTIONS_COUNT = $QUESTIONS_COUNT + 1;
		}

		if ($levels && $school && $categories) {

			// łączymy wszystkie kombinacje kategorii w jedna tablicę
			$allCategoriesCombinations = array();
			foreach ($levels as $LEVEL) {
				foreach ($categories as $CATEGORY) {
					$allCategoriesCombinations[] = array(
						$LEVEL,
						$school,
						$CATEGORY
					);
				}
			}
		
			// pobierz id wszystkich pytan pogrupowanych na kategorie
			$categoriesQuestions = $dbQuestionCategories->categoriesQuestions($user_id,$status);

			// przed wylosowanie pytań ustalamy 3 listy pytań, z których potem wyznaczymy wspólną część
			// I - pytania w/g zaznaczonych poziomów trudności
			$questionsByLevels = array();
			foreach($levels as $l) {
				if(isset($categoriesQuestions[$l])) $questionsByLevels = array_merge($questionsByLevels,$categoriesQuestions[$l]);
			}

			// II - pytania w/g wybranej szkoły
			$questionsBySchool = $categoriesQuestions[$school];
			// III - pytania w/g wybranych kategorii
			$questionsByCategories = array();
			foreach($categories as $c) {
				$questionsByCategories = array_merge($questionsByCategories,$categoriesQuestions[$c]);
			}

			// teraz najciekawsze - ustalenie części wspólnej
			$questions = array();
			foreach($questionsByLevels as $q) {
				if(in_array($q,$questionsBySchool)&&in_array($q,$questionsByCategories)) $questions[] = $q;
			}
			sort($questions);

			if (count($questions) < $QUESTIONS_COUNT) {
				// jeżeli mniej niż dziesięć przekieruj i pokaż komunikat
				$this->_redirect('/index/za-malo-pytan/');
			}
		} else {
			// pobierz id wszystkich pytań
			$questions = $dbQuestions->findAllQuestionsIds();
			shuffle($questions);
		}
		shuffle($questions);
		$questions = array_unique($questions);
		$questions = array_slice($questions, 0, $QUESTIONS_COUNT);
		return $questions;
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

	public function exportTestowAction()
	{

		$dbTests = new Model_Test;
		$tests = $dbTests->findAllToExport();
		foreach ($tests AS $test) {
			$this->zapiszTestAction($test->pass);
		}
		die();
	}

	public function zapiszTestAction($testPass = null)
	{
		if (is_null($testPass)) $testPass = $this->_request->getParam('id');
		$dbUser = new Model_User;
		$dbTests = new Model_Test;
		$dbAttempts = new Model_Attempt;
		$dbQuestions = new Model_Question;
		$dbAnswers = new Model_Answer;
		header('content-type: text/plain; charset=utf-8');
		if ($testRow = $dbTests->getByPass($testPass)) {
			$user = $dbUser->findById($testRow->author_id);
			$client = GN_Gapps::getClientFromToken($user->getAccessToken(), $this->_googleapps['consumerKey'], $this->_googleapps['consumerSecret']);
			$tests = $dbAttempts->getByTestPass($testPass);
			$name = $this->view->translate('Wyniki testu').' "' . $testRow->name . '" - ' . date('Y-m-d', strtotime($testRow->created));
			$punkty = array();
			$testyWszystkich = array();
			foreach ($tests AS $key => $test) {
				$questions = unserialize($test->questions);
				$answers = unserialize($test->answers);
				$testy = array();
				if (!is_array($questions)) continue;
				foreach ($questions AS $i => $id) {
					$t = array();
					$q = $dbQuestions->findByQuestionId($id);
					$t['pytanie'] = $q->question;

					$a = $dbAnswers->findById($answers[$i]['id']);
					$correct = $answers[$i]['correct'];

					$t['odpowiedź'] = $a[0]->answer;
					$t['poprawnie'] = $correct ? $this->view->translate('Tak') : $this->view->translate('Nie');
					$testy[] = $t;
				}

				$testy[] = array('', '', '');
				$testy[] = array($this->view->translate('Punkty razem'), '', $test->points);
				$punkty[] = array(
					$this->view->translate('Osoba') => $test->nick,
					$this->view->translate('Platforma') => $test->status >= 100 ? $this->view->translate('Mobilny') : $this->view->translate('Stacjonarny'),
					$this->view->translate('Rozpoczęto') => date('d-m-Y, H:i:s', $test->server_started),
					$this->view->translate('Zakończono') => date('d-m-Y, H:i:s', $test->server_finished),
					$this->view->translate('Zdobyte punkty') => $test->points,
					$this->view->translate('Pozostało czasu') => $test->time_left,

				);
				$testyWszystkich[($key+1).'. '.$test->nick] = $testy;
			}

			GN_Gapps::dbArray2Spreadsheet($client, $punkty, $name);
			GN_Gapps::dbArray2Spreadsheet($client, $testyWszystkich, $name);

			$testRow->time_exported = time();
			$testRow->save();

		}
		$this->_redirect('/nauczyciel');
	}

	public function wyslijZaproszenieAction() {
		$dbTests = new Model_Test;
		$id = $this->_request->getParam('id');
		$pass = $this->_request->getParam('pass');
		if (isset($id)) {		
			if ($test = $dbTests->getById($id)) {
				$this->view->test = $test;
			}			
		}
		if (isset($pass)) {		
			if ($test = $dbTests->getByPass($pass)) {
				$this->view->test = $test;
			}			
		}
		if(isset($test->id)) {
			$dbInvitations = new Model_Invitation;
			$invitations = $dbInvitations->getByTestId($test->id);
			$this->view->invitations = $invitations;
		}
		if(!isset($id)&&!isset($pass)&&!isset($this->view->test->id)) {
			$this->_redirect('/');
		}
	}

	public function wyslijZaproszenieAjaxAction() {
		if ($this->observer && isset($_POST['pass']) && isset($_POST['mailto'])) {
			$data = array
			(
				'userName' => $this->user->name,
				'userMail' => $this->user->email,
				'pass' => $_POST['pass'],
				'testName' => $_POST['testName'],
				'mailto' => $_POST['mailto']
			);
			$dbTests = new Model_Test;
			$test = $dbTests->getByPass($_POST['pass']);
			if(isset($test->id)) {

				$label = 'sendInvitation';
				if($test->active_from == null && $test->active_to == null) {
					$label = 'sendInvitation';
				} elseif($test->active_from != null && $test->active_to == null) {
					$label = 'sendInvitationActiveFrom';				
				} elseif($test->active_from == null && $test->active_to != null) {
					$label = 'sendInvitationActiveTo';				
				} elseif($test->active_from != null && $test->active_to != null) {
					$label = 'sendInvitationActiveFromTo';				
				}

				$response = $this->observer->observe($label, true, $data);
				$dbInvitations = new Model_Invitation;
				$invitation = $dbInvitations->getByTestIdAndEmail($test->id,$_POST['mailto']);				
				if(!count($invitation)>0) {				
					$data = array(
						'email' => $_POST['mailto'],
						'test_id' => $test->id
					);
					$invitation_id = $dbInvitations->insert($data);
					echo json_encode(
						array(
							'error' => 0,
							'response' => $response,
							'invitation_id' => $invitation_id
						)
					);
				}
			}				
		} else {
			echo json_encode(
				array(
					'error' => 1
				)
			);
		}		
		die;
	}

}
