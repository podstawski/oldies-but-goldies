<?php

require_once 'RestController.php';

class CoachesController extends RestController
{
    protected $_modelName = 'User';

    public function indexAction()
    {
        $columns = User::table()->columns;
        $params = $this->getRequest()->getParams();
        $params['role_id'] = Role::COACH;
        
        if ($conditions = array_intersect_key($params, $columns)) {
            $conditions = array_filter($conditions, function($value){
                return !empty($value);
            });
            if ($conditions) {
                $expr = trim(implode(' = ? AND ', array_keys($conditions)) . ' = ?');
                $conditions = array_values($conditions);
                array_unshift($conditions, $expr);
            }
        } else {
            $conditions = array();
        }

        $params = array(
            'select' => 'id, CASE is_google WHEN 1 THEN email ELSE username END AS username, role_id, email, first_name, last_name',
            'from' => 'users',
            'conditions' => $conditions,
        );

        $data = User::all($params);

        array_walk($data, function(&$item) {
            $item -> username = sprintf("%s %s (%s)", $item -> first_name, $item -> last_name, $item -> username);
            $item = $item->to_array();
        });

        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }

    protected function _getById($ignoreView = false)
    {
        $id = $this->_getParam('id');
        $row = User::find(
            array(
                'select' => 'id, CASE is_google WHEN 1 THEN email ELSE username END AS username, role_id, email, first_name, last_name',
                'from' => 'users',
                'conditions' => array('id = ? AND role_id IN (?,?)', $id, Role::COACH,Role::ADMIN)
            )
        );

        if (is_null($row)) {
            throw new ActiveRecord\RecordNotFound('Record with ID ' . $id . ' was not found.');
        }

        return $row;
    }

    protected function _getPagerOptionsForModel()
    {
        $dbusername = $this->getInvokeArg('bootstrap')->getOption('db');
        $dbusername = $dbusername['username'];

        $options = parent::_getPagerOptionsForModel();
        if (!array_key_exists('total_records', $options)) {
            $options['select'] = 'id, CASE is_google WHEN 1 THEN email ELSE username END AS username, role_id, email, first_name, last_name';
        }
        $conditions = 'username <> \'' . $dbusername . '\' AND role_id IN (' . Role::COACH . ',' . Role::ADMIN . ')';
        if (array_key_exists('conditions', $options)) {
            $options['conditions'] .= ' AND (' . $conditions . ')';
        } else {
            $options['conditions'] = $conditions;
        }
        return $options;
    }
}

