<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
class Service_Quiz
{
    const HELP_EXPERT  = 1;
    const HELP_OPINION = 2;
    const HELP_HALF    = 4;

    /**
     * @param $quiz_id string
     * @param $user_id string
     * @return array
     */
    public function init($quiz_id, $user_id)
    {
        $userRow = User::find($user_id);
        $quizRow = Quiz::find($quiz_id);

        $quizScoreRow = $this->_getQuizScoreRow($user_id, $quiz_id);
        $data = array(
            'user_name'     => $userRow->username,
            'time_limit'    => $quizRow->time_limit,
        );

        if ($quizScoreRow) {
            $data['deadline'] = $quizScoreRow->start_time + $quizRow->time_limit - date('U');
        } else {
            $data['deadline'] = $quizRow->time_limit;
        }

        return $this->_responseToString($data);
    }

    /**
     * @param $quiz_id integer
     * @param $user_id integer
     * @return array
     */
    public function start($quiz_id, $user_id)
    {
        $bestResultRow = QuizScores::find(array(
            'select'    => 'score',
            'order'     => 'score DESC',
            'limit'     => 1,
        ));
        $quizScoreRow = $this->_getQuizScoreRow($user_id, $quiz_id);
        if ($quizScoreRow) {
            return $this->_responseToString(array(
                'level'         => $quizScoreRow->level,
                'score'         => $quizScoreRow->score,
                'best_result'   => $bestResultRow->score,
                'help'          => $this->_replaceStatusWithHelpObject($quizScoreRow->status)
            ));
        } else {
            $data = array(
                'user_id' => $user_id,
                'quiz_id' => $quiz_id,
                'start_time' => date('U'),
                'total_time' => 0,
                'level' => 0,
                'score' => 0,
                'status'  => 0
            );
            QuizScores::create($data);
            $data['best_result'] = ($bestResultRow) ? $bestResultRow->score : 0;
            return $this->_responseToString($data);
        }
    }

    /**
     * @param $data object
     * @return string
     */
    public function save_result($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        $quizScoreRow               = $this->_getQuizScoreRow($data['user_id'], $data['quiz_id']);
        $quizScoreRow->level        = $data['level'] + 1; //RB flash returns bad level, we have to increment it
        $quizScoreRow->score        = $data['result'];
        $quizScoreRow->total_time   += $data['time'];
        $quizScoreRow->status       = isset($data['help']) ? $this->_replaceHelpObjectWithStatus($data['help']) : 0;
        if ($quizScoreRow->is_valid()) {
            return $quizScoreRow->save() ? 'true' : 'false';
        } else {
            return 'false';
        }
    }

    /**
     * @param $quiz_id integer
     * @param $user_id integer
     * @return mixed
     */
    public function best_results($quiz_id, $user_id)
    {
        $quizScoreRow = $this->_getQuizScoreRow($user_id, $quiz_id);
        if (!$quizScoreRow) {
            return 'false';
        }
        $result = array();
        $result['best_result'] = array();
        $result['best_result']['level']     = $quizScoreRow->level;
        $result['best_result']['user_name'] = $quizScoreRow->user->username;
        $result['best_result']['result']    = $quizScoreRow->score;
        $result['best_result']['time']      = $quizScoreRow->total_time;
        $result['best_result'] = $this->_responseToString($result['best_result']);

        $allScores = QuizScores::all(array(
            'order' => 'score DESC',
            'include' => array('user')
        ));

        $results = array_map(function($scoreRow) {
            return array(
                'user_name' => $scoreRow->user->username, 
                'level'     => (string) $scoreRow->level,
                'result'    => (string) $scoreRow->score,
                'time'      => (string) $scoreRow->total_time
            );
        }, $allScores);

        $result['results'] = $results;

        return $result;
    }

    private function _getQuizScoreRow($user_id, $quiz_id)
    {
        return QuizScores::find(array(
            'conditions' => array('user_id = ? AND quiz_id = ?', $user_id, $quiz_id),
            'include' => array('user')
        ));
    }

    private function _responseToString($data)
    {
        array_walk_recursive($data, function(&$el) {
            $el = (string)$el;
        });
        return $data;
    }

    private function _replaceHelpObjectWithStatus($obj)
    {
        $val = 0;
        $val += ((bool)$obj['expert']) ? self::HELP_EXPERT : 0;
        $val += ((bool)$obj['half']) ? self::HELP_HALF : 0;
        $val += ((bool)$obj['opinion']) ? self::HELP_OPINION : 0;
        return $val;
    }

    private function _replaceStatusWithHelpObject($status)
    {
        $obj = new stdClass();
        $obj->expert    = ($status & self::HELP_EXPERT)     ? 1 : 0;
        $obj->opinion   = ($status & self::HELP_OPINION)    ? 1 : 0;
        $obj->half      = ($status & self::HELP_HALF)       ? 1 : 0;

        return $this->_responseToString((array)$obj);
    }
}
