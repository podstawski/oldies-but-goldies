<?php
class Millionaire_Model_Question extends Zend_Db_Table_Abstract {
	protected $_name = 'questions'; 
	protected $_primary = 'id'; 

	function findByQuestionId($question = false) {
		if($question) {
			$query = $this->select(); 
			$query->where('id = ?', $question);
			$result = $this->fetchRow($query);
			return $result;
		} else {
			return false;
		}
	}

	function getById($id) {
		return $this->findByQuestionId($id);
	}

	function getByQuestionsIds($id) {
		$query = $this->select(); 
		$query->where('id IN(?)', $id); 
		$result = $this->fetchAll($query);
		return $result;
	}
	
	function getByUsersIds($users_ids) {
		$query = $this->select(); 
		$query->where('author_id IN(?)', $users_ids); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByUserId($user_id) {
		$query = $this->select(); 
		$query->where('author_id = ?', $user_id); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByAuthorId($author_id,$page=1,$pageLimit=20) {
		$query = $this->select();
		$query->where('author_id = ?', $author_id);
		$query->order('questions.id');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
	}

	function getAll($page=1,$pageLimit=20,$use_pagination=true) {
		$query = $this->select(); 
		$query->order('id');
		if($use_pagination) {
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
			$paginator->setItemCountPerPage($pageLimit); 
			$paginator->setCurrentPageNumber($page); 
			return $paginator;
		} else {
			$result = $this->fetchAll($query);
			return $result;				
		}
	}

	function getUserQuestionsAndAnswers($author_id = false, $array = true) {
		if($author_id) {
			$answerTable = new Model_Answer;
			$query = $answerTable->select()->setIntegrityCheck(false);
			$query->from(array('q' => 'questions'), array('q.id', 'q.question', 'q.author_id', 'q.source', 'q.created', 'q.question_hash'));
			$query->join(array('a' => 'answers'), 'q.id = a.question_id', array('a.answer', 'a.is_correct')); 
			if(is_array($author_id)) {
				$query->where('author_id IN (?)', $author_id);
			} else {
				$query->where('author_id = ?', $author_id);
			}
			$query->order('q.id');
			$result = $this->fetchAll($query);
			if($array) {
				return $result->toArray();				
			} else {				
				return $result;				
			}
		} else {
			return false;
		}
	}

	function findByQuestion($keywords,$page=1,$pageLimit=20) {
		$query = $this->select(); 
		$query->where('question LIKE ?', "%$keywords%"); 
		$query->order('id');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
	}

	function questionSearch($params) {
		if(!isset($params['perPage'])) {
			$params['perPage'] = 20;
		}
		if(!isset($params['page'])) {
			$params['page'] = 1;
		}

		$query = $this->select();
		if(isset($params['sort'])) {
			switch($params['sort']) {
				case 'id':
					$query->order('id');
					break;
				case 'question':
					$query->order('question');
					break;
				case 'author_id':
					$query->order('author_id');
					break;
				default:
					$query->order('id');
					break;
			}
		} else {
			$query->order('id');
		}

		$query->from('questions', array('id', 'question', 'category_ids' => new Zend_Db_Expr('array_to_string( 
				array( 
					SELECT categories.id 
					FROM question_categories 
					INNER JOIN categories ON categories.id = question_categories.category_id 
					WHERE question_categories.question_id = questions.id 
					GROUP BY categories.id 
				), \'#\' 
			)'),
			'author_id','question_hash','media','source','created','status','flag','flag_data'
		));

		if(isset($params['question'])) {
			$query->where('LOWER(question) LIKE LOWER(?)', '%'.$params['question'].'%'); 
		}
		if(isset($params['author_id'])) {
			$query->where('author_id = ?', $params['author_id']); 
		}
		if(isset($params['status'])) {
			$query->where('status = ?', $params['status']); 
		}
		if(isset($params['source'])) {
			$query->where('source LIKE ?', '%'.$params['source'].'%'); 
		}
		if(isset($params['media'])) {
			$query->where('media LIKE ?', '%'.$params['media'].'%'); 
		}
		
		if(isset($params['flag1']) && isset($params['flag10'])) {
			$query->where('flag > ?', 0); 
		} elseif (isset($params['flag1']) && !isset($params['flag10'])) {
			$query->where('flag = ?', 1); 
		} elseif (isset($params['flag10']) && !isset($params['flag1'])) {
			$query->where('flag = ?', 10); 
		}

		$categories = array();
		if(isset($params['level'])) {
			$categories[] = $params['level'];
		}
		if(isset($params['school'])) {
			$categories[] = $params['school'];
		}
		if(isset($params['powiat'])) {
			$categories[] = $params['powiat'];
		}
		if(isset($params['category'])) {
			$categories[] = $params['category'];
		}

		if(count($categories)>0) {
			$dbQuestionCategory = new Model_QuestionCategory;
			$questionCategoriesIds = $dbQuestionCategory->findQuestionsByCategory($categories);
			$questionsIds = array();
			foreach($questionCategoriesIds as $key=>$value) {
				$questionsIds[] = $key;					
			}
			if(count($questionsIds)>0){
				$query->where('id IN(?)', $questionsIds);				 
			} else {
				$query->where('id IN(?)', array(0));				 
			}
		}

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 

		$paginator->setItemCountPerPage($params['perPage']); 
		$paginator->setCurrentPageNumber($params['page']); 
		return $paginator;
	}

	function questionSearchNoPagination($params) {
		if(!isset($params['perPage'])) {
			$params['perPage'] = 20;
		}
		if(!isset($params['page'])) {
			$params['page'] = 1;
		}

		$query = $this->select();
		if(isset($params['sort'])) {
			switch($params['sort']) {
				case 'id':
					$query->order('id');
					break;
				case 'question':
					$query->order('question');
					break;
				case 'author_id':
					$query->order('author_id');
					break;
				case 'status':
					$query->order('status');
					break;
				default:
					$query->order('id');
					break;
			}
		} else {
			$query->order('id');
		}

		//$query = Zend_Db_Table::getDefaultAdapter()->select() 
		$query->from('questions', array('id', 'question', 'category_ids' => new Zend_Db_Expr('array_to_string( 
				array( 
					SELECT categories.id 
					FROM question_categories 
					INNER JOIN categories ON categories.id = question_categories.category_id 
					WHERE question_categories.question_id = questions.id 
					GROUP BY categories.id 
				), \'#\' 
			)'),
			'author_id','question_hash','media','source','created','status','flag','flag_data'
		));


		if(isset($params['question'])) {
			$query->where('question LIKE ?', '%'.$params['question'].'%'); 
		}
		if(isset($params['author_id'])) {
			$query->where('author_id = ?', $params['author_id']); 
		}
		if(isset($params['status'])) {
			$query->where('status = ?', $params['status']); 
		}
		if(isset($params['source'])) {
			$query->where('source LIKE ?', '%'.$params['source'].'%'); 
		}
		
		if(isset($params['flag1']) && isset($params['flag10'])) {
			$query->where('flag > ?', 0); 
		} elseif (isset($params['flag1']) && !isset($params['flag10'])) {
			$query->where('flag = ?', 1); 
		} elseif (isset($params['flag10']) && !isset($params['flag1'])) {
			$query->where('flag = ?', 10); 
		}

		$categories = array();
		if(isset($params['level'])) {
			$categories[] = $params['level'];
		}
		if(isset($params['school'])) {
			$categories[] = $params['school'];
		}
		if(isset($params['category'])) {
			$categories[] = $params['category'];
		}

		if(count($categories)>0) {
			$dbQuestionCategory = new Model_QuestionCategory;
			$questionCategoriesIds = $dbQuestionCategory->findQuestionsByCategory($categories);
			$questionsIds = array();
			foreach($questionCategoriesIds as $key=>$value) {
				$questionsIds[] = $key;					
			}
			if(count($questionsIds)>0){
				$query->where('id IN(?)', $questionsIds);				 
			} else {
				$query->where('id IN(?)', array(0));				 
			}
		}
		$results = $this->fetchAll($query);
		return $results;
	}

	function findByQuestionContent($hash) {
		$query = $this->select(); 
		$query->where('question_hash = ?', $hash); 
		$result = $this->fetchRow($query);
		return $result;
	}

	function findByHash($hash) {
		$query = $this->select(); 
		$query->where('question_hash = ?', $hash); 
		$result = $this->fetchRow($query);
		return $result;
	}

	function findAllQuestionsIds() {
		$query = $this->select(); 
		$query->from('questions', array('id')); 
		$result = $this->fetchAll($query);
		// zwracamy sam id w tablicy
		if(count($result)>0) {
			$ids = array();
			foreach($result as $key=>$val) {
				$ids[] = $val->id;
			}
		} else {
			$ids = false;
		}
		return $ids;
	}

	function findAllActiveQuestionsIds() {
		$query = $this->select(); 
		$query->where('status = ?', 10); 
		$query->from('questions', array('id')); 
		$result = $this->fetchAll($query);
		// zwracamy sam id w tablicy
		if(count($result)>0) {
			$ids = array();
			foreach($result as $key=>$val) {
				$ids[] = $val->id;
			}
		} else {
			$ids = false;
		}
		return $ids;
	}
	
	function getQuestions($page=1,$pageLimit=20) {
		$answerTable = new Model_Answer;
		$query = $answerTable->select()->setIntegrityCheck(false);
		$query->from(array('q' => 'questions'), array('q.id', 'q.question')); 
		$query->join(array('a' => 'answers'), 'q.id = a.question_id', array('a.answer', 'a.is_correct')); 
		$query->order('id');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
	} 
			
}
