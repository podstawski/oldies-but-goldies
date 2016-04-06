<?php

require_once 'RestController.php';

class SurveyGroupsController extends RestController
{
    protected $_modelName = 'Group';

    protected function _getPagerOptionsForModel()
    {
        $options = parent::_getPagerOptionsForModel();

        //frontend.app.form.survey.Send get's data from here
        if (isset($this->getRequest()->method)) {
            $options['from'] = 'groups';
            if (!array_key_exists('total_records', $options)) {
                $options['select']  = 'groups.id, groups.name, groups.advance_level, COUNT(gu.id) AS members,
                                       su.deadline, survey_owner.username, su.sent';
            }
            $options['group']   = 'groups.id, survey_owner.username, groups.name, groups.advance_level, su.deadline, su.sent';

            $options['joins']   = 'LEFT JOIN group_users gu ON gu.group_id = groups.id
                                  

                                  LEFT JOIN survey_users su ON (su.user_id = gu.user_id and su.survey_id='.intval($this->getRequest()->surveyId).')
                                  LEFT JOIN surveys ON (surveys.id = su.survey_id)
                                  LEFT JOIN users AS survey_owner ON survey_owner.id = surveys.user_id';

            /*$options['conditions'] = array(
                'surveys.id is null or surveys.id=?',
                $this->getRequest()->surveyId
            );*/
        
        } else { 
            $options['conditions']  = array('survey_id=?', $this->getRequest()->surveyId);

            $options['select']      = 'groups.id, survey_id, groups.name, groups.advance_level, group_id, avg(percent_result) as average_score, count(*) as replies_count';
            $options['group']       = 'group_id, groups.id, groups.name, groups.advance_level, survey_id';
            $options['order']       = 'groups.id';
            $options['from']        = 'groups';

            $options['joins']       = 'inner join group_users on (group_users.group_id = groups.id)
                                       inner join users on users.id = group_users.user_id
                                       left join survey_results sr on sr.user_id = group_users.user_id';
        }
        return $options;
    }

    public function postAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_METHOD_NOT_ALLOWED);
    }

    public function putAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_METHOD_NOT_ALLOWED);
    }

    public function deleteAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_METHOD_NOT_ALLOWED);
    }
}

