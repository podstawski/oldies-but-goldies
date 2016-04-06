<?php

require_once 'RestController.php';

class SurveysController extends RestController
{
    public function postAction()
    {
        $post = $this->_getRequestData('POST');

        if (isset($post['data'])) {
            $data = json_decode($post["data"]);
        } else {
            $data = $this->_request->getParams();
        }

        $method = isset($post['method']) ?
                $post['method'] :
                $this->_request->getParam('method');

        switch($method) {
            default:
            case 'create':
                $this->_create($data);
                break;

            case 'fill':
                $this->_fill($data);
                break;
            case 'copyFromLibrary':
                 $this->_copyFromLibrary($data);
                break;
            case 'send':
                return $this->_send($data);
                break;
            case 'finish':
                return $this->_finishResume($data, date('Y-m-d H:i:s'));
                break;
            case 'resume':
                return $this->_finishResume($data, null);
                break;
            case 'addToLibrary':

                if (!Survey::count(
                    array(
                        'library' => 1,
                        'id'=> $this->_getParam('surveyId')
                    )
                )) {
                    $sl = Survey::find(
                        array(
                         'id' => $this->_getParam('surveyId')
                        )
                    );
                    $sl->library=1;
                    try{
                        $sl->save();
                    }catch(\Exception $e) {
                        $this->setRestResponseAndExit(
                            $e->getMessage(),
                            self::HTTP_SERVER_ERROR
                        );
                    }

                    $this->setRestResponseAndExit(null, self::HTTP_CREATED);
                }
                $this->setRestResponseAndExit(null, self::HTTP_OK);
                break;
        }
    }

    public function deleteAction()
    {
        $method = $this->_getParam('method');

        switch ( $method )
        {
            case 'removeFromLibrary':
                $sl = Survey::find(array('id'=>$this->_getParam('id')));
                $sl->library = 0;
                $sl->save();
                break;

            default:
                if (SurveyUser::count(
                    array(
                         'survey_id' => $this->_getParam('id')
                    )
                )) {
                    SurveyUser::delete_all(
                        array(
                             'survey_id' => $this->_getParam('id')
                        )
                    );
                }

                parent::deleteAction();
                break;
        }

    }


    public function indexAction()
    {
        $data = $this->getRequest();
        switch($data->method) {
            case 'averageSurveyResults':
                return $this->_averageSurveyResults($this->getRequest());
                break;

            default:
                parent::indexAction();
                break;
        }
    }
    public function getAction()
    {
        $data = $this->getRequest();

        switch($data->method) {

            case 'detailedResults':
                return $this->_detailedResults($data);
                break;

            default:

                $identity = Yala_User::getInstance()->getIdentity();

                try {
                    $row = $this->_getById();
                    $data = $row->to_array();

                    //MW build response tree
                    $survey = Survey::findForUser(
                        $data['id'],
                        array(
                             'identity'=>$identity
                        )
                    );

                    foreach ($survey->questions as $question) {
                        $data['questions'][$question->id] = $question->to_array();
                        foreach ($question->possible_answers as $answer) {
                            $data['questions'][$question->id]['possible_answers'][] = $answer->to_array();
                        }
                    }

                    $this->setRestResponseAndExit($data, self::HTTP_OK);
                } catch (ActiveRecord\ActiveRecordException $e) {
                    $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
                }
                break;
        }


    }
    public function putAction()
    {
        $putData = $this->_getRequestData('PUT');
        $putData = json_decode($putData['data']);
        
        $data = json_decode($this->_getParam('data'));
        $survey = null;


        try{
            $dataAr = (array) $data;
            if (isset($dataAr['archive']) && isset($dataAr['id']) && count($dataAr)==2) {
                $survey = Survey::find($dataAr['id']);
                $survey->archived = $dataAr['archive'];
                $survey->save();
                $this->setRestResponseAndExit(null, self::HTTP_OK);
            }

        }catch(Exception $e) {
            $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
        }



        $connection = Survey::connection();
        $connection->transaction();
        try {
            if (Survey::isResolved($putData->survey->id)) {
                $this->setRestResponseAndExit("Ankieta została już wypełniona. Nie można jej edytować.", self::HTTP_CONFLICT);
            }

            if (Survey::isSent($putData->survey->id)) {
                $this->setRestResponseAndExit("Ankieta została już wysłana do użytkowników. Nie można jej edytować.", self::HTTP_CONFLICT);
            }

            $user_id=Survey::find($putData->survey->id)->user_id;
            
            Survey::find($putData->survey->id)->delete();
            
            if ($user_id) $putData->survey->user_id=$user_id;

            $survey = new Survey((array) $putData->survey);
//            $survey->deadline = date("Y-m-d", strtotime($putData->survey->deadline));
            $survey->save();

            foreach ($putData->questions as $question_post) {
                $possible_answers = $question_post->possible_answers;
                unset($question_post->possible_answers);

                $question = new Question((array)$question_post);
                $question->survey_id = $survey->id;
                $question->save();

                foreach ($possible_answers as $possible_answer_post) {
                    $answer = new PossibleAnswer((array)$possible_answer_post);
                    $answer->question_id = $question->id;
                    $answer->save();
                }
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
        }

    }
    protected function _create($data)
    {
        $connection = Survey::connection();
        $connection->transaction();

        try {
            $survey = new Survey((array)$data->survey);
            $survey->type = $survey->type == 'survey' ? 'survey' : 'test';
            $survey->user_id = Yala_User::getUid();
            $survey->save();

            foreach ($data->questions as $question_post) {
                $possible_answers = $question_post->possible_answers;
                unset($question_post->possible_answers);

                $question = new Question((array)$question_post);

                $question->survey_id = $survey->id;
                $question->save();
                foreach ($possible_answers as $possible_answer_post) {
                    $possible_answer = new PossibleAnswer((array)$possible_answer_post);
                    $possible_answer->question_id = $question->id;
                    $possible_answer->save();
                }
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_SERVER_ERROR);
        }
        $this->setRestResponseAndExit(null, self::HTTP_CREATED);
    }
    protected function _fill($data)
    {
        $surveyId = $data->surveyId;
        $data = $data->data;

        $connection = Survey::connection();
        $connection->transaction();

        try{
            $surveyResult = new SurveyResult(
                array(
                  'user_id' => Yala_User::getUid(),
                  'survey_id'=>$surveyId,
                  'completed'=>true
                )
            );
            $surveyResult->save();

            $surveyUser = SurveyUser::find(
                array(
                     'conditions' => array(
                         'survey_id' => $surveyId,
                         'user_id' => Yala_User::getUid()
                     )
                )
            );
            $surveyUser->filled=1;
            $surveyUser->save();


            foreach ($data as $item) {
                $sr = new SurveyDetailedResult(
                    array(
                        'question_id'   => $item->questionId,
                        'answer_id'     => $item->answerId,
                        'answer_content'=> ($item->answerId) ? null : $item->value,
                        'survey_result_id'=>$surveyResult->id
                    )
                );
                $sr->save();
            }
            $surveyResult->percent_result = SurveyResult::calculateScore($surveyResult->id);
            $surveyResult->save();


            $connection->commit();
        }catch(Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
        }

        $this->setRestResponseAndExit(null, self::HTTP_CREATED);

    }
    protected function _send($data)
    {

        $connection = Group::connection();
        try{
            $connection->transaction();

            if (!$data->surveyId) {
                $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
            }

            foreach ($data->groups as $group) {
                $dbGroup = Group::find_by_id($group->id, array('with'=>'users'));
                foreach ($dbGroup->users as $user) {
//                    if this user already received the survey, continue with another one
//                    so if he is in two groups, he will receive the survey only once
                    if (SurveyUser::count(array('user_id'=>$user->id, 'survey_id'=>$data->surveyId))) {
                        //update deadline
                        $su = SurveyUser::first(array('user_id'=>$user->id, 'survey_id'=>$data->surveyId));
                        $su->deadline = (strripos($group->deadline, "1970") !== false) ? null : $group->deadline;
                        $su->save();
                        continue;
                    }

                    $su = new SurveyUser();
                    $su->user_id = $user->id;
                    $su->survey_id = $data->surveyId;
                    $su->filled = false;
                    $su->sent = date('Y-m-d H:i:s', time());
                    $su->deadline = $group->deadline;
                    $su->save();
                }

            }
            $connection->commit();
        }catch(Exception $e) {
            $connection->rollback();
            //the only exception we get here and we care, is that survey was sent earlier to that group
            //(so row in db was created, and we get duplicate key exception),
            //thou this is not exceptional situation, that's the fastest quick&dirty solution
            // - to rewrite in free time
            $this->setRestResponseAndExit(null, self::HTTP_OK);
        }
        $this->setRestResponseAndExit(null, self::HTTP_CREATED);
    }

    protected function _finishResume($data, $date)
    {

        try{
            $survey = Survey::find($data['surveyId']);
            $survey->completed = $date;
            $survey->save();

            if ($date){
                //pobierz uzytkownikow ktorzy nie wypelnili ankiety, ustaw im, ze wypelnili
                //(sprawdzic jak to sie zachowanie przy wynikach itd)

                //pobierz testy uzytkownikow, wstaw im wszedzie 0pkt
            }
        }catch(Exception $e){
            $this->setRestResponseAndExit(null, self::HTTP_SERVER_ERROR);
        }

        $this->setRestResponseAndExit(null, self::HTTP_OK);
    }

    protected function _pagedData(\Closure $dataFilter = null)
    {
        if ($this->_modelName) {
            $options = $this->_getPagerOptionsForModel();
            $identity = Yala_User::getInstance()->getIdentity();

            if (isset($this->getRequest()->method)) {
                $count = array_key_exists('total_records', $options);

                switch($this->getRequest()->method) {
                    case 'listForGroupAndSurvey':
                          $this->_listForGroupAndSurvey($this->getRequest(), $count);
                        break;
                    case 'averageResults':
                         return $this->_averageResults($this->getRequest()->getParams(), $count);
                        break;
                    case 'library':
                        return $this->_library($this->getRequest(), $count);
                        break;
                }

            } else {

                if (isset($this->getRequest()->archived) && $identity->role_id != Role::USER) {
                    $options['conditions'] = $this->getRequest()->archived ?
                            array('archived = 1') :
                            array('archived != 1');
                }

                if (array_key_exists('total_records', $options)) {
                    unset($options['total_records']);
                    $options['select'] = 'COUNT(*) AS total_records';

                    $data = Survey::firstForUser(
                        $options,
                        array(
                            'identity' => $identity,
                            'filled' =>  $this->getRequest()->filled,
                            'type'=> $this->getRequest()->type,
                            'archived' => $this->getRequest()->archived
                        )
                    );
                    $this->setRestResponseAndExit($data->to_array(), self::HTTP_OK);
                } else {
                    $data = Survey::allForUser(
                        $options,
                        array(
                            'identity' => $identity,
                            'filled' =>  $this->getRequest()->filled,
                            'type'=> $this->getRequest()->type,
                            'archived' => $this->getRequest()->archived
                        )
                    );

                    array_walk(
                        $data,
                        function(&$item) {
                            $item = $item->to_array();
                        }
                    );
                    $this->setRestResponseAndExit($data, self::HTTP_OK);
                }
            }
        }
    }
    public function _listForGroupAndSurvey($data, $count)
    {
        $options = array(
            'conditions' => array(
                'group_id=?', $data->groupId
            ),
            'from'      => 'users',
            'select'    => 'percent_result as average_score, su.deadline, sr.created, username, users.id',
            
            'joins'     => array(
                            ' LEFT JOIN group_users gu ON gu.user_id = users.id
                              LEFT JOIN survey_results sr on (sr.user_id = users.id and survey_id='.intval($data->surveyId).')
                              LEFT JOIN survey_users su ON su.user_id = gu.user_id and su.survey_id=' . intval($data->surveyId)
            ),
        );
        $data = null;

        if ($count) {
            $options['select'] = 'COUNT(*) AS total_records';
            $data = User::first($options)->to_array();

        } else {
            $data = User::find('all', $options);

            array_walk(
                $data,
                function(&$item) {
                    $item = $item->to_array();
                }
            );

        }
        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }

    public function _detailedResults($data)
    {
        //hack, when user belongs to many groups and no group_id specified, we pick up the first group
        $groupId = null;
        if (!isset($data->groupId) || $data->groupId == 'undefined'){
            $group = GroupUser::first(array('user_id' => $data->userId));
            $groupId = $group->group_id;
        }else{
            $groupId = $data->groupId;
        }

        $data =  
        Survey::all(
             array(
                'select' => 's.name, s.type as mainType, sq.type, sq.title as question_name, sq.id as question_id, spa.content, spa.correct, (CASE WHEN spa.correct = 1 THEN sdr.answer_id ELSE null END) as answer_id, sdr.answer_content',

                'joins'  => 
                    'INNER JOIN survey_questions AS sq ON sq.survey_id = s.id
                    LEFT JOIN survey_possible_answers AS spa ON spa.question_id = sq.id
                    INNER JOIN survey_results AS sr ON sr.survey_id = s.id AND sr.user_id = '.intval($data->userId).'
                    INNER JOIN survey_detailed_results AS sdr ON sdr.survey_result_id = sr.id AND sdr.question_id = sq.id',
                'from'   => 'surveys AS s',
                
                
                'conditions'=> array(
                    's.id = ? ',
                    $data->surveyId
                )
            )
        );

        array_walk(
            $data,
            function(&$item) {
                $item = $item->to_array();
            }
        );

        $out = array();
        $i=0;
        foreach ($data as $item) {
            if (!$i){
                $out["info"]['survey_name'] = $item['name'];
                ++$i;
            }
            $out["data"][$item['question_id']][] = $item;
        }


        $this->setRestResponseAndExit($out, self::HTTP_OK);
    }

    public function _averageResults($data, $count=false)
    {

         $options = array(
            'select' => 's.name, sr.percent_result as percent',

            'joins'=> 'INNER JOIN surveys s ON s.id = sr.survey_id',

            'from' => 'survey_results sr',
            'conditions' => array('sr.user_id=?', $data['id'])
        );

        if ($count) {
            $options['select'] = 'COUNT(*) AS total_records';
            $data = Survey::first($options);

            if (is_object($data)) {
                $data = $data->to_array();
            } else {
                $this->setRestResponseAndExit("data is not object", self::HTTP_SERVER_ERROR);
            }
        } else {
            $data = Survey::all($options);
            array_walk(
                $data,
                function(&$item) {
                    $item = $item->to_array();
                    if (empty($item['percent'])){
                        $item['percent'] = "0";
                    }
                    $item['percent'] .= '%';
                }
            );

        }
        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }
    public function _library($data, $count=false)
    {
        $options = array(
            'select' => 'surveys.*, surveys.user_id, users.username',
            'joins'  => 'LEFT JOIN users ON users.id = surveys.user_id',
            'from'   => 'surveys',
            'conditions'=> array('surveys.library=1 and surveys.type=?', $data->type)

        );

        if ($count) {
            $options['select'] = 'COUNT(*) AS total_records';
            $data = Survey::first($options)->to_array();
        } else {
            $data = Survey::all($options);

            array_walk(
                $data,
                function(&$item) {
                    $item = $item->to_array();
                }
            );
        }
        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }
    public function _averageSurveyResults($data)
    {
        $data =  Survey::all(
            array(
                'select' => 'COUNT(*) as replies_count, name, answer_id, spa.content, q.title, q.id, correct',

                'joins'=>   'INNER JOIN survey_questions q ON q.survey_id = surveys.id
                             LEFT JOIN survey_possible_answers spa ON spa.question_id = q.id
                             LEFT JOIN survey_detailed_results sdr ON sdr.answer_id = spa.id',

                'from'=>' surveys',

                'group' => 'surveys.name, q.type, answer_id, spa.content,q.title, q.id, correct',
                'order' => 'q.id asc',
                'conditions'=> array(
                    "survey_id=? AND (
                        (q.type='text' and answer_id is null ) OR
                        (q.type='checkboxes' and answer_id is not null) OR
                        (q.type='multichoice' and answer_id is not null)
                    )",
                    $data->surveyId
                )
            )
        );

        $out = array();

        array_walk(
            $data,
            function(&$item) {
                $item = $item->to_array();
            }
        );

        foreach ($data as $item) {
            $out[$item['id']][] = $item;
        }

        $this->setRestResponseAndExit($out, self::HTTP_OK);
    }

    public function _copyFromLibrary($data)
    {
        $conn = Survey::connection();
        $conn->transaction();
        try{
            //pobierz ankiete i utworz nowy obiekt
            $survey = Survey::find($data['surveyId']);

            $newSurvey = new Survey;

            //przepisz atrybuty, skasuj ID i zapisz
            foreach ($survey->attributes() as $key => $val) {
                $newSurvey->{$key} = $val;
            }
            $newSurvey->id = null;
            $newSurvey->user_id = Yala_User::getUid();
            $newSurvey->archived = false;
            $newSurvey->library = 0;
            $newSurvey->name = 'Kopia - ' . $newSurvey->name;

            $newSurvey->save();

            //przepisz pytania
            foreach ($survey->questions as $question) {
                //nowy ID ankiety, ale usuwamy ID pytania, zapisujemy, i pytanie ma nowe ID
                $newQuestion = new Question();

                //przepisujemy wszystkie atrybutu
                foreach ($question->attributes() as $questionKey => $questionVal) {
                    $newQuestion->{$questionKey} = $questionVal;
                }

                //ustawiamy nowe id ankiety i ID pytania
                $newQuestion->survey_id = $newSurvey->id;
                $newQuestion->id = null;
                $newQuestion->save();

                //usuwamy ID odpowiedzi, przy zapisie dostanie nowe
                foreach ($question->possible_answers as $pa) {
                    $newPa = new PossibleAnswer();
                    //przepisujemy wszystkie atrybutu
                    foreach ($pa->attributes() as $paKey => $paValue) {
                        $newPa->{$paKey} = $paValue;
                    }
                    $newPa->id = null;
                    $newPa->question_id = $newQuestion->id;
                    $newPa->save();
                }
            }

            $conn->commit();
            $this->setRestResponseAndExit(null, self::HTTP_CREATED);
        }catch(Exception $e) {
            $conn->rollback();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_SERVER_ERROR);
        }
    }
}