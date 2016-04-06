<?php

class Model_Queue extends Zend_Db_Table_Abstract
{
	const NOW       = 1;
	const MIDNIGHT  = 2;
    const NEXTMONTH = 3;

	protected $_name = 'queue';
    protected $_rowClass = 'Model_QueueRow';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );

	public function add(Playgine_Task_Abstract $task, $message)
	{
		return $this->insert(array(
			'company_id'    => $task->getCompany()->id,
			'type'          => $task->getTaskId(),
			'day'           => $task->getDay(),
            'date'          => date('c'),
            'data'          => json_encode($task->getOptions()),
            'cost'          => $task->getCost(),
            'text'          => $message
        ));
	}
}
