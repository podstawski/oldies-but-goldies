<?php

require_once 'MillionaireController.php';

class MillionaireGraController extends MillionaireController
{
	/**
	 * @var Model_Answer
	 */
	protected $dbAnswers;

	/**
	 * @var Model_Attempt
	 */
	protected $dbAttempts;

	/**
	 * @var Model_Lifebuoy
	 */
	protected $dbLifeBuoys;

	/**
	 * @var Model_Question
	 */
	protected $dbQuestions;

	/**
	 * @var Model_QuestionCategory
	 */
	protected $dbQuestionCategories;

	/**
	 * @var Model_Test
	 */
	protected $dbTests;

	/**
	 * @var bool
	 */
	protected $_isRenderAction = true;

	/**
	 * @var string
	 */
	protected $_responseContent = '';

	public function init()
	{
		parent::init();

		if ($this->_request->getParam('action') === 'die') {
			try {
				throw new Exception('Just testing...');
			} catch (Exception $e) {
				echo $e->getTraceAsString();
			}
			die;
		}

		$this->dbAnswers = new Model_Answer;
		$this->dbAttempts = new Model_Attempt;
		$this->dbLifeBuoys = new Model_Lifebuoy;
		$this->dbQuestions = new Model_Question;
		$this->dbQuestionCategories = new Model_QuestionCategory;
		$this->dbTests = new Model_Test;
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
		// przekieruj do stony głównej jeżeli cos nie teges
		if (!isset($this->ZendSession->gameData['sessionHash']) || !isset($this->ZendSession->gameData['testPass'])) {
			$this->_redirectExit('index', 'index');
		}

		$gameData = $this->readGameData();
		if ($gameData['lastQuestion'] > 0) {
			$questionAndAnswers = $this->getRandomQuestion($gameData['lastQuestion']);
			$questionAndAnswers['answers'] = $gameData['lastQuestionAnswers'];
		} else {
			$questionAndAnswers = $this->getNextQuestion();
			$gameData['lastQuestion'] = $questionAndAnswers['question']->id;
			$gameData['lastQuestionAnswers'] = $questionAndAnswers['answers'];
			$this->ZendSession->gameData = $gameData;
		}

		// sprawdź czy obrazek jest przygotowany czy trzeba wygenerować miniaturkę
		if (
			$questionAndAnswers['question']->media != '' &&
			!strstr($questionAndAnswers['question']->media, 'youtube') &&
			(
				stristr($questionAndAnswers['question']->media, 'bmp') ||
				stristr($questionAndAnswers['question']->media, 'gif') ||
				stristr($questionAndAnswers['question']->media, 'jpg') ||
				stristr($questionAndAnswers['question']->media, 'jpeg') ||
				stristr($questionAndAnswers['question']->media, 'png')
			)
		) {
			if ($thumbnail = $this->prepareImage($questionAndAnswers['question']->media, $questionAndAnswers['question']->question_hash)) {
				$questionAndAnswers['question']->media = '';
				$questionAndAnswers['question']->source = $thumbnail;
				$data = array(
					'media' => '',
					'source' => $thumbnail
				);
				$where = $this->dbQuestions->getAdapter()->quoteInto('id = ?', $questionAndAnswers['question']->id);
				$this->dbQuestions->update($data, $where);
			}
		}
		// wczytaj test i wyrzuc do widoku jeżeli inny password niż DEMO
		if(isset($this->ZendSession->gameData['testPass']) && $this->ZendSession->gameData['testPass'] != 'DEMO') {
			$this->view->test = $this->dbTests->getByPass($this->ZendSession->gameData['testPass']);
		}

		// zmienne gry
		$this->view->question = $questionAndAnswers['question'];
		$this->view->answers = $questionAndAnswers['answers'];
		$this->view->gameData = $gameData;
	}

	public function startAction()
	{
		if (isset($_POST['pass'])) {
			$dbTest = new Model_Test;
			$pass = strtoupper($_POST['pass']);
			$test = $dbTest->getByPass($pass);
			if ($pass == 'DEMO') {
				$gameData = $this->createGameData($pass, $_POST['nick']);
				$this->_redirectExit('demo');
			} elseif (isset($test->id)) {
				// 1. Na początku sprawdź status testu
				if ($test->status < 1) {
					$this->_forward('test-closed');
				} else {
					// 2. Nastepnie sprawdź czy gra już nie została rozpoczęta (jeżeli została wczytaj stan gry z sesji)
					if (!isset($this->ZendSession->gameData['testPass']) || $this->ZendSession->gameData['testPass'] != $pass) {
						// 3. W przypadku gry drużynowej, sprawdź czy ilość grup nie osiągnęła maksymalnej wartości okreslonej przez nauczyciela
						if ($test->mode_players === 2) {
							if ($test->groups > 0) {
								$dbAttempts = new Model_Attempt;
								$attempts = $dbAttempts->getByTestPass($pass);
								$count_attempts = count($attempts);
								if ($count_attempts >= $test->groups) {
									$this->_redirect('/gra/room-is-full');
								}
							}
							// sprawdź czy wszystkie pytania z testu są nadal w bazie danych
							if (in_array($test->mode_questions, array(1, 3))) {
								$questions = unserialize($test->questions);
								if ($this->findDeletedQuestions($questions)) {
									$this->_forward('brak-pytan');
								}
							}
						}
						$test_array = $test->toArray();
						$gameData = $this->createGameData($pass, $_POST['nick'], $test_array);
						$this->saveGameState($gameData);
					} else {
						$gameData = $this->readGameData($pass);
						$this->saveGameState($gameData);
					}
					$this->_forward('index');
				}
			} else {
				$this->_forward('wrong-password');
			}
		} else {
			$this->_forward('index');
		}
	}

	public function demoAction()
	{
		$gameData = $this->createGameData('DEMO', $this->view->userName);

		// gra
		if ($gameData['lastQuestion'] > 0) {
			$questionAndAnswers = $this->getRandomQuestion($gameData['lastQuestion']);
			$questionAndAnswers['answers'] = $gameData['lastQuestionAnswers'];
		}
		else {
			$questionAndAnswers = $this->getNextQuestion();
			$gameData['lastQuestion'] = $questionAndAnswers['question']->id;
			$gameData['lastQuestionAnswers'] = $questionAndAnswers['answers'];
			$this->ZendSession->gameData = $gameData;
		}

		// zmienne gry
		$this->view->question = $questionAndAnswers['question'];
		$this->view->answers = $questionAndAnswers['answers'];
		$this->view->gameData = $gameData;

		$this->_forward('index');
	}

	public function ajaxAction()
	{
		$this->_isRenderAction = false;

		if (!isset($_POST['answer'])) {
			$_POST['answer'] = 0;
		}

		$gameData = $this->readGameData();
		$step = $gameData['step'];

		// wylosuj kolejne pytanie
		//while(!ina_array())
		$questionAndAnswers = $this->getNextQuestion($step + 1);		

		// sprawdź czy obrazek jest przygotowany czy trzeba wygenerować miniaturkę
		if (
			$questionAndAnswers['question']->media != '' &&
			!strstr($questionAndAnswers['question']->media, 'youtube') &&
			(
				stristr($questionAndAnswers['question']->media, 'bmp') ||
				stristr($questionAndAnswers['question']->media, 'gif') ||
				stristr($questionAndAnswers['question']->media, 'jpg') ||
				stristr($questionAndAnswers['question']->media, 'jpeg') ||
				stristr($questionAndAnswers['question']->media, 'png')
			)
		) {
			if ($thumbnail = $this->prepareImage($questionAndAnswers['question']->media, $questionAndAnswers['question']->question_hash)) {
				$questionAndAnswers['question']->media = '';
				$questionAndAnswers['question']->source = $thumbnail;
				$data = array(
					'media' => '',
					'source' => $thumbnail
				);
				$where = $this->dbQuestions->getAdapter()->quoteInto('id = ?', $questionAndAnswers['question']->id);
				$this->dbQuestions->update($data, $where);
			}
		}

		// walidacja linka do youtube'a
		if (isset($questionAndAnswers['question']->media) && $questionAndAnswers['question']->media != '' && strstr($questionAndAnswers['question']->media, 'youtube')) {
			$media = parse_url($questionAndAnswers['question']->media);
			parse_str($media['query'], $yt);
		}
		if (!isset($yt['v'])) {
			$questionAndAnswers['question']->media = '';
		} else {
			$questionAndAnswers['question']->media = $yt['v'];
		}

		// object to array
		$questionAndAnswers['question'] = array(
			'id' => $questionAndAnswers['question']->id,
			'question' => $questionAndAnswers['question']->question,
			'hash' => $questionAndAnswers['question']->question_hash,
			'media' => $questionAndAnswers['question']->media,
			'source' => $questionAndAnswers['question']->source
		);

		// sprawdź czy odpowiedź jest prawidłowa i jeżeli zapodano jakiegos posta
		if (
			(isset($_POST['answer']) && $_POST['answer'] > 0) &&
			(isset($_POST['question']) && $_POST['question'] > 0)
		) {
			// podaj kolejność prawidłowego pytania
			foreach ($gameData['lastQuestionAnswers'] as $key => $value) {
				if ($value['is_correct'] == 1) {
					$correct_answer = $key + 1;
				}
			}
			// czy podano prawidłową odpowiedź?
			$gameData['currentQuestion']++;
			$is_correct = $this->dbAnswers->findById($_POST['answer']);
			$questionAndAnswers['correct'] = $is_correct[0]->is_correct;
			if ($is_correct[0]->is_correct > 0) {
				$gameData['points'] = $gameData['points'] + 1;
			}
			$questionAndAnswers['currentQuestion'] = $gameData['currentQuestion'];
		} else {
			$gameData['currentQuestion']++;
			$questionAndAnswers['correct'] = 'false';
			$questionAndAnswers['currentQuestion'] = $gameData['currentQuestion'];
		}

		// przygotowanie danych do zwrotu
		$response = $questionAndAnswers;
		if (isset($correct_answer)) {
			$response['correct_answer'] = $correct_answer;
		}

		// zapisz dane gry do sesji
		$gameData['history']['questions'][$step] = $_POST['question'];
		if (isset($_POST['answer'])) {
			// zapisujemy kolejne odpowiedzi
			$gameData['history']['answers'][$step] = array(
				'id' => $_POST['answer'],
				'correct' => $questionAndAnswers['correct']
			);
			// różne zmienne opisujące czas odpowiedzi
			$gameData['history']['answersTime'][$step] = array(
				'server' => time()
			);
			if (isset($_POST['currentTime'])) {
				$gameData['history']['answersTime'][$step]['client'] = $_POST['currentTime'];
			}
			if (isset($_POST['timeLeft'])) {
				$gameData['history']['answersTime'][$step]['clientTimeLeft'] = $_POST['timeLeft'];
			}
		}
		$gameData['lastQuestion'] = $questionAndAnswers['question']['id'];
		$gameData['lastQuestionAnswers'] = $questionAndAnswers['answers'];
		$gameData['step'] = $step + 1;
		if (isset($_POST['time'])) {
			if ($gameData['time'] > $_POST['time']) $gameData['time'] = $_POST['time'];
		}
		if (isset($_POST['timeStarted'])) {
			$gameData['timeStarted'] = $_POST['timeStarted'];
		}
		$this->ZendSession->gameData = $gameData;
		$this->saveGameState($gameData);

		// send score2platform
		if (isset($this->user->email) && $gameData['testPass'] != 'DEMO') {
			$this->score2platform($this->user->email, $gameData['testPass'], $gameData['sessionHash'], $gameData['points']);
		}

		// feed back
		$this->_responseContent = $response;
	}

	public function ajaxRefreshGameDataAction()
	{
		$this->_isRenderAction = false;
		if (isset($_POST['testPass'])) {
			$gameData = $this->readGameData();
			$this->_responseContent = $gameData;
		}
	}

	public function ajaxNewGameAction()
	{
		$this->_isRenderAction = false;
		$this->ZendSession->gameData = array();
		// Zend_Session::destroy('headmaster');
	}

	public function ajaxLifeBuoyAction()
	{
		$this->_isRenderAction = false;
		$request = false;
		if (isset($_POST['question_id']) && isset($_POST['lifebuoy_type'])) {
			$gameData = $this->readGameData();
			switch ($_POST['lifebuoy_type']) {
				case 1:
					if (!isset($gameData['lifeBuoys'][$_POST['lifebuoy_type']])) {
						$request = $this->dbLifeBuoys->findByQuestionId($_POST['question_id'], $_POST['lifebuoy_type']);
						$gameData['lifeBuoys'][$_POST['lifebuoy_type']] = 1;
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['client'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['clientTimeLeft'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['server'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['step'] = $gameData['step'];
						$this->ZendSession->gameData = $gameData;
						$this->saveGameState($gameData);
						$this->_responseContent = $request;
					}
					break;
				case 2:
					if (!isset($gameData['lifeBuoys'][$_POST['lifebuoy_type']])) {
						$gameData['lifeBuoys'][$_POST['lifebuoy_type']] = 1;
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['client'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['clientTimeLeft'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['server'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['step'] = $gameData['step'];
						$this->ZendSession->gameData = $gameData;
						$this->saveGameState($gameData);
						$correct = rand(38, 48);
						$wrong = array(
							1 => rand(20, 40),
							2 => rand(5, 11)
						);
						$wrong[3] = 100 - array_sum($wrong) - $correct;
						shuffle($wrong);
						$response = array();
						$letters = 'ABCD';
						$i = 0;
						foreach ($gameData['lastQuestionAnswers'] as $key => $value) {
							if ($value['is_correct'] == 0) {
								$response[] = array(
									'letter' => $letters[$key],
									'text' => $value['answer'],
									'probability' => $wrong[$i]
								);
								$i++;
							} else {
								$response[] = array(
									'letter' => $letters[$key],
									'text' => $value['answer'],
									'probability' => $correct
								);
							}
						}
						$this->_responseContent = $response;
					}
					break;
				case 3:
					if (!isset($gameData['lifeBuoys'][$_POST['lifebuoy_type']])) {
						$gameData['lifeBuoys'][$_POST['lifebuoy_type']] = 1;
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['client'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['clientTimeLeft'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['server'] = time();
						$gameData['lifeBuoysTime'][$_POST['lifebuoy_type']]['step'] = $gameData['step'];
						$this->ZendSession->gameData = $gameData;
						$this->saveGameState($gameData);
						$response = array();
						foreach ($gameData['lastQuestionAnswers'] as $key => $value) {
							if ($value['is_correct'] == 0) {
								$response[] = $key + 1;
							}
						}
						shuffle($response);
						$this->_responseContent = $response;
					}
					break;
			}
		}
	}

	public function ajaxTimeAction()
	{
		$this->_isRenderAction = false;
		if (isset($_POST['testPass']) && isset($_POST['sessionHash']) && isset($_POST['timeStarted']) && isset($_POST['currentTime']) && isset($_POST['timeLeft'])) {
			$gameData = $this->readGameData();
			$gameData['timeStarted'] = $_POST['timeStarted'];
			$gameData['timeFinished'] = $_POST['currentTime'];
			$gameData['timeLeft'] = $_POST['timeLeft'];
			$this->ZendSession->gameData = $gameData;
			$this->saveGameState(array(
				'sessionHash' => $_POST['sessionHash'],
				'testPass' => $_POST['testPass'],
				'timeStarted' => $_POST['timeStarted'],
				'timeFinished' => $_POST['currentTime'],
				'timeLeft' => $_POST['timeLeft']
			));
		}
	}

	public function ajaxGetGroupmodeDataAction()
	{
		$this->_isRenderAction = false;
		if (isset($_POST['testPass'])) {
			$response = array();
			$test = $this->dbTests->getByPass($_POST['testPass']);
			if (isset($test->id)) {
				$attempts = $this->dbAttempts->getForGroupMode($_POST['testPass']);
				foreach ($attempts as $att) {
					$a = $att->toArray();
					$a['answers'] = unserialize($a['answers']);
					$a['lifebuoys'] = unserialize($a['lifebuoys']);
					$response[] = $a;
				}
			}
			$this->_responseContent = $response;
		}
	}

	public function ajaxFlagAction()
	{
		$this->_isRenderAction = false;
		if (isset($_POST['id']) && isset($_POST['comment'])) {
			$response = array();
			$question = $this->dbQuestions->findByQuestionId($_POST['id']);
			if (isset($question->id) && isset($this->user->id)) {
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
				if ($this->user->user_role >= 3) {
					$flag = 10;
				} else {
					$flag = 1;
				}
				$data = array(
					'flag' => $flag,
					'flag_data' => serialize($flag_data)
				);
				$where = $this->dbQuestions->getAdapter()->quoteInto('id = ?', $_POST['id']);
				$this->dbQuestions->update($data, $where);
				$response = array('action' => 'saved', 'flag_data' => $flag_data, 'user' => $this->user->toArray());
			} else {
				$response = array('action' => 'error');
			}
			$this->_responseContent = $response;
		}
	}

	public function debugAction()
	{
		echo '<pre>';
		print_r($this->ZendSession->gameData);
		echo '</pre>';
		die;
	}

	// zapisywanie czasu do bazy danych
	public function ajaxTimeLeftAction()
	{
		$this->_isRenderAction = false;
		if (isset($_POST['testPass']) && isset($_POST['sessionHash']) && isset($_POST['timeLeft'])) {
			$this->ZendSession->gameData['timeLeft'] = $_POST['timeLeft'];
			$data = array(
				'sessionHash' => $_POST['sessionHash'],
				'testPass' => $_POST['testPass'],
				'timeLeft' => $_POST['timeLeft']
			);
			// opcjonalne zmienne
			if (isset($_POST['currentTime'])) {
				$this->ZendSession->gameData['timeStarted'] = $_POST['currentTime'];
				$data['time_started'] = $_POST['currentTime'];
			}
			// opcjonalne zmienne
			if (isset($_POST['timeFinished'])) {
				$this->ZendSession->gameData['timeFinished'] = $_POST['timeFinished'];
				$data['time_finished'] = $_POST['timeFinished'];
			}
			if (isset($_POST['status'])) {
				$data['status'] = $_POST['status'];
				if ($_POST['status'] == 0) {
					$data['server_finished'] = time();
				}
			}
			$this->saveGameState($data);
		}
		$this->_responseContent = '';
	}

	// pobierz pytanie, ale niekoniecznie przypadkowe! :-)
	protected function getRandomQuestion($id = 0)
	{
		// baza danych

		// pobierz id wszystkich pytań
		$answers = array();

		if ($id > 0) {
			$questions = $this->dbQuestions->findAllQuestionsIds();
			$question = $this->dbQuestions->findByQuestionId($id);
			$answers = $this->dbAnswers->findByQuestionId($question->id, 4);
		} else {
			// powtarzaj dopóki nie znajdziesz pytania z 4 odpowiedziami
			$questions = $this->dbQuestions->findAllActiveQuestionsIds();
			$bezpiecznik = 0;
			while (count($answers) != 4) {
				// wylosuj pytanie, ale id pobierz z tablicy pobranej wcześniej
				$rand = rand(1, count($questions));
				$bezpiecznik++;
				if (isset($questions[$rand])) {
					$question = $this->dbQuestions->findByQuestionId($questions[$rand]);
					// pobierz odpowiedzi na wylosowane pytanie
					$answers = $this->dbAnswers->findByQuestionId($question->id, 4);
				}
				if ($bezpiecznik > 100) {
					echo '<pre>';
					print_r($answers);
					echo '</pre><hr>';
					die(__FILE__ . ': ' . __LINE__);
				}
			}
		}
		return array(
			'question' => $question,
			'answers' => $answers
		);
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

	// pobierz pytanie, ale niekoniecznie przypadkowe! :-)
	protected function getNextQuestion($step = 0)
	{
		$gameData = $this->readGameData();
		if(isset($gameData['ownQuestions'][$step])) {
			$questionAndAnswers = $this->getRandomQuestion($gameData['ownQuestions'][$step]);
		} else {
			$questionAndAnswers = $this->getRandomQuestion();
		}
		return $questionAndAnswers;				
	}


	protected function getTestQuestions($levels = false, $school = false, $categories = false, $user_id = false, $status = false)
	{
		// baza danych

		$questions = array();
		$QUESTIONS = array();

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
			$categoriesQuestions = $this->dbQuestionCategories->categoriesQuestions($user_id,$status);

			// wydzielamy część wspólną dla każdej kombinacji i ją sumujemy
			foreach ($allCategoriesCombinations as $allCategories) {
				// jeżeli tablica ma więcej wymiarów spłaszcz ją - if you know what I mean
				$array_to_merge = array();
				foreach ($allCategories as $k=>$v) {					
					if(is_array($v)) {
						$array_to_merge = array_merge($array_to_merge,$v);
						unset($allCategories[$k]);
					}
				}				
				if(count($array_to_merge)>0) {
					$allCategories = array_unique(array_merge($array_to_merge,$allCategories));
				}
				// tworzymy dwuwymiarową tablicę z idekami pytań
				$arrays = array();
				foreach ($allCategories as $category_id) {
					if(isset($categoriesQuestions[$category_id]))
					$arrays[] = $categoriesQuestions[$category_id];
				}
				// wydzielamy część wspólną
				$questions = array_merge($this->dbQuestionCategories->arrays_common_part($arrays), $questions);
				if (isset($questionsByLevels[$allCategories[0]])) {
					$questionsByLevels[$allCategories[0]] = array_merge($this->dbQuestionCategories->arrays_common_part($arrays), $questionsByLevels[$allCategories[0]]);
				} else {
					$questionsByLevels[$allCategories[0]] = $this->dbQuestionCategories->arrays_common_part($arrays);
				}
			}

			// pomieszanie pytań
			foreach ($questionsByLevels as $key => $value) {
				shuffle($value);
				$questionsByLevels[$key] = $value;
			}

			// przygotuj ostateczną listę pytań
			if (count($levels) === 3) {
				$questionsByLevels[1] = array_slice($questionsByLevels[1], 0, 3);
				$questionsByLevels[2] = array_slice($questionsByLevels[2], 0, 4);
				$questionsByLevels[3] = array_slice($questionsByLevels[3], 0, 3);
				$QUESTIONS = array_merge($questionsByLevels[1], $questionsByLevels[2], $questionsByLevels[3]);
			} elseif (count($levels) === 2) {
				foreach ($levels as $lev) {
					$questionsByLevels[$lev] = array_slice($questionsByLevels[$lev], 0, 5);
					$QUESTIONS = array_merge($QUESTIONS, $questionsByLevels[$lev]);
				}
			} else {
				if (count($questions) >= 10) {
					// jeżeli więcej niż dziesięć przytnij array
					shuffle($questions);
					$QUESTIONS = array_slice($questions, 0, 10);
				} else {
					// jeżeli mniej niż dziesięć dolosuj pytań z puli WSZYSTKICH
					$questions_to_add = $this->dbQuestions->findAllQuestionsIds();
					shuffle($questions_to_add);
					shuffle($questions);
					$questions = array_merge($questions, $questions_to_add);
					$QUESTIONS = array_slice($questions, 0, 10);
				}
			}
		} else {
			// pobierz id wszystkich pytań
			$questions = $this->dbQuestions->findAllQuestionsIds();
			shuffle($questions);
			$QUESTIONS = array_slice($questions, 0, 10);
		}
		return $QUESTIONS;
	}

	// tworzenie pustej tablicy z danymi gry
	protected function createGameData($pass, $nick = '', $test = false)
	{
		if (!$test) {
			$test = array(
				'time' => 5,
				'mode_questions' => 2,
				'mode_end' => 2,
				'mode_players' => 1
			);
		}
		$ownQuestions = array();
		if ($pass != 'DEMO') {
			$tests = $this->dbTests->getByPass($pass);
			switch ($test['mode_questions']) {
				case 1:
					$ownQuestions = $tests->questions;
					$ownQuestions = unserialize($ownQuestions);
					break;
				case 2:
					$test_categories = unserialize($tests->categories);
					$test_levels = $test_categories[0];
					$test_school = $test_categories[1];
					$test_categories = array_slice($test_categories, 2);
					$ownQuestions = $this->getTestQuestions($test_levels, $test_school, $test_categories);
					shuffle($ownQuestions);
					break;
				case 3:
					$ownQuestions = unserialize($tests->questions);
					shuffle($ownQuestions);
					break;
				default:
					break;
			}
		} else {
			$ownQuestions = array();
		}
		$gameData = array(
			'testPass' => $pass,
			'nick' => $nick,
			'sessionHash' => md5(time() . 'testPass' . rand(1, 100)),
			'currentQuestion' => 0,
			'history' => array(
				'questions' => array(),
				'answers' => array(),
				'answersTime' => array()
			),
			'lastQuestion' => 0,
			'lastQuestionAnswers' => array(),
			'ownQuestions' => $ownQuestions,
			'lifeBuoys' => array(),
			'lifeBuoysTime' => array(),
			'time' => $test['time'] * 60,
			'timeStarted' => 0,
			'timeLeft' => 0,
			'points' => 0,
			'step' => 0,
			'modeQuestions' => $test['mode_questions'],
			'modeEnd' => $test['mode_end'],
			'modePlayers' => $test['mode_players'],
			'status' => 1
		);
		$this->ZendSession->gameData = $gameData;
		return $gameData;
	}

	protected function readGameData()
	{
		// sprawdz czy w sesji juz coś zapisano
		$gameData = $this->ZendSession->gameData;
		// jeżeli gra nie jest demem, i jeżeli brak w bazie danych tego co zapisano w sesji, to weź spierdalaj
		if(isset($gameData['testPass']) && $gameData['testPass']!='DEMO') {
			$attempt = $this->dbAttempts->getByHash($gameData['sessionHash']);
			if(!isset($attempt->id)) {
				$this->ZendSession->gameData = array();
				$this->_redirect('index/index');
			}
		}
		return $gameData;		
	}

	// zapisywanie stanu gry do bazy danych
	protected function saveGameState($gameData = false)
	{
		// sprawdź czy jest session hash i czy przypadkiem to nie gra DEMO
		if ($gameData && isset($gameData['sessionHash']) && isset($gameData['testPass']) && $gameData['testPass'] != 'DEMO') {
			// 1. prosta walidacja
			$data = array();
			if (isset($gameData['testPass'])) $data['test_pass'] = $gameData['testPass'];
			if (isset($gameData['nick'])) $data['nick'] = $gameData['nick'];
			if (isset($gameData['sessionHash'])) $data['session_hash'] = $gameData['sessionHash'];
			if (isset($gameData['history']['questions'])) $data['questions'] = serialize($gameData['history']['questions']);
			if (isset($gameData['history']['answers'])) $data['answers'] = serialize($gameData['history']['answers']);
			if (isset($gameData['history']['answersTime'])) $data['answers_time'] = serialize($gameData['history']['answersTime']);
			if (isset($gameData['lifeBuoys'])) $data['lifebuoys'] = serialize($gameData['lifeBuoys']);
			if (isset($gameData['lifeBuoysTime'])) $data['lifebuoys_time'] = serialize($gameData['lifeBuoysTime']);
			if (isset($gameData['timeStarted'])) $data['time_started'] = $gameData['timeStarted'];
			if (isset($gameData['timeFinished'])) $data['time_finished'] = $gameData['timeFinished'];
			if (isset($gameData['timeLeft'])) $data['time_left'] = $gameData['timeLeft'];
			if (isset($gameData['points'])) $data['points'] = $gameData['points'] * 10;
			if (isset($gameData['step'])) $data['step'] = $gameData['step'];
			if (isset($gameData['status'])) $data['status'] = $gameData['status'];

			// 2. jeżeli uzytkownik jest zalogowany poprzez OpenId zapisz jego id :]
			if (isset($this->user)) {
				$data['user_id'] = $this->user->id;
			} else {
				$data['user_id'] = 0;
			}

			// 3. ustaw status na zakończony jeżeli...
			if (isset($data['step']) && $data['step'] >= 10) {
				$data['status'] = 0;
				$data['server_finished'] = time();
			}

			// 4. wyjeb puste rekordy by ulźyć nieco bazie danych
			foreach ($data as $key => $value) {
				if ($value == '' || $value == NULL || $value == 'a:0:{}') {
					unset($data[$key]);
				}
			}

			// 5. zapisz do bazy danych... tworząc nowy lub nadpisując istniejący rekord
			$dbGameData = $this->dbAttempts->getByHash($gameData['sessionHash']);
			if (isset($dbGameData->id)) {
				$where = $this->dbAttempts->getAdapter()->quoteInto('id = ?', $dbGameData->id);
				$this->dbAttempts->update($data, $where);
			} else {
				$data['server_started'] = time();
				$this->dbAttempts->insert($data);
			}

			// 6. zwróć zwalidowane i uporządkowane dane
			return ($data);
		}
	}

	public function score2platform($mail, $game, $attempt, $score)
	{
		$googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
		if (isset($googleapps['json_link']) && isset($googleapps['id'])) {
			$token = '/links/all';
			$pos = strpos($googleapps['json_link'], $token);
			$link = substr($googleapps['json_link'], 0, $pos + strlen($token) - 9);
			$id = $googleapps['id'] ? : 'game';
			$link .= 'games/score/mail/' . $mail . '/sig/' . GN_User::getSig($mail, $googleapps['json_hash']) . '/game/' . $game . '/game_attempt_id/' . $attempt . '/score/' . $score;
			return json_decode(file_get_contents($link));
		} else {
			return false;
		}
	}

	protected function findDeletedQuestions($questions = array())
	{
		$result = false;
		if (is_array($questions)) {
			$dbQuestion = new Model_Question;
			foreach ($questions as $questionId) {
				$question = $dbQuestion->findByQuestionId($questionId);
				if (!isset($question->id)) {
					$result = true;
				} else {
				}
			}
		}
		return $result;
	}

	protected function updateModeratorAction()
	{
		$dbUser = new Model_User;
		$dbUserRoles = new Model_UserRole;
		$role = $dbUserRoles->getByName('Moderator');
		if (!isset($role->id) && isset($this->user->email)) {
			$admin = $dbUserRoles->getByName('Moderator');
			if (isset($admin->id)) {
				$data = array('name' => 'Moderator');
				$where = $dbUserRoles->getAdapter()->quoteInto('id = ?', $admin->id);
				$dbUserRoles->update($data, $where);
				$data = array('name' => 'Administrator');
				$dbUserRoles->insert($data);
			}
		}
		$moderators = $dbUser->findByRank(5);
		if (count($moderators) === 0 && isset($this->user->id)) {
			$data = array('user_role' => 5);
			$where = $dbUser->getAdapter()->quoteInto('id = ?', $this->user->id);
			$dbUser->update($data, $where);
		}
		$this->_redirectExit('index', 'index');
	}

	protected function brakPytanAction()
	{

	}

	public function wrongPasswordAction()
	{

	}

	public function testClosedAction()
	{

	}

	public function roomIsFullAction()
	{

	}

	public function imageFilesExistAction(){
		$d = DIRECTORY_SEPARATOR;
		$output = array(
			'thumbnail' => 0,
			'big' => 0
		);
		if(isset($_POST['filename'])) {
			if(file_exists('.'.$d.'uploads'.$d.$_POST['filename'].'.jpg')) {
				$output['thumbnail'] = 1;
			} 
			if(file_exists('.'.$d.'uploads'.$d.$_POST['filename'].'_big.jpg')) {
				$output['big'] = 1;
			}
		}
		echo json_encode($output);
		die;
	}
	
}

