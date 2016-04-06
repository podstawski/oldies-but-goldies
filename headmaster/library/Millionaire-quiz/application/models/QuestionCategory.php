<?php
class Millionaire_Model_QuestionCategory extends Zend_Db_Table_Abstract 
{ 
	protected $_name = 'question_categories'; 
	protected $_primary = 'id';
    
    function findByQuestionId($question_id) {
        $query = $this->select();
        $query->where('question_id = ?', $question_id); 
        $result = $this->fetchRow($query);
        return $result;
    }

    function getAll() {
        $query = $this->select(); 
        $result = $this->fetchAll($query);
        return $result;
    }
	
    function findAllByQuestionId($question_id) {
        $query = $this->select(); 
        $query->where('question_id = ?', $question_id); 
        $result = $this->fetchAll($query);
        return $result;
    }
   
    function getByQuestionsIds($question_id) {
        $query = $this->select(); 
        $query->where('question_id IN(?)', $question_id); 
        $result = $this->fetchAll($query);
        return $result;
    }
	
    function getByCategoryId($category_id) {
        $query = $this->select(); 
        $query->where('category_id = ?', $category_id); 
        $result = $this->fetchAll($query);
        return $result;
    }
	
	function getByCategoryIdAndActive($category_id) {
        $query = $this->select()->setIntegrityCheck(false);
        $query->from(array('a' => 'question_categories'), array('a.question_id','a.category_id')); 
		$query->join(array('b' => 'questions'), 'b.id = a.question_id', array('b.id','b.status')); 
        $query->where('a.category_id = ?', $category_id)->where('COALESCE(b.status, 0) = ?', 0); 
		return $this->fetchAll($query);
	}
	
    function findByQuestionIdAndCatId($question_id, $category_id) {
        $query = $this->select(); 
        $query->where('question_id = ?',$question_id)->where('category_id = ?',$category_id); 
        $result = $this->fetchAll($query);
        return $result;
    }
    
    function getCategories($question_id) {
        $query = $this->select()->setIntegrityCheck(false);
        $query->from(array('q' => 'question_categories'), array('q.id','q.question_id','q.category_id')); 
        $query->join(array('c' => 'categories'), 'c.id = q.category_id', array('c.name','c.parent_id','c.category_type_id')); 
        $query->where('question_id = ?', $question_id); 
        $categories = $this->fetchAll($query);
        $result = array();
		foreach($categories as $key=>$category) {
            $result[$category->category_type_id][] = array(
				'id' => $category->category_id,
				'name' => $category->name,
				'parent' => $category->parent_id
            );
        }
        return $result;
    }

    function findQuestionsByCategory($categories) {
        $query = $this->select(); 			
		$query->where('category_id in (?)', $categories);
        $result = $this->fetchall($query);
		
		// zwracamy sam id w tablicy
		foreach($result as $key=>$val) {
			$ids[$val->question_id][] = $val->category_id;
		}
		foreach($ids as $key=>$val) {
			if(count($val)<count($categories)) unset($ids[$key]);
		}
        return $ids;
    }

	function getByQuestionAndType($question_id,$category_type_id) {
    	$dbQuestionCategories = new Model_QuestionCategory;
		$query = $dbQuestionCategories->select()->setIntegrityCheck(false);
		$query->from(array('a' => 'question_categories'), array('a.id', 'a.question_id', 'a.category_id')); 
		$query->join(array('b' => 'categories'), 'b.id = a.category_id', array('b.name','b.category_type_id')); 
		$query->where('a.question_id = ?', $question_id)->where('b.category_type_id = ?',$category_type_id);
		$query->order('b.category_type_id ASC', 'a.category_id ASC');
		$result = $dbQuestionCategories->fetchAll($query);
		return $result;
	}

    function findByCategory($category,$page=1,$pageLimit=20) {
        $dbQuestions = new Model_Question;
        $query = $dbQuestions->select()->setintegritycheck(false);
        $query->from(array('q' => 'questions'), array('q.id','q.author_id','q.question','q.question_hash','q.media','q.source','q.created','q.status')); 
        $query->join(array('c' => 'question_categories'), 'q.id = c.question_id', array('c.category_id')); 
        $query->where('c.category_id = ?', $category);
		$query->order('q.id');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
    }

    function countQuestions($status = false, $author_id = false, $own_questions=false) {
		$query = $this->select();
        $dbQuestions = new Model_Question;
        $query = $dbQuestions->select()->setintegritycheck(false);
	    $query->from(array('q' => 'questions'), array('q.id','q.author_id','q.status')); 
		$query->join(array('c' => 'question_categories'), 'q.id = c.question_id', array('c.category_id'));
		if($status) {
			$query->where('status = ?', $status);
		}
		if($author_id) {
			$query->where('author_id = ?', $author_id);
		}
		$questions = $this->fetchAll($query);
		$result = array();
		foreach($questions as $key=>$question) {
			if(isset($result[$question->category_id])) {
				$result[$question->category_id]++;
			} else {
				$result[$question->category_id] = 1;
			}
		}			
		return $result;
    }

    function categoriesTree($user_id = false, $status = false) {
    	$dbQuestionCategories = new Model_QuestionCategory;
		$query = $dbQuestionCategories->select()->setIntegrityCheck(false);
		$query->from(array('a' => 'question_categories'), array('a.id', 'a.question_id', 'a.category_id')); 
		$query->join(array('b' => 'categories'), 'b.id = a.category_id', array('b.id','b.name','b.category_type_id')); 
		if($user_id) {
			$query->join(array('c' => 'questions'), 'c.id = a.question_id', array('c.author_id','c.status'));
			$query->where('c.author_id = ?', $user_id);
			if($status) {
				$query->orWhere('c.status = ?', $status);
			}
		} elseif($status) {
			$query->join(array('c' => 'questions'), 'c.id = a.question_id', array('c.author_id','c.status'));
			$query->where('c.status = ?', $status);
		}
		$query->order('b.category_type_id ASC', 'a.category_id ASC');
        $kategorie = $dbQuestionCategories->fetchAll($query);
		foreach($kategorie as $key=>$kategoria) {
			$result[$kategoria['category_type_id']][$kategoria->category_id][] = $kategoria->question_id;
		}
		return $result;
    }

    function categoriesQuestions($user_id = false, $status = false, $count_parent_categories = false) {
    	$dbQuestionCategories = new Model_QuestionCategory;
		$query = $dbQuestionCategories->select()->setIntegrityCheck(false);
		$query->from(array('a' => 'question_categories'), array('a.id', 'a.question_id', 'a.category_id')); 
		$query->join(array('b' => 'categories'), 'b.id = a.category_id', array('b.id','b.name','b.category_type_id')); 
		if($user_id) {
			$query->join(array('c' => 'questions'), 'c.id = a.question_id', array('c.author_id','c.status'));
			$query->where('c.author_id = ?', $user_id);
			if($status) {
				$query->orWhere('c.status = ?', $status);
			}
		} elseif($status) {
			$query->join(array('c' => 'questions'), 'c.id = a.question_id', array('c.author_id','c.status'));
			$query->where('c.status = ?', $status);
		}
		$query->order('b.category_type_id ASC', 'a.category_id ASC');
        $kategorie = $dbQuestionCategories->fetchAll($query);
		foreach($kategorie as $key=>$kategoria) {
			$result[$kategoria->category_id][] = $kategoria->question_id;
		}
		if($count_parent_categories) {
			$dbCategories = new Model_Category;
			$kategorie = $dbCategories->getCategoriesArray();
			foreach($kategorie as $kat) {
				if($kat['parent_id'] != 0 && is_array($pytania_w_kategoriach[$kat['id']])) {
					if(isset($result[$kat['parent_id']]) && is_array($result[$kat['parent_id']])) {
						$result[$kat['parent_id']] = array_merge($result[$kat['parent_id']], $result[$kat['id']]);
					} else {				
						$result[$kat['parent_id']] = $result[$kat['id']];
					}
				}
			}			
		}
		return $result;
    }

    function questionCategories() {
    	$dbQuestionCategories = new Model_QuestionCategory;
		$query = $dbQuestionCategories->select()->setIntegrityCheck(false);
		$query->from(array('a' => 'question_categories'), array('a.id', 'a.question_id', 'a.category_id')); 
		$query->join(array('b' => 'categories'), 'b.id = a.category_id', array('b.id','b.name','b.category_type_id')); 
		$query->order('b.category_type_id ASC', 'a.category_id ASC');
        $kategorie = $dbQuestionCategories->fetchAll($query);
		foreach($kategorie as $key=>$kategoria) {
			$result[$kategoria->question_id][] = $kategoria->category_id;
		}
		return $result;
    }

	// wyznaczanie wspólnej części tablic
	function arrays_common_part($arrays = false) {
		if($arrays && is_array($arrays)) {
			$common = array();
			$commonVierified = array();
			foreach ($arrays as $array) {
				foreach ($array as $key=>$value) {
					if(isset($common[$value])) {
						$common[$value]++;
					} else {
						$common[$value] = 1;
					}
				}
			}
			foreach ($common as $key => $value) {
				if($value === count($arrays)) {
					$commonVierified[] = $key; 
				}
			}
			$commonVierified = array_unique($commonVierified);
			return $commonVierified;
		} else {
			return false; 		
		}
	}

	function categoriesCommonPart($categories = false, $user_id = false, $status = false) {
		$result = array(
			'questions' => array()
		); 
    	if($categories && is_array($categories) && count($categories) > 1) {

			$categoriesTree = $this->categoriesTree($user_id,$status);
			$arrays = array();
			foreach($categories as $key=>$value) {
				if(isset($categoriesTree[$key+1][$value])) {
					$arrays[$value] = $categoriesTree[$key+1][$value];	
				}				
			}

			if(count($arrays)!=count($categories)) {
				$arrays = array();
			}

			if(count($arrays)>1) {
				$verifiedResult = $this->arrays_common_part($arrays);
			} else {
				$verifiedResult = end($arrays);
			}
			
			if(count($categories) === 2) {
				// $categoriesTree [$category_type] [$category_id] [$question_id]
				$questions_categories = array();
				foreach ($categoriesTree[3] as $catId => $queIds) {					
					foreach($queIds as $queId) {
						$questions_categories[$queId][] = $catId;
					}
				}
			}			

			$verifiedResultCategories = array();
			
			if(is_array($verifiedResult) && count($verifiedResult)>0) {
				if(is_array($questions_categories) && count($questions_categories)>0) {
					foreach($questions_categories as $question_id=>$qCategories) {
						if(is_array($qCategories) && count($qCategories)>0) {									
							foreach($qCategories as $qCategory) {
								if(in_array($question_id, $verifiedResult)) {
									if(isset($verifiedResultCategories[$qCategory])) {
										$verifiedResultCategories[$qCategory]++;
									} else {
										$verifiedResultCategories[$qCategory] = 1;
									}
								}
							}
						}
					}
				}
			}

			$result['questions'] = $verifiedResult;
			$result['categories'] = $verifiedResultCategories;			
			if(count($result['questions'])<1) {
				$result = false;
			}

		} else {
				$result = 'Gimme an array!';    		
    	}
    	return $result;
    }
	
}
