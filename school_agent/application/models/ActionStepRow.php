<?php
class Model_ActionStepRow extends Zend_Db_Table_Row_Abstract
{
	public $data;

	public function init()
	{
		parent::init();
		$this->data = unserialize(base64_decode($this->params));
	}

	public function save()
	{
		$this->params = base64_encode(serialize($this->data));
		parent::save();
	}
}
?>
