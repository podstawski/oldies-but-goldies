<?php
class Millionaire_Model_Answer extends Zend_Db_Table_Abstract 
{ 

	protected $_name = 'answers'; 
	protected $_primary = 'id';
        
    function findByQuestionId($question_id=false, $limit=4) {
    	if($question_id) {
	    	// po pierwsze: pobieramy prawidłową/e odpowiedź/i
	        $query = $this->select(); 
	        $query->where('question_id = ?', $question_id); 
	        $query->where('is_correct > ?', 0); 
	        $correct = $this->fetchAll($query);
			$result = array();
			foreach($correct as $key=>$answer) {
				$result[] = array(
					'id' => $answer->id,
					'question_id' => $answer->question_id,
					'answer' => $answer->answer,
					'is_correct' => $answer->is_correct,
					'probability' => $answer->probability
				);
			}
			// po drugie: jeżeli są prawidłowe odpowiedzi, doklejamy do nich nie prawidłowe
			if(count($result)>0) {
	    	    $query = $this->select(); 
		        $query->where('question_id = ?', $question_id); 
	        	$query->where('is_correct = ?', 0);
				$query->limit($limit-count($result)); 
	    	    $answers = $this->fetchAll($query);
				foreach($answers as $key=>$answer) {
					$result[] = array(
						'id' => $answer->id,
						'question_id' => $answer->question_id,
						'answer' => $answer->answer,
						'is_correct' => $answer->is_correct,
						'probability' => $answer->probability
					);
				}
				// losujemy kolejność odpowiedzi
		        shuffle($result);
				return $result;
			} else {
				// jeżeli niema prawidłowych odpowiedzi zwróć FALSE
				return false;
			}
		} else {
			return false;
		} 
	}
	        
    function findAllByQuestionId($question_id=false) {
    	if($question_id) {
	        $query = $this->select(); 
	        $query->where('question_id = ?', $question_id); 
	        $query->order('RANDOM()');
	        $result = $this->fetchAll($query);
    		return $result;
    	} else {
    		return false;
    	}
    }    

    function getAll($question_id=false) {
    	if($question_id) {
			$query = $this->select(); 
			if(is_array($question_id)) {
				$query->where('question_id IN(?)', $question_id); 
			} else {
		        $query->where('question_id = ?', $question_id); 
			}
	        $query->order('id ASC');
	        $result = $this->fetchAll($query);
    		return $result;
    	} else {
    		return false;
    	}
    }    

    /**
     * @return array
     */
    public function getAllByQuestionIdArray() {
        $query = $this->select(); 
        $result = $this->fetchAll($query);
		$array = array();
		foreach ($result as $value) {
			if(!isset($array[$value->question_id])) $array[$value->question_id] = array();
			$array[$value->question_id][] = $value->toArray(); 
		}
        return $array;
    }
	        
    function findById($answer_id,$question_id=false) {
        $query = $this->select();
		$query->where('id = ?', $answer_id);
		if($question_id) $query->where('question_id = ?', $question_id);
		$query->limit(1); 
        $result = $this->fetchAll($query);
        return $result;
    }
	        
	function getById($answer_id) {
		if($answer_id>0) {
			$query = $this->select();
			$query->where('id = ?', $answer_id);
	        $result = $this->fetchRow($query);
		} else {
			$result = null;
		}
        return $result;
    }
	    
}
