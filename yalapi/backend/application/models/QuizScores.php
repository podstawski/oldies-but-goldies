<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
class QuizScores extends AclModel
{
    static $use_view = true;
    
    static public $validates_presence_of = array(
        array('user_id'),
        array('quiz_id'),
        array('level'),
        array('score'),
        array('start_time'),
        array('total_time')
    );

    static $belongs_to = array(
        array('User')
    );
    
    static $after_save = 'RunAcl';
    
    public function RunAcl()
    {
        $this->grant(Role::USER, $this->user_id, $this->id);
    }
    
    
}
