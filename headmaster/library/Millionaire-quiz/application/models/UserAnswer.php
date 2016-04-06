<?php

class Millionaire_Model_UserAnswer extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_answers';

    public function getAll() {
        $query = $this->select(); 
        $result = $this->fetchAll($query);
        return $result;
    }    

    public function getCorrect() {
		$query = $this->select(); 
		$query->where('is_correct = ?',1);
        $result = $this->fetchAll($query);
        return $result;
    }    

    public function byUserId($user_id) {
		$query = $this->select(); 
		$query->where('user_id = ?',$user_id);
        $result = $this->fetchAll($query);
        return $result;
    }    

	public function byQuestionId($question_id) {
		$query = $this->select(); 
		if(is_array($question_id)) {	
			$query->where('	question_id IN(?)',$question_id);
		} else {
			$query->where('question_id = ?',$question_id);
		}
        $result = $this->fetchAll($query);
        return $result;
    }    

	public function byQuestionsIds($question_id) {
		return $this->byQuestionId($question_id);
	}

	public function getByQuestionsIds($question_id) {
		return $this->byQuestionId($question_id);
	}

    public function getCorrectJokers() {
		$query = $this->select(); 
		$query->where('is_correct = ?',1)->where('is_joker = ?',1);
        $result = $this->fetchAll($query);
        return $result;
    }    
    
}

