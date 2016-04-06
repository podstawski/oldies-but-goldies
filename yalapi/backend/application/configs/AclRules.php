<?php

//Notation: Create Read Update Delete

class AclRules
{
    public static $ALL_RIGHTS='CRUD';
    
    public static $rules = array (
        'all' => array(
            Role::SUPER_ADMIN => array (
                'users'                     => 'CRUD',
            ),
            Role::ADMIN => array (
                'lessons'                   => 'CRUD',
                'courses'                   => 'CRUD',
                'groups'                    => 'CRUD',
                'group_users'               => 'CRUD',
                'users'                     => 'CRUD',
                'course_units'              => 'CRUD',
                'projects'                  => 'CRUD',
                'project_leaders'           => 'CRUD',
                'training_centers'          => 'CRUD',
                'rooms'                     => 'CRUD',
                
                'survey_detailed_results'   => 'CRUD',
                'survey_possible_answers'   => 'CRUD',
                'survey_questions'          => 'CRUD',
                'survey_results'            => 'CRUD',
                'survey_users'              => 'CRUD',
                'surveys'                   => 'CRUD',
                
                'quizzes'                   => 'CRUD',
                'quiz_users'                => 'CRUD',
                'quiz_scores'               => 'CRUD',
                
                'reports'                   => 'CRUD',
                'user_profile'              => 'CRUD',
                
                'messages'                  => 'CRUD',
                'message_users'             => 'CRUD',
                'message_attachments'       => 'CRUD',

                'lesson_presence'           => 'CRUD',
                'exams'                     => 'CRUD',
                'exam_grades'               => 'CRUD',
                'course_schedule'           => 'CRUD',
            ),
            
            Role::PROJECT_LEADER => array(
                'projects'                  => 'C',
                'project_leaders'           => 'C',                
                'training_centers'          => 'CR',
                'rooms'                     => 'CR',
                
                'lessons'                   => 'CR',                

                'users'                     => 'CR',
                
                'courses'                   => 'C',
                'course_units'              => 'C',
                'groups'                    => 'C',
                'group_users'               => 'CUD',
                
                'quizzes'                   => 'R',
                'quiz_users'                => 'CR',
                'quiz_scores'               => 'R',

                'user_profile'              => 'C',                

                'lesson_presence'           => 'CR',
                'exams'                     => 'C',
                'exam_grades'               => 'C',
                'course_schedule'           => 'C',

                'messages'                  => 'C',
                'message_users'             => 'C',
                'message_attachments'       => 'C',

                'survey_detailed_results'   => 'C',
                'survey_possible_answers'   => 'C',
                'survey_questions'          => 'C',
                'survey_results'            => 'C',
                'survey_users'              => 'C',
                'surveys'                   => 'C',          
            ),
            
            Role::COACH => array(
                'users'                     => 'R',
                
                'courses'                   => 'R',
                'course_units'              => 'R',
                'groups'                    => 'R',
                'group_users'               => 'CRUD',
                
                'quizzes'                   => 'R',
                'quiz_users'                => 'CR',
                'quiz_scores'               => 'R',

                'user_profile'              => 'C',

                'lesson_presence'           => 'CR',
                'exams'                     => 'C',
                'exam_grades'               => 'C',
                'course_schedule'           => 'C',

                'messages'                  => 'C',
                'message_users'             => 'C',
                'message_attachments'       => 'C',

                'survey_detailed_results'   => 'C',
                'survey_possible_answers'   => 'C',
                'survey_questions'          => 'C',
                'survey_results'            => 'C',
                'survey_users'              => 'C',
                'surveys'                   => 'C',
            ),
            Role::USER => array (
                'users'                     => 'R',
                
                'user_profile'              => 'C',
                'messages'                  => 'C',
                'message_users'             => 'C',
                'message_attachments'       => 'C',
                'quiz_scores'               => 'C',

                'survey_detailed_results'   => 'C',
                'survey_results'            => 'C',
            )
        ),
        'specific' => array (
            
            Role::PROJECT_LEADER => array(
                'projects'                  => 'RU',
                'project_leaders'           => 'R',                
                'training_centers'          => 'U',
                'rooms'                     => 'UD',
                'lessons'                   => 'RUD',
            
                
                'courses'                   => 'RUD',
                'course_units'              => 'RUD',
                'groups'                    => 'RUD',
                'group_users'               => 'RUD',                
                
            ),
            
            Role::USER => array (
                'courses'                   => 'R',
                'course_units'              => 'R',
                'groups'                    => 'R',
               
                
                'lessons'                   => 'R',
                'quizzes'                   => 'R',
                'quiz_scores'               => 'R',
                'user_profile'              => 'RU',
                'lesson_presence'           => 'R',
                'exams'                     => 'R',
                'exam_grades'               => 'R',
                'course_schedule'           => 'R',

                'messages'                  => 'R',
                'message_users'             => 'RUD',
                'message_attachments'       => 'R',

                'survey_detailed_results'   => 'R',
                'survey_possible_answers'   => 'R',
                'survey_questions'          => 'R',
                'survey_results'            => 'R',
                'survey_users'              => 'RU',
                'surveys'                   => 'R'
            ),
            Role::COACH => array (
                'lessons'                   => 'R',
                'user_profile'              => 'RU',
                'training_centers'          => 'R',                 
                
                'messages'                  => 'R',
                'message_users'             => 'RUD',
                'message_attachments'       => 'R',
                
                'survey_detailed_results'   => 'R',
                'survey_possible_answers'   => 'R',
                'survey_questions'          => 'R',
                'survey_results'            => 'R',
                'survey_users'              => 'R',
                'surveys'                   => 'R',
                
                'course_schedule'           => 'RUD',
            )
        )
    );
    
   
    private static $file_parsed = false;
    public static $role_token_trans= array (
        'admin'             => Role::ADMIN,
        'super_admin'       => Role::SUPER_ADMIN,
        'user'              => Role::USER,
        'coach'             => Role::COACH,
        'project_leader'    => Role::PROJECT_LEADER,
        'center_leader'     => Role::CENTER_LEADER,
        
    );
    
    private static function parse_array($array,$prefix=null)
    {
        foreach ($array AS $k=>$v)
        {
            if (is_array($v)) self::parse_array($v,$k);
            else
            {
                $key=explode('.',strtolower(str_replace(' ','',$prefix?"$prefix.$k":$k)));
                
                if (isset(self::$role_token_trans[$key[1]]))
                {
                    $key[1]=self::$role_token_trans[$key[1]];
                    self::$rules[$key[0]][$key[1]][$key[2]] = strtoupper(str_replace(' ','',$v));
                }
            }
            
        }
        
    }
    public static function getRules()
    {
        
        if (!self::$file_parsed)
        {
            self::$file_parsed=true;
            if (file_exists(__DIR__.'/AclRules.ini'))
            {
                self::parse_array(parse_ini_file (__DIR__.'/AclRules.ini',true));
            }
        }
        
        $args=func_get_args();
    
        if (count($args)==0) return self::$rules;
        if (count($args)==1 && isset(self::$rules[$args[0]]) ) return self::$rules[$args[0]];
        if (count($args)==2 && isset(self::$rules[$args[0]]) && isset(self::$rules[$args[0]][$args[1]]) ) return self::$rules[$args[0]][$args[1]];
        if (count($args)==3 && isset(self::$rules[$args[0]]) && isset(self::$rules[$args[0]][$args[1]]) && isset(self::$rules[$args[0]][$args[1]][$args[2]]) ) return self::$rules[$args[0]][$args[1]][$args[2]];

        //print_r(self::$rules);

	return array();
    }
    
}
