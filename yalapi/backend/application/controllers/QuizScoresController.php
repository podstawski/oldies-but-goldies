<?php

require_once 'RestController.php';

class QuizScoresController extends RestController
{
    protected $_modelName = 'QuizScores';
    protected $_autoPager = false;

    public function indexAction()
    {
        $filter = function(&$item) {
           $temp = $item->to_array();
           $temp['start_time'] = date("d-m-Y H:i:s", $temp['start_time']);
           $item = $temp;
        };
        return $this->_pagedData($filter);
    }

    public function getAction()
    {
        $this->setRestResponseAndExit(null, self::HTTP_BAD_REQUEST);
    }

    public function postAction()
    {
        $this->getAction();
    }

    public function putAction()
    {
        $this->getAction();
    }

    public function deleteAction()
    {
        $this->getAction();
    }

    protected function _getPagerOptionsForModel()
    {
        $options                = parent::_getPagerOptionsForModel();
        $tableName              = $this->_getTableNameFromModelClass($this->_modelName);
        $quizzesTableName       = $this->_getTableNameFromModelClass('Quiz');
        $groupsTableName        = $this->_getTableNameFromModelClass('Group');
        $usersTableName         = $this->_getTableNameFromModelClass('User');
        $userGroupsTableName    = $this->_getTableNameFromModelClass('GroupUser');
        $quizId                 = $this->_getParam('quiz_id', null);

        $options['select'] = "users.first_name || ' ' || users.last_name || ' ' || '(' || users.username || ')' AS username, " .
                             "$quizzesTableName.name, level, score, total_time, start_time, $groupsTableName.name as group_name";
        $options['joins']  = "INNER JOIN $usersTableName ON user_id = $usersTableName.id
                              INNER JOIN $userGroupsTableName ON $userGroupsTableName.user_id = $usersTableName.id
                              INNER JOIN $groupsTableName ON $userGroupsTableName.group_id = $groupsTableName.id
                              INNER JOIN $quizzesTableName ON quiz_id = $quizzesTableName.id AND $quizzesTableName.id = $quizId";
        $options['from']   = $tableName;
        return $options;
    }
}
