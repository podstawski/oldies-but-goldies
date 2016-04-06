<?php

class SurveyDetailedResult extends AclModel
{
    static $table_name = 'survey_detailed_results';

    static $belongs_to = array(
        array('survey_result')
    );
    
    static $after_save = 'RunAcl';
    
    public function RunAcl()
    {
        self::grant(Role::COACH,$this->survey_result->survey->user_id,$this->id);
    }    
    
}