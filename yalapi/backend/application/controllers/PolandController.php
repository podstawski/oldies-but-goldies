<?php

require_once 'RestController.php';

class PolandController extends RestController
{
    public function indexAction()
    {
        $columns = call_user_func(array($this->_modelName, 'table'))->columns;

        if ($conditions = array_intersect_key($this->getRequest()->getParams(), $columns)) {
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
            'from' => $this->_getTableNameFromModelClass($this->_modelName),
            'conditions' => $conditions,
            'order' => 'name ASC'
        );

        $data = call_user_func_array(
            array($this->_modelName, 'all'),
            array($params)
        );

        array_walk($data, function(&$item) {
            $item = $item->to_array();
        });

        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }
}

