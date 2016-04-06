<?php

class Millionaire_Model_Lifebuoy extends Zend_Db_Table_Abstract 
{ 
	protected $_name = 'lifebuoys'; 
	protected $_primary = 'id'; 
       
    function findByQuestionId($question_id, $lifebuoy_type = false) {
		$query = $this->select();
		if($lifebuoy_type) {
			$query->where('question_id = ?', $question_id, 'lifebuoy_type= ?', $lifebuoy_type); 
			$dbLifeBuoy = $this->fetchRow($query);
			$LifeBuoysArray = array(
				'id' => $dbLifeBuoy->id,
				'question_id' => $dbLifeBuoy->question_id,
				'lifebuoy' => $dbLifeBuoy->lifebuoy,
				'lifebuoy_type' => $dbLifeBuoy->lifebuoy_type
			);
		} 
		else  {
			$query->where('question_id = ?', $question_id); 
			$dbLifeBuoys = $this->fetchAll($query);
			$LifeBuoysArray = array();
			foreach($dbLifeBuoys as $dbLifeBuoy) {
				$LifeBuoysArray[] = array(
					'id' => $dbLifeBuoy->id,
					'question_id' => $dbLifeBuoy->question_id,
					'lifebuoy' => $dbLifeBuoy->lifebuoy,
					'lifebuoy_type' => $dbLifeBuoy->lifebuoy_type
				);
			}
		}
		return $LifeBuoysArray;
	}

	function getByQuestionId($question_id, $lifebuoy_type = false) {
		return $this->findByQuestionId($question_id, $lifebuoy_type);
	}

}
