<?php

class TrainingCenter extends AclModel
{
    static $use_view = true;
    
    static $has_many = array(
        array('courses')
    );

    static $validates_presence_of = array(
        array('name'),
        array('street'),
        array('zip_code'),
        array('city')
    );

    public function get_full_address()
    {
       return $this->name . PHP_EOL . $this->street . PHP_EOL . $this->zip_code . ' ' . $this->city;
    }
    
    
    public static function findAllRooms($id)
    {
        if (!is_array($id)) $id=array($id);
        
        return self::getArrayOfFieldValues(Room::find('all', array('conditions' => array('training_center_id IN (?)', $id))));
    }
    
    public static function projectLeaders($id)
    {
        if (!is_array($id)) $id=array($id);

        $projects=self::getArrayOfFieldValues(Course::find('all',array('conditions' => array('training_center_id IN (?)', $id))),'project_id');
        $leaders=self::getArrayOfFieldValues(ProjectLeaders::find('all',array('conditions' => array('project_id IN (?)', $projects))),'user_id');

        return array_unique($leaders);
    }
    
}
