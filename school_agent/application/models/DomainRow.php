<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_DomainRow extends GN_Model_DomainRow
{
	//aktualna "migracja"
	public function getActiveAction()
	{
		$modelAction = new Model_Action();
		$select = $modelAction->selectByDomainId($this->id);
		$select->where('active = TRUE');
		return $modelAction->fetchRow($select);
	}

	public function getLastAction()
	{
		$modelAction = new Model_Action();
		$select = $modelAction->selectByDomainId($this->id);
		$select->order('id DESC');
		return $modelAction->fetchRow($select);
	}

	public function getFirstAction()
	{
		$modelAction = new Model_Action();
		$select = $modelAction->selectByDomainId($this->id);
		$select->order('id ASC');
		return $modelAction->fetchRow($select);
	}

	//akcja która się teraz wykonuje
	public function getWorkingAction()
	{
		$modelAction = new Model_Action();
		$select = $modelAction->selectByDomainId($this->id);
		$select->where('date_start IS NOT NULL');
		$select->where('date_end IS NULL');
		$select->order('id ASC');
		return $modelAction->fetchRow($select);
	}

	public function setActiveActionId($actionId)
	{
		$modelAction = new Model_Action();
		$modelAction->update(array('active' => 'FALSE'), array('domain_id = ?' => $this->id));
		$modelAction->update(array('active' => 'TRUE'), array('domain_id = ?' => $this->id, 'id = ?' => $actionId));
		return true;
	}
}
