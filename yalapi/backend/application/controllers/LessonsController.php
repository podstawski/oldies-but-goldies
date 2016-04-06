<?php
require_once 'RestController.php';

class LessonsController extends RestController
{
    private  $dateFormat = 'Y-m-d H:i';
    
    function indexAction()
    {
        ActiveRecord\Serialization::$DATETIME_FORMAT = $this->dateFormat;

        $params = array();
        $paramNames = array('coaches', 'rooms', 'courseunits', 'courseunits_not', 'ds', 'de', 'count', 'group');

        foreach($paramNames as $param)
        {
            if($this->_getParam($param) !== null)
            {
                $value = json_decode($this->_getParam($param));
                if(count($value) > 0) { $params[$param] = $value; }
            }
        }

        $flags = array();
        if($this->_getParam('flags') !== null)
        {
            $flags = json_decode($this->_getParam('flags'));
            if(!in_array('main', $flags)) {
                $params['courseunits_not'] = $params['courseunits'];
                unset($params['courseunits']);
            }
        }

        $lessonModel = new Lesson();
        $response = $lessonModel->getLessonBy($params);

        $flagsToAdd = array();
        foreach($flags as $flag){

            $flagsToAdd[$flag] = true;
        }

        foreach ($response as &$lesson)
        {
            $lesson = array_merge($lesson, $flagsToAdd);
            if(!isset($lesson['main'])) {
                $lesson['color'] = "#E8E8E8";
                $lesson['textColor'] = "#000000";
            }
        }

        $this->setRestResponseAndExit($response, self::HTTP_OK);
    }

    public function postAction()
    {
        ActiveRecord\Serialization::$DATETIME_FORMAT = $this->dateFormat;

        $post = $this->_getRequestData('POST');
        (sizeof($post) === 0) ? $this->setRestResponseAndExit('no post data!', self::HTTP_NOT_ACCEPTABLE) : false;

        $this->checkLessonDate($post);

        $recurring = (isset($post['recurring'])) ? json_decode($post['recurring']) : null;
        unset($post['recurring']);

        $lessonModel = new Lesson();

        $state = ($recurring == null) ?
            $lessonModel->createLesson($post) :
            $lessonModel->createLessons($post, $recurring);
        
        (!is_array($state)) ?
            $this->setRestResponseAndExit(null, self::HTTP_CREATED) :
            $this->setRestResponseAndExit($state, self::HTTP_NOT_ACCEPTABLE);
    }

    public function putAction()
    {
        ActiveRecord\Serialization::$DATETIME_FORMAT = $this->dateFormat;

        $post = $this->_getRequestData('PUT');
        (sizeof($post) == 0) ? $this->setRestResponseAndExit('no post data!', self::HTTP_NOT_ACCEPTABLE) : false;

        $this->checkLessonDate($post);

        $lesson = new Lesson();
        $correctDate = $lesson->isCorrectDate($post, true);
        $noCollisons = ($correctDate) ? $lesson->noCollisions($post, $this->_getParam('id')) : true;

        if ($noCollisons && $correctDate) {
            unset($post['recurring']);
            parent::putAction($post);
        }

        $this->setRestResponseAndExit($lesson->lessonErrors, self::HTTP_NOT_ACCEPTABLE);
    }

    protected function checkLessonDate(array $data)
    {
        $options = $this->getInvokeArg('bootstrap')->getOption('lessons');
        if (isset($options['max_date'])
        &&  isset($data['start_date'])
        &&  ((new DateTime($data['start_date'])) > (new DateTime($options['max_date'])))
        ) {
            $this->setRestResponseAndExit($this->view->translate('you cannot schedule lesson after %s', $options['max_date']), self::HTTP_NOT_ACCEPTABLE);
        }
    }
}