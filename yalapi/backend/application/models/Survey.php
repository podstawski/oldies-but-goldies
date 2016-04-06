<?php

class Survey extends AclModel
{
    static $table_name = 'surveys';
    static $use_view = true;
    
    static $after_save = 'RunAcl';

    static $validates_presence_of = array(
        array('name'),
    );

    static $has_many = array(
        array('questions'),
        array('survey_users')
    );
    static $belongs_to = array(
        array('user')
    );

    public static function findAllQuestionsIds($survey_id)
    {
        return self::getArrayOfFieldValues(SurveyQuestion::find('all',array('conditions'=>array('survey_id=?',$survey_id))));
    }

    public static function findAllPossibleResultsIds($survey_id)
    {
        $question_ids=self::findAllQuestionsIds($survey_id);
        return count($question_ids)?self::getArrayOfFieldValues(SurveyPossibleAnswer::find('all',array('conditions'=>array('question_id IN (?)',$question_ids)))):array();    
    }


    public function RunAcl()
    {
        if ($this->field_has_changed('library'))
        {
            list($old,$new)=$this->get_field_change('library');
            
            $users=Role::findAllUserIds(Role::COACH);
            $owner=$this->user_id;
            $i=array_search($owner,$users);
            if (strlen($i)) unset($users[$i]);
            if ($new)
            {
                self::grant(Role::COACH,$users,$this->id);
                self::grant(Role::COACH,$users,self::findAllQuestionsIds($this->id),'survey_questions');
                self::grant(Role::COACH,$users,self::findAllPossibleResultsIds($this->id),'survey_possible_answers');            
            }
            else
            {
                self::revoke(Role::COACH,$users,$this->id);
                self::revoke(Role::COACH,$users,self::findAllQuestionsIds($this->id),'survey_questions');
                self::revoke(Role::COACH,$users,self::findAllPossibleResultsIds($this->id),'survey_possible_answers');            
                
            }
        }
    }


    private static function _forUser(array $options, array $params)
    {
        $conditions = array();
        $conditionValues = array();


        //If user, return only surveys assigned for him
        if ($params['identity']->role_id == Role::USER) {

            $join = 'RIGHT JOIN survey_users su
                     ON (su.survey_id = '.self::getTableName().'.id AND
                     su.user_id = ' . ((int)$params['identity']->id) . ')';

            $options['joins'][] = $join;

            if (isset($params['filled'])) {
                $conditions[] = 'su.filled=?';
                $conditionValues[] = (($params['filled'] == "true") ? 1 : 0);
            }
        }

        if (isset($params['archived'])) {
            if ($params['archived']) {
                $conditions[] = self::getTableName().'.archived=1';
            } else {
                $conditions[] = '('.self::getTableName().'.archived = 0 or '.self::getTableName().'.archived is null)';
            }

        }
        if (isset($params['type'])) {
            $conditions[] = self::getTableName().'.type=?';
            $conditionValues[] = $params['type'];
        }

        //If coach or manager, show surveys that they have created
        if ($params['identity']->role_id != Role::ADMIN &&
            $params['identity']->role_id != Role::USER) {

            $conditions[] = self::getTableName().'.user_id = ?';
            $conditionValues[] = $params['identity']->id;
        }

        array_unshift($conditionValues, join(" AND ", $conditions));
        $options['conditions'] = $conditionValues;

        return $options;
    }

    public static function allForUser(array $options, array $params)
    {
        
        $options['select'] = self::getTableName().'.*, users.username';

        if (Yala_User::getRoleName() == "user") {
            $options['select'] .= ', su.deadline';
        }

        $options['order'] = null;
        $options['joins'][] = 'LEFT JOIN users ON users.id = '.self::getTableName().'.user_id';

        return Survey::all(self::_forUser($options, $params));
    }

    public static function firstForUser(array $options, $params)
    {
        return Survey::first(self::_forUser($options, $params));
    }

    public static function findForUser($id, array $params)
    {
        $options = self::_forUser(array(), $params);
        $options['conditions'] = self::getTableName().'.id=' . $id;

        if (isset($params['filled'])) {
            $options['conditions'] .=
                    ' and su.filled=' .
                    ($params['filled'] == "true") ? 1 : 0;
        }

        $options['joins'][] =
                'LEFT JOIN survey_questions
                 ON survey_questions.survey_id = '.self::getTableName().'.id';

        return Survey::find($id, $options);

    }
    public static function isSent($id)
    {
        return SurveyUser::count(array('conditions'=>array('survey_id'=>$id)));
    }

    public static function isResolved($id)
    {
        return SurveyResult::count(array('conditions'=>array('survey_id'=>$id)));
    }
    
    
  
}
