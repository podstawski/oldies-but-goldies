<?php
class Model_ActionRow extends Zend_Db_Table_Row_Abstract
{
	public function addStep($type, array $params)
	{
		$model = new Model_ActionStep();
		$as = $model->createRow();
		$as->action_id = $this->id;
		$as->type = $type;
		$as->data = $params;
		$as->save();
		return $as;
	}

	public function getSteps($direction = Model_Action::DIRECTION_FORWARD)
	{
		$model = new Model_ActionStep();
		$select = $model
			->select(true)
			->where('action_id = ?', $this->id)
			->order('id ' . ($direction == Model_Action::DIRECTION_FORWARD ? 'ASC' : 'DESC'))
			;
		return $model->fetchAll($select);
	}

	public function getExecutedSteps()
	{
		$model = new Model_ActionStep();
		$select = $model
			->select(true)
			->where('action_id = ?', $this->id)
			->order(array('date_executed ASC', 'id ASC'))
			;
		return $model->fetchAll($select);
	}

	public function getNextAction()
	{
		$model = $this->getTable();
		$select = $model
			->select(true)
			->where('domain_id = ?', $this->domain_id)
			->where('id > ?', $this->id)
			->order('id ASC')
			;
		return $model->fetchRow($select);
	}

	public function getPreviousAction()
	{
		$model = $this->getTable();
		$select = $model
			->select(true)
			->where('domain_id = ?', $this->domain_id)
			->where('id < ?', $this->id)
			->order('id DESC')
			;
		return $model->fetchRow($select);
	}
}
?>
