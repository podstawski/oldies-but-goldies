<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

require_once 'RestController.php';

class ProjectLeadersController extends RestController
{
    protected $_modelName = 'User';

    public function indexAction()
    {
        $columns = User::table()->columns;
        $params = $this->getRequest()->getParams();
        $params['role_id'] = Role::PROJECT_LEADER;

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
            $item->username = sprintf("%s %s (%s)", $item -> first_name, $item -> last_name, $item -> username);
            $item = $item->to_array();
        });

        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }

    public function getAction()
    {
        try {
            $id = $this->_getParam('id');
            $project = Project::find($id);

            if (is_null($project)) {
                throw new ActiveRecord\RecordNotFound('Project with ID ' . $id . ' was not found.');
            }

            $leaders = User::all(array(
                'select' => 'id, username, first_name, last_name, role_id, email',
                'conditions' => array('role_id = ?', Role::PROJECT_LEADER)
            ));

            array_walk($leaders, function (&$leader) use ($id) {
                $leader->username = sprintf("%s %s (%s)", $leader->first_name, $leader->last_name, $leader->username);
                $leader = $leader->to_array();
                $leader['value'] = !!ProjectLeaders::find_by_project_id_and_user_id($id, $leader['id']);
            });

            $this->setRestResponseAndExit($leaders, self::HTTP_OK);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }
}