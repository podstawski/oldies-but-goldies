<?php
class Millionaire_Model_Category extends Zend_Db_Table_Abstract 
{
    const CATEGORY_TYPE_LEVEL   = 1;
    const CATEGORY_TYPE_REGION  = 2;
    const CATEGORY_TYPE_SUBJECT = 3;

    const CATEGORY_LEVEL_EASY   = 1;
    const CATEGORY_LEVEL_MEDIUM = 2;
    const CATEGORY_LEVEL_HARD   = 3;

    public static $categoryTypes = array(
        self::CATEGORY_TYPE_LEVEL,
        self::CATEGORY_TYPE_REGION,
        self::CATEGORY_TYPE_SUBJECT,
    );

    public static $levelNames = array(
        self::CATEGORY_LEVEL_EASY => 'easy',
        self::CATEGORY_LEVEL_MEDIUM => 'medium',
        self::CATEGORY_LEVEL_HARD => 'hard',
    );

	protected $_name = 'categories'; 
	protected $_primary = 'id';

    function findByParentTypeName($parent, $type, $category=false) {
        $query = $this->select();
		if($category) {
	        $query
	        	->where('parent_id = ?', $parent)
				->where('upper(name) = upper(?)', $category) // case kurwa insensitive!
				->where('category_type_id = ?', $type);
	        $result = $this->fetchRow($query);
		} else {
	        $query
	        	->where('parent_id = ?', $parent)
				->where('category_type_id = ?', $type);
			$query->order('name ASC');
	        $result = $this->fetchAll($query);
		}
        return $result;
    }
	
	function findById($id) {
		$query = $this->select(); 
        $query->where('id = ?', $id); 
        $result = $this->fetchRow($query);
		return $result;
	}

	function getById($id) {
		return $this->findById($id);
	}

	function findByName($name) {
		$query = $this->select(); 
    	$query->where('name = ?', $name);
	    $result = $this->fetchRow($query);
		return $result;
	}
	
	function findByType($type, $byName=false, $toArray=false) {
		$query = $this->select(); 
        $query->where('category_type_id = ?', $type); 
        if($byName) $query->order('name ASC');
        $result = $this->fetchAll($query);
		return $result;
	}
	
	function findByParent($parent_id, $type=3, $byName=true) {
		$query = $this->select(); 
        $query
        	->where('parent_id = ?', $parent_id)
			->where('category_type_id = ?', $type);
        if($byName) $query->order('name ASC');
        $result = $this->fetchAll($query);
		return $result;
	}
	
	function findByQuestionId($id) {
		$query = $this->select();
	}

	function getCategoriesArray() {
        $query = $this->select();         
		$query->order('id');
        $result = $this->fetchAll($query);
        $categories = $result->toArray();
        return $categories;
	} 
	
} ?>
