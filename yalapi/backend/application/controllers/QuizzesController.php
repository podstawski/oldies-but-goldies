<?php
/**
 * Description
 * @author Radosław Benkel 
 */

require_once 'RestController.php';
class QuizzesController extends RestController
{
    protected $_modelName = 'Quiz';

    protected $_autoPager = false;

    public function indexAction()
    {
        $filter = function(&$item) {
           $temp = $item->to_array();
           if ($temp['start_time']) {
               $temp['start_time'] = date("d-m-Y H:i:s", $temp['start_time']);
           }
           $item = $temp;
        };
        return $this->_pagedData($filter);
    }

    protected function _getPagerOptionsForModel()
    {
        $tableName = $this->_getTableNameFromModelClass($this->_modelName);
        $options = parent::_getPagerOptionsForModel();
        $options['select'] = "$tableName.*, level, score, total_time, start_time";
        $options['joins']  = "LEFT JOIN quiz_scores ON quiz_scores.quiz_id  = $tableName.id AND quiz_scores.user_id = " . Yala_User::getUid();
        $options['from']   = $tableName;
        return $options;
    }

}
