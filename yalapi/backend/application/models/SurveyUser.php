<?php

class SurveyUser extends AclModel
{
    static $table_name = 'survey_users';
    
    static $after_save = 'RunAcl';

    static $belongs_to = array(
        array('survey')
    );
    
    public function RunAcl()
    {
        self::grant(Role::USER,$this->user_id,$this->id);
        self::grant(Role::USER,$this->user_id,$this->survey_id,'surveys');
        self::grant(Role::COACH,$this->user_id,Survey::findAllQuestionsIds($this->survey_id),'survey_questions');
        self::grant(Role::COACH,$this->user_id,Survey::findAllPossibleResultsIds($this->survey_id),'survey_possible_answers');            
        
    }
}