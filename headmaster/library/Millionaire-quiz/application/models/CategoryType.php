<?php
class Millionaire_Model_CategoryType extends Zend_Db_Table_Abstract 
{ 
	protected $_name = 'category_types'; 
	protected $_primary = 'id'; 

	function getCategoryTypesArray() {
        $query = $this->select();         
		$query->order('id');		
        $result = $this->fetchAll($query);		
        $category_types = array();
        foreach($result as $category_type) {
            $category_types[$category_type->id] = array(
                                  'id' => $category_type->id,
                                  'name' => $category_type->name
                                  );
        }
        return $category_types;
	} 

}
