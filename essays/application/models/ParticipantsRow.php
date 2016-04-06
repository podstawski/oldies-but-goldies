<?php
class Model_ParticipantsRow extends Zend_Db_Table_Row {
	public function getTest() {
		$model = new Model_Tests();
		return $model->fetchRow($model->select()->where('id = ?', $this->test_id));
	}
}
