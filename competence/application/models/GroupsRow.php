<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Model_GroupsRow extends Zend_Db_Table_Row
{
	public function getAssociatedUsers()
	{
		$model = new Model_Users();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->join('user_groups', 'user_groups.user_id = users.id', array('owner'))
			->where('group_id = ?', $this->id)
			;
		return $model->fetchAll($select);
	}

}
